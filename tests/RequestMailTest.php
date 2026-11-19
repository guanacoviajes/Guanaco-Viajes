<?php
declare(strict_types=1);

use App\Services\RequestMail;
use PHPUnit\Framework\TestCase;

final class RequestMailTest extends TestCase
{
    private array $names = ['chile' => 'Chile', 'peru' => 'Perú', 'bosnia-y-herzegovina' => 'Bosnia y Herzegovina'];

    public function testSubjectHasNameAndCountries(): void
    {
        $s = RequestMail::subject('presupuesto', ['nombre' => 'Ana Pérez', 'destinos' => ['chile', 'peru']], $this->names);
        $this->assertSame('Nueva solicitud de presupuesto · Ana Pérez · Chile, Perú', $s);
    }

    public function testSubjectWhenDestinationUnknown(): void
    {
        $s = RequestMail::subject('presupuesto', ['nombre' => 'Luis', 'destinos' => ['aun-no-lo-se']], $this->names);
        $this->assertSame('Nueva solicitud de presupuesto · Luis · destino por decidir', $s);
    }

    public function testSubjectIsCappedForManyCountries(): void
    {
        $names = [];
        $slugs = [];
        for ($i = 0; $i < 30; $i++) {
            $names["p$i"] = "País número $i";
            $slugs[] = "p$i";
        }
        $s = RequestMail::subject('presupuesto', ['nombre' => 'Ana', 'destinos' => $slugs], $names);
        $this->assertLessThanOrEqual(120, mb_strlen($s));
        $this->assertStringEndsWith('…', $s);
    }

    public function testContactSubjectHasNoCountries(): void
    {
        $this->assertSame('Nuevo mensaje de contacto · María', RequestMail::subject('contacto', ['nombre' => 'María'], $this->names));
    }

    public function testSummaryListsEverything(): void
    {
        $rows = RequestMail::summary('presupuesto', [
            'destinos' => ['chile'], 'meses' => ['2027-03'], 'flexible' => true, 'duracion' => 'semana',
            'adultos' => 2, 'menores' => 1, 'edades_menores' => '9 años', 'intereses' => ['Naturaleza'], 'mensaje' => 'Hola',
        ], $this->names);
        $this->assertSame('Chile', $rows['Destino']);
        $this->assertSame('Mar 2027 · Flexible con las fechas', $rows['Fechas']);
        $this->assertSame('2 adultos y 1 menor (9 años)', $rows['Viajeros']);
    }
}
