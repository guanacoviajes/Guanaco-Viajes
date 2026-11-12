<?php
declare(strict_types=1);

use App\Core\Text;
use PHPUnit\Framework\TestCase;

final class TextTest extends TestCase
{
    public function testEscapesHtml(): void
    {
        $h = Text::render('<script>alert(1)</script> <img src=x onerror=alert(1)>');
        $this->assertStringNotContainsString('<script', $h);
        $this->assertStringNotContainsString('<img', $h);
        $this->assertStringContainsString('&lt;script&gt;', $h);
    }

    public function testDangerousLinksAreNotLinked(): void
    {
        $h = Text::render('[x](javascript:alert(1)) [y](data:text/html,hola) [z](//evil.com)');
        $this->assertStringNotContainsString('<a ', $h);
    }

    public function testSafeLinks(): void
    {
        $h = Text::render('[web](https://example.com) [aquí](/contacto) [mail](mailto:a@b.es)');
        $this->assertSame(3, substr_count($h, '<a '));
        $this->assertStringContainsString('rel="noopener" target="_blank"', $h);
    }

    public function testQuoteInHrefCannotBreakOut(): void
    {
        $h = Text::render('[x](https://a.com/" onmouseover="alert(1))');
        $this->assertStringNotContainsString('" onmouseover', $h);
    }

    public function testBlocks(): void
    {
        $h = Text::render("## Título\n\nUn *párrafo* con **negrita**.\n\n> cita\n\n- a\n- b");
        $this->assertStringContainsString('<h2>Título</h2>', $h);
        $this->assertStringContainsString('<em>párrafo</em>', $h);
        $this->assertStringContainsString('<strong>negrita</strong>', $h);
        $this->assertStringContainsString('<blockquote>cita</blockquote>', $h);
        $this->assertStringContainsString('<ul><li>a</li><li>b</li></ul>', $h);
    }

    public function testEmptyAndDates(): void
    {
        $this->assertSame('', Text::render("  \n "));
        $this->assertSame('12 de mayo de 2027', Text::dateEs('2027-05-12'));
        $this->assertSame('12 – 24 de mayo de 2027', Text::rangeEs('2027-05-12', '2027-05-24'));
        $this->assertSame('28 de mayo de 2027 – 5 de junio de 2027', Text::rangeEs('2027-05-28', '2027-06-05'));
        $this->assertSame('', Text::dateEs(null));
    }
}
