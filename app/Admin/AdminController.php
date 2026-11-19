<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Http;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;

/** Base de todas las pantallas del panel: exige sesión, evita la caché y comprueba el CSRF. */
abstract class AdminController
{
    protected array $user;

    public function __construct()
    {
        self::prepare();
        $u = Auth::user();
        if ($u === null) {
            Http::redirect('/admin/entrar', 303);
        }
        $this->user = $u;
    }

    /** Cabeceras y sesión propias del panel (también las usa el acceso, que no exige sesión). */
    public static function prepare(): void
    {
        Session::useAdmin();
        header('Cache-Control: private, no-store, max-age=0');
        header('X-Robots-Tag: noindex, nofollow');
    }

    protected function render(string $template, array $data, string $title, string $section = ''): void
    {
        echo self::page($template, $data, $title, $section, $this->user);
    }

    public static function page(string $template, array $data, string $title, string $section = '', ?array $user = null): string
    {
        $content = View::partial('admin/' . $template, $data);
        return View::partial('admin/layout', [
            'content' => $content,
            'title'   => $title,
            'section' => $section,
            'user'    => $user,
            'flash'   => Flash::pull('admin_msg'),
        ]);
    }

    protected function flash(string $type, string $message): void
    {
        Flash::put('admin_msg', ['type' => $type, 'text' => $message]);
    }

    protected function redirect(string $to): never
    {
        Http::redirect($to, 303);
    }

    /** Todo POST del panel lleva el token CSRF; si falla, se vuelve atrás sin hacer nada. */
    protected function csrf(Request $r, string $back): void
    {
        // Si lo enviado supera post_max_size, PHP descarta todo el formulario (también el CSRF):
        // se avisa del motivo real en lugar de decir que la sesión caducó.
        $len = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        $max = self::iniBytes((string) ini_get('post_max_size'));
        if ($len > 0 && $max > 0 && $len > $max && !$_POST) {
            $this->flash('error', 'El archivo es demasiado grande para este servidor (límite actual: ' . round($max / 1048576) . ' MB). '
                . 'Reduce la foto, o sube el límite «post_max_size» y «upload_max_filesize» en las opciones de PHP del hosting.');
            $this->redirect($back);
        }
        if (!Csrf::verify($r->post('_csrf'))) {
            $this->flash('error', 'La sesión ha caducado. Vuelve a intentarlo.');
            $this->redirect($back);
        }
    }

    protected function intParam(string $v): int
    {
        return ctype_digit($v) ? (int) $v : 0;
    }

    private static function iniBytes(string $v): int
    {
        $v = trim($v);
        $n = (int) $v;
        return match (strtolower(substr($v, -1))) { 'g' => $n * 1073741824, 'm' => $n * 1048576, 'k' => $n * 1024, default => $n };
    }
}
