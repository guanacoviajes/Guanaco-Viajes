<?php
// Copia este archivo como config/config.php y rellena tus datos.
// config.php NO se sube al repositorio ni debe compartirse.
return [
    'app' => [
        'env'      => 'production',          // 'production' o 'development'
        'debug'    => false,                  // true solo en desarrollo
        'url'      => 'https://guanacoviajes.es',
        'timezone' => 'Europe/Madrid',
    ],
    'db' => [
        'host'    => 'localhost',
        'name'    => 'NOMBRE_BASE_DE_DATOS',
        'user'    => 'USUARIO_BASE_DE_DATOS',
        'pass'    => 'CONTRASEÑA_BASE_DE_DATOS',
        'charset' => 'utf8mb4',
    ],
    'mail' => [
        'host'      => 'smtp.hostinger.com',
        'port'      => 465,
        'secure'    => 'ssl',
        'user'      => 'hola@guanacoviajes.es',
        'pass'      => 'CONTRASEÑA_DEL_BUZON',
        'from'      => 'hola@guanacoviajes.es',
        'from_name' => 'Guanaco Viajes',
        'notify_to' => 'hola@guanacoviajes.es',
    ],
];
