<?php
declare(strict_types=1);

namespace App\Admin;

final class SlidesController extends CrudController
{
    protected function cfg(): array
    {
        return [
            'table' => 'carousel_slides', 'section' => 'inicio', 'path' => '/admin/inicio/carrusel', 'singular' => 'diapositiva', 'plural' => 'Carrusel de inicio',
            'create' => 'Añadir una diapositiva', 'delete' => true, 'order' => 'activo DESC, orden, id',
            'intro' => 'Las fotos grandes de la portada. Se recomiendan tres. El texto va en un bloque sólido, nunca sobre la foto.',
            'help' => '<b>Importante:</b> el menú va sobre la foto. Elige el color del menú según la foto: «oscuro» si arriba hay cielo o zonas claras, «claro» si arriba la foto es oscura. Evita fotos con la parte alta muy mezclada de claro y oscuro.',
            'deleteConfirm' => '¿Eliminar esta diapositiva?',
            'columns' => [
                ['Diapositiva', static fn($r) => e(trim($r['titular'] . ' ' . $r['titular_enfasis']))],
                ['Etiqueta', static fn($r) => e($r['kicker'])],
                ['Menú', static fn($r) => $r['tono_menu'] === 'oscuro' ? 'Texto oscuro' : 'Texto claro'],
                ['Orden', static fn($r) => (int) $r['orden']],
                ['Estado', static fn($r) => $r['activo'] ? '<span class="tag pub">Activa</span>' : '<span class="tag draft">Oculta</span>'],
            ],
        ];
    }

    protected function defaults(): array
    {
        return ['activo' => '1', 'tono_menu' => 'oscuro', 'foto_pos' => '50% 50%', 'orden' => '10', 'cta_url' => '/presupuesto', 'cta_texto' => 'Pide tu presupuesto'];
    }

    protected function fields(?array $row): array
    {
        return [
            ['name' => 'kicker', 'label' => 'Etiqueta pequeña sobre el titular', 'max' => 80, 'help' => 'Por ejemplo: «Viajes a medida».'],
            ['name' => 'titular', 'label' => 'Titular', 'required' => true, 'max' => 120, 'help' => 'Por ejemplo: «Viaja con».'],
            ['name' => 'titular_enfasis', 'label' => 'Titular: palabra en cursiva', 'max' => 80, 'help' => 'Por ejemplo: «curiosidad».'],
            ['name' => 'foto', 'label' => 'Foto', 'type' => 'image', 'required' => true, 'help' => 'Horizontal, de al menos 1800 px de ancho si puede ser.'],
            ['name' => 'foto_pos', 'label' => 'Encuadre de la foto', 'type' => 'pos'],
            ['name' => 'foto_alt', 'label' => 'Descripción de la foto', 'max' => 200],
            ['name' => 'tono_menu', 'label' => 'Color del texto del menú sobre esta foto', 'type' => 'select', 'required' => true, 'options' => ['oscuro' => 'Oscuro (para fotos claras arriba)', 'claro' => 'Claro (para fotos oscuras arriba)']],
            ['name' => 'cta_texto', 'label' => 'Texto del botón', 'max' => 60],
            ['name' => 'cta_url', 'label' => 'A dónde lleva el botón', 'type' => 'url', 'help' => 'Por ejemplo: /presupuesto, /contacto o /viajes-en-grupo.'],
            ['name' => 'orden', 'label' => 'Orden', 'type' => 'number', 'min' => 0, 'max' => 999],
            ['name' => 'activo', 'label' => 'Activa (se muestra en el carrusel)', 'type' => 'check'],
        ];
    }

    protected function validate(array &$data, ?array $row): array
    {
        return (($data['cta_texto'] ?? '') !== '' && ($data['cta_url'] ?? '') === '') ? ['cta_url' => 'Si hay botón, indica a dónde lleva.'] : [];
    }
}
