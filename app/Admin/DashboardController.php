<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Repositories\Requests;
use App\Repositories\Settings;

final class DashboardController extends AdminController
{
    public function index(Request $r): void
    {
        $recent = Requests::search([], 1, 8)['rows'];
        $todo = [];
        if (Settings::get('legal.revisado') !== '1') {
            $todo[] = ['Los textos legales aún no están marcados como revisados por Nadidú. No publiques la web hasta que lo estén.', '/admin/paginas'];
        }
        if (Settings::get('home.quien.foto') === '') {
            $todo[] = ['Falta tu foto para «Quién está detrás» y «Quiénes somos».', '/admin/ajustes#foto'];
        }
        if (Settings::get('social.instagram') === '' && Settings::get('social.facebook') === '' && Settings::get('social.x') === '') {
            $todo[] = ['Aún no has añadido ninguna red social (los iconos del pie no aparecen hasta que lo hagas).', '/admin/ajustes#redes'];
        }
        if ((int) Database::value('SELECT COUNT(*) FROM departures WHERE publicada = 1') === 0) {
            $todo[] = ['No hay ninguna salida de viaje en grupo publicada (la página muestra tres huecos «Próximamente»).', '/admin/salidas'];
        }
        $pending = Requests::pendingNotices();
        if ($pending > 0) {
            array_unshift($todo, ["$pending solicitud(es) no han podido avisarte por correo. Revísalas y reenvía el aviso.", '/admin/solicitudes?aviso=pendiente']);
        }
        $this->render('dashboard', [
            'nuevas'  => Requests::countNew(),
            'mes'     => Requests::countSince(30),
            'total'   => (int) Database::value('SELECT COUNT(*) FROM requests'),
            'recent'  => $recent,
            'todo'    => $todo,
            'paises'  => (int) Database::value('SELECT COUNT(*) FROM countries WHERE publicado = 1'),
            'posts'   => (int) Database::value('SELECT COUNT(*) FROM posts WHERE publicado = 1'),
        ], 'Panel', 'panel');
    }
}
