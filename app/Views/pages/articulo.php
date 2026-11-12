<?php
/** @var array $post  @var list<array> $relacionados */
use App\Core\Text;
?>
<?= \App\Core\View::partial('partials/crumbs', ['items' => [['Blog', '/blog'], [$post['titulo'], null]]]) ?>
<article class="sec" style="padding-top:56px">
  <div class="wrap">
    <div class="art">
      <?php if (!empty($post['pais'])): ?><a class="tag" style="margin-top:0;text-decoration:none" href="/destinos/<?= e($post['pais_cont']) ?>/<?= e($post['pais_slug']) ?>"><?= e($post['pais']) ?></a><?php endif; ?>
      <h1><?= e($post['titulo']) ?></h1>
      <?php $mins = max(1, (int) ceil(str_word_count(strip_tags((string) $post['cuerpo']), 0, 'áéíóúüñÁÉÍÓÚÜÑ') / 200)); ?>
      <div class="meta"><time datetime="<?= e(date('Y-m-d', (int) strtotime((string) ($post['publicado_en'] ?: $post['created_at'])))) ?>"><?= e(Text::dateEs($post['publicado_en'] ?: $post['created_at'])) ?></time> · <a href="/quienes-somos">Daniel Conti</a> · <?= $mins ?> min de lectura</div>
      <?php if ($post['portada'] !== ''): ?><div class="cover"><?= img($post['portada'], $post['portada_alt'], ['eager' => true]) ?></div><?php endif; ?>
      <div class="body"><?= Text::render($post['cuerpo']) ?></div>
      <aside class="art-cta" aria-label="Diseñemos tu viaje">
        <?php if (!empty($post['pais'])): ?>
        <p><strong>¿Te apetece viajar a <?= e($post['pais']) ?>?</strong> Cuéntanos tu idea y diseñamos tu <a href="/destinos/<?= e($post['pais_cont']) ?>/<?= e($post['pais_slug']) ?>">viaje a medida a <?= e($post['pais']) ?></a>.</p>
        <a class="btn b2" href="/presupuesto?pais=<?= e($post['pais_slug']) ?>">Pide tu presupuesto</a>
        <?php else: ?>
        <p><strong>¿Tienes una idea de viaje?</strong> En Guanaco Viajes diseñamos <a href="/viajes-a-medida">viajes a medida</a> por Sudamérica y Europa.</p>
        <a class="btn b2" href="/presupuesto">Pide tu presupuesto</a>
        <?php endif; ?>
      </aside>
    </div>
  </div>
</article>
<?php if ($relacionados): ?>
<section class="sec sec-tight" aria-labelledby="rel-t">
  <div class="wrap">
    <div class="kick">Sigue leyendo</div>
    <h2 id="rel-t">Más <em>historias</em></h2>
    <?= \App\Core\View::partial('partials/post-grid', ['posts' => $relacionados]) ?>
  </div>
</section>
<?php endif; ?>
