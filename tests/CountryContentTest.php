<?php
declare(strict_types=1);

use App\Core\Seo;
use App\Services\CountryContent;
use PHPUnit\Framework\TestCase;

final class CountryContentTest extends TestCase
{
    public function testEmptySlotsAreIgnored(): void
    {
        [$out, $err] = CountryContent::clean('zonas', [
            ['nombre' => '', 'descripcion' => '', 'dias' => ''],
            ['nombre' => ' Norte ', 'descripcion' => 'Desierto', 'dias' => '4-6 días'],
            ['nombre' => '', 'descripcion' => '', 'dias' => ''],
        ]);
        $this->assertNull($err);
        $this->assertSame([['nombre' => 'Norte', 'dias' => '4-6 días', 'descripcion' => 'Desierto', 'epoca' => '', 'foto' => '']], $out);
    }

    public function testNothingFilledMeansNoContentAndNullInDatabase(): void
    {
        [$out, $err] = CountryContent::clean('faqs', [['pregunta' => '', 'respuesta' => '']]);
        $this->assertNull($err);
        $this->assertSame([], $out);
        $this->assertNull(CountryContent::encode($out));
        $this->assertSame([], CountryContent::clean('faqs', 'no es una lista')[0]);
    }

    public function testRequiredFieldMissingGivesClearError(): void
    {
        [, $err] = CountryContent::clean('zonas', [['nombre' => '', 'descripcion' => 'Solo descripción', 'dias' => '']]);
        $this->assertStringContainsString('falta «Nombre de la zona»', (string) $err);
        [, $err] = CountryContent::clean('faqs', [['pregunta' => '¿Algo?', 'respuesta' => '']]);
        $this->assertStringContainsString('falta «Respuesta»', (string) $err);
    }

    public function testZoneDescriptionIsCappedAt120(): void
    {
        [$ok] = CountryContent::clean('zonas', [['nombre' => 'A', 'descripcion' => str_repeat('x', 120), 'dias' => '']]);
        $this->assertCount(1, $ok);
        [, $err] = CountryContent::clean('zonas', [['nombre' => 'A', 'descripcion' => str_repeat('x', 121), 'dias' => '']]);
        $this->assertStringContainsString('máximo 120', (string) $err);
        // los acentos cuentan como un carácter (no como bytes)
        [$ok2] = CountryContent::clean('zonas', [['nombre' => 'A', 'descripcion' => str_repeat('ñ', 120), 'dias' => '']]);
        $this->assertCount(1, $ok2);
    }

    public function testZonePhotoOnlyAcceptsUploadedImagePaths(): void
    {
        $ok = 'uploads/2026/11/' . str_repeat('ab', 12) . '.jpg';
        [$out] = CountryContent::clean('zonas', [['nombre' => 'Norte', 'foto' => $ok]]);
        $this->assertSame($ok, $out[0]['foto']);
        foreach (['../../config/config.php', '/etc/passwd', 'https://evil.example/x.jpg', 'uploads/2026/11/x.php', 'uploads/../app/x.jpg', "uploads/2026/11/{$ok}"] as $bad) {
            [$out] = CountryContent::clean('zonas', [['nombre' => 'Norte', 'foto' => $bad]]);
            $this->assertSame('', $out[0]['foto'], 'debe descartar: ' . $bad);
            $json = json_encode([['nombre' => 'Norte', 'foto' => $bad]]);
            $this->assertSame('', CountryContent::decode('zonas', $json)[0]['foto'], 'al leer, debe descartar: ' . $bad);
        }
    }

    public function testAPhotoAloneDoesNotMakeABlock(): void
    {
        [$out, $err] = CountryContent::clean('zonas', [['nombre' => '', 'foto' => 'uploads/2026/11/' . str_repeat('ab', 12) . '.jpg']]);
        $this->assertNull($err);
        $this->assertSame([], $out);
    }

    public function testZoneSeasonIsOptionalAndCapped(): void
    {
        [$ok, $err] = CountryContent::clean('zonas', [['nombre' => 'Norte', 'epoca' => str_repeat('e', 80)]]);
        $this->assertNull($err);
        $this->assertSame('', $ok[0]['dias']);
        [, $err] = CountryContent::clean('zonas', [['nombre' => 'Norte', 'epoca' => str_repeat('e', 81)]]);
        $this->assertStringContainsString('máximo 80', (string) $err);
        // un contenido guardado con la versión anterior (sin época) se sigue leyendo bien
        $old = json_encode([['nombre' => 'Sur', 'descripcion' => 'Lagos', 'dias' => '5 días']]);
        $this->assertSame('', CountryContent::decode('zonas', $old)[0]['epoca']);
    }

