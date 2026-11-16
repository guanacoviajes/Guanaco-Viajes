<?php
declare(strict_types=1);

use App\Services\ImageService;
use PHPUnit\Framework\TestCase;

final class ImageServiceTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/gv-img-' . bin2hex(random_bytes(4));
        mkdir($this->dir, 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/{,*/,*/*/,*/*/*/}*', GLOB_BRACE) ?: [] as $f) {
            is_file($f) && unlink($f);
        }
        foreach (array_reverse(glob($this->dir . '/{,*/,*/*/}*', GLOB_BRACE) ?: []) as $d) {
            is_dir($d) && @rmdir($d);
        }
        @rmdir($this->dir);
    }

    private function file(string $name, string $bytes): array
    {
        $p = $this->dir . '/' . $name;
        file_put_contents($p, $bytes);
        return ['name' => $name, 'tmp_name' => $p, 'error' => UPLOAD_ERR_OK, 'size' => strlen($bytes), 'type' => 'image/jpeg'];
    }

    private function jpeg(int $w, int $h, array $rgb = [120, 160, 90]): string
    {
        $im = imagecreatetruecolor($w, $h);
        imagefill($im, 0, 0, imagecolorallocate($im, ...$rgb));
        ob_start();
        imagejpeg($im, null, 90);
        return (string) ob_get_clean();
    }

    public function testRejectsNonImages(): void
    {
        $this->assertStringContainsString('JPG, PNG o WebP', (string) ImageService::check($this->file('a.jpg', '<?php system($_GET["c"]); ?>')));
        $this->assertStringContainsString('JPG, PNG o WebP', (string) ImageService::check($this->file('b.jpg', 'Esto no es una imagen')));
        $this->assertStringContainsString('JPG, PNG o WebP', (string) ImageService::check($this->file('c.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>')));
        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        $this->assertStringContainsString('JPG, PNG o WebP', (string) ImageService::check($this->file('d.jpg', $gif)));
    }

    public function testRejectsEmptyAndMissingFiles(): void
    {
        $this->assertNotNull(ImageService::check($this->file('v.jpg', '')));
        $this->assertSame('No has elegido ninguna imagen.', ImageService::check(['error' => UPLOAD_ERR_NO_FILE]));
        $this->assertStringContainsString('pesa demasiado', (string) ImageService::check(['error' => UPLOAD_ERR_INI_SIZE]));
        $this->assertNotNull(ImageService::check(['error' => UPLOAD_ERR_OK, 'tmp_name' => '/no/existe.jpg']));
        $this->assertNotNull(ImageService::check(['error' => UPLOAD_ERR_PARTIAL]));
    }

    public function testRejectsHugeDimensions(): void
    {
        $png = fn(int $w, int $h) => (function () use ($w, $h) {
            $im = imagecreate($w, $h);
            imagecolorallocate($im, 1, 1, 1);
            ob_start();
            imagepng($im, null, 9);
            return (string) ob_get_clean();
        })();
        $this->assertStringContainsString('demasiado grande', (string) ImageService::check($this->file('ancha.png', $png(9000, 40))));
        $this->assertStringContainsString('demasiado grande', (string) ImageService::check($this->file('mp.png', $png(7000, 6000))));
        $this->assertNull(ImageService::check($this->file('ok.png', $png(2000, 1000))));
    }

    public function testMimeComesFromContentNotFromName(): void
    {
        // un JPEG con extensión .png se acepta (manda el contenido) y un PHP con extensión .jpg se rechaza
        $this->assertNull(ImageService::check($this->file('jpeg-con-ext.png', $this->jpeg(100, 80))));
        $this->assertNotNull(ImageService::check($this->file('php.jpg', "\xFF\xD8\xFF\xE0<?php echo 1;")));
    }

    public function testStoreCreatesMainAndSmallJpegWithRandomName(): void
    {
        $r = ImageService::store($this->file('foto original con ñ.jpg', $this->jpeg(4000, 2000)), true, $this->dir);
        $this->assertNull($r['error']);
        $this->assertMatchesRegularExpression('#^uploads/\d{4}/\d{2}/[a-f0-9]{24}\.jpg$#', (string) $r['path']);
        $main = $this->dir . '/' . $r['path'];
        $small = preg_replace('/\.jpg$/', '-800.jpg', $main);
        $medium = preg_replace('/\.jpg$/', '-1400.jpg', $main);
        $this->assertFileExists($main);
        $this->assertFileExists($small);
        $this->assertFileExists($medium);
        $this->assertSame([2560, 1280], array_slice(getimagesize($main), 0, 2));
        $this->assertSame([1400, 700], array_slice(getimagesize($medium), 0, 2));
        $this->assertSame([800, 400], array_slice(getimagesize($small), 0, 2));
        $this->assertSame('image/jpeg', getimagesize($main)['mime']);
        $this->assertStringNotContainsString('original', $r['path']);
    }

    public function testNeverUpscales(): void
    {
        $r = ImageService::store($this->file('p.jpg', $this->jpeg(500, 300)), false, $this->dir);
        $this->assertSame([500, 300], array_slice(getimagesize($this->dir . '/' . $r['path']), 0, 2));
    }

    public function testHiddenCodeInJpegIsRemoved(): void
    {
        $payload = $this->jpeg(300, 200) . "\n<?php echo 'pwned'; system(\$_GET['c']); ?>";
        $payload = substr_replace($payload, "\xFF\xFE\x00\x1C<?php echo 'x'; ?>pad", 2, 0);
        $r = ImageService::store($this->file('c.jpg', $payload), false, $this->dir);
        $this->assertNull($r['error']);
        $out = (string) file_get_contents($this->dir . '/' . $r['path']);
        $this->assertStringNotContainsString('<?php', $out);
        $this->assertStringNotContainsString('pwned', $out);
        $this->assertStringNotContainsString('system(', $out);
    }

    public function testTransparentPngBecomesWhiteBackground(): void
    {
        $im = imagecreatetruecolor(100, 100);
        imagesavealpha($im, true);
        imagefill($im, 0, 0, imagecolorallocatealpha($im, 0, 0, 0, 127));
        ob_start();
        imagepng($im);
        $r = ImageService::store($this->file('t.png', (string) ob_get_clean()), false, $this->dir);
        $img = imagecreatefromjpeg($this->dir . '/' . $r['path']);
        $c = imagecolorat($img, 5, 5);
        $this->assertGreaterThan(240, ($c >> 16) & 255);
        $this->assertGreaterThan(240, $c & 255);
    }

    public function testGradePullsColoursTowardsTheBrandPalette(): void
    {
        $mk = function (array $rgb) {
            $im = imagecreatetruecolor(2, 2);
            imagefill($im, 0, 0, imagecolorallocate($im, ...$rgb));
            ImageService::grade($im);
            $c = imagecolorat($im, 0, 0);
            return [($c >> 16) & 255, ($c >> 8) & 255, $c & 255];
        };
        [$r, $g, $b] = $mk([0, 0, 0]);          // negro → casi negro con matiz verdoso (30 % hacia el carbón #1F2D28)
        $this->assertEqualsWithDelta(9, $r, 3);
        $this->assertEqualsWithDelta(14, $g, 3);
        $this->assertEqualsWithDelta(12, $b, 3);
        $this->assertTrue($g > $r && $b > $r);
        [$r, $g, $b] = $mk([255, 255, 255]);    // blanco → hacia el marfil, sin llegar a blanco puro
        $this->assertLessThan(765, $r + $g + $b);
        $this->assertGreaterThan(225, min($r, $g, $b));
        [$r, $g, $b] = $mk([0, 120, 255]);      // azul saturado → bastante menos saturado
        $sat = (max($r, $g, $b) - min($r, $g, $b)) / max($r, $g, $b, 1);
        $this->assertLessThan(0.65, $sat);
        [$r, $g, $b] = $mk([128, 128, 128]);    // gris → sigue siendo gris
        $this->assertLessThan(14, max($r, $g, $b) - min($r, $g, $b));
    }

    public function testFilesHelperReturnsMainAndSmall(): void
    {
        $f = ImageService::files('uploads/2026/11/abc.jpg');
        $this->assertCount(3, $f);
        $this->assertStringEndsWith('/uploads/2026/11/abc.jpg', $f[0]);
        $this->assertStringEndsWith('/uploads/2026/11/abc-800.jpg', $f[1]);
        $this->assertStringEndsWith('/uploads/2026/11/abc-1400.jpg', $f[2]);
    }

    public function testMainKeepsHighResolutionUpTo2560(): void
    {
        $r = ImageService::store($this->file('g.jpg', $this->jpeg(2200, 1400)), false, $this->dir);
        $this->assertNull($r['error']);
        $this->assertSame([2200, 1400], array_slice(getimagesize($this->dir . '/' . $r['path']), 0, 2), 'una foto de 2200 px no se reduce a 1800');
    }

    public function testNoMediumVariantWhenPhotoIsNotBiggerThanIt(): void
    {
        $r = ImageService::store($this->file('m.jpg', $this->jpeg(1400, 900)), false, $this->dir);
        $this->assertFileDoesNotExist($this->dir . '/' . preg_replace('/\.jpg$/', '-1400.jpg', $r['path']));
        $this->assertFileExists($this->dir . '/' . preg_replace('/\.jpg$/', '-800.jpg', $r['path']));
    }

    public function testFullResolutionColorAndNoLossOfDetail(): void
    {
        // tablero de líneas finas rojas sobre blanco: con color submuestreado (4:2:0) se emborronan
        $im = imagecreatetruecolor(1600, 1000);
        imagefill($im, 0, 0, imagecolorallocate($im, 255, 255, 255));
        $red = imagecolorallocate($im, 200, 20, 20);
        for ($x = 0; $x < 1600; $x += 6) {
            imagefilledrectangle($im, $x, 0, $x + 1, 999, $red);
        }
        ob_start();
        imagejpeg($im, null, 100);
        $bytes = (string) ob_get_clean();
        $r = ImageService::store($this->file('l.jpg', $bytes), false, $this->dir);
        $out = imagecreatefromjpeg($this->dir . '/' . $r['path']);
        $c = imagecolorat($out, 600, 500); // una línea roja
        $this->assertGreaterThan(150, ($c >> 16) & 255);
        $this->assertLessThan(90, $c & 255, 'el rojo no se desvanece hacia el blanco');
        // 4:4:4 = submuestreo 1×1 en la cabecera JPEG (componentes Y, Cb, Cr con factor 0x11)
        $head = file_get_contents($this->dir . '/' . $r['path'], false, null, 0, 4096);
        $this->assertSame(1, preg_match('/\xFF[\xC0\xC2]\x00\x11.{5}\x03\x01\x11/s', $head), 'el color se guarda a resolución completa');
    }

    public function testTransparentPngGoesOnWhite(): void
    {
        $im = imagecreatetruecolor(300, 200);
        imagealphablending($im, false);
        imagesavealpha($im, true);
        imagefill($im, 0, 0, imagecolorallocatealpha($im, 0, 0, 0, 127));
        ob_start();
        imagepng($im);
        $png = (string) ob_get_clean();
        $file = $this->file('t.png', $png);
        $file['type'] = 'image/png';
        $r = ImageService::store($file, false, $this->dir);
        $this->assertNull($r['error']);
        $c = imagecolorat(imagecreatefromjpeg($this->dir . '/' . $r['path']), 150, 100);
        $this->assertGreaterThan(240, ($c >> 16) & 255, 'la transparencia queda blanca, no negra');
    }
}
