<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Texto enriquecido mínimo y SEGURO para contenido editable.
 * Todo se escapa primero; solo se permiten:
 *   ## Subtítulo · > Cita · - Lista · *énfasis* · **negrita** · [texto](https://enlace o /ruta)
 */
final class Text
{
    public static function render(string $src): string
    {
        $src = str_replace(["\r\n", "\r"], "\n", trim($src));
        if ($src === '') {
            return '';
        }
        $html = [];
        foreach (preg_split('/\n\s*\n/', $src) ?: [] as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }
            if (str_starts_with($block, '## ')) {
                $html[] = '<h2>' . self::inline(substr($block, 3)) . '</h2>';
            } elseif (str_starts_with($block, '> ')) {
                $html[] = '<blockquote>' . self::inline(trim(preg_replace('/^>\s?/m', '', $block))) . '</blockquote>';
            } elseif (preg_match('/^- /', $block)) {
                $items = array_map(
                    static fn(string $l): string => '<li>' . self::inline(preg_replace('/^-\s+/', '', trim($l))) . '</li>',
                    array_filter(explode("\n", $block), static fn($l) => trim($l) !== '')
                );
                $html[] = '<ul>' . implode('', $items) . '</ul>';
            } else {
                $html[] = '<p>' . nl2br(self::inline($block), false) . '</p>';
            }
        }
        return implode("\n", $html);
    }

    private static function inline(string $s): string
    {
        $s = e($s);
        $s = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $s) ?? $s;
        $s = preg_replace('/(?<![\*\w])\*(?!\s)(.+?)(?<!\s)\*(?!\*)/s', '<em>$1</em>', $s) ?? $s;
        $s = preg_replace_callback('/\[([^\]]+)\]\(((?:https?:\/\/|\/(?!\/)|mailto:)[^\s)]*)\)/', static function (array $m): string {
            $ext = str_starts_with($m[2], 'http');
            return '<a href="' . $m[2] . '"' . ($ext ? ' rel="noopener" target="_blank"' : '') . '>' . $m[1] . '</a>';
        }, $s) ?? $s;
        return $s;
    }

    /** Fecha larga en español: «12 de mayo de 2027». */
    public static function dateEs(?string $date): string
    {
        if (!$date) {
            return '';
        }
        $t = strtotime($date);
        if ($t === false) {
            return '';
        }
        $m = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        return (int) date('j', $t) . ' de ' . $m[(int) date('n', $t) - 1] . ' de ' . date('Y', $t);
    }

    /** Rango de fechas corto: «12 – 24 de mayo de 2027». */
    public static function rangeEs(?string $a, ?string $b): string
    {
        if (!$a) {
            return '';
        }
        if (!$b || $b === $a) {
            return self::dateEs($a);
        }
        $ta = strtotime($a);
        $tb = strtotime($b);
        if ($ta === false || $tb === false) {
            return self::dateEs($a);
        }
        if (date('Y-m', $ta) === date('Y-m', $tb)) {
            return (int) date('j', $ta) . ' – ' . self::dateEs($b);
        }
        return self::dateEs($a) . ' – ' . self::dateEs($b);
    }
}
