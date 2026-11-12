<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Subida segura de imágenes desde el panel.
 *
 * Nunca se conserva el archivo original: se decodifica, se redimensiona y se vuelve a codificar como JPEG
 * (así desaparece cualquier código escondido, metadatos o GPS). El nombre lo genera el servidor.
 * Opcionalmente aplica el tratamiento de color de la marca (el mismo de todas las fotos de la web).
 */
final class ImageService
{
    public const MAX_BYTES = 12 * 1024 * 1024;
    public const MAX_SIDE_IN = 8000;          // lado máximo permitido al subir
    public const MAX_PIXELS_IN = 40_000_000;  // 40 megapíxeles
    public const MAIN = 1800;                 // lado largo de la imagen principal
    public const SMALL = 800;                 // versión pequeña para móviles
    public const QUALITY = 82;

    private const ALLOWED = ['image/jpeg' => 'jpeg', 'image/png' => 'png', 'image/webp' => 'webp'];

    /**
     * @param array<string,mixed> $file entrada de $_FILES
     * @return array{path: ?string, error: ?string} ruta relativa a public/ (p. ej. «uploads/2026/11/ab12.jpg»)
     */
    public static function store(array $file, bool $grade = true, ?string $root = null): array
    {
        $root ??= PUBLIC_PATH;
        $err = self::check($file);
        if ($err !== null) {
            return ['path' => null, 'error' => $err];
        }
        $tmp = (string) $file['tmp_name'];
        $info = @getimagesize($tmp);
        $type = $info['mime'] ?? '';
        $fn = 'imagecreatefrom' . self::ALLOWED[$type];
        $img = @$fn($tmp);
        if ($img === false) {
            return ['path' => null, 'error' => 'No se ha podido leer la imagen. Prueba a exportarla de nuevo como JPG.'];
        }
        if ($type === 'image/jpeg') {
            $img = self::orient($img, $tmp);
        }
        $main = self::fit($img, self::MAIN);
        imagedestroy($img);
        if ($grade) {
            self::grade($main);
        }
        $small = self::fit($main, self::SMALL);

        $dir = $root . '/uploads/' . date('Y') . '/' . date('m');
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            return ['path' => null, 'error' => 'No se puede escribir en la carpeta de subidas.'];
        }
        $name = bin2hex(random_bytes(12));
        $ok = self::saveJpeg($main, "$dir/$name.jpg") && self::saveJpeg($small, "$dir/$name-" . self::SMALL . '.jpg');
        imagedestroy($main);
        imagedestroy($small);
        if (!$ok) {
            return ['path' => null, 'error' => 'No se ha podido guardar la imagen.'];
        }
        return ['path' => 'uploads/' . date('Y') . '/' . date('m') . "/$name.jpg", 'error' => null];
    }

    /** Devuelve un mensaje de error (en español) o null si el archivo es aceptable. */
    public static function check(array $file): ?string
    {
        $code = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($code === UPLOAD_ERR_NO_FILE) {
            return 'No has elegido ninguna imagen.';
        }
        if ($code === UPLOAD_ERR_INI_SIZE || $code === UPLOAD_ERR_FORM_SIZE) {
            return 'La imagen pesa demasiado (máximo ' . (self::MAX_BYTES / 1048576) . ' MB).';
        }
        if ($code !== UPLOAD_ERR_OK) {
            return 'La imagen no se ha subido bien. Inténtalo de nuevo.';
        }
        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_file($tmp) || !(is_uploaded_file($tmp) || (defined('IMAGE_TEST_MODE') && IMAGE_TEST_MODE))) {
            return 'La imagen no se ha subido bien. Inténtalo de nuevo.';
        }
        if (filesize($tmp) > self::MAX_BYTES) {
            return 'La imagen pesa demasiado (máximo ' . (self::MAX_BYTES / 1048576) . ' MB).';
        }
        // El tipo se decide por el CONTENIDO real del archivo, nunca por su nombre ni por lo que diga el navegador.
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp) ?: '';
        if (!isset(self::ALLOWED[$mime])) {
            return 'Solo se admiten imágenes JPG, PNG o WebP.';
        }
        $info = @getimagesize($tmp);
        if ($info === false || ($info['mime'] ?? '') !== $mime) {
            return 'El archivo no es una imagen válida.';
        }
        [$w, $h] = $info;
        if ($w < 1 || $h < 1 || $w > self::MAX_SIDE_IN || $h > self::MAX_SIDE_IN || $w * $h > self::MAX_PIXELS_IN) {
            return 'La imagen es demasiado grande (máximo 8000 px de lado y 40 megapíxeles). Reduce su tamaño y vuelve a subirla.';
        }
        if (!self::fitsInMemory($w, $h)) {
            return 'La imagen necesita más memoria de la que permite el servidor. Redúcela a unos 4000 px de lado.';
        }
        return null;
    }

    private static function fitsInMemory(int $w, int $h): bool
    {
        $limit = self::bytes((string) ini_get('memory_limit'));
        if ($limit <= 0) {
            return true; // sin límite
        }
        // GD usa ~4 bytes/píxel; se necesitan la original, la principal y la reducida
        $need = (int) ($w * $h * 4 * 1.8) + 12 * 1048576;
        return $need < $limit - memory_get_usage(true);
    }

    private static function bytes(string $v): int
    {
        $v = trim($v);
        if ($v === '' || $v === '-1') {
            return 0;
        }
        $n = (int) $v;
        return match (strtolower(substr($v, -1))) {
            'g' => $n * 1073741824,
            'm' => $n * 1048576,
            'k' => $n * 1024,
            default => $n,
        };
    }

    /** Corrige la orientación según el EXIF (al recodificar se pierde, así que se aplica antes). */
    private static function orient(\GdImage $img, string $file): \GdImage
    {
        if (!function_exists('exif_read_data')) {
            return $img;
        }
        $ex = @exif_read_data($file);
        $o = (int) ($ex['Orientation'] ?? 1);
        $angle = [3 => 180, 6 => -90, 8 => 90][$o] ?? 0;
        if ($angle !== 0) {
            $r = imagerotate($img, $angle, 0);
            if ($r !== false) {
                imagedestroy($img);
                return $r;
            }
        }
        return $img;
    }

    /** Reduce (nunca amplía) para que el lado largo no pase de $max. */
    private static function fit(\GdImage $src, int $max): \GdImage
    {
        $w = imagesx($src);
        $h = imagesy($src);
        $scale = min(1.0, $max / max($w, $h));
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));
        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, true);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255)); // los PNG con transparencia quedan sobre blanco
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        return $dst;
    }

    private static function saveJpeg(\GdImage $img, string $path): bool
    {
        imageinterlace($img, true);
        $ok = imagejpeg($img, $path, self::QUALITY);
        if ($ok) {
            @chmod($path, 0644);
        }
        return $ok;
    }

    /**
     * Tratamiento de color de la marca (el mismo que se aplicó a todas las fotos):
     *  · saturación general ×0,66 y de cianes/azules (tono 150–270°) ×0,55
     *  · sombras hacia el carbón #1F2D28 (peso 0,30, curva (1−L)^1,6) · luces hacia el marfil #F3EEE4 (peso 0,14, curva L²)
     *  · canal azul ×0,97
     */
    public static function grade(\GdImage $img): void
    {
        $w = imagesx($img);
        $h = imagesy($img);
        $carbon = [0x1F / 255, 0x2D / 255, 0x28 / 255];
        $marfil = [0xF3 / 255, 0xEE / 255, 0xE4 / 255];
        // tablas precalculadas: evita hacer potencias por cada píxel
        $ws = [];
        $wh = [];
        for ($i = 0; $i <= 255; $i++) {
            $l = $i / 255;
            $ws[$i] = ((1 - $l) ** 1.6) * 0.30;
            $wh[$i] = ($l ** 2) * 0.14;
        }
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $c = imagecolorat($img, $x, $y);
                $r = (($c >> 16) & 255) / 255;
                $g = (($c >> 8) & 255) / 255;
                $b = ($c & 255) / 255;

                // 1 · saturación selectiva de cianes y azules (HSV)
                $mx = max($r, $g, $b);
                $mn = min($r, $g, $b);
                $d = $mx - $mn;
                if ($d > 0 && $mx > 0) {
                    if ($mx === $r) {
                        $hue = 60 * fmod((($g - $b) / $d), 6);
                    } elseif ($mx === $g) {
                        $hue = 60 * ((($b - $r) / $d) + 2);
                    } else {
                        $hue = 60 * ((($r - $g) / $d) + 4);
                    }
                    if ($hue < 0) {
                        $hue += 360;
                    }
                    if ($hue > 150 && $hue < 270) {
                        $k = 0.55; // misma saturación nueva = s×0,55: se acerca cada canal al máximo
                        $r = $mx - ($mx - $r) * $k;
                        $g = $mx - ($mx - $g) * $k;
                        $b = $mx - ($mx - $b) * $k;
                    }
                }
                // 2 · saturación general
                $l = 0.299 * $r + 0.587 * $g + 0.114 * $b;
                $r = $l + ($r - $l) * 0.66;
                $g = $l + ($g - $l) * 0.66;
                $b = $l + ($b - $l) * 0.66;
                // 3 · sombras al carbón, luces al marfil
                $li = (int) round(max(0.0, min(1.0, $l)) * 255);
                $s = $ws[$li];
                $hi = $wh[$li];
                $keep = 1 - $s - $hi;
                $r = $r * $keep + $carbon[0] * $s + $marfil[0] * $hi;
                $g = $g * $keep + $carbon[1] * $s + $marfil[1] * $hi;
                $b = ($b * $keep + $carbon[2] * $s + $marfil[2] * $hi) * 0.97;

                imagesetpixel($img, $x, $y, (self::clamp($r) << 16) | (self::clamp($g) << 8) | self::clamp($b));
            }
        }
    }

    private static function clamp(float $v): int
    {
        return $v <= 0 ? 0 : ($v >= 1 ? 255 : (int) round($v * 255));
    }

    /** Rutas de los archivos (principal y pequeño) de una imagen subida. @return list<string> */
    public static function files(string $path): array
    {
        $out = [PUBLIC_PATH . '/' . $path];
        $small = preg_replace('/\.jpg$/', '-' . self::SMALL . '.jpg', $path);
        if ($small !== null && $small !== $path) {
            $out[] = PUBLIC_PATH . '/' . $small;
        }
        return $out;
    }
}
