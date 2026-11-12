<?php
/** @var list<array> $posts */
use App\Core\Text;
?>
<div class="bgrid">
  <?php foreach ($posts as $p): ?>
  <article class="bc">
    <a href="/blog/<?= e($p['slug']) ?>">
      <span class="bph"><?php if ($p['portada'] !== ''): ?><?= img($p['portada'], $p['portada_alt']) ?><?php endif; ?></span>
      <?php if (!empty($p['pais'])): ?><span class="tag"><?= e($p['pais']) ?></span><?php endif; ?>
      <h3><?= e($p['titulo']) ?></h3>
      <?php if (!empty($p['publicado_en'])): ?><div class="d"><?= e(Text::dateEs($p['publicado_en'])) ?></div><?php endif; ?>
    </a>
  </article>
  <?php endforeach; ?>
</div>
