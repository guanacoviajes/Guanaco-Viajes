<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Http;
use App\Core\Request;
use App\Core\View;
use App\Services\ImageService;

final class CountriesController extends CrudController
{
    protected function cfg(): array
    {
        return [
            'table' => 'countries', 'section' => 'destinos', 'path' => '/admin/destinos', 'singular' => 'destino', 'plural' => 'Destinos',
            'create' => 'Añadir un destino', 'delete' => true, 'order' => "FIELD(continente,'sudamerica','europa'), orden, nombre", 'unique' => ['slug'],
            'intro' => 'Los países que aparecen en el menú, en la página de destinos, en las tarjetas y en el formulario de presupuesto.',
            'help' => 'Al guardar, el país aparece solo en el menú, en las tarjetas y en el formulario. <b>Para crear un destino nuevo solo necesitas</b> nombre, continente, resumen y una foto.',
            'deleteConfirm' => '¿Eliminar este destino? Las salidas y artículos que lo usan quedarán sin país. No se puede deshacer.',
            'deleteHint' => 'Si solo quieres ocultarlo temporalmente, desmarca «Publicado» en lugar de eliminarlo.',
            'viewUrl' => static fn(array $r): string => '/destinos/' . $r['continente'] . '/' . $r['slug'],
            'columns' => [
                ['Destino', static fn($r) => e($r['nombre'])],
                ['Continente', static fn($r) => $r['continente'] === 'sudamerica' ? 'Sudamérica' : 'Europa'],
                ['Orden', static fn($r) => (int) $r['orden']],
                ['Estado', static fn($r) => $r['publicado'] ? '<span class="tag pub">Publicado</span>' : '<span class="tag draft">Oculto</span>'],
            ],
        ];
    }

    protected function defaults(): array
    {
        return ['publicado' => '1', 'foto_pos' => '50% 50%', 'orden' => '10', 'continente' => 'sudamerica'];
    }

    protected function fields(?array $row): array
    {
        return [
            ['name' => 'nombre', 'label' => 'Nombre', 'required' => true, 'max' => 80, 'slugFrom' => true],
            ['name' => 'slug', 'label' => 'Dirección web (slug)', 'type' => 'slug', 'required' => true, 'max' => 60, 'help' => 'Aparece en la URL: /destinos/…/costa-rica. Cambiarla rompe los enlaces antiguos.'],
            ['name' => 'continente', 'label' => 'Continente', 'type' => 'select', 'required' => true, 'options' => ['sudamerica' => 'Sudamérica', 'europa' => 'Europa'],
                'help' => 'Con un destino de Sudamérica, el formulario no ofrece «fin de semana largo».'],
            ['name' => 'orden', 'label' => 'Orden', 'type' => 'number', 'min' => 0, 'max' => 999, 'help' => 'Los números más bajos salen primero. El primero de cada continente se muestra ampliado.'],
            ['name' => 'resumen', 'label' => 'Resumen (genérico, sin rutas típicas)', 'type' => 'textarea', 'required' => true, 'max' => 600, 'help' => 'Unas 50 palabras de lo que se puede descubrir. Sin precios, fechas ni promesas.'],
            ['name' => 'foto', 'label' => 'Foto principal', 'type' => 'image', 'required' => true],
            ['name' => 'foto_pos', 'label' => 'Encuadre de la foto', 'type' => 'pos'],
            ['name' => 'foto_alt', 'label' => 'Descripción de la foto (para accesibilidad y buscadores)', 'max' => 200, 'help' => 'Describe lo que se ve, por ejemplo: «Persona caminando junto a una laguna con volcanes al fondo».'],
            ['name' => 'seo_title', 'label' => 'Título para buscadores (opcional)', 'max' => 160, 'help' => 'Si lo dejas vacío se usa «Nombre · Viajes a medida · Guanaco Viajes».'],
            ['name' => 'seo_desc', 'label' => 'Descripción para buscadores (opcional)', 'type' => 'textarea', 'max' => 255, 'help' => 'Si lo dejas vacío se usa el resumen.'],
            ['name' => 'publicado', 'label' => 'Publicado (visible en la web)', 'type' => 'check'],
        ];
    }

    protected function extra(?array $row): string
    {
        $photos = Database::all('SELECT id, archivo, alt FROM country_photos WHERE country_id = ? ORDER BY orden, id', [$row['id']]);
        return View::partial('admin/countries_gallery', ['row' => $row, 'photos' => $photos, 'csrf' => \App\Core\Csrf::field()]);
    }

    public function addPhoto(Request $r, string $id): void
    {
        $row = $this->find($id) ?? Http::notFound();
        $back = '/admin/destinos/' . $row['id'];
        $this->csrf($r, $back);
        if ((int) Database::value('SELECT COUNT(*) FROM country_photos WHERE country_id = ?', [$row['id']]) >= 6) {
            $this->flash('error', 'Ya hay 6 fotos en la galería, que es el máximo. Elimina alguna antes de añadir otra.');
            $this->redirect($back);
        }
        $res = ImageService::store($_FILES['foto'] ?? [], ($_POST['grade'] ?? '1') === '1');
        if ($res['error'] !== null) {
            $this->flash('error', $res['error']);
            $this->redirect($back);
        }
        $alt = mb_substr(trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', (string) ($_POST['alt'] ?? '')) ?? ''), 0, 200);
        $next = (int) Database::value('SELECT COALESCE(MAX(orden), 0) + 1 FROM country_photos WHERE country_id = ?', [$row['id']]);
        Database::run('INSERT INTO country_photos (country_id, archivo, alt, orden) VALUES (?,?,?,?)', [$row['id'], $res['path'], $alt, $next]);
        $this->flash('ok', 'Foto añadida a la galería.');
        $this->redirect($back);
    }

    public function removePhoto(Request $r, string $id, string $fid): void
    {
        $row = $this->find($id) ?? Http::notFound();
        $back = '/admin/destinos/' . $row['id'];
        $this->csrf($r, $back);
        Database::run('DELETE FROM country_photos WHERE id = ? AND country_id = ?', [$this->intParam($fid), $row['id']]);
        $this->flash('ok', 'Foto quitada de la galería. Si no se usa en ningún otro sitio, puedes borrar el archivo en «Imágenes».');
        $this->redirect($back);
    }
}
