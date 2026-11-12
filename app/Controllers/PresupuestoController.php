<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Http;
use App\Core\Request;
use App\Core\View;
use App\Repositories\Content;
use App\Repositories\Countries;
use App\Repositories\Requests;
use App\Services\FormGuard;
use App\Services\FormOptions;
use App\Services\RequestMail;
use App\Services\RequestValidator;

final class PresupuestoController
{
    use GuardsSubmissions;

    /** Paso del formulario al que pertenece cada error. */
    private const STEP_OF = [
        'destinos' => 1, 'fechas' => 2, 'duracion' => 2, 'viajeros' => 3, 'edades' => 3,
        'mensaje' => 4, 'nombre' => 5, 'email' => 5, 'telefono' => 5, 'consentimiento' => 5,
    ];

    /** @return array{slug: array<string,string>, names: array<string,string>} */
    private function countries(): array
    {
        $cont = [];
        $names = [];
        foreach (Countries::all() as $c) {
            $cont[$c['slug']] = $c['continente'];
            $names[$c['slug']] = $c['nombre'];
        }
        return ['slug' => $cont, 'names' => $names];
    }

    public function show(Request $r): void
    {
        $this->noCache();
        $c = $this->countries();
        $old = Flash::pull('old', []);
        $errors = Flash::pull('errors', []);
        $aviso = Flash::pull('aviso');

        // Entradas con destino ya elegido: ?pais=chile,peru → paso 2 · ?pais=aun-no-lo-se → paso 4
        $pre = [];
        $start = 1;
        if (!$old && isset($_GET['pais']) && is_string($_GET['pais'])) {
            foreach (explode(',', $_GET['pais']) as $s) {
                $s = trim($s);
                if (isset($c['slug'][$s]) || $s === RequestValidator::UNKNOWN) {
                    $pre[] = $s;
                }
            }
            $pre = in_array(RequestValidator::UNKNOWN, $pre, true) ? [RequestValidator::UNKNOWN] : array_values(array_unique($pre));
            if ($pre) {
                $start = $pre === [RequestValidator::UNKNOWN] ? 4 : 2;
            }
        }
        if ($errors) {
            $start = min(array_map(static fn($k) => self::STEP_OF[$k] ?? 5, array_keys($errors)));
        }

        echo View::render('pages/presupuesto', [
            'paises'     => Countries::byContinent(),
            'intereses'  => Content::interests(),
            'meses'      => FormOptions::meses(new \DateTimeImmutable('now')),
            'old'        => $old,
            'errors'     => $errors,
            'pre'        => $pre,
            'start'      => $start,
            'aviso'      => is_string($aviso) ? $aviso : null,
            'csrf'       => Csrf::field(),
            'timeToken'  => FormGuard::timeToken(),
        ], [
            ...\App\Core\Seo::page('presupuesto', 'Pide tu presupuesto de viaje a medida · Guanaco Viajes', 'Cuéntanos tu idea en cinco pasos cortos y diseñamos tu viaje a medida por Sudamérica o Europa.'),
            'franja'      => false,
            'bubble'      => false,
            'scripts'     => ['assets/js/presupuesto.js'],
        ]);
    }

    public function send(Request $r): void
    {
        $why = $this->guard($r);
        if ($why === 'bot') {
            // A un robot no se le dice que ha fallado: parece éxito y no se guarda nada.
            Flash::put('gracias', ['nombre' => 'amigo', 'canal' => 'correo', 'rows' => [], 'falso' => true]);
            Http::redirect('/presupuesto/gracias', 303);
        }
        if ($why !== null && $why !== 'rapido') {
            Flash::put('old', $this->oldInput());
            Flash::put('aviso', match ($why) {
                'limite'   => 'Has enviado varias solicitudes seguidas. Espera un rato o escríbenos por WhatsApp o correo.',
                'caducado' => 'El formulario ha caducado. Revisa tus respuestas y vuelve a enviarlo.',
                default    => 'No hemos podido comprobar el formulario. Revisa tus respuestas y vuelve a enviarlo.',
            });
            Http::redirect('/presupuesto', 303);
        }

        $c = $this->countries();
        $intereses = [];
        foreach (Content::interests() as $i) {
            $intereses[(int) $i['id']] = $i['nombre'];
        }
        $res = RequestValidator::budget($_POST, $c['slug'], $intereses, FormOptions::meses(new \DateTimeImmutable('now')));
        if ($res['errors']) {
            Flash::put('old', $this->oldInput());
            Flash::put('errors', $res['errors']);
            Http::redirect('/presupuesto', 303);
        }

        if ($why === 'rapido') {
            // Datos correctos pero enviados en menos de 4 s: lo propio de un robot. No se guarda ni se envía nada.
            Flash::put('gracias', ['nombre' => 'amigo', 'canal' => 'correo', 'rows' => [], 'falso' => true]);
            Http::redirect('/presupuesto/gracias', 303);
        }

        $d = $res['data'];
        $id = Requests::insert('presupuesto', $d, FormOptions::consentimiento(), FormGuard::ipHash($r->ip()));
        $aviso = RequestMail::notify($id, 'presupuesto', $d, $c['names']);
        $acuse = RequestMail::ack('presupuesto', $d, $c['names']);
        Requests::markSent($id, $aviso, $acuse);

        Flash::put('gracias', [
            'nombre' => explode(' ', $d['nombre'])[0],
            'canal'  => $d['canal'],
            'rows'   => RequestMail::summary('presupuesto', $d, $c['names']),
        ]);
        Http::redirect('/presupuesto/gracias', 303);
    }

    public function thanks(Request $r): void
    {
        $this->noCache();
        $g = Flash::pull('gracias');
        if (!is_array($g)) {
            Http::redirect('/presupuesto', 302);
        }
        echo View::render('pages/gracias', ['g' => $g], [
            'title'  => 'Gracias · Guanaco Viajes',
            'robots' => 'noindex,follow',
            'franja' => false,
        ]);
    }
}
