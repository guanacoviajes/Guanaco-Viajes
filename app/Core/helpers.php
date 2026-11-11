<?php
declare(strict_types=1);

use App\Core\Config;

/** Escapa texto para HTML. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** URL interna absoluta desde la raíz del sitio. */
function url(string $path = '/'): string
{
    return '/' . ltrim($path, '/');
}

/** Ruta de un recurso estático con versión (cambia al editar el archivo). */
function asset(string $path): string
{
    $path = '/' . ltrim($path, '/');
    $file = PUBLIC_PATH . $path;
    $v = is_file($file) ? (string) filemtime($file) : '0';
    return $path . '?v=' . $v;
}

/**
 * Etiqueta <img> con anchura y altura reales (evita saltos de diseño).
 * $attrs: class, pos (object-position), eager (bool), sizes.
 */
function img(string $src, string $alt = '', array $attrs = []): string
{
    static $dims = [];
    $file = PUBLIC_PATH . '/' . ltrim($src, '/');
    if (!isset($dims[$src])) {
        $info = is_file($file) ? @getimagesize($file) : false;
        $dims[$src] = $info ? [$info[0], $info[1]] : [null, null];
    }
    [$w, $h] = $dims[$src];
    $eager = !empty($attrs['eager']);
    $out = '<img src="' . e('/' . ltrim($src, '/')) . '" alt="' . e($alt) . '"';
    if ($w && $h) {
        $out .= ' width="' . $w . '" height="' . $h . '"';
    }
    if (!empty($attrs['class'])) {
        $out .= ' class="' . e($attrs['class']) . '"';
    }
    if (!empty($attrs['pos'])) {
        $out .= ' style="object-position:' . e($attrs['pos']) . '"';
    }
    if (!empty($attrs['sizes'])) {
        $out .= ' sizes="' . e($attrs['sizes']) . '"';
    }
    $out .= $eager ? ' fetchpriority="high" decoding="async"' : ' loading="lazy" decoding="async"';
    return $out . '>';
}

/** Valor de configuración. */
function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}
