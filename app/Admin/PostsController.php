<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Text;
use App\Repositories\Posts;

final class PostsController extends CrudController
{
    /** @var list<int> países elegidos en el formulario (el primero es el principal) */
    private array $paisIds = [];

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
                ['País', static function ($r) {
                    $ids = Posts::countryIds((int) $r['id']);
                    if ($ids === []) {
                        return '—';
                    }
                    $in = implode(',', array_fill(0, count($ids), '?'));
                    $names = array_column(\App\Core\Database::all("SELECT id, nombre FROM countries WHERE id IN ($in)", $ids), 'nombre', 'id');
                    return e(implode(', ', array_map(static fn($i) => (string) ($names[$i] ?? '—'), $ids)));
                }],
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
        $todos = [];
        foreach (\App\Core\Database::all('SELECT id, nombre, publicado FROM countries ORDER BY FIELD(continente, "sudamerica", "europa"), orden, nombre') as $c) {
            $todos[(string) $c['id']] = $c['nombre'] . ((int) $c['publicado'] === 1 ? '' : ' (oculto)');
        }
        if (Posts::hasPivot()) {
            $pais = ['name' => 'paises', 'type' => 'multicheck', 'label' => 'Países a los que se refiere', 'options' => $todos,
                'default' => $row ? Posts::countryIds((int) $row['id']) : [],
                'help' => 'Marca uno o varios. El primero que marques es el principal (sale en las migas, la etiqueta y los datos para Google). El artículo aparece en la ficha de cada país marcado.'];
        } else {
            $sin = ['' => 'Sin país'] + $todos;
            $pais = ['name' => 'country_id', 'label' => 'País al que se refiere', 'type' => 'select', 'options' => $sin, 'null' => null,
                'help' => 'Si eliges uno, el artículo también sale en la ficha de ese país y se puede filtrar en el blog.'];
        }
        return [
            ['name' => 'titulo', 'label' => 'Título', 'required' => true, 'max' => 200, 'slugFrom' => true],
            ['name' => 'slug', 'label' => 'Dirección web (slug)', 'type' => 'slug', 'required' => true, 'max' => 120, 'help' => 'Aparece en la URL: /blog/mi-articulo.'],
            $pais,
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
        if (Posts::hasPivot()) {
            $this->paisIds = array_values(array_map('intval', (array) ($data['paises'] ?? [])));
            unset($data['paises']);
            $data['country_id'] = $this->paisIds[0] ?? null;
        } else {
            $data['country_id'] = ($data['country_id'] ?? '') === '' ? null : (int) $data['country_id'];
        }
        if (!empty($data['publicado']) && empty($data['publicado_en'])) {
            $data['publicado_en'] = ($row && $row['publicado_en']) ? $row['publicado_en'] : date('Y-m-d H:i:s');
        }
        return [];
    }

    protected function afterSave(int $id, array $data, ?array $row, \App\Core\Request $r): void
    {
        if (Posts::hasPivot()) {
            Posts::syncCountries($id, $this->paisIds);
        }
    }
}
