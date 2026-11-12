<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;

final class SitemapController
{
    public function index(Request $r): void
    {
        $base = rtrim((string) config('app.url'), '/');
        $urls = [];
        foreach (['/', '/viajes-a-medida', '/viajes-en-grupo', '/destinos', '/destinos/sudamerica', '/destinos/europa', '/quienes-somos', '/blog', '/contacto', '/presupuesto', '/aviso-legal', '/politica-de-privacidad', '/politica-de-cookies', '/condiciones-generales'] as $p) {
            $urls[] = [$p, null];
        }
        foreach (Database::all('SELECT continente, slug, updated_at FROM countries WHERE publicado = 1 ORDER BY continente, orden') as $c) {
            $urls[] = ['/destinos/' . $c['continente'] . '/' . $c['slug'], $c['updated_at']];
        }
        foreach (Database::all('SELECT slug, updated_at FROM posts WHERE publicado = 1 AND (publicado_en IS NULL OR publicado_en <= NOW()) ORDER BY publicado_en DESC') as $p) {
            $urls[] = ['/blog/' . $p['slug'], $p['updated_at']];
        }
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as [$path, $mod]) {
            echo '  <url><loc>' . htmlspecialchars($base . $path, ENT_XML1) . '</loc>'
                . ($mod ? '<lastmod>' . date('Y-m-d', (int) strtotime((string) $mod)) . '</lastmod>' : '') . "</url>\n";
        }
        echo '</urlset>';
    }
}
