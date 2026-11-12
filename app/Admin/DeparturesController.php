<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Request;
use App\Repositories\Countries;
use App\Core\Text;

final class DeparturesController extends CrudController
{
    protected function cfg(): array
    {
        return [
            'table' => 'departures', 'section' => 'salidas', 'path' => '/admin/salidas', 'singular' => 'salida', 'plural' => 'Viajes en grupo',
            'create' => 'Añadir una salida', 'delete' => true, 'order' => 'publicada DESC, fecha_inicio IS NULL, fecha_inicio, orden, id',
            'intro' => 'Las salidas publicadas sustituyen a los huecos «Próximamente» de la página de viajes en grupo (siempre se muestran al menos tres). Las que ya han terminado dejan de mostrarse solas.',
            'help' => 'Datos de referencia que dijiste: <b>grupos de 10 a 12 personas</b> y precio «desde». Las condiciones de reserva se explican cuando la persona contacta. Escríbelos aquí en cada salida cuando estén confirmados.',
            'deleteConfirm' => '¿Eliminar esta salida? No se puede deshacer.',
            'deleteHint' => 'Para ocultarla sin borrarla, desmarca «Publicada».',
            'columns' => [
                ['Salida', static fn($r) => e($r['titulo'])],
                ['Fechas', static fn($r) => e(Text::rangeEs($r['fecha_inicio'], $r['fecha_fin']) ?: '—')],
                ['Desde', static fn($r) => e($r['precio_desde'] ?: '—')],
                ['Estado', static function ($r) {
                    if (!$r['publicada']) {
                        return '<span class="tag draft">Sin publicar</span>';
                    }
                    return ($r['fecha_fin'] && $r['fecha_fin'] < date('Y-m-d')) ? '<span class="tag warn">Terminada</span>' : '<span class="tag pub">Publicada</span>';
                }],
            ],
        ];
    }

    protected function defaults(): array
    {
        return ['plazas_texto' => 'Grupo de 10 a 12 personas'];
    }

    protected function fields(?array $row): array
    {
        $paises = ['' => 'Sin país concreto'];
        foreach (Countries::all() as $c) {
            $paises[(string) $c['id']] = $c['nombre'];
        }
        return [
            ['name' => 'titulo', 'label' => 'Título de la salida', 'required' => true, 'max' => 160],
            ['name' => 'country_id', 'label' => 'País', 'type' => 'select', 'options' => $paises, 'null' => null, 'help' => 'Si eliges un país, la salida también aparece en su ficha.'],
            ['name' => 'descripcion', 'label' => 'Descripción breve', 'type' => 'textarea', 'max' => 600],
            ['name' => 'fecha_inicio', 'label' => 'Primer día', 'type' => 'date'],
            ['name' => 'fecha_fin', 'label' => 'Último día', 'type' => 'date'],
            ['name' => 'plazas_texto', 'label' => 'Tamaño del grupo', 'max' => 80, 'help' => 'Por ejemplo: «Grupo de 10 a 12 personas».'],
            ['name' => 'precio_desde', 'label' => 'Precio «desde»', 'max' => 40, 'help' => 'Escríbelo con el símbolo, por ejemplo: «2.450 €». Se muestra como «Desde 2.450 €».'],
            ['name' => 'foto', 'label' => 'Foto', 'type' => 'image'],
            ['name' => 'foto_alt', 'label' => 'Descripción de la foto', 'max' => 200],
            ['name' => 'publicada', 'label' => 'Publicada (visible en la web)', 'type' => 'check'],
            ['name' => 'orden', 'label' => 'Orden (si hay varias en la misma fecha)', 'type' => 'number', 'min' => 0, 'max' => 999, 'default' => 0],
        ];
    }

    protected function validate(array &$data, ?array $row): array
    {
        $e = [];
        if (!empty($data['fecha_inicio']) && !empty($data['fecha_fin']) && $data['fecha_fin'] < $data['fecha_inicio']) {
            $e['fecha_fin'] = 'El último día no puede ser anterior al primero.';
        }
        if (!empty($data['publicada']) && empty($data['fecha_inicio'])) {
            $e['fecha_inicio'] = 'Para publicar una salida indica al menos su primer día.';
        }
        $data['country_id'] = ($data['country_id'] ?? '') === '' ? null : (int) $data['country_id'];
        return $e;
    }
}
