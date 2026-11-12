<?php
/**
 * Comprobación del servidor. Ejecútalo por SSH desde la carpeta de la web:   php comprobar.php
 * No modifica nada. Te dice qué falta antes (o después) de instalar.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$cnt = ['ok' => 0, 'ko' => 0, 'aviso' => 0];
$say = static function (string $t, string $m, string $extra = '') use (&$cnt): void {
    $cnt[$t]++;
    $mark = ['ok' => '  OK  ', 'ko' => ' FALLA', 'aviso' => ' AVISO'][$t];
    echo $mark . '  ' . $m . ($extra !== '' ? '  → ' . $extra : '') . "\n";
};

echo "\nGuanaco Viajes · comprobación del servidor\n\n";

version_compare(PHP_VERSION, '8.2.0', '>=')
    ? $say('ok', 'PHP ' . PHP_VERSION)
    : $say('ko', 'PHP ' . PHP_VERSION, 'hace falta 8.2 o superior (hPanel → Avanzado → Configuración de PHP)');

foreach (['pdo_mysql' => 'base de datos', 'mbstring' => 'textos', 'gd' => 'fotos', 'fileinfo' => 'subida de fotos', 'sodium' => 'verificación en dos pasos', 'openssl' => 'correo y seguridad', 'ctype' => 'validaciones', 'json' => 'datos'] as $ext => $para) {
    extension_loaded($ext) ? $say('ok', "extensión $ext") : $say('ko', "extensión $ext", "actívala en hPanel → Configuración de PHP → Extensiones ($para)");
}
extension_loaded('iconv') ? $say('ok', 'extensión iconv') : $say('aviso', 'extensión iconv', 'sin ella el QR del panel no se dibuja y se muestra la clave manual; actívala en hPanel');
extension_loaded('exif') ? $say('ok', 'extensión exif') : $say('aviso', 'extensión exif', 'sin ella no se corrige la orientación de fotos hechas con el móvil');
if (extension_loaded('gd')) {
    (gd_info()['JPEG Support'] ?? false) ? $say('ok', 'GD con soporte JPEG') : $say('ko', 'GD sin soporte JPEG');
}

$mb = static function (string $v): int {
    $n = (int) $v;
    return match (strtolower(substr(trim($v), -1))) { 'g' => $n * 1024, 'm' => $n, 'k' => intdiv($n, 1024), default => intdiv($n, 1048576) };
};
$up = ini_get('upload_max_filesize'); $po = ini_get('post_max_size');
$mb($up) >= 16 ? $say('ok', "upload_max_filesize = $up") : $say('aviso', "upload_max_filesize = $up", 'sube a 16M en hPanel → Configuración de PHP → Opciones (si no, las fotos grandes se rechazan)');
$mb($po) >= 24 ? $say('ok', "post_max_size = $po") : $say('aviso', "post_max_size = $po", 'sube a 24M en hPanel → Configuración de PHP → Opciones');
echo "       (por SSH puede mostrar valores distintos a los de la web; la web usa los de hPanel)\n";

$root = __DIR__;
$public = is_dir($root . '/public_html') ? $root . '/public_html' : (is_dir($root . '/public') ? $root . '/public' : null);

$cfgFile = $root . '/config/config.php';
if (!is_file($cfgFile)) {
    $say('ko', 'config/config.php', 'copia config/config.example.php como config/config.php y rellénalo');
} else {
    $cfg = require $cfgFile;
    $say('ok', 'config/config.php existe');
    $g = static function (string $path) use ($cfg) { foreach (explode('.', $path) as $p) { if (!is_array($cfg) || !array_key_exists($p, $cfg)) return null; $cfg = $cfg[$p]; } return $cfg; };
    (is_string($g('app.secret')) && strlen($g('app.secret')) >= 32 && !str_contains($g('app.secret'), 'CAMBIA')) ? $say('ok', 'app.secret definido') : $say('ko', 'app.secret', 'cadena aleatoria de 32 caracteres o más');
    $g('app.env') === 'production' ? $say('ok', 'entorno: production') : $say('aviso', 'entorno: ' . var_export($g('app.env'), true), 'en la web real debe ser production');
    $g('app.debug') === false ? $say('ok', 'debug desactivado') : $say('ko', 'app.debug', 'debe ser false en la web real');
    preg_match('~^https://[^/]+$~', (string) $g('app.url')) ? $say('ok', 'app.url = ' . $g('app.url')) : $say('ko', 'app.url', 'dirección con https:// y sin barra final, p. ej. https://guanacoviajes.es');
    foreach (['db.name', 'db.user', 'db.pass', 'mail.pass'] as $k) {
        $v = (string) $g($k);
        ($v !== '' && !preg_match('/^(NOMBRE|USUARIO|CONTRASE)/', $v)) ? $say('ok', "$k rellenado") : $say('ko', $k, 'sin rellenar');
    }
    $tok = (string) $g('admin.install_token');
    if (strlen($tok) >= 16 && !str_contains($tok, 'CAMBIA')) {
        $say('ok', 'admin.install_token definido');
    } else {
        $say('aviso', 'admin.install_token', 'solo lo necesitas para crear tu acceso al panel (mínimo 16 caracteres)');
    }

    if (extension_loaded('pdo_mysql') && $g('db.name') && $g('db.user')) {
        try {
            $pdo = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $g('db.host') ?? 'localhost', $g('db.name')), (string) $g('db.user'), (string) $g('db.pass'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $say('ok', 'conexión a la base de datos');
            $have = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
            $need = ['settings', 'countries', 'country_photos', 'carousel_slides', 'trip_types', 'posts', 'interests', 'pages', 'departures', 'requests', 'admin_users', 'auth_attempts'];
            $miss = array_diff($need, $have);
            $miss ? $say('ko', 'tablas', 'faltan: ' . implode(', ', $miss) . ' → importa database/instalacion.sql') : $say('ok', 'las 12 tablas existen');
            if (!$miss) {
                $n = (int) $pdo->query('SELECT COUNT(*) FROM countries')->fetchColumn();
                $n > 0 ? $say('ok', "contenido inicial cargado ($n destinos)") : $say('ko', 'contenido inicial', 'la base está vacía → importa database/instalacion.sql');
                $a = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
                $a > 0 ? $say('ok', 'ya existe un usuario del panel') : $say('aviso', 'usuario del panel', 'aún no creado: entra en /admin/instalar');
            }
        } catch (Throwable $e) {
            $say('ko', 'conexión a la base de datos', 'revisa db.host / db.name / db.user / db.pass');
        }
    }
}

foreach (['storage/logs', 'storage/cache'] as $d) {
    is_dir("$root/$d") && is_writable("$root/$d") ? $say('ok', "$d escribible") : $say('ko', "$d", 'debe existir y ser escribible (chmod 755 o 775)');
}
if ($public !== null) {
    $up = $public . '/uploads';
    is_dir($up) && is_writable($up) ? $say('ok', basename($public) . '/uploads escribible') : $say('ko', basename($public) . '/uploads', 'debe existir y ser escribible (chmod 755 o 775)');
    is_file($public . '/index.php') ? $say('ok', basename($public) . '/index.php') : $say('ko', basename($public) . '/index.php', 'falta');
    is_file($public . '/.htaccess') ? $say('ok', basename($public) . '/.htaccess') : $say('ko', basename($public) . '/.htaccess', 'falta (archivo oculto: actívalo en el gestor de archivos)');
}

echo "\n{$cnt['ok']} correctas · {$cnt['aviso']} avisos · {$cnt['ko']} fallos\n\n";
exit($cnt['ko'] > 0 ? 1 : 0);
