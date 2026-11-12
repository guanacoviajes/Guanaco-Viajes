<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Sesión PHP. Se inicia solo cuando hace falta (formularios con CSRF y panel),
 * así los visitantes que solo leen no reciben ninguna cookie.
 */
final class Session
{
    /** 'public' (formularios) o 'admin' (panel): cookies distintas para que no se mezclen. */
    private static string $scope = 'public';

    /** Debe llamarse antes de iniciar ninguna sesión en esa petición. */
    public static function useAdmin(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE && self::$scope !== 'admin') {
            throw new \LogicException('La sesión pública ya estaba iniciada.');
        }
        self::$scope = 'admin';
    }

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $admin = self::$scope === 'admin';
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        session_name($admin ? 'gvadm' : 'gvsid');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => $admin ? '/admin' : '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => $admin ? 'Strict' : 'Lax',
        ]);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_start();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }
}
