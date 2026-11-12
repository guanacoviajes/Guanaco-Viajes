<?php
/**
 * @var list<array> $slides
 * @var list<array> $tipos
 * @var array{sudamerica: list<array>, europa: list<array>} $paises
 * @var list<array> $posts
 */
use App\Repositories\Settings;

$S = static fn(string $k, string $d = ''): string => Settings::get($k, $d);
$continentes = ['sudamerica' => 'Sudamérica', 'europa' => 'Europa'];
$quienFoto = $S('home.quien.foto');
?>

<?php if ($slides): ?>
<!-- 1 · Carrusel -->
<section class="carousel" id="carousel" aria-roledescription="carrusel" aria-label="Destacados" data-interval="<?= e($S('home.carrusel.segundos', '4')) ?>">
  <?php foreach ($slides as $i => $s): ?>
  <div class="slide<?= $i === 0 ? ' is-active' : '' ?>" data-tone="<?= e($s['tono_menu']) ?>"
       role="group" aria-roledescription="diapositiva" aria-label="<?= $i + 1 ?> de <?= count($slides) ?>"
       <?= $i === 0 ? '' : 'inert aria-hidden="true"' ?>>
    <?= img($s['foto'], $s['foto_alt'], ['class' => 'slide-img', 'pos' => $s['foto_pos'], 'eager' => $i === 0]) ?>
    <div class="blk">
      <?php if ($s['kicker'] !== ''): ?><div class="kick"><?= e($s['kicker']) ?></div><?php endif; ?>
      <?php if ($i === 0): ?><h1><?php else: ?><p class="h1"><?php endif; ?>
        <?= e($s['titular']) ?><?php if ($s['titular_enfasis'] !== ''): ?> <em><?= e($s['titular_enfasis']) ?></em><?php endif; ?>
      <?= $i === 0 ? '</h1>' : '</p>' ?>
      <?php if ($s['cta_texto'] !== ''): ?>
      <a class="btn bm" href="<?= e($s['cta_url']) ?>"><?= e($s['cta_texto']) ?></a>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (count($slides) > 1): ?>
  <div class="cdots" role="group" aria-label="Elegir diapositiva">
    <?php foreach ($slides as $i => $s): ?>
    <button type="button" aria-label="Ir a la diapositiva <?= $i + 1 ?>"<?= $i === 0 ? ' aria-current="true"' : '' ?>><i></i></button>
    <?php endforeach; ?>
    <button type="button" class="cpause" aria-label="Pausar la reproducción automática" data-state="playing"><span aria-hidden="true"></span></button>
  </div>
  <?php endif; ?>
</section>
<?php endif; ?>

<!-- 2 · Tipos de viaje -->
<?php if ($tipos): ?>
<section class="sec sec-tipos" aria-labelledby="tipos-t">
  <div class="wrap">
    <div class="tv" data-tabs="tipos">
      <div class="tv-text">
        <div class="tv-head">
          <div class="kick"><?= e($S('home.tipos.kicker', 'Tipos de viaje')) ?></div>
          <h2 id="tipos-t"><?= e($S('home.tipos.titulo', 'El viaje,')) ?> <em><?= e($S('home.tipos.enfasis', 'a tu manera')) ?></em></h2>
        </div>
        <div class="tv-tabs" role="tablist" aria-label="Tipos de viaje">
          <?php foreach ($tipos as $i => $t): ?>
          <button type="button" class="tv-tab<?= $i === 0 ? ' on' : '' ?>" role="tab" id="tab-<?= e($t['slug']) ?>"
                  aria-selected="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="panel-<?= e($t['slug']) ?>"
                  data-t="<?= e($t['slug']) ?>"<?= $i === 0 ? '' : ' tabindex="-1"' ?>><?= e($t['titulo']) ?></button>
          <?php endforeach; ?>
        </div>
        <?php foreach ($tipos as $i => $t): ?>
        <div class="tv-panel<?= $i === 0 ? ' on' : '' ?>" role="tabpanel" id="panel-<?= e($t['slug']) ?>"
             aria-labelledby="tab-<?= e($t['slug']) ?>" data-p="<?= e($t['slug']) ?>"<?= $i === 0 ? '' : ' hidden' ?>>
          <h3><?= e($t['titulo']) ?></h3>
          <p><?= e($t['descripcion']) ?></p>
          <a class="btn b1" href="<?= e($t['cta_url']) ?>"><?= e($t['cta_texto']) ?></a>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="tv-img">
        <?php foreach ($tipos as $i => $t): ?>
        <?= img($t['foto'], $t['foto_alt'], ['class' => $i === 0 ? 'on' : '', 'pos' => $t['foto_pos']]) ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- 3 · Destinos -->
<section class="sec sec-tight sec-dest" aria-labelledby="dest-t">
  <div class="wrap">
    <div class="kick"><?= e($S('home.destinos.kicker', 'Destinos')) ?></div>
    <h2 id="dest-t"><?= e($S('home.destinos.titulo', 'Elige por dónde')) ?> <em><?= e($S('home.destinos.enfasis', 'empezar')) ?></em></h2>
    <?= \App\Core\View::partial('partials/destinos-rail', ['paises' => $paises]) ?>
    <div class="todos"><a class="btn b2" href="/destinos"><?= e($S('home.destinos.boton', 'Ver todos los destinos')) ?></a></div>
  </div>
</section>

<!-- 4 · Cómo funciona + primer paso del formulario -->
<?= \App\Core\View::partial('partials/como', ['paises' => $paises]) ?>

<!-- 5 · Quién está detrás -->
<section class="sec" aria-labelledby="quien-t">
  <div class="wrap quien<?= $quienFoto === '' && !config('app.debug') ? ' sin-foto' : '' ?>">
    <?php if ($quienFoto !== ''): ?>
    <div class="foto-real"><?= img($quienFoto, 'Daniel Conti viajando') ?></div>
    <?php elseif (config('app.debug')): ?>
    <div class="foto">Aquí irá tu foto viajando.<br>Solo tú.<br><small>(solo se ve en desarrollo)</small></div>
    <?php endif; ?>
    <div>
      <div class="kick"><?= e($S('home.quien.kicker', 'Quién está detrás')) ?></div>
      <h2 class="l" id="quien-t"><?= e($S('home.quien.texto')) ?></h2>
      <a class="btn b2" href="/quienes-somos"><?= e($S('home.quien.boton', 'Conoce nuestra historia')) ?></a>
    </div>
  </div>
</section>

<?php if ($posts): ?>
<!-- 6 · Blog (solo si hay artículos publicados) -->
<section class="sec sec-tight" aria-labelledby="blog-t">
  <div class="wrap">
    <div class="kick"><?= e($S('home.blog.kicker', 'Blog')) ?></div>
    <h2 id="blog-t"><?= e($S('home.blog.titulo', 'Historias de')) ?> <em><?= e($S('home.blog.enfasis', 'viaje')) ?></em></h2>
    <div class="bgrid">
      <?php foreach ($posts as $p): ?>
      <article class="bc">
        <a href="/blog/<?= e($p['slug']) ?>">
          <?php if ($p['portada'] !== ''): ?><span class="bph"><?= img($p['portada'], $p['portada_alt']) ?></span><?php endif; ?>
          <?php if (!empty($p['pais'])): ?><span class="tag"><?= e($p['pais']) ?></span><?php endif; ?>
          <h3><?= e($p['titulo']) ?></h3>
        </a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
