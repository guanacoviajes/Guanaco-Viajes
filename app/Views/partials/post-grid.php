<?php
/** @var list<array> $posts  @var bool $compact solo imagen y título (para las «ideas» de una ficha de país) */
$compact = $compact ?? false;
use App\Core\Text;
?>
<div class="bgrid">
  <?php foreach ($posts as $p): ?>
  <article class="bc">
    <a href="/blog/<?= e($p['slug']) ?>">
      <span class="bph"><?php if ($p['portada'] !== ''): ?><?= img($p['portada'], $p['portada_alt'], ['sizes' => '(max-width: 820px) 100vw, 33vw']) ?><?php endif; ?></span>
      <?php if (!$compact && !empty($p['pais'])): ?><span class="tag"><?= e($p['pais']) ?></span><?php endif; ?>
      <h3><?= e($p['titulo']) ?></h3>
      <?php if (!$compact && !empty($p['publicado_en'])): ?><div class="d"><?= e(Text::dateEs($p['publicado_en'])) ?></div><?php endif; ?>
    </a>
  </article>
  <?php endforeach; ?>
</div>
