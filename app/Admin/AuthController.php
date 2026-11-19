<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Http;
use App\Core\QrCode;
use App\Core\Request;
use App\Core\Totp;
use App\Services\FormGuard;

/** Entrada al panel: instalación inicial, contraseña, segundo paso y salida. */
final class AuthController
{
    public function __construct()
    {
        AdminController::prepare();
    }

    private function page(string $tpl, array $data, string $title): void
    {
        echo AdminController::page($tpl, $data + ['csrf' => Csrf::field()], $title, '', null);
    }

    private function back(string $to, string $type, string $msg): never
    {
        Flash::put('admin_msg', ['type' => $type, 'text' => $msg]);
        Http::redirect($to, 303);
    }

    private function csrfOrBack(Request $r, string $to): void
    {
        if (!Csrf::verify($r->post('_csrf'))) {
            $this->back($to, 'error', 'La página ha caducado. Vuelve a intentarlo.');
        }
    }

    // ---------- instalación (solo mientras no exista ningún usuario) ----------

    private function installOpen(): bool
    {
        return Auth::userCount() === 0 && strlen(Auth::installToken()) >= 16;
    }

    public function installForm(Request $r): void
    {
        if (!$this->installOpen()) {
            Http::notFound();
        }
        $this->page('install', ['old' => Flash::pull('old', [])], 'Crear el acceso al panel');
    }

    public function install(Request $r): void
    {
        if (!$this->installOpen()) {
            Http::notFound();
        }
        $this->csrfOrBack($r, '/admin/instalar');
        $ip = FormGuard::ipHash($r->ip());
        if (Auth::blocked('login', $ip, 'instalar')) {
            $this->back('/admin/instalar', 'error', 'Demasiados intentos. Espera unos minutos.');
        }
        $email = mb_strtolower($r->post('email'));
        $pw = (string) ($_POST['password'] ?? '');
        $pw2 = (string) ($_POST['password2'] ?? '');
        $errors = [];
        if (!hash_equals(Auth::installToken(), $r->post('token'))) {
            Auth::record('login', $ip, 'instalar', false);
            $errors[] = 'El código de instalación no es correcto.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Escribe un correo válido.';
        }
        if ($pw !== $pw2) {
            $errors[] = 'Las dos contraseñas no coinciden.';
        } elseif (($p = Auth::passwordProblem($pw, $email)) !== null) {
            $errors[] = $p;
        }
        if ($errors) {
            Flash::put('old', ['email' => $email]);
            $this->back('/admin/instalar', 'error', implode(' ', $errors));
        }
        $id = Auth::create($email, $pw);
        Auth::setPending($id);
        $this->back('/admin/activar', 'ok', 'Acceso creado. Ahora activa la verificación en dos pasos.');
    }

    // ---------- contraseña ----------

    public function loginForm(Request $r): void
    {
        if (Auth::user() !== null) {
            Http::redirect('/admin', 303);
        }
        if (Auth::userCount() === 0) {
            $this->installOpen() ? Http::redirect('/admin/instalar', 303) : Http::notFound();
        }
        $this->page('login', ['old' => Flash::pull('old', [])], 'Acceso al panel');
    }

    public function login(Request $r): void
    {
        $this->csrfOrBack($r, '/admin/entrar');
        $email = mb_strtolower($r->post('email'));
        $pw = (string) ($_POST['password'] ?? '');
        $ip = FormGuard::ipHash($r->ip());
        $subject = Auth::subjectHash($email);
        if (Auth::blocked('login', $ip, $subject)) {
            $this->back('/admin/entrar', 'error', 'Demasiados intentos fallidos. Espera 15 minutos antes de volver a probar.');
        }
        $user = Auth::findByEmail($email);
        $ok = Auth::verifyPassword($user, $pw);
        Auth::record('login', $ip, $subject, $ok);
        if (!$ok || $user === null) {
            Flash::put('old', ['email' => $email]);
            // mismo mensaje exista o no el usuario
            $this->back('/admin/entrar', 'error', 'El correo o la contraseña no son correctos.');
        }
        Auth::setPending((int) $user['id']);
        Http::redirect($user['totp_enabled'] ? '/admin/verificar' : '/admin/activar', 303);
    }

