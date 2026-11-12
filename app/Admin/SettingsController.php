<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Http;
use App\Core\Request;
use App\Repositories\Settings;

final class SettingsController extends AdminController
{
    public function index(Request $r): void
    {
        $this->render('settings_index', ['groups' => SettingsRegistry::groups()], 'Textos y ajustes', 'ajustes');
    }

    /** @return list<array<string,mixed>> campos del formulario para un grupo del catálogo */
    private function fieldsOf(array $group): array
    {
        $out = [];
        foreach ($group['items'] as $key => [$label, $type, $def, $help, $req]) {
            $f = ['name' => str_replace('.', '__', $key), 'label' => $label, 'type' => $type, 'help' => $help, 'required' => $req, 'placeholder' => $def];
            if ($type === 'textarea') {
                $f['max'] = 800;
            }
            if ($type === 'number') {
                $f['min'] = 2;
                $f['max'] = 15;
            }
            if ($type === 'image') {
                $f['help'] = $help;
            }
            $out[] = $f;
        }
        return $out;
    }

    public function show(Request $r, string $grupo): void
    {
        $groups = SettingsRegistry::groups();
        $g = $groups[$grupo] ?? Http::notFound();
        $values = [];
        foreach ($g['items'] as $key => $_) {
            $values[str_replace('.', '__', $key)] = Settings::get($key);
        }
        $old = Flash::pull('old_form', []);
        $values = $old ? array_merge($values, $old) : $values;
        $this->render('settings_form', [
            'g' => $g, 'slug' => $grupo,
            'html' => FormKit::render($this->fieldsOf($g), $values, Flash::pull('form_errors', [])),
            'csrf' => Csrf::field(),
        ], $g['title'], 'ajustes');
    }

    public function save(Request $r, string $grupo): void
    {
        $groups = SettingsRegistry::groups();
        $g = $groups[$grupo] ?? Http::notFound();
        $back = '/admin/ajustes/' . $grupo;
        $this->csrf($r, $back);
        $fields = $this->fieldsOf($g);
        $current = [];
        foreach ($g['items'] as $key => $_) {
            $current[str_replace('.', '__', $key)] = Settings::get($key);
        }
        [$data, $err] = FormKit::collect($fields, $_POST, $_FILES, $current);
        // reglas propias de ajustes
        foreach ($g['items'] as $key => [$label, $type]) {
            $n = str_replace('.', '__', $key);
            if ($key === 'site.whatsapp' && isset($data[$n]) && !preg_match('/^\d{9,15}$/', (string) $data[$n])) {
                $err[$n] = 'Escribe solo números, con el prefijo del país y sin el +. Ejemplo: 34600538885.';
            }
            if ($key === 'site.email' && isset($data[$n]) && !filter_var($data[$n], FILTER_VALIDATE_EMAIL)) {
                $err[$n] = 'Escribe un correo válido.';
            }
            if ($key === 'site.email_reservas' && ($data[$n] ?? '') !== '' && !filter_var($data[$n], FILTER_VALIDATE_EMAIL)) {
                $err[$n] = 'Escribe un correo válido.';
            }
            if ($type === 'url' && str_starts_with($key, 'social.') && ($data[$n] ?? '') !== '' && !preg_match('#^https://#i', (string) $data[$n])) {
                $err[$n] = 'La dirección de una red social debe empezar por https://';
            }
        }
        if ($err) {
            $keep = [];
            foreach ($_POST as $k => $v) {
                if (is_string($v) && $k !== '_csrf') {
                    $keep[$k] = $v;
                }
            }
            Flash::put('old_form', $keep);
            Flash::put('form_errors', $err);
            $this->flash('error', 'Revisa los campos marcados en rojo. No se ha guardado nada.');
            $this->redirect($back);
        }
        foreach ($g['items'] as $key => [$label, $type]) {
            $n = str_replace('.', '__', $key);
            if (!array_key_exists($n, $data)) {
                continue; // imagen sin cambios
            }
            Database::run('INSERT INTO settings (clave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)', [$key, (string) $data[$n]]);
        }
        $this->flash('ok', 'Cambios guardados.');
        $this->redirect($back);
    }
}
