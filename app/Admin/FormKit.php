<?php
declare(strict_types=1);

namespace App\Admin;

use App\Services\CountryContent;
use App\Services\ImageService;

/**
 * Dibuja y valida los formularios del panel a partir de una lista de campos.
 * Campo: ['name', 'label', 'type' => text|slug|textarea|tall|number|select|check|date|datetime|image|pos|url, …opciones]
 */
final class FormKit
{
    /** Posiciones de recorte de una foto (hacia dónde se centra cuando se recorta). */
    public const POS = [
        '50% 50%' => 'Centro',
        '50% 25%' => 'Arriba',
        '50% 75%' => 'Abajo',
        '25% 50%' => 'Izquierda',
        '75% 50%' => 'Derecha',
        '25% 25%' => 'Arriba a la izquierda',
        '75% 25%' => 'Arriba a la derecha',
        '25% 75%' => 'Abajo a la izquierda',
        '75% 75%' => 'Abajo a la derecha',
    ];


    /**
     * @param list<array<string,mixed>> $fields
     * @param array<string,mixed> $values
     * @param array<string,string> $errors
     */
    public static function render(array $fields, array $values, array $errors): string
    {
        $o = '';
        foreach ($fields as $f) {
            $o .= self::field($f, $values, $errors);
        }
        return $o;
    }

