<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Http;
use App\Core\Request;
use App\Core\View;
use App\Repositories\Content;
use App\Repositories\Countries;
use App\Repositories\Departures;
use App\Repositories\Pages;
use App\Repositories\Settings;

final class PagesController
{
    private const LEGAL = ['aviso-legal', 'politica-de-privacidad', 'politica-de-cookies', 'condiciones-generales'];

    private static function tipo(string $slug): array
    {
        foreach (Content::tripTypes() as $t) {
            if ($t['slug'] === $slug) {
                return $t;
            }
        }
        Http::notFound();
    }

    public function medida(Request $r): void
    {
        $tipo = self::tipo('viajes-a-medida');
        echo View::render('pages/a-medida', ['tipo' => $tipo, 'paises' => Countries::byContinent()], [
            'title'       => 'Viajes a medida · Guanaco Viajes',
            'description' => 'Diseñamos viajes a medida por Sudamérica y Europa. Tú traes la idea, aunque sea solo una inquietud, y la convertimos en un viaje pensado para ti.',
            'og_image'    => $tipo['foto'],
        ]);
    }

    public function grupo(Request $r): void
    {
        $tipo = self::tipo('viajes-en-grupo');
        $salidas = Departures::published();
        echo View::render('pages/grupo', [
            'tipo'    => $tipo,
            'salidas' => $salidas,
            'huecos'  => max(0, 3 - count($salidas)),
        ], [
            'title'       => 'Viajes en grupo · Guanaco Viajes',
            'description' => 'Salidas en grupo con propuestas diferentes, cuidadas y con personalidad, para compartir el camino con otras personas.',
            'og_image'    => $tipo['foto'],
        ]);
    }

    public function quienes(Request $r): void
    {
        $p = Pages::find('quienes-somos') ?? Http::notFound();
        echo View::render('pages/quienes', ['pagina' => $p], [
            'title'       => $p['seo_title'] ?: 'Quiénes somos · Guanaco Viajes',
            'description' => $p['seo_desc'] ?: 'Guanaco Viajes nace de viajar por Sudamérica y encontrarse con guanacos en los Andes: libertad, curiosidad y viajes pensados para ti.',
        ]);
    }

    public function legal(Request $r, string $slug = ''): void
    {
        $slug = $slug !== '' ? $slug : ltrim($r->path(), '/');
        if (!in_array($slug, self::LEGAL, true)) {
            Http::notFound();
        }
        $p = Pages::find($slug) ?? Http::notFound();
        echo View::render('pages/legal', ['pagina' => $p], [
            'title'       => $p['titulo'] . ' · Guanaco Viajes',
            'description' => $p['seo_desc'] ?: ('Consulta el texto de ' . mb_strtolower($p['titulo']) . ' de Guanaco Viajes.'),
            'franja'      => false,
        ]);
    }
}