    public function testMaximumNumberOfBlocksPerList(): void
    {
        $mk = static fn(int $n, array $row): array => array_fill(0, $n, $row);
        $this->assertNull(CountryContent::clean('zonas', $mk(20, ['nombre' => 'Z', 'descripcion' => '', 'dias' => '']))[1]);
        $this->assertNotNull(CountryContent::clean('zonas', $mk(21,['nombre' => 'Z', 'descripcion' => '', 'dias' => '']))[1]);
        $this->assertNull(CountryContent::clean('faqs', $mk(6, ['pregunta' => 'P', 'respuesta' => 'R']))[1]);
        $this->assertNotNull(CountryContent::clean('faqs', $mk(7, ['pregunta' => 'P', 'respuesta' => 'R']))[1]);
    }

    public function testControlCharactersAreStrippedButAnswerKeepsParagraphs(): void
    {
        [$out] = CountryContent::clean('faqs', [['pregunta' => "¿Hola\x00\nmundo?", 'respuesta' => "Uno\r\n\r\nDos\x07"]]);
        $this->assertSame('¿Hola mundo?', $out[0]['pregunta']);
        $this->assertSame("Uno\n\nDos", $out[0]['respuesta']);
        $this->assertSame(['Uno', 'Dos'], CountryContent::paragraphs($out[0]['respuesta']));
    }

    public function testRoundTripThroughJson(): void
    {
        $blocks = [['nombre' => 'Norte', 'dias' => '4-6 días', 'descripcion' => 'Con «comillas» y ñ', 'epoca' => 'Todo el año', 'foto' => 'uploads/2026/11/' . str_repeat('a1', 12) . '.jpg']];
        $this->assertSame($blocks, CountryContent::decode('zonas', CountryContent::encode($blocks)));
    }

    public function testDecodeIsTolerant(): void
    {
        foreach ([null, '', '   ', 'no es json', '{"a":1}', '123', '"texto"', '[1,2,3]', '[[]]', false, 42] as $bad) {
            $this->assertSame([], CountryContent::decode('zonas', $bad), 'debe tolerar: ' . var_export($bad, true));
        }
        // bloque sin lo obligatorio: no se enseña; campos de otro tipo: se ignoran
        $json = json_encode([['nombre' => '', 'descripcion' => 'x'], ['nombre' => 'Bien', 'descripcion' => 5, 'dias' => null]]);
        $this->assertSame([['nombre' => 'Bien', 'dias' => '', 'descripcion' => '', 'epoca' => '', 'foto' => '']], CountryContent::decode('zonas', $json));
    }

    public function testDecodeNeverReturnsMoreThanTheMaximum(): void
    {
        $many = array_fill(0, 25, ['nombre' => 'Z', 'descripcion' => '', 'dias' => '']);
        $this->assertCount(20,CountryContent::decode('zonas', json_encode($many)));
    }

    public function testFaqPageStructuredData(): void
    {
        $node = Seo::faqPage([
            ['pregunta' => '¿Hace falta visado?', 'respuesta' => "Depende.\n\nConsúltalo."],
            ['pregunta' => '¿Y la moneda?', 'respuesta' => 'Peso.'],
        ]);
        $this->assertSame('FAQPage', $node['@type']);
        $this->assertCount(2, $node['mainEntity']);
        $this->assertSame('Question', $node['mainEntity'][0]['@type']);
        $this->assertSame('¿Hace falta visado?', $node['mainEntity'][0]['name']);
        $this->assertSame("Depende.\nConsúltalo.", $node['mainEntity'][0]['acceptedAnswer']['text']);
        $this->assertSame('Answer', $node['mainEntity'][1]['acceptedAnswer']['@type']);
        $json = Seo::json($node);
        $this->assertNotFalse(json_decode($json, true));
    }

    public function testFaqPageJsonCannotBreakOutOfTheScriptTag(): void
    {
        $json = Seo::json(Seo::faqPage([['pregunta' => 'x', 'respuesta' => '</script><script>alert(1)</script>']]));
        $this->assertStringNotContainsString('</script>', $json);
    }
}