    private static function field(array $f, array $v, array $errors): string
    {
        $n = (string) $f['name'];
        $type = (string) ($f['type'] ?? 'text');
        $val = $v[$n] ?? ($f['default'] ?? '');
        $err = $errors[$n] ?? null;
        $id = 'f-' . $n;
        $label = '<span>' . e((string) $f['label']) . (!empty($f['required']) ? ' <abbr title="obligatorio" style="text-decoration:none;color:var(--acento)">*</abbr>' : '') . '</span>';
        $help = !empty($f['help']) ? '<small>' . e((string) $f['help']) . '</small>' : '';
        $cls = 'f' . ($err ? ' err' : '');
        $e = $err ? '<span class="e" role="alert">' . e($err) . '</span>' : '';
        $attrs = (!empty($f['required']) ? ' required' : '') . ($err ? ' aria-invalid="true"' : '');

        switch ($type) {
            case 'blocks':
                return self::blocks($f, $val, $err);
            case 'multicheck':
                return self::multicheck($f, $val, $err);
            case 'check':
                return '<label class="check"><input type="hidden" name="' . e($n) . '" value="0"><input type="checkbox" name="' . e($n) . '" value="1"' . ((string) $val === '1' ? ' checked' : '') . '> ' . e((string) $f['label']) . ($help ? " $help" : '') . '</label>';
            case 'textarea':
            case 'tall':
                $max = (int) ($f['max'] ?? 2000);
                $ph = isset($f['placeholder']) && $f['placeholder'] !== '' ? ' placeholder="' . e((string) $f['placeholder']) . '"' : '';
                return "<label class=\"$cls\" for=\"$id\">$label$help<textarea id=\"$id\" name=\"" . e($n) . '" maxlength="' . $max . '"' . ($type === 'tall' ? ' class="tall"' : '') . "$ph$attrs>" . e((string) $val) . "</textarea>$e</label>";
            case 'select':
                $opts = '';
                $options = $f['options'] ?? [];
                if (!empty($f['empty'])) {
                    $opts .= '<option value="">' . e((string) $f['empty']) . '</option>';
                }
                foreach ($options as $k => $l) {
                    $opts .= '<option value="' . e((string) $k) . '"' . ((string) $val === (string) $k ? ' selected' : '') . '>' . e((string) $l) . '</option>';
                }
                return "<label class=\"$cls\" for=\"$id\">$label$help<select id=\"$id\" name=\"" . e($n) . "\"$attrs>$opts</select>$e</label>";
            case 'pos':
                $opts = self::POS;
                if ($val !== '' && !isset($opts[(string) $val])) {
                    $opts = [(string) $val => 'Actual (' . $val . ')'] + $opts;
                }
                $o = '';
                foreach ($opts as $k => $l) {
                    $o .= '<option value="' . e((string) $k) . '"' . ((string) $val === (string) $k ? ' selected' : '') . '>' . e((string) $l) . '</option>';
                }
                return "<label class=\"$cls\" for=\"$id\">$label<small>" . e((string) ($f['help'] ?? 'Qué parte de la foto se ve cuando se recorta.')) . "</small><select id=\"$id\" name=\"" . e($n) . "\">$o</select>$e</label>";
            case 'image':
                $cur = (string) ($v[$n] ?? '');
                $has = $cur !== '';
                $src = $has ? '/' . ltrim($cur, '/') : '';
                $grade = '<label class="check" style="margin:8px 0 0"><input type="hidden" name="grade_' . e($n) . '" value="0"><input type="checkbox" name="grade_' . e($n) . '" value="1" checked> Aplicar el tratamiento de color de la marca</label>';
                return '<div class="' . $cls . '"><label for="' . $id . '">' . $label . '</label>' . $help . '<div class="imgbox"><img id="prev-' . e($n) . '" src="' . e($src) . '" alt=""' . ($has ? '' : ' hidden') . '>'
                    . '<div class="ctl"><input type="file" id="' . $id . '" name="' . e($n) . '" accept="image/jpeg,image/png,image/webp" data-preview="#prev-' . e($n) . '"' . (!empty($f['required']) && !$has ? ' required' : '') . '>'
                    . '<p class="hint">JPG, PNG o WebP, hasta 12 MB. <strong>Sube la foto original, sin reducirla antes</strong>: se guarda con calidad alta hasta 2560 px de ancho y se preparan versiones más ligeras para móviles. Una foto más pequeña nunca se amplía.' . ($has ? ' Si no eliges otra, se conserva la actual.' : '') . '</p>' . $grade . '</div></div>' . $e . '</div>';
            case 'number':
                $min = isset($f['min']) ? ' min="' . (int) $f['min'] . '"' : '';
                $max = isset($f['max']) ? ' max="' . (int) $f['max'] . '"' : '';
                $ph = isset($f['placeholder']) && $f['placeholder'] !== '' ? ' placeholder="' . e((string) $f['placeholder']) . '"' : '';
                return "<label class=\"$cls\" for=\"$id\">$label$help<input id=\"$id\" type=\"number\" name=\"" . e($n) . '" value="' . e((string) $val) . "\"$min$max$ph$attrs>$e</label>";
            case 'date':
                return "<label class=\"$cls\" for=\"$id\">$label$help<input id=\"$id\" type=\"date\" name=\"" . e($n) . '" value="' . e((string) $val) . "\"$attrs>$e</label>";
            case 'datetime':
                $dv = $val ? date('Y-m-d\TH:i', strtotime((string) $val)) : '';
                return "<label class=\"$cls\" for=\"$id\">$label$help<input id=\"$id\" type=\"datetime-local\" name=\"" . e($n) . '" value="' . e($dv) . "\"$attrs>$e</label>";
            case 'url':
                return "<label class=\"$cls\" for=\"$id\">$label$help<input id=\"$id\" type=\"text\" inputmode=\"url\" name=\"" . e($n) . '" value="' . e((string) $val) . '" maxlength="255"' . (isset($f['placeholder']) ? ' placeholder="' . e((string) $f['placeholder']) . '"' : '') . "$attrs>$e</label>";
            default: // text, slug
                $max = (int) ($f['max'] ?? 200);
                $extra = '';
                if ($type === 'slug') {
                    $extra = ' data-slug-to';
                }
                if (!empty($f['slugFrom'])) {
                    $extra .= ' data-slug-from';
                }
                $ph = isset($f['placeholder']) ? ' placeholder="' . e((string) $f['placeholder']) . '"' : '';
                return "<label class=\"$cls\" for=\"$id\">$label$help<input id=\"$id\" type=\"text\" name=\"" . e($n) . '" value="' . e((string) $val) . '" maxlength="' . $max . "\"$extra$ph$attrs>$e</label>";
        }
    }

