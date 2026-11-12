<?php
declare(strict_types=1);

namespace App\Core;

/** Mensajes de un solo uso entre dos peticiones (por ejemplo, tras enviar un formulario). */
final class Flash
{
    public static function put(string $key, mixed $value): void
    {
        Session::set('_flash_' . $key, $value);
    }

    public static function pull(string $key, mixed $default = null): mixed
    {
        $v = Session::get('_flash_' . $key, $default);
        Session::forget('_flash_' . $key);
        return $v;
    }
}
