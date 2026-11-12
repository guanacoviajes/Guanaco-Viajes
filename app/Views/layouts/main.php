<?php
/** @var string $content  @var array<string,mixed> $meta */
use App\Repositories\Settings;

$base      = rtrim((string) config('app.url', ''), '/');
$path      = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$canonical = $meta['canonical'] ?? ($base . ($path === '/' ? '/' : rtrim($path, '/')));
$ogImage   = \App\Core\Seo::abs((string) $meta['og_image']);
$ogType    = (string) ($meta['og_type'] ?? 'website');
$graph     = array_merge(\App\Core\Seo::siteNodes(), (array) ($meta['schema'] ?? []));
$gv = \App\Repositories\Settings::get('seo.google_verification');
$bv = \App\Repositories\Settings::get('seo.bing_verification');
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
<meta property="og:type" content="<?= e($ogType) ?>">
<meta property="og:locale" content="es_ES">
<meta property="og:site_name" content="Guanaco Viajes">
<meta property="og:title" content="<?= e($meta['title']) ?>">
<?php if ($desc !== ''): ?><meta property="og:description" content="<?= e($desc) ?>">
<?php endif; ?>
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<?php if (!empty($meta['og_alt'])): ?><meta property="og:image:alt" content="<?= e($meta['og_alt']) ?>">
<?php endif; ?>
<?php if ($ogType === 'article' && !empty($meta['article'])): $ar = $meta['article']; ?>
<meta property="article:published_time" content="<?= e($ar['published']) ?>">
<meta property="article:modified_time" content="<?= e($ar['modified']) ?>">
<meta property="article:author" content="Daniel Conti">
<?php if (!empty($ar['section'])): ?><meta property="article:section" content="<?= e($ar['section']) ?>">
<?php endif; endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($meta['title']) ?>">
<?php if ($desc !== ''): ?><meta name="twitter:description" content="<?= e($desc) ?>">
<?php endif; ?>
<meta name="twitter:image" content="<?= e($ogImage) ?>">
<?php if ($gv !== ''): ?><meta name="google-site-verification" content="<?= e($gv) ?>">
<?php endif; ?>
<?php if ($bv !== ''): ?><meta name="msvalidate.01" content="<?= e($bv) ?>">
<?php endif; ?>
<link rel="alternate" type="application/rss+xml" title="Guanaco Viajes · Blog" href="<?= e(\App\Core\Seo::feedUrl()) ?>">
<script type="application/ld+json"><?= \App\Core\Seo::json(['@context' => 'https://schema.org', '@graph' => $graph]) ?></script>
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
