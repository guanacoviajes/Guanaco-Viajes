<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Config;

/**
 * Antispam sin servicios externos:
 *  - campo trampa («sitio_web») que una persona nunca ve ni rellena,
 *  - tiempo mínimo entre que se muestra el formulario y se envía,
 *  - límite de envíos por IP (la IP se guarda solo como hash).
 */
final class FormGuard
{
    public const MIN_SECONDS = 4;
    public const MAX_SECONDS = 7200;
    public const MAX_PER_HOUR = 5;
    public const MAX_PER_DAY = 15;

    private static function secret(): string
    {
        $s = (string) Config::get('app.secret', '');
        if (strlen($s) < 16) {
            throw new \RuntimeException('Falta app.secret en config/config.php (mínimo 16 caracteres).');
        }
        return $s;
    }

    /** Marca de tiempo firmada que viaja en un campo oculto. */
    public static function timeToken(?int $now = null): string
    {
        $t = (string) ($now ?? time());
        return $t . '.' . hash_hmac('sha256', 'form|' . $t, self::secret());
    }

    /** 'ok' | 'rapido' | 'caducado' | 'invalido' */
    public static function checkTime(?string $token, ?int $now = null): string
    {
        $now ??= time();
        if (!is_string($token) || !preg_match('/^(\d{9,11})\.([a-f0-9]{64})$/', $token, $m)) {
            return 'invalido';
        }
        if (!hash_equals(hash_hmac('sha256', 'form|' . $m[1], self::secret()), $m[2])) {
            return 'invalido';
        }
        $age = $now - (int) $m[1];
        if ($age < self::MIN_SECONDS) {
            return 'rapido';
        }
        return $age > self::MAX_SECONDS ? 'caducado' : 'ok';
    }

    public static function ipHash(string $ip): string
    {
        return hash_hmac('sha256', $ip, self::secret());
    }
}
