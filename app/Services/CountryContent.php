<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Contenido ampliado de la página de país: zonas (cada una con su época recomendada y, si se quiere, una foto) y preguntas frecuentes.
 * Se guarda como JSON en columnas de texto. Aquí se valida lo que llega del panel (con topes) y se lee de forma
 * tolerante lo guardado (si el JSON está roto o falta la columna, simplemente no hay contenido).
 */
final class CountryContent
{
    public const MAX_ZONAS = 4;
    public const MAX_FAQ = 6;
    public const ZONA_DESC_MAX = 120;
    /** Forma de las rutas de las fotos que guarda el panel. */
    public const FOTO_PATTERN = '#^uploads/\d{4}/\d{2}/[a-f0-9]{24}\.jpg$#';

    /** Definición de cada lista: columnas (clave => [etiqueta, máximo, obligatoria]) y número máximo de bloques. */
    public static function spec(string $list): array
    {
        return match ($list) {
            'zonas' => ['max' => self::MAX_ZONAS, 'singular' => 'zona', 'cols' => [
                'nombre'      => ['Nombre de la zona', 60, true],
                'dias'        => ['Días recomendados', 30, false],
                'descripcion' => ['Descripción breve', self::ZONA_DESC_MAX, false],
                'epoca'       => ['Cuándo ir (época recomendada)', 80, false],
                'foto'        => ['Foto', 255, false],   // ruta de una imagen ya subida (la sube el panel, no se escribe a mano)
            ]],
            'faqs' => ['max' => self::MAX_FAQ, 'singular' => 'pregunta', 'cols' => [
                'pregunta'  => ['Pregunta', 160, true],
                'respuesta' => ['Respuesta', 1000, true],
            ]],
            default => throw new \InvalidArgumentException("Lista desconocida: $list"),
        };
    }

    /**
     * Limpia lo enviado desde el formulario: ignora los bloques totalmente vacíos y comprueba obligatorios y longitudes.
     * @param mixed $raw lo que llega en $_POST[campo] (array de bloques) o cualquier otra cosa
     * @return array{0: list<array<string,string>>, 1: ?string} bloques válidos y mensaje de error (o null)
     */
    public static function clean(string $list, mixed $raw): array
    {
        $spec = self::spec($list);
        $out = [];
        $blocks = is_array($raw) ? array_values($raw) : [];
        foreach ($blocks as $i => $b) {
            if (!is_array($b)) {
                continue;
            }
            $row = [];
            foreach ($spec['cols'] as $k => [$label]) {
                $v = $b[$k] ?? '';
                $v = is_string($v) ? str_replace(["\r\n", "\r"], "\n", $v) : '';
                $v = $k === 'respuesta'
                    ? trim((string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/u', '', $v))
                    : trim((string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $v));
                if ($k === 'foto' && !preg_match(self::FOTO_PATTERN, $v)) {
                    $v = '';
                }
                $row[$k] = $v;
            }
            if (implode('', array_diff_key($row, ['foto' => 1])) === '') {
                continue; // casilla vacía (una foto suelta no cuenta)
            }
            $n = count($out) + 1;
            foreach ($spec['cols'] as $k => [$label, $max, $req]) {
                if ($req && $row[$k] === '') {
                    return [[], "En la {$spec['singular']} {$n} falta «{$label}»."];
                }
                if (mb_strlen($row[$k]) > $max) {
                    return [[], "En la {$spec['singular']} {$n}, «{$label}» es demasiado largo (máximo {$max} caracteres)."];
                }
            }
            $out[] = $row;
        }
        if (count($out) > $spec['max']) {
            return [[], "Hay demasiados bloques (máximo {$spec['max']})."];
        }
        return [$out, null];
    }

    /** @param list<array<string,string>> $blocks */
    public static function encode(array $blocks): ?string
    {
        return $blocks === [] ? null : json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Lee lo guardado. Tolerante: null, vacío, JSON roto o con otra forma → lista vacía.
     * @return list<array<string,string>>
     */
    public static function decode(string $list, mixed $stored): array
    {
        if (!is_string($stored) || trim($stored) === '') {
            return [];
        }
        $data = json_decode($stored, true);
        if (!is_array($data)) {
            return [];
        }
        $spec = self::spec($list);
        $out = [];
        foreach ($data as $b) {
            if (!is_array($b)) {
                continue;
            }
            $row = [];
            foreach (array_keys($spec['cols']) as $k) {
                $row[$k] = is_string($b[$k] ?? null) ? $b[$k] : '';
                if ($k === 'foto' && !preg_match(self::FOTO_PATTERN, $row[$k])) {
                    $row[$k] = '';
                }
            }
            $req = array_keys(array_filter($spec['cols'], static fn($c) => $c[2]));
            if (array_filter($req, static fn($k) => $row[$k] === '') !== []) {
                continue; // un bloque sin lo obligatorio no se enseña
            }
            $out[] = $row;
            if (count($out) >= $spec['max']) {
                break;
            }
        }
        return $out;
    }

    /** Párrafos de un texto de varias líneas (para las respuestas). @return list<string> */
    public static function paragraphs(string $text): array
    {
        $parts = preg_split('/\n{2,}/', trim($text)) ?: [];
        return array_values(array_filter(array_map(static fn($p) => trim($p), $parts), static fn($p) => $p !== ''));
    }
}
