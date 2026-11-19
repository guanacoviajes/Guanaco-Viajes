<?php
declare(strict_types=1);

use App\Services\FormOptions;
use PHPUnit\Framework\TestCase;

final class FormOptionsTest extends TestCase
{
    public function testTwelveMonthsStartingNextMonth(): void
    {
        $m = FormOptions::meses(new DateTimeImmutable('2026-11-12'));
        $this->assertCount(12, $m);
        $this->assertSame('2026-12', array_key_first($m));
        $this->assertSame('2027-11', array_key_last($m));
        $this->assertSame('Dic 2026', $m['2026-12']);
    }

    public function testYearBoundaryAndLastDayOfMonth(): void
    {
        $m = FormOptions::meses(new DateTimeImmutable('2026-12-31'));
        $this->assertSame('2027-01', array_key_first($m));
        $this->assertSame('2027-12', array_key_last($m));
        $m = FormOptions::meses(new DateTimeImmutable('2027-01-31'));
        $this->assertSame('2027-02', array_key_first($m));
    }

    public function testMonthLabel(): void
    {
        $this->assertSame('May 2027', FormOptions::mesLabel('2027-05'));
        $this->assertSame('rarito', FormOptions::mesLabel('rarito'));
    }
}
