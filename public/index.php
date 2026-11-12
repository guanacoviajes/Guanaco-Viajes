<?php
declare(strict_types=1);

// Servidor de desarrollo de PHP: sirve los archivos estáticos que existan.
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
    if (is_file($file) && basename($file) !== 'index.php') {
        return false;
    }
}

define('PUBLIC_PATH', __DIR__);

// La aplicación vive junto a esta carpeta (../app). Si en tu hosting no puedes
// colocarla fuera de la carpeta pública, consulta docs/INSTALACION.md.
require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Request;
use App\Core\Router;

// Una sola dirección por página (con o sin «www», según app.url): evita contenido duplicado en Google.
// Solo se corrige la diferencia «www», así que la dirección temporal de Hostinger sigue funcionando.
if (App\Core\Config::get('app.env') === 'production' && PHP_SAPI !== 'cli') {
    $want = strtolower((string) parse_url((string) App\Core\Config::get('app.url'), PHP_URL_HOST));
    $have = strtolower(explode(':', (string) ($_SERVER['HTTP_HOST'] ?? ''))[0]);
    if ($want !== '' && $have !== $want && preg_replace('/^www\./', '', $have) === preg_replace('/^www\./', '', $want)) {
        App\Core\Http::redirect(rtrim((string) App\Core\Config::get('app.url'), '/') . ($_SERVER['REQUEST_URI'] ?? '/'), 301);
    }
}

$router = new Router();
require ROOT . '/app/routes.php';
$router->dispatch(new Request());
