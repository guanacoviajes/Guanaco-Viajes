<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Request;

/** Textos largos editables: Quiénes somos y las páginas legales. */
final class PagesAdminController extends CrudController
{
    private const NOMBRES = [
        'quienes-somos' => 'Quiénes somos', 'aviso-legal' => 'Aviso legal', 'politica-de-privacidad' => 'Política de privacidad',
        'politica-de-cookies' => 'Política de cookies', 'condiciones-generales' => 'Condiciones generales',
    ];

    protected function cfg(): array
    {
        return [
            'table' => 'pages', 'section' => 'paginas', 'path' => '/admin/paginas', 'singular' => 'página', 'plural' => 'Páginas y textos legales',
            'create' => null, 'delete' => false, 'order' => "FIELD(slug,'quienes-somos','aviso-legal','politica-de-privacidad','politica-de-cookies','condiciones-generales')",
            'intro' => 'El texto largo de «Quiénes somos» y los cuatro textos legales.',
            'help' => '<b>Formato:</b> deja una línea en blanco entre párrafos. <code>## Título</code> crea un apartado (los textos legales muestran un índice con ellos), <code>- texto</code> una lista, <code>*cursiva*</code>, <code>**negrita**</code> y <code>[texto](https://enlace)</code> un enlace.',
            'columns' => [
                ['Página', static fn($r) => e(self::NOMBRES[$r['slug']] ?? $r['titulo'])],
                ['Última edición', static fn($r) => e(date('d/m/Y H:i', strtotime($r['updated_at'])))],
            ],
            'viewUrl' => static fn(array $r): string => '/' . $r['slug'],
        ];
    }

    protected function fields(?array $row): array
    {
        $legal = $row && $row['slug'] !== 'quienes-somos';
        return [
            ['name' => 'titulo', 'label' => 'Título de la página', 'required' => true, 'max' => 200],
            ['name' => 'cuerpo', 'label' => 'Texto', 'type' => 'tall', 'required' => true, 'max' => 120000,
                'help' => $legal ? 'Texto basado en el de NADIDU SL. Debe revisarlo Nadidú antes de publicar.' : ''],
            ['name' => 'seo_title', 'label' => 'Título para buscadores (opcional)', 'max' => 160],
            ['name' => 'seo_desc', 'label' => 'Descripción para buscadores (opcional)', 'type' => 'textarea', 'max' => 255],
        ];
    }

    /** La lista de páginas lleva además el control «textos legales revisados». */
    public function index(Request $r): void
    {
        $c = $this->cfg();
        $rows = Database::all('SELECT * FROM `pages` ORDER BY ' . $c['order']);
        $rev = \App\Repositories\Settings::get('legal.revisado') === '1';
        $this->render('pages_admin', ['c' => $c, 'rows' => $rows, 'revisado' => $rev, 'csrf' => Csrf::field()], $c['plural'], 'paginas');
    }

    public function revision(Request $r): void
    {
        $this->csrf($r, '/admin/paginas');
        $val = ($r->post('revisado') === '1') ? '1' : '0';
        Database::run('INSERT INTO settings (clave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)', ['legal.revisado', $val]);
        $this->flash('ok', $val === '1' ? 'Marcado: los textos legales están revisados por Nadidú.' : 'Marcado: los textos legales aún no están revisados.');
        $this->redirect('/admin/paginas');
    }
}
