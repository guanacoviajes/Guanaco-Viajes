<?php
/** @var array $post  @var list<array> $relacionados */
use App\Core\Text;
?>
<?= \App\Core\View::partial('partials/crumbs', ['items' => [['Blog', '/blog'], [$post['titulo'], null]]]) ?>
<article class="sec" style="padding-top:56px">
  <div class="wrap">
    <div class="art">
      <?php if (!empty($post['pais'])): ?><a class="tag" style="margin-top:0;text-decoration:none" href="/blog?pais=<?= e($post['pais_slug']) ?>"><?= e($post['pais']) ?></a><?php endif; ?>
      <h1><?= e($post['titulo']) ?></h1>
      <div class="meta"><?= e(Text::dateEs($post['publicado_en'] ?: $post['created_at'])) ?> · Daniel Conti</div>
      <?php if ($post['portada'] !== ''): ?><div class="cover"><?= img($post['portada'], $post['portada_alt'], ['eager' => true]) ?></div><?php endif; ?>
      <div class="body"><?= Text::render($post['cuerpo']) ?></div>
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
