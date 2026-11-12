<?php
declare(strict_types=1);

// Arranque mínimo para las pruebas unitarias: no abre base de datos ni sesión.
define('ROOT', dirname(__DIR__));
define('PUBLIC_PATH', ROOT . '/public');
define('IMAGE_TEST_MODE', true); // permite probar subidas sin pasar por el servidor web

spl_autoload_register(static function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $f = ROOT . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($f)) {
            require $f;
        }
    }
});
require ROOT . '/app/Core/helpers.php';

// Configuración falsa solo para las pruebas (no toca config/config.php)
$tmp = sys_get_temp_dir() . '/gv-test-config.php';
file_put_contents($tmp, "<?php return ['app' => ['secret' => 'secreto-de-pruebas-0123456789abcdef'], 'db' => [], 'mail' => []];");
\App\Core\Config::load($tmp);
