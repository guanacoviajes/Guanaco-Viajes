<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Config;
use App\Core\Crypto;
use App\Core\Database;
use App\Core\Session;
use App\Core\Totp;

/** Acceso al panel: contraseña + verificación en dos pasos, con sesión limitada y freno a la fuerza bruta. */
final class Auth
{
    public const IDLE_SECONDS = 1800;      // 30 min sin actividad
    public const ABSOLUTE_SECONDS = 28800; // 8 h como máximo
    public const MAX_FAILS = 8;            // fallos en 15 min antes de bloquear
    public const PRE_SECONDS = 300;        // tiempo para completar el segundo paso

    private static ?array $user = null;

    // ---------- usuarios ----------

    public static function userCount(): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM admin_users');
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM admin_users WHERE id = ?', [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::one('SELECT * FROM admin_users WHERE email = ?', [mb_strtolower(trim($email))]);
    }

    public static function create(string $email, string $password): int
    {
        Database::run('INSERT INTO admin_users (email, password_hash) VALUES (?, ?)', [mb_strtolower(trim($email)), self::hash($password)]);
        return (int) Database::lastId();
    }

    // ---------- contraseñas ----------

    public static function hash(string $password): string
    {
        return password_hash($password, defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT);
    }

    /** Hash falso para que comprobar un usuario que no existe tarde lo mismo que uno real. */
    private static function dummyHash(): string
    {
        static $h = null;
        return $h ??= self::hash('contraseña-inexistente-' . bin2hex(random_bytes(8)));
    }

    public static function verifyPassword(?array $user, string $password): bool
    {
        $ok = password_verify($password, $user['password_hash'] ?? self::dummyHash());
        return $user !== null && $ok;
    }

    /** null si la contraseña es aceptable; si no, el motivo. */
    public static function passwordProblem(string $pw, string $email = ''): ?string
    {
        if (mb_strlen($pw) < 12) {
            return 'La contraseña debe tener al menos 12 caracteres.';
        }
        if (mb_strlen($pw) > 200) {
            return 'La contraseña es demasiado larga.';
        }
        $low = mb_strtolower($pw);
        if ($email !== '' && str_contains($low, mb_strtolower(explode('@', $email)[0]))) {
            return 'La contraseña no debe contener tu correo.';
        }
        foreach (['contraseña', 'password', '123456', 'qwerty', 'guanaco', 'abcdef'] as $bad) {
            if (str_contains($low, $bad)) {
                return 'Esa contraseña es demasiado previsible. Usa una frase larga que solo tú sepas.';
            }
        }
        if (preg_match('/^(.)\1+$/u', $pw) || count(array_unique(preg_split('//u', $pw, -1, PREG_SPLIT_NO_EMPTY) ?: [])) < 6) {
            return 'La contraseña es demasiado repetitiva.';
        }
        return null;
    }

    // ---------- freno a la fuerza bruta ----------

    public static function record(string $scope, string $ipHash, string $subject, bool $ok): void
    {
        Database::run('INSERT INTO auth_attempts (scope, ip_hash, subject, ok) VALUES (?,?,?,?)', [$scope, $ipHash, $subject, $ok ? 1 : 0]);
        Database::run('DELETE FROM auth_attempts WHERE created_at < (NOW() - INTERVAL 2 DAY)');
    }

    public static function blocked(string $scope, string $ipHash, string $subject): bool
    {
        $byIp = (int) Database::value(
            'SELECT COUNT(*) FROM auth_attempts WHERE scope = ? AND ip_hash = ? AND ok = 0 AND created_at >= (NOW() - INTERVAL 15 MINUTE)',
            [$scope, $ipHash]
        );
        $bySubject = (int) Database::value(
            'SELECT COUNT(*) FROM auth_attempts WHERE scope = ? AND subject = ? AND ok = 0 AND created_at >= (NOW() - INTERVAL 15 MINUTE)',
            [$scope, $subject]
        );
        return $byIp >= self::MAX_FAILS || $bySubject >= self::MAX_FAILS;
    }

    public static function subjectHash(string $s): string
    {
        return hash_hmac('sha256', 'sujeto|' . mb_strtolower(trim($s)), (string) Config::get('app.secret'));
    }

    // ---------- sesión ----------

    /** Paso intermedio: la contraseña es correcta y falta el segundo factor. */
    public static function setPending(int $userId): void
    {
        Session::regenerate();
        Session::set('pend_id', $userId);
        Session::set('pend_time', time());
    }

    public static function pending(): ?array
    {
        $id = Session::get('pend_id');
        $t = (int) Session::get('pend_time', 0);
        if (!is_int($id) || time() - $t > self::PRE_SECONDS) {
            Session::forget('pend_id');
            Session::forget('pend_time');
            return null;
        }
        return self::find($id);
    }

    public static function complete(array $user): void
    {
        Session::regenerate();
        Session::forget('pend_id');
        Session::forget('pend_time');
        Session::set('admin_id', (int) $user['id']);
        Session::set('admin_ver', (int) $user['session_version']);
        Session::set('admin_login', time());
        Session::set('admin_seen', time());
        Database::run('UPDATE admin_users SET last_login = NOW() WHERE id = ?', [$user['id']]);
        self::$user = null;
    }

    /** Usuario con sesión válida, o null (y la sesión se cierra si caducó o se invalidó). */
    public static function user(): ?array
    {
        if (self::$user !== null) {
            return self::$user;
        }
        $id = Session::get('admin_id');
        if (!is_int($id)) {
            return null;
        }
        $now = time();
        $u = self::find($id);
        $expired = $now - (int) Session::get('admin_seen', 0) > self::IDLE_SECONDS
            || $now - (int) Session::get('admin_login', 0) > self::ABSOLUTE_SECONDS;
        if ($u === null || $expired || (int) $u['session_version'] !== (int) Session::get('admin_ver', 0) || !$u['totp_enabled']) {
            self::logout();
            return null;
        }
        Session::set('admin_seen', $now);
        return self::$user = $u;
    }

    public static function logout(): void
    {
        Session::start();
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        self::$user = null;
    }

    /** Cierra todas las sesiones abiertas (por ejemplo, al cambiar la contraseña). */
    public static function invalidateSessions(int $userId): void
    {
        Database::run('UPDATE admin_users SET session_version = session_version + 1 WHERE id = ?', [$userId]);
    }

    // ---------- segundo factor ----------

    public static function secretOf(array $user): ?string
    {
        return $user['totp_secret'] ? Crypto::decrypt($user['totp_secret']) : null;
    }

    public static function startEnrollment(int $userId): string
    {
        $secret = Totp::newSecret();
        Database::run('UPDATE admin_users SET totp_secret = ?, totp_enabled = 0, totp_last_step = 0 WHERE id = ?', [Crypto::encrypt($secret), $userId]);
        return $secret;
    }

    /** Valida el código al activar la verificación; si es correcto, la deja activa y devuelve los códigos de recuperación. */
    public static function confirmEnrollment(array $user, string $code): ?array
    {
        $secret = self::secretOf($user);
        if ($secret === null) {
            return null;
        }
        $step = Totp::verify($secret, $code, 0);
        if ($step === null) {
            return null;
        }
        [$plain, $hashes] = self::newRecoveryCodes();
        Database::run(
            'UPDATE admin_users SET totp_enabled = 1, totp_last_step = ?, recovery_codes = ? WHERE id = ?',
            [$step, json_encode($hashes), $user['id']]
        );
        return $plain;
    }

    /** Acepta un código de la aplicación o un código de recuperación (que se gasta). */
    public static function acceptSecondFactor(array $user, string $input): bool
    {
        $secret = self::secretOf($user);
        if ($secret !== null) {
            $step = Totp::verify($secret, $input, (int) $user['totp_last_step']);
            if ($step !== null) {
                Database::run('UPDATE admin_users SET totp_last_step = ? WHERE id = ?', [$step, $user['id']]);
                return true;
            }
        }
        $norm = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $input) ?? '');
        if (strlen($norm) === 10) {
            $h = self::recoveryHash($norm);
            $list = json_decode((string) $user['recovery_codes'], true) ?: [];
            foreach ($list as $i => $stored) {
                if (hash_equals($stored, $h)) {
                    unset($list[$i]);
                    Database::run('UPDATE admin_users SET recovery_codes = ? WHERE id = ?', [json_encode(array_values($list)), $user['id']]);
                    return true;
                }
            }
        }
        return false;
    }

    /** @return array{0: list<string>, 1: list<string>} códigos en claro (para mostrar una vez) y sus hashes */
    public static function newRecoveryCodes(int $n = 8): array
    {
        $alpha = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $plain = [];
        $hashes = [];
        for ($i = 0; $i < $n; $i++) {
            $c = '';
            for ($j = 0; $j < 10; $j++) {
                $c .= $alpha[random_int(0, strlen($alpha) - 1)];
            }
            $plain[] = substr($c, 0, 5) . '-' . substr($c, 5);
            $hashes[] = self::recoveryHash($c);
        }
        return [$plain, $hashes];
    }

    private static function recoveryHash(string $code10): string
    {
        return hash_hmac('sha256', 'recuperacion|' . $code10, (string) Config::get('app.secret'));
    }

    public static function recoveryLeft(array $user): int
    {
        return count(json_decode((string) $user['recovery_codes'], true) ?: []);
    }

    public static function installToken(): string
    {
        return (string) Config::get('admin.install_token', '');
    }
}
