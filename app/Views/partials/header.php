<?php
/** @var bool $overlay */
use App\Repositories\Countries;
use App\Repositories\Settings;

$paises = Countries::byContinent();
$cta    = Settings::get('site.cta_url', '/presupuesto');
$path   = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/') ?: '/';
$is = static fn(string $prefix): bool => $path === $prefix || str_starts_with($path, $prefix . '/');
$cls = 'site-header ' . ($overlay ? 'is-overlay at-top' : 'is-solid');
?>
<header class="<?= $cls ?>" id="site-header" data-tone="oscuro">
  <div class="hdr">
    <a class="brand" href="/" aria-label="Guanaco Viajes, inicio">
      <img class="logo logo-light" src="/assets/img/logo/guanaco-completo-claro.svg" width="1000" height="470" alt="">
      <img class="logo logo-dark" src="/assets/img/logo/guanaco-completo-oscuro.svg" width="1000" height="470" alt="">
    </a>

    <nav class="mainnav" aria-label="Principal">
      <ul>
        <li class="has-sub">
          <button type="button" class="sub-toggle" aria-expanded="false" aria-controls="sub-tipos">Tipos de viaje</button>
          <div class="sub" id="sub-tipos">
            <ul>
              <li><a href="/viajes-a-medida">Viajes a medida</a></li>
              <li><a href="/viajes-en-grupo">Viajes en grupo</a></li>
            </ul>
          </div>
        </li>
        <li class="has-sub">
          <button type="button" class="sub-toggle" aria-expanded="false" aria-controls="sub-destinos">Destinos</button>
          <div class="sub sub-wide" id="sub-destinos">
            <?php foreach (['sudamerica' => 'Sudamérica', 'europa' => 'Europa'] as $key => $label): ?>
            <div class="sub-col">
              <a class="sub-head" href="/destinos/<?= e($key) ?>"><?= e($label) ?></a>
              <ul>
                <?php foreach ($paises[$key] as $p): ?>
                <li><a href="/destinos/<?= e($key) ?>/<?= e($p['slug']) ?>"><?= e($p['nombre']) ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <?php endforeach; ?>
          </div>
        </li>
        <li><a href="/quienes-somos"<?= $is('/quienes-somos') ? ' aria-current="page"' : '' ?>>Quiénes somos</a></li>
        <li><a href="/blog"<?= $is('/blog') ? ' aria-current="page"' : '' ?>>Blog</a></li>
        <li><a href="/contacto"<?= $is('/contacto') ? ' aria-current="page"' : '' ?>>Contacto</a></li>
        <li><a class="btn btn-cta" href="<?= e($cta) ?>">Pide tu presupuesto</a></li>
      </ul>
    </nav>

    <button type="button" class="burger" aria-label="Abrir menú" aria-expanded="false" aria-controls="mpanel"><i></i></button>
  </div>
</header>

<div class="mpanel" id="mpanel" hidden>
  <div class="mpanel-in">
    <details>
      <summary>Tipos de viaje</summary>
      <ul>
        <li><a href="/viajes-a-medida">Viajes a medida</a></li>
        <li><a href="/viajes-en-grupo">Viajes en grupo</a></li>
      </ul>
    </details>
    <details>
      <summary>Destinos</summary>
      <?php foreach (['sudamerica' => 'Sudamérica', 'europa' => 'Europa'] as $key => $label): ?>
      <p class="m-head"><a href="/destinos/<?= e($key) ?>"><?= e($label) ?></a></p>
      <ul>
        <?php foreach ($paises[$key] as $p): ?>
        <li><a href="/destinos/<?= e($key) ?>/<?= e($p['slug']) ?>"><?= e($p['nombre']) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <?php endforeach; ?>
    </details>
    <a class="m-link" href="/quienes-somos">Quiénes somos</a>
    <a class="m-link" href="/blog">Blog</a>
    <a class="m-link" href="/contacto">Contacto</a>
    <a class="btn btn-cta m-cta" href="<?= e($cta) ?>">Pide tu presupuesto</a>
  </div>
</div>