    // ---------- segundo paso ----------

    public function verifyForm(Request $r): void
    {
        $u = Auth::pending() ?? $this->back('/admin/entrar', 'error', 'Vuelve a empezar: la sesión de acceso caducó.');
        if (!$u['totp_enabled']) {
            Http::redirect('/admin/activar', 303);
        }
        $this->page('verify', [], 'Verificación en dos pasos');
    }

    public function verify(Request $r): void
    {
        $this->csrfOrBack($r, '/admin/verificar');
        $u = Auth::pending() ?? $this->back('/admin/entrar', 'error', 'Vuelve a empezar: la sesión de acceso caducó.');
        $ip = FormGuard::ipHash($r->ip());
        $subject = Auth::subjectHash($u['email']);
        if (Auth::blocked('totp', $ip, $subject)) {
            $this->back('/admin/entrar', 'error', 'Demasiados intentos fallidos. Espera 15 minutos antes de volver a probar.');
        }
        $ok = Auth::acceptSecondFactor($u, $r->post('code'));
        Auth::record('totp', $ip, $subject, $ok);
        if (!$ok) {
            $this->back('/admin/verificar', 'error', 'El código no es correcto o ha caducado. Prueba con el siguiente.');
        }
        $fresh = Auth::find((int) $u['id']) ?? $u;
        $left = Auth::recoveryLeft($fresh);
        Auth::complete($fresh);
        if (strlen(preg_replace('/[^A-Za-z0-9]/', '', $r->post('code')) ?? '') === 10) {
            $this->back('/admin/seguridad', 'aviso', "Has entrado con un código de recuperación. Te quedan $left. Conviene generar otros nuevos.");
        }
        Http::redirect('/admin', 303);
    }

    // ---------- activar la verificación en dos pasos ----------

    public function enrollForm(Request $r): void
    {
        $u = Auth::pending() ?? $this->back('/admin/entrar', 'error', 'Vuelve a empezar: la sesión de acceso caducó.');
        if ($u['totp_enabled']) {
            Http::redirect('/admin/verificar', 303);
        }
        $secret = Auth::secretOf($u) ?? Auth::startEnrollment((int) $u['id']);
        $uri = Totp::uri($secret, $u['email']);
        $this->page('enroll', [
            'secret' => trim(chunk_split($secret, 4, ' ')),
            'qr'     => QrCode::svg($uri),
        ], 'Activar la verificación en dos pasos');
    }

    public function enroll(Request $r): void
    {
        $this->csrfOrBack($r, '/admin/activar');
        $u = Auth::pending() ?? $this->back('/admin/entrar', 'error', 'Vuelve a empezar: la sesión de acceso caducó.');
        $ip = FormGuard::ipHash($r->ip());
        $subject = Auth::subjectHash($u['email']);
        if (Auth::blocked('totp', $ip, $subject)) {
            $this->back('/admin/entrar', 'error', 'Demasiados intentos fallidos. Espera 15 minutos antes de volver a probar.');
        }
        $codes = Auth::confirmEnrollment($u, $r->post('code'));
        Auth::record('totp', $ip, $subject, $codes !== null);
        if ($codes === null) {
            $this->back('/admin/activar', 'error', 'El código no es correcto. Comprueba que la hora de tu móvil es automática y prueba con el siguiente.');
        }
        Auth::complete(Auth::find((int) $u['id']) ?? $u);
        \App\Core\Flash::put('recovery_codes', $codes);
        Http::redirect('/admin/codigos-de-recuperacion', 303);
    }

    public function logout(Request $r): void
    {
        if (Csrf::verify($r->post('_csrf'))) {
            Auth::logout();
        }
        Http::redirect('/admin/entrar', 303);
    }
}
