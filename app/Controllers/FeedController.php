<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Seo;
use App\Repositories\Posts;

/** Feed RSS del blog: ayuda a lectores y a buscadores a descubrir artículos nuevos. */
final class FeedController
{
    public function blog(Request $r): void
    {
        $base = Seo::base();
        $x = static fn(string $s): string => htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        header('Content-Type: application/rss+xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"><channel>';
        echo '<title>Guanaco Viajes · Blog</title><link>' . $x($base . '/blog') . '</link>';
        echo '<atom:link href="' . $x(Seo::feedUrl()) . '" rel="self" type="application/rss+xml"/>';
        echo '<description>Historias, ideas y rincones de Sudamérica y Europa para viajar con curiosidad.</description><language>es-ES</language>';
        foreach (Posts::page(1, 20, null) as $p) {
            $date = (string) ($p['publicado_en'] ?? $p['created_at'] ?? 'now');
            echo '<item><title>' . $x((string) $p['titulo']) . '</title>'
                . '<link>' . $x($base . '/blog/' . $p['slug']) . '</link>'
                . '<guid isPermaLink="true">' . $x($base . '/blog/' . $p['slug']) . '</guid>'
                . '<pubDate>' . date(DATE_RSS, (int) strtotime($date)) . '</pubDate>'
                . '<description>' . $x(Seo::clip((string) $p['extracto'], 300)) . '</description></item>';
        }
        echo '</channel></rss>';
    }
}
