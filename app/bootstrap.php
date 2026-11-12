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

// Cargador de clases: prefijo del espacio de nombres → carpeta
spl_autoload_register(static function (string $class): void {
    static $map = [
        'App\\'                 => '/app/',                          // código propio
        'PHPMailer\\PHPMailer\\' => '/app/Vendor/PHPMailer/src/',    // correo (incluido en el proyecto)
        'BaconQrCode\\'         => '/app/Vendor/BaconQrCode/src/',    // códigos QR del panel
        'DASPRiD\\Enum\\'       => '/app/Vendor/DASPRID/src/',        // dependencia de los QR
    ];
    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = ROOT . $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_file($file)) {
                require $file;
            }
            return;
        }
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
