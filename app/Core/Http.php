<?php
declare(strict_types=1);

namespace App\Core;

final class Http
{
    /** Cabeceras de seguridad para todas las respuestas. */
    public static function securityHeaders(): void
    {
        if (headers_sent()) {
            return;
        }
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
        header(
            "Content-Security-Policy: default-src 'self'; img-src 'self' data:; "
            . "style-src 'self' 'unsafe-inline'; script-src 'self'; font-src 'self'; "
            . "connect-src 'self'; form-action 'self'; base-uri 'self'; frame-ancestors 'none'"
        );
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        if ($https && Config::get('app.env') === 'production') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }

    public static function redirect(string $to, int $code = 302): never
    {
        header('Location: ' . $to, true, $code);
        exit;
    }

    public static function notFound(): never
    {
        http_response_code(404);
        echo View::render('pages/error', ['code' => 404], [
            'title'  => 'Página no encontrada',
            'robots' => 'noindex',
            'franja' => false,
            'bubble' => true,
        ]);
        exit;
    }
}
