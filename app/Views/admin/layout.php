<?php
/**
 * @var string $content  @var string $title  @var string $section  @var ?array $user  @var mixed $flash
 */
use App\Core\Csrf;
use App\Repositories\Requests;

$nav = [
    ['panel',        '/admin',                'Panel',               'M4 13h6V4H4v9zm0 7h6v-5H4v5zm8 0h8v-9h-8v9zm0-16v5h8V4h-8z'],
    ['solicitudes',  '/admin/solicitudes',    'Solicitudes',         'M4 5h16v11H9l-5 4V5z'],
    ['destinos',     '/admin/destinos',       'Destinos',            'M12 2a7 7 0 00-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 119.5 9 2.5 2.5 0 0112 11.5z'],
    ['salidas',      '/admin/salidas',        'Viajes en grupo',     'M16 11a3 3 0 100-6 3 3 0 000 6zm-8 0a3 3 0 100-6 3 3 0 000 6zm0 2c-2.3 0-7 1.2-7 3.5V19h14v-2.5C15 14.2 10.3 13 8 13zm8 0c-.3 0-.6 0-1 .1 1.2.8 2 1.9 2 3.4V19h6v-2.5c0-2.3-4.7-3.5-7-3.5z'],
    ['blog',         '/admin/blog',           'Blog',                'M4 4h16v4H4zm0 6h10v10H4zm12 0h4v2h-4zm0 4h4v2h-4zm0 4h4v2h-4z'],
    ['inicio',       '/admin/inicio',         'Inicio y carrusel',   'M3 11l9-8 9 8v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z'],
    ['paginas',      '/admin/paginas',        'Páginas y legales',   'M6 2h9l5 5v15H6zm8 1.5V8h4.5z'],
    ['formulario',   '/admin/formulario',     'Formulario',          'M4 4h16v4H4zm0 6h16v4H4zm0 6h10v4H4z'],
    ['ajustes',      '/admin/ajustes',        'Textos y ajustes',    'M19.4 13a7.6 7.6 0 000-2l2.1-1.6-2-3.5-2.5 1a7.6 7.6 0 00-1.7-1L15 3h-4l-.4 2.9a7.6 7.6 0 00-1.7 1l-2.5-1-2 3.5L6.6 11a7.6 7.6 0 000 2l-2.1 1.6 2 3.5 2.5-1a7.6 7.6 0 001.7 1L11 21h4l.4-2.9a7.6 7.6 0 001.7-1l2.5 1 2-3.5L19.4 13zM13 15.5A3.5 3.5 0 1113 8.5a3.5 3.5 0 010 7z'],
    ['imagenes',     '/admin/imagenes',       'Imágenes',            'M4 5h16v14H4zm2 12h12l-4-5-3 4-2-2.5z'],
    ['seguridad',    '/admin/seguridad',      'Seguridad',           'M12 2l8 3v6c0 5-3.4 9.4-8 11-4.6-1.6-8-6-8-11V5l8-3z'],
];
$news = $user ? Requests::countNew() : 0;
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> · Panel Guanaco Viajes</title>
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="<?= e(asset('assets/css/admin.css')) ?>">
<script src="<?= e(asset('assets/js/admin.js')) ?>" defer></script>
</head>
<body class="<?= $user ? 'adm' : 'adm adm-guest' ?>">
<a class="skip" href="#main">Saltar al contenido</a>
<?php if ($user): ?>
<header class="adm-top">
  <button type="button" class="adm-burger" aria-label="Abrir el menú" aria-expanded="false" aria-controls="adm-side"><i></i></button>
  <a class="adm-brand" href="/admin"><img src="/assets/img/logo/guanaco-animal.svg" width="30" height="32" alt=""><span>Panel</span></a>
</header>
<div class="adm-scrim" hidden></div>
<aside class="adm-side" id="adm-side">
  <a class="adm-brand big" href="/admin"><img src="/assets/img/logo/guanaco-animal.svg" width="34" height="36" alt=""><span><b>Guanaco</b> Viajes<small>Panel de administración</small></span></a>
  <nav aria-label="Panel">
    <?php foreach ($nav as [$key, $href, $label, $icon]): ?>
    <a href="<?= e($href) ?>"<?= $section === $key ? ' aria-current="page" class="on"' : '' ?>>
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?= e($icon) ?>"/></svg><?= e($label) ?>
      <?php if ($key === 'solicitudes' && $news > 0): ?><span class="badge" aria-label="<?= (int) $news ?> nuevas"><?= (int) $news ?></span><?php endif; ?>
    </a>
    <?php endforeach; ?>
  </nav>
  <div class="adm-foot">
    <a href="/" target="_blank" rel="noopener">Ver la web ↗</a>
    <span class="who"><?= e($user['email']) ?></span>
    <form method="post" action="/admin/salir"><?= Csrf::field() ?><button type="submit" class="link">Cerrar sesión</button></form>
  </div>
</aside>
<?php endif; ?>
<main id="main" class="adm-main" tabindex="-1">
  <?php if (is_array($flash)): ?>
  <p class="msg msg-<?= e($flash['type']) ?>" role="<?= $flash['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($flash['text']) ?></p>
  <?php endif; ?>
  <?= $content ?>
</main>
</body>
</html>
