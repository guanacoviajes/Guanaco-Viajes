<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Http;
use App\Core\Request;
use App\Core\View;
use App\Repositories\Content;
use App\Repositories\Countries;
use App\Repositories\Departures;
use App\Repositories\Posts;

final class DestinosController
{
    private const CONT = ['sudamerica', 'europa'];

    public function index(Request $request, ?string $continente = null): void
    {
        if ($continente !== null && !in_array($continente, self::CONT, true)) {
            Http::notFound();
        }
        $label = $continente ? Countries::continentLabel($continente) : null;
        echo View::render('pages/destinos', [
            'paises' => Countries::byContinent(),
            'activo' => $continente ?? 'sudamerica',
        ], [
            'title'       => ($label ? "Destinos en $label" : 'Destinos') . ' · Guanaco Viajes',
            'description' => $label
                ? "Países de $label para viajar con Guanaco Viajes: el viaje se diseña a medida contigo."
                : 'Destinos de Sudamérica y Europa. Elige un país y diseñamos el viaje a medida contigo.',
        ]);
    }

    public function pais(Request $request, string $continente, string $slug): void
    {
        $pais = Countries::find($slug);
        if ($pais === null || !in_array($continente, self::CONT, true)) {
            Http::notFound();
        }
        if ($pais['continente'] !== $continente) {
            Http::redirect('/destinos/' . $pais['continente'] . '/' . $pais['slug'], 301);
        }
        $id = (int) $pais['id'];
        echo View::render('pages/pais', [
            'pais'    => $pais,
            'galeria' => Countries::gallery($id),
            'salidas' => Departures::published($id),
            'posts'   => Posts::page(1, 3, $id),
            'otros'   => Countries::others($pais['continente'], $id),
            'paises'  => Countries::byContinent(),
        ], [
            'title'       => $pais['seo_title'] ?: ($pais['nombre'] . ' · Viajes a medida · Guanaco Viajes'),
            'description' => $pais['seo_desc'] ?: mb_substr((string) $pais['resumen'], 0, 155),
            'og_image'    => $pais['foto'],
        ]);
    }
}
