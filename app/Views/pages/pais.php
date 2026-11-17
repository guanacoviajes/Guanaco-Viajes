<?php
/**
 * @var array $pais  @var array $galeria  @var list<array> $salidas  @var list<array> $posts  @var list<array> $otros
 * @var array $paises  @var array $c contenido ampliado (zonas, faqs)  @var int $ideasTotal
 */
use App\Core\Text;
use App\Repositories\Countries;
use App\Services\CountryContent;

$label = Countries::continentLabel($pais['continente']);
$n = e($pais['nombre']);
?>
<?= \App\Core\View::partial('partials/crumbs', ['items' => [['Destinos', '/destinos'], [$label, '/destinos/' . $pais['continente']], [$pais['nombre'], null]]]) ?>
<header class="phero phero-pais">
  <?= img($pais['foto'], $pais['foto_alt'], ['pos' => $pais['foto_pos'], 'eager' => true, 'sizes' => '100vw']) ?>
  <div class="blk">
    <div class="kick">Viajes a medida · <?= e($label) ?></div>
    <h1><?= e($pais['nombre']) ?></h1>
    <p><?= e($pais['resumen']) ?></p>
    <a class="btn bm" href="#disena-tu-viaje">Diseñemos tu viaje a <?= $n ?></a>
  </div>
</header>

<?php if (count($galeria) >= 2): ?>
<section class="sec" aria-labelledby="gal-t">
  <div class="wrap">
    <div class="kick">Galería</div>
    <h2 id="gal-t"><?= $n ?>, <em>en imágenes</em></h2>
    <div class="gal gal-<?= count($galeria) ?>">
      <?php foreach ($galeria as $g): ?>
      <figure><?= img($g['archivo'], $g['alt'], ['sizes' => '(max-width: 820px) 100vw, 50vw']) ?></figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($c['zonas']): ?>
<!-- Zonas y cuándo ir -->
<section class="sec sec-tight" aria-labelledby="zon-t">
  <div class="wrap">
    <div class="kick">Zonas</div>
    <h2 id="zon-t">Zonas y cuándo ir a <em><?= $n ?></em></h2>
    <p class="zon-intro">Elige las zonas que te apetezcan y nosotros las unimos en tu viaje.</p>
    <div class="zon-row" role="region" aria-label="Zonas de <?= $n ?>" tabindex="0" style="--n:<?= count($c['zonas']) ?>">
      <?php foreach ($c['zonas'] as $z): ?>
      <article class="zona">
        <h3><?= e($z['nombre']) ?></h3>
        <?php if ($z['descripcion'] !== ''): ?><p class="zdesc"><?= e($z['descripcion']) ?></p><?php endif; ?>
        <?php if ($z['dias'] !== ''): ?><p class="zdias"><span class="sr-only">Días recomendados: </span><?= e($z['dias']) ?></p><?php endif; ?>
        <?php if ($z['epoca'] !== ''): ?><p class="zepoca"><span class="zlabel">Cuándo ir</span> <?= e($z['epoca']) ?></p><?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($salidas): ?>
<section class="sec sec-tight" aria-labelledby="sal-t">
  <div class="wrap">
    <div class="kick">Viajes en grupo</div>
    <h2 id="sal-t">Salidas a <em><?= $n ?></em></h2>
    <?= \App\Core\View::partial('partials/salidas', ['salidas' => $salidas]) ?>
  </div>
</section>
<?php endif; ?>

<?php if ($c['faqs']): ?>
<!-- Preguntas frecuentes -->
<section class="sec sec-tight" aria-labelledby="faq-t">
  <div class="wrap faq-wrap">
    <div class="kick">Preguntas frecuentes</div>
    <h2 id="faq-t">Preguntas frecuentes sobre <em><?= $n ?></em></h2>
    <div class="faq">
      <?php foreach ($c['faqs'] as $f): ?>
      <details>
        <summary><?= e($f['pregunta']) ?></summary>
        <div class="faq-a"><?php foreach (CountryContent::paragraphs($f['respuesta']) as $par): ?><p><?= e($par) ?></p><?php endforeach; ?></div>
      </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?= \App\Core\View::partial('partials/como', [
    'paises'  => $paises,
    'pre'     => [$pais['slug']],
    'titulo'  => 'Diseñemos tu viaje',
    'enfasis' => 'a ' . $pais['nombre'],
]) ?>

<?php if ($posts): ?>
<!-- Ideas para tu viaje -->
<section class="sec sec-tight" aria-labelledby="ideas-t">
  <div class="wrap">
    <div class="kick">Blog</div>
    <h2 id="ideas-t">Ideas para tu viaje a <em><?= $n ?></em></h2>
    <?= \App\Core\View::partial('partials/post-grid', ['posts' => $posts, 'compact' => true]) ?>
    <?php if ($ideasTotal > 3): ?>
    <p class="ideas-mas"><a class="btn b2" href="/blog?pais=<?= e($pais['slug']) ?>">Ver todas las ideas sobre <?= $n ?></a></p>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php if ($otros): ?>
<section class="sec" aria-labelledby="otros-t">
  <div class="wrap">
    <div class="kick">Sigue explorando</div>
    <h2 id="otros-t">Más destinos en <em><?= e($label) ?></em></h2>
    <div class="minis">
      <?php foreach ($otros as $o): ?>
      <a class="mini" href="/destinos/<?= e($o['continente']) ?>/<?= e($o['slug']) ?>">
        <span class="ph"><?= img($o['foto'], $o['foto_alt'], ['pos' => $o['foto_pos'], 'sizes' => '(max-width: 820px) 74vw, 25vw']) ?></span>
        <h3><?= e($o['nombre']) ?></h3>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
