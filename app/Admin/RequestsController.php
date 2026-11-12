<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Csrf;
use App\Core\Http;
use App\Core\Request;
use App\Repositories\Countries;
use App\Repositories\Requests;
use App\Services\RequestMail;

final class RequestsController extends AdminController
{
    private const PER = 20;

    /** @return array<string,string> filtros recibidos por la URL */
    private function filters(): array
    {
        $f = [];
        foreach (['estado', 'origen', 'q'] as $k) {
            if (isset($_GET[$k]) && is_string($_GET[$k]) && trim($_GET[$k]) !== '') {
                $f[$k] = trim($_GET[$k]);
            }
        }
        return $f;
    }

    public function index(Request $r): void
    {
        $f = $this->filters();
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $res = Requests::search($f, $page, self::PER);
        $this->render('requests/index', [
            'rows'   => $res['rows'],
            'total'  => $res['total'],
            'f'      => $f,
            'page'   => $page,
            'pages'  => max(1, (int) ceil($res['total'] / self::PER)),
            'csrf'   => Csrf::field(),
        ], 'Solicitudes', 'solicitudes');
    }

    public function show(Request $r, string $id): void
    {
        $row = Requests::find($this->intParam($id)) ?? Http::notFound();
        if ($row['estado'] === 'nueva') {
            Requests::setEstado((int) $row['id'], 'leida');
            $row['estado'] = 'leida';
        }
        $names = [];
        foreach (Countries::all() as $c) {
            $names[$c['slug']] = $c['nombre'];
        }
        $d = Requests::toData($row);
        $this->render('requests/show', [
            'r'       => $row,
            'resumen' => ($row['origen'] === 'presupuesto') ? RequestMail::summary('presupuesto', $d, $names) : [],
            'csrf'    => Csrf::field(),
        ], 'Solicitud n.º ' . $row['id'], 'solicitudes');
    }

    private function load(string $id): array
    {
        return Requests::find($this->intParam($id)) ?? Http::notFound();
    }

    public function estado(Request $r, string $id): void
    {
        $row = $this->load($id);
        $this->csrf($r, '/admin/solicitudes/' . $row['id']);
        Requests::setEstado((int) $row['id'], $r->post('estado'));
        $this->flash('ok', 'Estado actualizado.');
        $this->redirect('/admin/solicitudes/' . $row['id']);
    }

    public function notas(Request $r, string $id): void
    {
        $row = $this->load($id);
        $this->csrf($r, '/admin/solicitudes/' . $row['id']);
        Requests::setNotas((int) $row['id'], trim((string) ($_POST['notas'] ?? '')));
        $this->flash('ok', 'Notas guardadas.');
        $this->redirect('/admin/solicitudes/' . $row['id']);
    }

    public function eliminar(Request $r, string $id): void
    {
        $row = $this->load($id);
        $this->csrf($r, '/admin/solicitudes/' . $row['id']);
        Requests::delete((int) $row['id']);
        $this->flash('ok', 'Solicitud eliminada definitivamente.');
        $this->redirect('/admin/solicitudes');
    }

    public function reenviar(Request $r, string $id): void
    {
        $row = $this->load($id);
        $back = '/admin/solicitudes/' . $row['id'];
        $this->csrf($r, $back);
        $names = [];
        foreach (Countries::all() as $c) {
            $names[$c['slug']] = $c['nombre'];
        }
        $d = Requests::toData($row);
        $aviso = (bool) $row['aviso_enviado'] || RequestMail::notify((int) $row['id'], $row['origen'], $d, $names);
        $acuse = (bool) $row['acuse_enviado'] || RequestMail::ack($row['origen'], $d, $names);
        Requests::markSent((int) $row['id'], $aviso, $acuse);
        $this->flash($aviso && $acuse ? 'ok' : 'error', $aviso && $acuse
            ? 'Correos enviados.'
            : 'No se ha podido enviar algún correo. Revisa la configuración del correo (storage/logs/mail.log).');
        $this->redirect($back);
    }

    /** Descarga en CSV (Excel en español: separador «;», UTF-8 con BOM). */
    public function exportar(Request $r): void
    {
        $rows = Requests::export($this->filters());
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="solicitudes-' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        $cols = ['n.º', 'Fecha', 'Origen', 'Estado', 'Nombre', 'Correo', 'Teléfono', 'Prefiere', 'Destinos', 'Meses', 'Flexible', 'Duración', 'Adultos', 'Menores', 'Edades', 'Intereses', 'Mensaje', 'Notas'];
        fputcsv($out, $cols, ';', '"', '\\');
        foreach ($rows as $x) {
            $line = [
                $x['id'], $x['created_at'], $x['origen'], $x['estado'], $x['nombre'], $x['email'], $x['telefono'], $x['canal'],
                implode(', ', json_decode((string) $x['destinos'], true) ?: []), implode(', ', json_decode((string) $x['meses'], true) ?: []),
                $x['flexible'] ? 'sí' : 'no', $x['duracion'], $x['adultos'], $x['menores'], $x['edades_menores'],
                implode(', ', json_decode((string) $x['intereses'], true) ?: []), $x['mensaje'], $x['notas'],
            ];
            // Evita que Excel ejecute como fórmula un texto que empiece por = + - @
            $line = array_map(static fn($c) => is_string($c) && $c !== '' && strpbrk($c[0], "=+-@\t\r") !== false ? "'" . $c : $c, $line);
            fputcsv($out, $line, ';', '"', '\\');
        }
        fclose($out);
        exit;
    }
}
