<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Request;
use App\Services\FormGuard;

final class SecurityController extends AdminController
{
    public function recoveryCodes(Request $r): void
    {
        $codes = Flash::pull('recovery_codes');
        if (!is_array($codes)) {
            $this->redirect('/admin/seguridad');
        }
        $this->render('codes', ['codes' => $codes], 'Códigos de recuperación', 'seguridad');
    }

    public function index(Request $r): void
    {
        $this->render('security', ['u' => $this->user, 'left' => Auth::recoveryLeft($this->user), 'csrf' => Csrf::field()], 'Seguridad', 'seguridad');
    }

    /** Pide la contraseña actual; devuelve false (con mensaje) si no coincide o hay demasiados fallos. */
    private function reauth(Request $r): bool
    {
        $ip = FormGuard::ipHash($r->ip());
        $subject = Auth::subjectHash($this->user['email']);
        if (Auth::blocked('login', $ip, $subject)) {
            $this->flash('error', 'Demasiados intentos fallidos. Espera 15 minutos.');
            return false;
        }
        $ok = Auth::verifyPassword($this->user, (string) ($_POST['actual'] ?? ''));
        Auth::record('login', $ip, $subject, $ok);
        if (!$ok) {
            $this->flash('error', 'La contraseña actual no es correcta.');
        }
        return $ok;
    }

    public function password(Request $r): void
    {
        $this->csrf($r, '/admin/seguridad');
        if (!$this->reauth($r)) {
            $this->redirect('/admin/seguridad');
        }
        $new = (string) ($_POST['nueva'] ?? '');
        if ($new !== (string) ($_POST['nueva2'] ?? '')) {
            $this->flash('error', 'Las dos contraseñas nuevas no coinciden.');
        } elseif (($p = Auth::passwordProblem($new, $this->user['email'])) !== null) {
            $this->flash('error', $p);
        } else {
            Database::run('UPDATE admin_users SET password_hash = ? WHERE id = ?', [Auth::hash($new), $this->user['id']]);
            Auth::invalidateSessions((int) $this->user['id']);
            Auth::logout();
            Flash::put('admin_msg', ['type' => 'ok', 'text' => 'Contraseña cambiada. Entra de nuevo con la nueva.']);
            $this->redirect('/admin/entrar');
        }
        $this->redirect('/admin/seguridad');
    }

    public function newRecovery(Request $r): void
    {
        $this->csrf($r, '/admin/seguridad');
        if (!$this->reauth($r)) {
            $this->redirect('/admin/seguridad');
        }
        [$plain, $hashes] = Auth::newRecoveryCodes();
        Database::run('UPDATE admin_users SET recovery_codes = ? WHERE id = ?', [json_encode($hashes), $this->user['id']]);
        Flash::put('recovery_codes', $plain);
        $this->redirect('/admin/codigos-de-recuperacion');
    }

    public function resetTotp(Request $r): void
    {
        $this->csrf($r, '/admin/seguridad');
        if (!$this->reauth($r)) {
            $this->redirect('/admin/seguridad');
        }
        Auth::startEnrollment((int) $this->user['id']);
        Auth::invalidateSessions((int) $this->user['id']);
        Auth::setPending((int) $this->user['id']);
        Flash::put('admin_msg', ['type' => 'aviso', 'text' => 'Configura la aplicación de verificación de nuevo. La anterior dejará de funcionar.']);
        $this->redirect('/admin/activar');
    }

    public function closeSessions(Request $r): void
    {
        $this->csrf($r, '/admin/seguridad');
        Auth::invalidateSessions((int) $this->user['id']);
        Auth::logout();
        Flash::put('admin_msg', ['type' => 'ok', 'text' => 'Se han cerrado todas las sesiones.']);
        $this->redirect('/admin/entrar');
    }
}
