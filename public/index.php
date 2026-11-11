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

$router = new Router();
require ROOT . '/app/routes.php';
$router->dispatch(new Request());
