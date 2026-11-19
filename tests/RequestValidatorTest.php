<?php
declare(strict_types=1);

use App\Services\RequestValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RequestValidatorTest extends TestCase
{
    private array $countries = ['chile' => 'sudamerica', 'peru' => 'sudamerica', 'italia' => 'europa', 'croacia' => 'europa'];
    private array $interests = [1 => 'Naturaleza', 2 => 'Cultura'];
    private array $months = ['2027-03' => 'Mar 2027', '2027-04' => 'Abr 2027'];

    private function ok(array $over = []): array
    {
        return array_merge([
            'destinos' => ['italia'], 'meses' => ['2027-03'], 'duracion' => 'semana', 'adultos' => '2', 'menores' => '0',
            'nombre' => 'Ana Pérez', 'email' => 'ana@example.com', 'canal' => 'correo', 'consentimiento' => '1',
        ], $over);
    }

    private function run_(array $in): array
    {
        return RequestValidator::budget($in, $this->countries, $this->interests, $this->months);
    }

    public function testValidRequestHasNoErrors(): void
    {
        $r = $this->run_($this->ok());
        $this->assertSame([], $r['errors']);
        $this->assertSame(['italia'], $r['data']['destinos']);
        $this->assertSame(2, $r['data']['adultos']);
    }

    public function testDestinationRequired(): void
    {
        $this->assertArrayHasKey('destinos', $this->run_($this->ok(['destinos' => [], 'meses' => ['2027-03']]))['errors']);
    }

    public function testInventedDestinationsAreDropped(): void
    {
        $r = $this->run_($this->ok(['destinos' => ['atlantida', 'chile']]));
        $this->assertSame(['chile'], $r['data']['destinos']);
    }

    public function testDestinationsAsStringDoNotBreak(): void
    {
        $this->assertArrayHasKey('destinos', $this->run_($this->ok(['destinos' => 'chile']))['errors']);
    }

    public function testUnknownExcludesCountries(): void
    {
        $r = $this->run_($this->ok(['destinos' => ['chile', 'aun-no-lo-se']]));
        $this->assertSame(['aun-no-lo-se'], $r['data']['destinos']);
    }

    public function testDatesRequireMonthOrFlexible(): void
    {
        $this->assertArrayHasKey('fechas', $this->run_($this->ok(['meses' => []]))['errors']);
        $this->assertArrayNotHasKey('fechas', $this->run_($this->ok(['meses' => [], 'flexible' => '1']))['errors']);
    }

    public function testMonthsOutsideTheListAreIgnored(): void
    {
        $r = $this->run_($this->ok(['meses' => ['1999-01', '2027-04', 'x']]));
        $this->assertSame(['2027-04'], $r['data']['meses']);
    }

    public function testLongWeekendNotAllowedForSouthAmerica(): void
    {
        $r = $this->run_($this->ok(['destinos' => ['chile'], 'duracion' => 'finde']));
        $this->assertArrayHasKey('duracion', $r['errors']);
        $this->assertSame('', $r['data']['duracion']);
    }

    public function testLongWeekendNotAllowedWhenMixed(): void
    {
        $this->assertArrayHasKey('duracion', $this->run_($this->ok(['destinos' => ['italia', 'peru'], 'duracion' => 'finde']))['errors']);
    }

    public function testLongWeekendAllowedForEurope(): void
    {
        $r = $this->run_($this->ok(['destinos' => ['italia', 'croacia'], 'duracion' => 'finde']));
        $this->assertArrayNotHasKey('duracion', $r['errors']);
        $this->assertSame('finde', $r['data']['duracion']);
    }

    public function testLongWeekendAllowedWhenUnknownDestination(): void
    {
        $this->assertArrayNotHasKey('duracion', $this->run_($this->ok(['destinos' => ['aun-no-lo-se'], 'duracion' => 'finde']))['errors']);
    }

    public function testUnknownDurationIsDropped(): void
    {
        $this->assertSame('', $this->run_($this->ok(['duracion' => 'cien-anos']))['data']['duracion']);
    }

    #[DataProvider('badTravellers')]
    public function testTravellersOutOfRange(string $adults, string $minors): void
    {
        $this->assertArrayHasKey('viajeros', $this->run_($this->ok(['adultos' => $adults, 'menores' => $minors]))['errors']);
    }

    public static function badTravellers(): array
    {
        return [['0', '0'], ['21', '0'], ['-1', '0'], ['abc', '0'], ['2', '11'], ['2', '-3'], ['1e3', '0'], ['2.5', '0'], ['', '0']];
    }

    public function testAgesIgnoredWithoutMinors(): void
    {
        $this->assertSame('', $this->run_($this->ok(['menores' => '0', 'edades_menores' => '4 y 9']))['data']['edades_menores']);
        $this->assertSame('4 y 9', $this->run_($this->ok(['menores' => '2', 'edades_menores' => '4 y 9']))['data']['edades_menores']);
    }

    public function testInterestsOnlyKnownIds(): void
    {
        $r = $this->run_($this->ok(['intereses' => ['1', '999', 'abc', '2']]));
        $this->assertSame(['Naturaleza', 'Cultura'], $r['data']['intereses']);
    }

    public function testMessageLimitAndControlCharacters(): void
    {
        $this->assertArrayHasKey('mensaje', $this->run_($this->ok(['mensaje' => str_repeat('x', 2001)]))['errors']);
        $r = $this->run_($this->ok(['mensaje' => "Hola\x00\x07 mundo\r\n\r\n\r\n\r\nFin"]));
        $this->assertSame("Hola mundo\n\nFin", $r['data']['mensaje']);
    }

    public function testNameIsSingleLine(): void
    {
        $r = $this->run_($this->ok(['nombre' => "Ana\r\nBcc: x@y.z"]));
        $this->assertStringNotContainsString("\n", $r['data']['nombre']);
        $this->assertStringNotContainsString("\r", $r['data']['nombre']);
    }

    public function testNameLength(): void
    {
        $this->assertArrayHasKey('nombre', $this->run_($this->ok(['nombre' => 'A']))['errors']);
        $this->assertArrayHasKey('nombre', $this->run_($this->ok(['nombre' => str_repeat('a', 101)]))['errors']);
    }

    #[DataProvider('badEmails')]
    public function testBadEmails(string $email): void
    {
        $this->assertArrayHasKey('email', $this->run_($this->ok(['email' => $email]))['errors'], $email);
    }

    public static function badEmails(): array
    {
        return [[''], ['sin-arroba'], ['a@b'], ['a@b.com, c@d.com'], ["a@b.com\r\nBcc: v@x.com"], ['a b@c.com'], ['<a@b.com>'], ['a@b.com;c@d.com'], [str_repeat('a', 150) . '@b.com']];
    }

    public function testGoodEmail(): void
    {
        $this->assertArrayNotHasKey('email', $this->run_($this->ok(['email' => 'ana.perez+viajes@correo.es']))['errors']);
    }

    #[DataProvider('phones')]
    public function testPhones(string $tel, bool $valid): void
    {
        $e = $this->run_($this->ok(['telefono' => $tel]))['errors'];
        $this->assertSame($valid, !isset($e['telefono']), $tel);
    }

    public static function phones(): array
    {
        return [['', true], ['+34 600 53 88 85', true], ['600538885', true], ['(91) 123-45-67', true], ['abc', false], ['12', false], ['+34 600 538 885 123 456', false], ['<script>', false]];
    }

    public function testWhatsappAndCallNeedPhone(): void
    {
        $this->assertArrayHasKey('telefono', $this->run_($this->ok(['canal' => 'whatsapp']))['errors']);
        $this->assertArrayHasKey('telefono', $this->run_($this->ok(['canal' => 'llamada']))['errors']);
        $this->assertArrayNotHasKey('telefono', $this->run_($this->ok(['canal' => 'whatsapp', 'telefono' => '600538885']))['errors']);
        $this->assertArrayNotHasKey('telefono', $this->run_($this->ok(['canal' => 'correo']))['errors']);
    }

    public function testUnknownChannelFallsBackToEmail(): void
    {
        $this->assertSame('correo', $this->run_($this->ok(['canal' => 'paloma']))['data']['canal']);
    }

    public function testConsentRequired(): void
    {
        $this->assertArrayHasKey('consentimiento', $this->run_($this->ok(['consentimiento' => '']))['errors']);
        $this->assertArrayHasKey('consentimiento', $this->run_($this->ok(['consentimiento' => '0']))['errors']);
        $in = $this->ok();
        unset($in['consentimiento']);
        $this->assertArrayHasKey('consentimiento', $this->run_($in)['errors']);
    }

    public function testNonStringValuesNeverCrash(): void
    {
        $r = $this->run_(['destinos' => [['x']], 'meses' => 5, 'nombre' => ['a'], 'email' => null, 'adultos' => [], 'mensaje' => 7, 'intereses' => 'x']);
        $this->assertNotEmpty($r['errors']);
    }

    public function testContactMessage(): void
    {
        $in = ['nombre' => 'María', 'email' => 'm@example.com', 'mensaje' => 'Hola, una duda', 'consentimiento' => '1'];
        $this->assertSame([], RequestValidator::message($in)['errors']);
        $this->assertArrayHasKey('mensaje', RequestValidator::message(array_merge($in, ['mensaje' => 'hey']))['errors']);
        $this->assertArrayHasKey('consentimiento', RequestValidator::message(array_merge($in, ['consentimiento' => '']))['errors']);
    }
}
