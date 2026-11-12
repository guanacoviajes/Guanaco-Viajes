<?php
/** @var string $content  @var array<string,mixed> $meta */
use App\Repositories\Settings;

$base      = rtrim((string) config('app.url', ''), '/');
$path      = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$canonical = $meta['canonical'] ?? ($base . ($path === '/' ? '/' : rtrim($path, '/')));
$ogImage   = $base . '/' . ltrim((string) $meta['og_image'], '/');
$desc      = (string) $meta['description'];
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($meta['title']) ?></title>
<?php if ($desc !== ''): ?><meta name="description" content="<?= e($desc) ?>">
<?php endif; ?>
<meta name="robots" content="<?= e($meta['robots']) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta property="og:type" content="website">
<meta property="og:locale" content="es_ES">
<meta property="og:site_name" content="Guanaco Viajes">
<meta property="og:title" content="<?= e($meta['title']) ?>">
<?php if ($desc !== ''): ?><meta property="og:description" content="<?= e($desc) ?>">
<?php endif; ?>
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#1F2D28">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="preload" href="/assets/fonts/newsreader-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/assets/fonts/manrope-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
<script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
<?php foreach ((array) ($meta['scripts'] ?? []) as $js): ?><script src="<?= e(asset($js)) ?>" defer></script>
<?php endforeach; ?>
</head>
<body class="<?= $meta['overlay'] ? 'has-overlay' : 'has-solid' ?>">
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<?= \App\Core\View::partial('partials/header', ['overlay' => (bool) $meta['overlay']]) ?>
<main id="contenido">
<?= $content ?>
</main>
<?php if ($meta['franja']): ?>
<?= \App\Core\View::partial('partials/franja') ?>
<?php endif; ?>
<?= \App\Core\View::partial('partials/footer') ?>
<?php if ($meta['bubble']): ?>
<?= \App\Core\View::partial('partials/bubble') ?>
<?php endif; ?>
</body>
</html>
