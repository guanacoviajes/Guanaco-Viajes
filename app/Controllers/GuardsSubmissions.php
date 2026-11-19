<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Request;
use App\Repositories\Requests;
use App\Services\FormGuard;

/** Comprobaciones previas comunes a los formularios que guardan datos. */
trait GuardsSubmissions
{
    /**
     * @return string|null null si todo está bien; si no, el motivo:
     *   'csrf' | 'bot' | 'rapido' | 'caducado' | 'limite'
     *   ('rapido' no se rechaza al momento: primero se validan los datos, para que una persona real vea sus errores)
     */
    private function guard(Request $r): ?string
    {
        if (!Csrf::verify($r->post('_csrf'))) {
            return 'csrf';
        }
        if ($r->post('sitio_web') !== '') {
            return 'bot';
        }
        $t = FormGuard::checkTime($r->post('t'));
        if ($t === 'invalido') {
            return 'bot';
        }
        if ($t === 'caducado') {
            return 'caducado';
        }
        $ip = FormGuard::ipHash($r->ip());
        if (Requests::recentFrom($ip, 60) >= FormGuard::MAX_PER_HOUR || Requests::recentFrom($ip, 1440) >= FormGuard::MAX_PER_DAY) {
            return 'limite';
        }
        return $t === 'rapido' ? 'rapido' : null;
    }

    private function noCache(): void
    {
        header('Cache-Control: private, no-store, max-age=0');
    }

    /** Todo el POST en bruto, solo para volver a rellenar el formulario si hay errores. */
    private function oldInput(): array
    {
        $out = [];
        foreach ($_POST as $k => $v) {
            if (in_array($k, ['_csrf', 't', 'sitio_web'], true)) {
                continue;
            }
            $out[(string) $k] = is_array($v)
                ? array_values(array_filter($v, 'is_string'))
                : (is_string($v) ? $v : '');
        }
        return $out;
    }
}