    /** Bloques repetibles de casillas fijas (zonas, cuándo ir, preguntas). Sin JavaScript se ven todas; con él, una a una. */
    private static function blocks(array $f, mixed $val, ?string $err): string
    {
        $n = (string) $f['name'];
        $list = (string) $f['list'];
        $spec = CountryContent::spec($list);
        $blocks = is_array($val) ? array_values($val) : CountryContent::decode($list, $val);
        $textareas = ['descripcion' => 2, 'nota' => 2, 'respuesta' => 5];
        $o = '<fieldset class="f blocks' . ($err ? ' err' : '') . '" data-blocks data-max="' . (int) $spec['max'] . '" data-noun="' . e((string) ($f['noun'] ?? $spec['singular'])) . '">';
        $o .= '<legend><span>' . e((string) $f['label']) . '</span></legend>';
        if (!empty($f['help'])) {
            $o .= '<small>' . e((string) $f['help']) . '</small>';
        }
        for ($i = 0; $i < $spec['max']; $i++) {
            $b = is_array($blocks[$i] ?? null) ? $blocks[$i] : [];
            $filled = implode('', array_map(static fn($v) => is_string($v) ? $v : '', $b)) !== '';
            $o .= '<div class="blk-slot" data-slot data-filled="' . ($filled ? '1' : '0') . '"><div class="blk-title">' . e(ucfirst((string) ($f['noun'] ?? $spec['singular']))) . ' ' . ($i + 1) . '</div><div class="blk-cols">';
            foreach ($spec['cols'] as $k => [$label, $max, $req]) {
                $id = 'f-' . $n . '-' . $i . '-' . $k;
                if ($k === 'foto') {
                    $cur = is_string($b['foto'] ?? null) && preg_match(CountryContent::FOTO_PATTERN, $b['foto']) ? $b['foto'] : '';
                    $nm = e($n) . '[' . $i . ']';
                    $o .= '<div class="f2 f2-foto"><label for="' . $id . '"><span>Foto de la zona <em>(opcional)</em></span></label><div class="imgbox"><img id="prev-' . $id . '" src="' . e($cur === '' ? '' : '/' . $cur) . '" alt=""' . ($cur === '' ? ' hidden' : '') . '>'
                        . '<div class="ctl"><input type="file" id="' . $id . '" name="' . $nm . '[foto]" accept="image/jpeg,image/png,image/webp" data-preview="#prev-' . $id . '">'
                        . '<input type="hidden" name="' . $nm . '[foto_actual]" value="' . e($cur) . '">'
                        . ($cur !== '' ? '<label class="check" style="margin:6px 0 0"><input type="checkbox" name="' . $nm . '[foto_quitar]" value="1"> Quitar la foto actual</label>' : '')
                        . '<p class="hint">Mejor horizontal: se muestra en formato 3:2. JPG, PNG o WebP, hasta 12 MB. Sube la original, sin reducirla.</p></div></div></div>';
                    continue;
                }
                $name = e($n) . '[' . $i . '][' . e($k) . ']';
                $v = is_string($b[$k] ?? null) ? $b[$k] : '';
                $count = ($k === 'descripcion') ? ' data-count="' . $max . '"' : '';
                $o .= '<label class="f2 f2-' . e($k) . '" for="' . $id . '"><span>' . e($label) . ($req ? '' : ' <em>(opcional)</em>') . '</span>';
                if (isset($textareas[$k])) {
                    $o .= '<textarea id="' . $id . '" name="' . $name . '" rows="' . $textareas[$k] . '" maxlength="' . $max . '"' . $count . '>' . e($v) . '</textarea>';
                } else {
                    $o .= '<input id="' . $id . '" type="text" name="' . $name . '" value="' . e($v) . '" maxlength="' . $max . '"' . $count . '>';
                }
                $o .= '</label>';
            }
            $o .= '</div></div>';
        }
        if (isset($spec['cols']['foto'])) {
            $o .= '<label class="check" style="margin:12px 0 0"><input type="hidden" name="grade_' . e($n) . '" value="0"><input type="checkbox" name="grade_' . e($n) . '" value="1" checked> Aplicar el tratamiento de color de la marca a las fotos nuevas</label>';
        }
        $o .= '<button type="button" class="btn-add" data-add hidden>Añadir otra ' . e((string) ($f['noun'] ?? $spec['singular'])) . '</button>';
        if ($err) {
            $o .= '<span class="e" role="alert">' . e($err) . '</span>';
        }
        return $o . '</fieldset>';
    }

    /** Lista de casillas para elegir varios elementos; el primero que se marca es el principal. */
    private static function multicheck(array $f, mixed $val, ?string $err): string
    {
        $n = (string) $f['name'];
        $sel = array_values(array_unique(array_map('strval', is_array($val) ? $val : [])));
        $o = '<fieldset class="f multicheck' . ($err ? ' err' : '') . '" data-multicheck data-name="' . e($n) . '">';
        $o .= '<legend><span>' . e((string) $f['label']) . '</span></legend>';
        if (!empty($f['help'])) {
            $o .= '<small>' . e((string) $f['help']) . '</small>';
        }
        $o .= '<div class="mc-grid">';
        foreach (($f['options'] ?? []) as $k => $label) {
            $k = (string) $k;
            $on = in_array($k, $sel, true);
            $o .= '<label class="mc-item"><input type="checkbox" name="' . e($n) . '[]" value="' . e($k) . '"' . ($on ? ' checked' : '') . '> <span>' . e((string) $label) . '</span><b class="mc-main" hidden>Principal</b></label>';
        }
        $o .= '</div><div data-order>';
        foreach ($sel as $k) {
            $o .= '<input type="hidden" name="' . e($n) . '_orden[]" value="' . e($k) . '">';
        }
        $o .= '</div>';
        if ($err) {
            $o .= '<span class="e" role="alert">' . e($err) . '</span>';
        }
        return $o . '</fieldset>';
    }

