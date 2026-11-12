<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Http;
use App\Core\Request;
use App\Core\View;
use App\Repositories\Countries;
use App\Repositories\Requests;
use App\Services\FormGuard;
use App\Services\FormOptions;
use App\Services\RequestMail;
use App\Services\RequestValidator;

final class ContactoController
{
    use GuardsSubmissions;

    public function show(Request $r): void
    {
        $this->noCache();
        echo View::render('pages/contacto', [
            'old'       => Flash::pull('old', []),
            'errors'    => Flash::pull('errors', []),
            'aviso'     => Flash::pull('aviso'),
            'enviado'   => Flash::pull('contacto_ok') === true,
            'csrf'      => Csrf::field(),
            'timeToken' => FormGuard::timeToken(),
        ], [
            ...\App\Core\Seo::page('contacto', 'Contacto · Guanaco Viajes', 'Escríbenos por WhatsApp o correo, o déjanos un mensaje. Para un viaje a medida, pide tu presupuesto.'),
            'franja'      => false,
        ]);
    }

    public function send(Request $r): void
    {
        $why = $this->guard($r);
        if ($why === 'bot') {
            Flash::put('contacto_ok', true);
            Http::redirect('/contacto#mensaje-enviado', 303);
        }
        if ($why !== null && $why !== 'rapido') {
            Flash::put('old', $this->oldInput());
            Flash::put('aviso', match ($why) {
                'limite'   => 'Has enviado varios mensajes seguidos. Espera un rato o escríbenos por WhatsApp.',
                'caducado' => 'El formulario ha caducado. Revisa tu mensaje y vuelve a enviarlo.',
                default    => 'No hemos podido comprobar el formulario. Revisa tu mensaje y vuelve a enviarlo.',
            });
            Http::redirect('/contacto#formulario', 303);
        }
        $res = RequestValidator::message($_POST);
        if ($res['errors']) {
            Flash::put('old', $this->oldInput());
            Flash::put('errors', $res['errors']);
            Http::redirect('/contacto#formulario', 303);
        }
        if ($why === 'rapido') {
            Flash::put('contacto_ok', true);
            Http::redirect('/contacto#mensaje-enviado', 303);
        }
        $d = $res['data'];
        $id = Requests::insert('contacto', $d, FormOptions::consentimientoContacto(), FormGuard::ipHash($r->ip()));
        $names = [];
        foreach (Countries::all() as $c) {
            $names[$c['slug']] = $c['nombre'];
        }
        $aviso = RequestMail::notify($id, 'contacto', $d, $names);
        $acuse = RequestMail::ack('contacto', $d, $names);
        Requests::markSent($id, $aviso, $acuse);
        Flash::put('contacto_ok', true);
        Http::redirect('/contacto#mensaje-enviado', 303);
    }
}
