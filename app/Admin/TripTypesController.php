<?php
declare(strict_types=1);

namespace App\Admin;

final class TripTypesController extends CrudController
{
    protected function cfg(): array
    {
        return [
            'table' => 'trip_types', 'section' => 'inicio', 'path' => '/admin/inicio/tipos', 'singular' => 'tipo de viaje', 'plural' => 'Tipos de viaje',
            'create' => null, 'delete' => false, 'order' => 'orden, id',
            'intro' => 'Los dos tipos de viaje: texto y foto de la home y de la cabecera de cada página. Los dos son fijos, solo se editan.',
            'columns' => [
                ['Tipo', static fn($r) => e($r['titulo'])],
                ['Botón', static fn($r) => e($r['cta_texto'] . ' → ' . $r['cta_url'])],
            ],
        ];
    }

    protected function fields(?array $row): array
    {
        return [
            ['name' => 'titulo', 'label' => 'Nombre', 'required' => true, 'max' => 80],
            ['name' => 'descripcion', 'label' => 'Descripción breve', 'type' => 'textarea', 'required' => true, 'max' => 400, 'help' => 'Sale en la home y en la cabecera de su página.'],
            ['name' => 'cta_texto', 'label' => 'Texto del botón (en la home)', 'max' => 60],
            ['name' => 'cta_url', 'label' => 'A dónde lleva el botón', 'type' => 'url'],
            ['name' => 'foto', 'label' => 'Foto', 'type' => 'image'],
            ['name' => 'foto_pos', 'label' => 'Encuadre de la foto', 'type' => 'pos'],
            ['name' => 'foto_alt', 'label' => 'Descripción de la foto', 'max' => 200],
        ];
    }
}
