<?php
declare(strict_types=1);

/**
 * Arranque de la aplicación.
 * Antes de cargarlo, public/index.php define PUBLIC_PATH (carpeta pública).
 */

define('ROOT', dirname(__DIR__));
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', ROOT . '/public');
}

spl_autoload_register(static function (string $class): void {
    // Código propio: App\Carpeta\Clase → app/Carpeta/Clase.php
    if (strncmp($class, 'App\\', 4) === 0) {
        $file = ROOT . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    // Biblioteca de correo incluida: PHPMailer\PHPMailer\X → app/Vendor/PHPMailer/src/X.php
    } elseif (strncmp($class, 'PHPMailer\\PHPMailer\\', 20) === 0) {
        $file = ROOT . '/app/Vendor/PHPMailer/src/' . substr($class, 20) . '.php';
    } else {
        return;
    }
    if (is_file($file)) {
        require $file;
    }
});

require ROOT . '/app/Core/helpers.php';

use App\Core\Config;
use App\Core\Http;

Config::load(ROOT . '/config/config.php');
date_default_timezone_set((string) Config::get('app.timezone', 'Europe/Madrid'));

// Errores: nunca se muestran al visitante en producción; se guardan en storage/logs.
error_reporting(E_ALL);
ini_set('display_errors', Config::get('app.debug', false) ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', ROOT . '/storage/logs/php-error.log');

set_exception_handler(static function (Throwable $e): void {
    error_log((string) $e);
    http_response_code(500);
    if (Config::get('app.debug', false)) {
        header('Content-Type: text/plain; charset=utf-8');
        echo $e;
    } else {
        echo \App\Core\View::render('pages/error', ['code' => 500], ['title' => 'Algo ha fallado', 'robots' => 'noindex']);
    }
});

// La sesión NO se inicia aquí: solo cuando un formulario o el panel la necesitan.
Http::securityHeaders();
