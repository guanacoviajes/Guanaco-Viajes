<?php
declare(strict_types=1);

use App\Controllers\BlogController;
use App\Controllers\DestinosController;
use App\Controllers\HomeController;
use App\Controllers\PagesController;
use App\Controllers\SitemapController;

/** @var \App\Core\Router $router */

$router->get('/', [HomeController::class, 'index']);

$router->get('/viajes-a-medida', [PagesController::class, 'medida']);
$router->get('/viajes-en-grupo', [PagesController::class, 'grupo']);

$router->get('/destinos', [DestinosController::class, 'index']);
$router->get('/destinos/{continente}', [DestinosController::class, 'index']);
$router->get('/destinos/{continente}/{pais}', [DestinosController::class, 'pais']);

$router->get('/quienes-somos', [PagesController::class, 'quienes']);
$router->get('/contacto', [PagesController::class, 'contacto']);

$router->get('/blog', [BlogController::class, 'index']);
$router->get('/blog/{slug}', [BlogController::class, 'show']);

foreach (['aviso-legal', 'politica-de-privacidad', 'politica-de-cookies', 'condiciones-generales'] as $legal) {
    $router->get('/' . $legal, [PagesController::class, 'legal']);
}

$router->get('/sitemap.xml', [SitemapController::class, 'index']);
