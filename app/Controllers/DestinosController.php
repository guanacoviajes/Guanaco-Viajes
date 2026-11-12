<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Http;
use App\Core\Request;
use App\Core\Seo;
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
            ...($label
                ? [
                    'title'       => "Viajes a medida por $label · Guanaco Viajes",
                    'description' => "Países de $label para viajar a medida con Guanaco Viajes: eliges el destino y diseñamos el viaje contigo, sin rutas cerradas.",
                ]
                : Seo::page('destinos', 'Destinos: Sudamérica y Europa a medida · Guanaco Viajes', 'Destinos de Sudamérica y Europa para viajar a medida. Elige un país y diseñamos el viaje contigo.')),
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
            'title'       => $pais['seo_title'] ?: ('Viajes a medida a ' . $pais['nombre'] . ' · Guanaco Viajes'),
            'description' => $pais['seo_desc'] ?: Seo::clip('Diseñamos tu viaje a medida a ' . $pais['nombre'] . '. ' . $pais['resumen'], 155),
            'og_image'    => $pais['foto'],
            'og_alt'      => $pais['foto_alt'],
            'schema'      => [[
                '@type'         => 'TouristDestination',
                'name'          => $pais['nombre'],
                'description'   => Seo::clip((string) $pais['resumen'], 300),
                'url'           => Seo::base() . '/destinos/' . $pais['continente'] . '/' . $pais['slug'],
                'image'         => Seo::abs((string) $pais['foto']),
                'containedInPlace' => ['@type' => 'Continent', 'name' => Countries::continentLabel($pais['continente'])],
            ]],
        ]);
    }
}
