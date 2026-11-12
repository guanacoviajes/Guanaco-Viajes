<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Text;
use App\Repositories\Countries;

final class PostsController extends CrudController
{
    protected function cfg(): array
    {
        return [
            'table' => 'posts', 'section' => 'blog', 'path' => '/admin/blog', 'singular' => 'artículo', 'plural' => 'Blog',
            'create' => 'Escribir un artículo', 'delete' => true, 'order' => 'created_at DESC, id DESC', 'unique' => ['slug'],
            'intro' => 'Los artículos publicados aparecen en el blog, en la home (los tres últimos) y en la ficha del país al que estén ligados.',
            'empty' => 'Todavía no hay artículos. Mientras no haya ninguno publicado, el blog se oculta de la home y muestra un mensaje en su página.',
            'deleteConfirm' => '¿Eliminar este artículo? No se puede deshacer.',
            'deleteHint' => 'Para quitarlo de la web sin borrarlo, desmarca «Publicado».',
            'viewUrl' => static fn(array $r): string => '/blog/' . $r['slug'],
            'help' => '<b>Formato del texto:</b> deja una línea en blanco entre párrafos. <code>## Título</code> crea un subtítulo, <code>&gt; texto</code> una cita, <code>- texto</code> una lista, <code>*cursiva*</code>, <code>**negrita**</code> y <code>[texto](https://enlace)</code> un enlace.',
            'columns' => [
                ['Título', static fn($r) => e($r['titulo'])],
                ['País', static fn($r) => e($r['country_id'] ? (string) (\App\Core\Database::value('SELECT nombre FROM countries WHERE id = ?', [$r['country_id']]) ?? '—') : '—')],
                ['Fecha', static fn($r) => e($r['publicado_en'] ? date('d/m/Y', strtotime($r['publicado_en'])) : '—')],
                ['Estado', static function ($r) {
                    if (!$r['publicado']) {
                        return '<span class="tag draft">Borrador</span>';
                    }
                    return ($r['publicado_en'] && strtotime($r['publicado_en']) > time()) ? '<span class="tag warn">Programado</span>' : '<span class="tag pub">Publicado</span>';
                }],
            ],
        ];
    }

    protected function fields(?array $row): array
    {
        $paises = ['' => 'Sin país'];
        foreach (Countries::all() as $c) {
            $paises[(string) $c['id']] = $c['nombre'];
        }
        return [
            ['name' => 'titulo', 'label' => 'Título', 'required' => true, 'max' => 200, 'slugFrom' => true],
            ['name' => 'slug', 'label' => 'Dirección web (slug)', 'type' => 'slug', 'required' => true, 'max' => 120, 'help' => 'Aparece en la URL: /blog/mi-articulo.'],
            ['name' => 'country_id', 'label' => 'País al que se refiere', 'type' => 'select', 'options' => $paises, 'null' => null, 'help' => 'Si eliges uno, el artículo también sale en la ficha de ese país y se puede filtrar por él.'],
            ['name' => 'extracto', 'label' => 'Resumen', 'type' => 'textarea', 'required' => true, 'max' => 400, 'help' => 'Una o dos frases. Sirve de descripción para buscadores y redes.'],
            ['name' => 'cuerpo', 'label' => 'Texto del artículo', 'type' => 'tall', 'required' => true, 'max' => 60000],
            ['name' => 'portada', 'label' => 'Foto de portada', 'type' => 'image'],
            ['name' => 'portada_alt', 'label' => 'Descripción de la foto', 'max' => 200],
            ['name' => 'publicado', 'label' => 'Publicado', 'type' => 'check'],
            ['name' => 'publicado_en', 'label' => 'Fecha de publicación', 'type' => 'datetime', 'help' => 'Si la dejas vacía se usa el momento en que publicas. Una fecha futura lo programa.'],
            ['name' => 'seo_title', 'label' => 'Título para buscadores (opcional)', 'max' => 160],
            ['name' => 'seo_desc', 'label' => 'Descripción para buscadores (opcional)', 'type' => 'textarea', 'max' => 255, 'help' => 'Si la dejas vacía se usa el resumen.'],
        ];
    }

    protected function validate(array &$data, ?array $row): array
    {
        $data['country_id'] = ($data['country_id'] ?? '') === '' ? null : (int) $data['country_id'];
        if (!empty($data['publicado']) && empty($data['publicado_en'])) {
            $data['publicado_en'] = ($row && $row['publicado_en']) ? $row['publicado_en'] : date('Y-m-d H:i:s');
        }
        return [];
    }
}