    /**
     * Archivo subido en el campo «campo» del bloque número $i (PHP lo guarda como $_FILES['lista']['name'][$i]['campo']).
     * @return array<string,mixed>|null null si en ese bloque no se eligió ningún archivo
     */
    private static function slotFile(mixed $files, int $i, string $campo): ?array
    {
        if (!is_array($files)) {
            return null;
        }
        $pick = static fn(string $attr) => $files[$attr][$i][$campo] ?? null;
        $error = $pick('error');
        if ($error === null || $error === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        return ['name' => $pick('name'), 'type' => $pick('type'), 'tmp_name' => $pick('tmp_name'), 'error' => $error, 'size' => $pick('size')];
    }

    /**
     * Valida y limpia lo enviado.
     * @param list<array<string,mixed>> $fields
     * @param array<string,mixed> $post  $_POST
     * @param array<string,mixed> $files $_FILES
     * @param array<string,mixed> $current valores actuales (al editar), para conservar las imágenes
     * @return array{0: array<string,mixed>, 1: array<string,string>} datos y errores
     */
    public static function collect(array $fields, array $post, array $files, array $current = []): array
    {
        $data = [];
        $err = [];
        foreach ($fields as $f) {
            $n = (string) $f['name'];
            $type = (string) ($f['type'] ?? 'text');
            $label = (string) $f['label'];
            $req = !empty($f['required']);
            $raw = $post[$n] ?? '';
            $s = is_string($raw) ? trim($raw) : '';

            switch ($type) {
                case 'blocks':
                    $rawBlocks = is_array($raw) ? $raw : [];
                    $withPhoto = isset(CountryContent::spec((string) $f['list'])['cols']['foto']);
                    if ($withPhoto) {
                        $fotoErr = null;
                        foreach ($rawBlocks as $i => $blk) {
                            if (!is_array($blk)) {
                                continue;
                            }
                            $text = '';
                            foreach ($blk as $kk => $vv) {
                                if (!in_array($kk, ['foto', 'foto_actual', 'foto_quitar'], true) && is_string($vv)) {
                                    $text .= trim($vv);
                                }
                            }
                            $up = self::slotFile($files[$n] ?? null, (int) $i, 'foto');
                            if ($text === '' && $up === null) {
                                $rawBlocks[$i]['foto'] = '';   // casilla vacía: no se guarda ni su foto
                                continue;
                            }
                            if ($up !== null) {
                                $r = ImageService::store($up, ($post['grade_' . $n] ?? '1') === '1');
                                if ($r['error'] !== null) {
                                    $fotoErr ??= 'Foto de la zona ' . ((int) $i + 1) . ': ' . $r['error'];
                                    $rawBlocks[$i]['foto'] = '';
                                } else {
                                    $rawBlocks[$i]['foto'] = $r['path'];
                                }
                            } elseif (($blk['foto_quitar'] ?? '') === '1') {
                                $rawBlocks[$i]['foto'] = '';
                            } else {
                                $rawBlocks[$i]['foto'] = is_string($blk['foto_actual'] ?? null) ? $blk['foto_actual'] : '';
                            }
                        }
                        if ($fotoErr !== null) {
                            $err[$n] = $fotoErr;
                        }
                    }
                    [$list, $msg] = CountryContent::clean((string) $f['list'], $rawBlocks);
                    if ($msg !== null && !isset($err[$n])) {
                        $err[$n] = $msg;
                    }
                    $data[$n] = CountryContent::encode($list);
                    break;
                case 'multicheck':
                    $valid = array_map('strval', array_keys($f['options'] ?? []));
                    $checked = array_values(array_unique(array_filter(
                        is_array($raw) ? array_map('strval', $raw) : [],
                        static fn($v) => in_array($v, $valid, true)
                    )));
                    // Orden en que se marcaron (lo envía el JavaScript); sin JavaScript, el de la lista.
                    $order = is_array($post[$n . '_orden'] ?? null) ? array_map('strval', $post[$n . '_orden']) : [];
                    $first = array_values(array_unique(array_filter($order, static fn($v) => in_array($v, $checked, true))));
                    $ids = array_merge($first, array_values(array_diff($checked, $first)));
                    if ($req && $ids === []) {
                        $err[$n] = "Elige al menos una opción en «{$label}».";
                    }
                    $data[$n] = array_map('intval', $ids);
                    break;
                case 'check':
                    $data[$n] = ($raw === '1') ? 1 : 0;
                    break;
                case 'image':
                    $file = $files[$n] ?? null;
                    if (is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                        $r = ImageService::store($file, ($post['grade_' . $n] ?? '1') === '1');
                        if ($r['error'] !== null) {
                            $err[$n] = $r['error'];
                        } else {
                            $data[$n] = $r['path'];
                        }
                    } elseif ($req && ($current[$n] ?? '') === '') {
                        $err[$n] = 'Elige una imagen.';
                    }
                    break;
                case 'number':
                    if ($s === '') {
                        if ($req) {
                            $err[$n] = "Indica «{$label}».";
                        }
                        $data[$n] = $f['default'] ?? 0;
                        break;
                    }
                    if (!preg_match('/^-?\d{1,6}$/', $s)) {
                        $err[$n] = "«{$label}» debe ser un número entero.";
                        break;
                    }
                    $i = (int) $s;
                    if (isset($f['min']) && $i < (int) $f['min'] || isset($f['max']) && $i > (int) $f['max']) {
                        $err[$n] = "«{$label}» debe estar entre " . ($f['min'] ?? '…') . ' y ' . ($f['max'] ?? '…') . '.';
                        break;
                    }
                    $data[$n] = $i;
                    break;
                case 'select':
                    $options = $f['options'] ?? [];
                    if ($s === '' && !$req) {
                        $data[$n] = $f['null'] ?? '';
                    } elseif (isset($options[$s])) {
                        $data[$n] = $s;
                    } else {
                        $err[$n] = "Elige una opción válida en «{$label}».";
                    }
                    break;
                case 'pos':
                    $data[$n] = preg_match('/^\d{1,3}% \d{1,3}%$/', $s) ? $s : '50% 50%';
                    break;
                case 'date':
                    if ($s === '') {
                        if ($req) {
                            $err[$n] = "Indica «{$label}».";
                        }
                        $data[$n] = null;
                    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $s) || !checkdate((int) substr($s, 5, 2), (int) substr($s, 8, 2), (int) substr($s, 0, 4))) {
                        $err[$n] = "«{$label}» no es una fecha válida.";
                    } else {
                        $data[$n] = $s;
                    }
                    break;
                case 'datetime':
                    if ($s === '') {
                        if ($req) {
                            $err[$n] = "Indica «{$label}».";
                        }
                        $data[$n] = null;
                    } elseif (($t = strtotime($s)) === false || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/', $s)) {
                        $err[$n] = "«{$label}» no es una fecha y hora válidas.";
                    } else {
                        $data[$n] = date('Y-m-d H:i:00', $t);
                    }
                    break;
                case 'url':
                    if ($s === '') {
                        if ($req) {
                            $err[$n] = "Indica «{$label}».";
                        }
                        $data[$n] = '';
                    } elseif (!preg_match('#^(https?://[^\s<>"\']+|/(?!/)[^\s<>"\']*|mailto:[^\s<>"\']+)$#i', $s) || mb_strlen($s) > 255) {
                        $err[$n] = "«{$label}» debe ser una dirección completa (https://…) o una ruta de la web (/contacto).";
                    } else {
                        $data[$n] = $s;
                    }
                    break;
                case 'slug':
                    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $s) || mb_strlen($s) > (int) ($f['max'] ?? 120)) {
                        $err[$n] = 'Usa solo letras minúsculas sin acentos, números y guiones (por ejemplo: costa-rica).';
                    }
                    $data[$n] = $s;
                    break;
                default: // text, textarea, tall
                    $multi = in_array($type, ['textarea', 'tall'], true);
                    $s = $multi
                        ? preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/u', '', str_replace(["\r\n", "\r"], "\n", $s)) ?? ''
                        : trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $s) ?? '');
                    $max = (int) ($f['max'] ?? ($multi ? 2000 : 200));
                    if ($req && $s === '') {
                        $err[$n] = "Indica «{$label}».";
                    } elseif (mb_strlen($s) > $max) {
                        $err[$n] = "«{$label}» es demasiado largo (máximo $max caracteres).";
                    }
                    $data[$n] = $s;
            }
        }
        return [$data, $err];
    }
}
