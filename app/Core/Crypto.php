<?php
declare(strict_types=1);

namespace App\Core;

/** Cifrado de valores sensibles guardados en la base de datos (la clave de la verificación en dos pasos). */
final class Crypto
{
    private static function key(): string
    {
        $secret = (string) Config::get('app.secret', '');
        if (strlen($secret) < 16) {
            throw new \RuntimeException('Falta app.secret en config/config.php.');
        }
        return sodium_crypto_generichash('guanaco-viajes|cifrado|' . $secret, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    }

    public static function encrypt(string $plain): string
    {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        return base64_encode($nonce . sodium_crypto_secretbox($plain, $nonce, self::key()));
    }

    public static function decrypt(string $blob): ?string
    {
        $raw = base64_decode($blob, true);
        if ($raw === false || strlen($raw) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            return null;
        }
        $nonce = substr($raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $plain = sodium_crypto_secretbox_open(substr($raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES), $nonce, self::key());
        return $plain === false ? null : $plain;
    }
}
