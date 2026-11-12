<?php
declare(strict_types=1);

namespace App\Admin;

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
                    . '<p class="hint">JPG, PNG o WebP, hasta 12 MB. Se reduce y optimiza automáticamente.' . ($has ? ' Si no eliges otra, se conserva la actual.' : '') . '</p>' . $grade . '</div></div>' . $e . '</div>';
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
