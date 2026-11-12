<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Http;
use App\Core\Request;

/**
 * Pantallas de gestión (listar, crear, editar, borrar) comunes a destinos, salidas, blog, carrusel…
 * Cada controlador concreto solo declara sus campos y columnas.
 */
abstract class CrudController extends AdminController
{
    /**
     * @return array{table:string, section:string, path:string, singular:string, plural:string, create:?string, delete:bool,
     *               order:string, unique?:list<string>, columns:list<array{0:string,1:callable}>, intro?:string, empty?:string}
     */
    abstract protected function cfg(): array;

    /** @return list<array<string,mixed>> */
    abstract protected function fields(?array $row): array;

    /** Validaciones propias; devuelve errores por campo. */
    protected function validate(array &$data, ?array $row): array
    {
        return [];
    }

    protected function afterSave(int $id, array $data, ?array $row, Request $r): void
    {
    }

    /** HTML adicional bajo el formulario al editar (por ejemplo, la galería de un país). */
    protected function extra(?array $row): string
    {
        return '';
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function find(string $id): ?array
    {
        $c = $this->cfg();
        $n = $this->intParam($id);
        return $n > 0 ? Database::one('SELECT * FROM `' . $c['table'] . '` WHERE id = ?', [$n]) : null;
    }

    public function index(Request $r): void
    {
        $c = $this->cfg();
        $rows = Database::all('SELECT * FROM `' . $c['table'] . '` ORDER BY ' . $c['order']);
        $this->render('crud/list', ['c' => $c, 'rows' => $rows, 'csrf' => Csrf::field()], $c['plural'], $c['section']);
    }

    public function create(Request $r): void
    {
        $c = $this->cfg();
        if (empty($c['create'])) {
            Http::notFound();
        }
        $old = Flash::pull('old_form', []);
        $this->form($c, null, $old ?: $this->defaults(), Flash::pull('form_errors', []));
    }

    public function edit(Request $r, string $id): void
    {
        $row = $this->find($id) ?? Http::notFound();
        $old = Flash::pull('old_form', []);
        $this->form($this->cfg(), $row, $old ? array_merge($row, $old) : $row, Flash::pull('form_errors', []));
    }

    private function form(array $c, ?array $row, array $values, array $errors): void
    {
        $title = $row ? ucfirst($c['singular']) . ': ' . ($row['titulo'] ?? $row['nombre'] ?? ('n.º ' . $row['id'])) : ($c['create'] ?? 'Nuevo');
        $this->render('crud/form', [
            'c' => $c, 'row' => $row, 'html' => FormKit::render($this->fields($row), $values, $errors),
            'extra' => $row ? $this->extra($row) : '', 'csrf' => Csrf::field(),
        ], $title, $c['section']);
    }

    public function store(Request $r): void
    {
        if (empty($this->cfg()['create'])) {
            Http::notFound();
        }
        $this->save($r, null);
    }

    public function update(Request $r, string $id): void
    {
        $this->save($r, $this->find($id) ?? Http::notFound());
    }

    private function save(Request $r, ?array $row): void
    {
        $c = $this->cfg();
        $back = $c['path'] . ($row ? '/' . $row['id'] : '/nuevo');
        $this->csrf($r, $back);
        [$data, $err] = FormKit::collect($this->fields($row), $_POST, $_FILES, $row ?? []);
        foreach ($c['unique'] ?? [] as $col) {
            if (isset($data[$col]) && !isset($err[$col])) {
                $sql = 'SELECT COUNT(*) FROM `' . $c['table'] . '` WHERE `' . $col . '` = ?' . ($row ? ' AND id <> ?' : '');
                if ((int) Database::value($sql, $row ? [$data[$col], $row['id']] : [$data[$col]]) > 0) {
                    $err[$col] = 'Ya existe otro con ese valor. Elige uno distinto.';
                }
            }
        }
        $err += $this->validate($data, $row);
        if ($err) {
            $keep = $_POST;
            foreach (array_keys($_FILES) as $k) {
                unset($keep[$k]);
            }
            Flash::put('old_form', array_map(static fn($v) => is_string($v) ? $v : '', $keep));
            Flash::put('form_errors', $err);
            $this->flash('error', 'Revisa los campos marcados en rojo.');
            $this->redirect($back);
        }
        if ($row) {
            $set = implode(', ', array_map(static fn($k) => "`$k` = ?", array_keys($data)));
            Database::run('UPDATE `' . $c['table'] . "` SET $set WHERE id = ?", [...array_values($data), $row['id']]);
            $id = (int) $row['id'];
        } else {
            $cols = implode(', ', array_map(static fn($k) => "`$k`", array_keys($data)));
            $ph = implode(', ', array_fill(0, count($data), '?'));
            Database::run('INSERT INTO `' . $c['table'] . "` ($cols) VALUES ($ph)", array_values($data));
            $id = (int) Database::lastId();
        }
        $this->afterSave($id, $data, $row, $r);
        $this->flash('ok', $row ? 'Cambios guardados.' : 'Creado correctamente.');
        $this->redirect($c['path'] . '/' . $id);
    }

    public function destroy(Request $r, string $id): void
    {
        $c = $this->cfg();
        $row = $this->find($id) ?? Http::notFound();
        $this->csrf($r, $c['path']);
        if (empty($c['delete'])) {
            Http::notFound();
        }
        Database::run('DELETE FROM `' . $c['table'] . '` WHERE id = ?', [$row['id']]);
        $this->flash('ok', 'Eliminado.');
        $this->redirect($c['path']);
    }
}
