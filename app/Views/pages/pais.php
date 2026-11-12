<?php
/**
 * @var array $pais  @var array $galeria  @var list<array> $salidas  @var list<array> $posts  @var list<array> $otros
 * @var array $paises
 */
use App\Core\Text;
use App\Repositories\Countries;

$label = Countries::continentLabel($pais['continente']);
?>
<?= \App\Core\View::partial('partials/crumbs', ['items' => [['Destinos', '/destinos'], [$label, '/destinos/' . $pais['continente']], [$pais['nombre'], null]]]) ?>
<header class="phero">
  <?= img($pais['foto'], $pais['foto_alt'], ['pos' => $pais['foto_pos'], 'eager' => true]) ?>
  <div class="blk">
    <div class="kick"><?= e($label) ?></div>
    <h1><?= e($pais['nombre']) ?></h1>
    <p><?= e($pais['resumen']) ?></p>
  </div>
</header>

<?php if (count($galeria) >= 2): ?>
<section class="sec" aria-labelledby="gal-t">
  <div class="wrap">
    <div class="kick">Galería</div>
    <h2 id="gal-t"><?= e($pais['nombre']) ?>, <em>en imágenes</em></h2>
    <div class="gal gal-<?= count($galeria) ?>">
      <?php foreach ($galeria as $g): ?>
      <figure><?= img($g['archivo'], $g['alt']) ?></figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($salidas): ?>
<section class="sec sec-tight" aria-labelledby="sal-t">
  <div class="wrap">
    <div class="kick">Viajes en grupo</div>
    <h2 id="sal-t">Salidas a <em><?= e($pais['nombre']) ?></em></h2>
    <?= \App\Core\View::partial('partials/salidas', ['salidas' => $salidas]) ?>
  </div>
</section>
<?php endif; ?>

<?php if ($posts): ?>
<section class="sec sec-tight" aria-labelledby="art-t">
  <div class="wrap">
    <div class="kick">Blog</div>
    <h2 id="art-t">Historias de <em><?= e($pais['nombre']) ?></em></h2>
    <?= \App\Core\View::partial('partials/post-grid', ['posts' => $posts]) ?>
  </div>
</section>
<?php endif; ?>

<?= \App\Core\View::partial('partials/como', [
    'paises'  => $paises,
    'pre'     => [$pais['slug']],
    'titulo'  => 'Diseñemos tu viaje',
    'enfasis' => 'a ' . $pais['nombre'],
]) ?>

<?php if ($otros): ?>
<section class="sec" aria-labelledby="otros-t">
  <div class="wrap">
    <div class="kick">Sigue explorando</div>
    <h2 id="otros-t">Más destinos en <em><?= e($label) ?></em></h2>
    <div class="minis">
      <?php foreach ($otros as $o): ?>
      <a class="mini" href="/destinos/<?= e($o['continente']) ?>/<?= e($o['slug']) ?>">
        <span class="ph"><?= img($o['foto'], $o['foto_alt'], ['pos' => $o['foto_pos']]) ?></span>
        <h3><?= e($o['nombre']) ?></h3>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
