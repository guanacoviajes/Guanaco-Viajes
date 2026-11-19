<?php
declare(strict_types=1);

use App\Core\Text;
use PHPUnit\Framework\TestCase;

final class TextSectionsTest extends TestCase
{
    public function testSplitsByHeadings(): void
    {
        $s = Text::sections("## Uno\n\nTexto uno.\n\n## Dos\n\nTexto dos.\n\n## Tres\n\nTexto tres.");
        $this->assertCount(3, $s);
        $this->assertStringStartsWith('## Uno', $s[0]);
        $this->assertStringContainsString('Texto dos.', $s[1]);
        $this->assertStringStartsWith('## Tres', $s[2]);
    }

    public function testTextBeforeFirstHeadingStaysWithTheFirstSection(): void
    {
        $s = Text::sections("Entrada suelta.\n\n## Uno\n\nTexto.\n\n## Dos\n\nOtro.");
        $this->assertCount(2, $s);
        $this->assertStringContainsString('Entrada suelta.', $s[0]);
        $this->assertStringContainsString('## Uno', $s[0]);
    }

    public function testOneSectionAndEmpty(): void
    {
        $this->assertCount(1, Text::sections("## Solo\n\nTexto."));
        $this->assertCount(1, Text::sections('Sin títulos, solo un párrafo.'));
        $this->assertSame([], Text::sections("  \n  "));
    }

    public function testWindowsLineEndings(): void
    {
        $this->assertCount(2, Text::sections("## A\r\n\r\nx\r\n\r\n## B\r\n\r\ny"));
    }

    public function testQuoteLinesAreNotHeadings(): void
    {
        $this->assertCount(1, Text::sections("## A\n\n> cita ## no es título\n\ntexto"));
    }
}
