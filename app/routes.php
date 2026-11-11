<?php
declare(strict_types=1);

use App\Controllers\HomeController;

/** @var \App\Core\Router $router */

$router->get('/', [HomeController::class, 'index']);
