<?php
declare(strict_types=1);

use App\Controllers\BlogController;
use App\Controllers\ContactoController;
use App\Controllers\DestinosController;
use App\Controllers\HomeController;
use App\Controllers\PagesController;
use App\Controllers\PresupuestoController;
use App\Controllers\SitemapController;

/** @var \App\Core\Router $router */

$router->get('/', [HomeController::class, 'index']);

$router->get('/viajes-a-medida', [PagesController::class, 'medida']);
$router->get('/viajes-en-grupo', [PagesController::class, 'grupo']);

$router->get('/destinos', [DestinosController::class, 'index']);
$router->get('/destinos/{continente}', [DestinosController::class, 'index']);
$router->get('/destinos/{continente}/{pais}', [DestinosController::class, 'pais']);

$router->get('/quienes-somos', [PagesController::class, 'quienes']);
$router->get('/contacto', [ContactoController::class, 'show']);
$router->post('/contacto/enviar', [ContactoController::class, 'send']);

$router->get('/presupuesto', [PresupuestoController::class, 'show']);
$router->post('/presupuesto/enviar', [PresupuestoController::class, 'send']);
$router->get('/presupuesto/gracias', [PresupuestoController::class, 'thanks']);

$router->get('/blog', [BlogController::class, 'index']);
$router->get('/blog/{slug}', [BlogController::class, 'show']);

foreach (['aviso-legal', 'politica-de-privacidad', 'politica-de-cookies', 'condiciones-generales'] as $legal) {
    $router->get('/' . $legal, [PagesController::class, 'legal']);
}

$router->get('/sitemap.xml', [SitemapController::class, 'index']);

// ---------- Panel de administración ----------
$router->get('/admin/instalar', [\App\Admin\AuthController::class, 'installForm']);
$router->post('/admin/instalar', [\App\Admin\AuthController::class, 'install']);
$router->get('/admin/entrar', [\App\Admin\AuthController::class, 'loginForm']);
$router->post('/admin/entrar', [\App\Admin\AuthController::class, 'login']);
$router->get('/admin/verificar', [\App\Admin\AuthController::class, 'verifyForm']);
$router->post('/admin/verificar', [\App\Admin\AuthController::class, 'verify']);
$router->get('/admin/activar', [\App\Admin\AuthController::class, 'enrollForm']);
$router->post('/admin/activar', [\App\Admin\AuthController::class, 'enroll']);
$router->post('/admin/salir', [\App\Admin\AuthController::class, 'logout']);

$router->get('/admin', [\App\Admin\DashboardController::class, 'index']);

$router->get('/admin/solicitudes', [\App\Admin\RequestsController::class, 'index']);
$router->get('/admin/solicitudes/exportar.csv', [\App\Admin\RequestsController::class, 'exportar']);
$router->get('/admin/solicitudes/{id}', [\App\Admin\RequestsController::class, 'show']);
$router->post('/admin/solicitudes/{id}/estado', [\App\Admin\RequestsController::class, 'estado']);
$router->post('/admin/solicitudes/{id}/notas', [\App\Admin\RequestsController::class, 'notas']);
$router->post('/admin/solicitudes/{id}/eliminar', [\App\Admin\RequestsController::class, 'eliminar']);
$router->post('/admin/solicitudes/{id}/reenviar', [\App\Admin\RequestsController::class, 'reenviar']);

$router->get('/admin/seguridad', [\App\Admin\SecurityController::class, 'index']);
$router->get('/admin/codigos-de-recuperacion', [\App\Admin\SecurityController::class, 'recoveryCodes']);
$router->post('/admin/seguridad/contrasena', [\App\Admin\SecurityController::class, 'password']);
$router->post('/admin/seguridad/recuperacion', [\App\Admin\SecurityController::class, 'newRecovery']);
$router->post('/admin/seguridad/reiniciar-2fa', [\App\Admin\SecurityController::class, 'resetTotp']);
$router->post('/admin/seguridad/cerrar-sesiones', [\App\Admin\SecurityController::class, 'closeSessions']);
