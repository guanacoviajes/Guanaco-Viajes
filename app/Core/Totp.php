<?php
declare(strict_types=1);

namespace App\Core;

/** Códigos de un solo uso de 6 dígitos (RFC 6238, compatibles con Google Authenticator, Authy, 1Password…). */
final class Totp
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    public const STEP = 30;

    public static function newSecret(int $bytes = 20): string
    {
        return self::base32Encode(random_bytes($bytes));
    }

    public static function base32Encode(string $bin): string
    {
        $bits = '';
        foreach (str_split($bin) as $c) {
            $bits .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT);
        }
        $out = '';
        foreach (str_split($bits, 5) as $chunk) {
            $out .= self::ALPHABET[bindec(str_pad($chunk, 5, '0'))];
        }
        return $out;
    }

    public static function base32Decode(string $b32): string
    {
        $b32 = strtoupper(preg_replace('/[\s=-]+/', '', $b32) ?? '');
        $bits = '';
        foreach (str_split($b32) as $c) {
            $p = strpos(self::ALPHABET, $c);
            if ($p === false) {
                return '';
            }
            $bits .= str_pad(decbin($p), 5, '0', STR_PAD_LEFT);
        }
        $out = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $out .= chr(bindec($byte));
            }
        }
        return $out;
    }

    /** Código para un intervalo de 30 s. $digits y $algo solo se cambian en las pruebas del estándar. */
    public static function code(string $secretB32, int $counter, int $digits = 6, string $algo = 'sha1'): string
    {
        $key = self::base32Decode($secretB32);
        $hash = hash_hmac($algo, pack('J', $counter), $key, true);
        $o = ord($hash[strlen($hash) - 1]) & 0x0F;
        $n = ((ord($hash[$o]) & 0x7F) << 24) | (ord($hash[$o + 1]) << 16) | (ord($hash[$o + 2]) << 8) | ord($hash[$o + 3]);
        return str_pad((string) ($n % (10 ** $digits)), $digits, '0', STR_PAD_LEFT);
    }

    /**
     * Comprueba el código admitiendo ±1 intervalo (relojes desajustados).
     * Devuelve el intervalo aceptado, o null. Un intervalo ya usado ($lastStep) no se acepta de nuevo.
     */
    public static function verify(string $secretB32, string $input, int $lastStep = 0, ?int $now = null): ?int
    {
        $input = preg_replace('/\s+/', '', $input) ?? '';
        if (!preg_match('/^\d{6}$/', $input)) {
            return null;
        }
        $step = intdiv($now ?? time(), self::STEP);
        for ($d = -1; $d <= 1; $d++) {
            $c = $step + $d;
            if ($c > $lastStep && hash_equals(self::code($secretB32, $c), $input)) {
                return $c;
            }
        }
        return null;
    }

    public static function uri(string $secretB32, string $account, string $issuer = 'Guanaco Viajes'): string
    {
        return 'otpauth://totp/' . rawurlencode($issuer . ':' . $account) . '?secret=' . $secretB32
            . '&issuer=' . rawurlencode($issuer) . '&algorithm=SHA1&digits=6&period=30';
    }
}
