<?php
declare(strict_types=1);

namespace App\Admin;

final class InterestsController extends CrudController
{
    protected function cfg(): array
    {
        return [
            'table' => 'interests', 'section' => 'formulario', 'path' => '/admin/formulario/opciones', 'singular' => 'opción', 'plural' => 'Opciones de «¿Qué te apetece?»',
            'create' => 'Añadir una opción', 'delete' => true, 'order' => 'activo DESC, orden, id',
            'intro' => 'Las opciones que el visitante puede tocar en el paso 4 del formulario de presupuesto (opcionales y de selección múltiple). Si más adelante detectas respuestas mejores, cámbialas aquí.',
            'deleteConfirm' => '¿Eliminar esta opción? Las solicitudes ya recibidas conservan lo que eligieron.',
            'columns' => [
                ['Opción', static fn($r) => e($r['nombre'])],
                ['Orden', static fn($r) => (int) $r['orden']],
                ['Estado', static fn($r) => $r['activo'] ? '<span class="tag pub">Visible</span>' : '<span class="tag draft">Oculta</span>'],
            ],
        ];
    }

    protected function defaults(): array
    {
        return ['activo' => '1', 'orden' => '50'];
    }

    protected function fields(?array $row): array
    {
        return [
            ['name' => 'nombre', 'label' => 'Texto de la opción', 'required' => true, 'max' => 60, 'help' => 'Corto, por ejemplo: «Naturaleza».'],
            ['name' => 'orden', 'label' => 'Orden', 'type' => 'number', 'min' => 0, 'max' => 999],
            ['name' => 'activo', 'label' => 'Visible en el formulario', 'type' => 'check'],
        ];
    }
}
