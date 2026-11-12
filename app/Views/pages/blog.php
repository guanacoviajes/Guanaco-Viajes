<?php
/** @var list<array> $posts  @var list<array> $filtro  @var ?string $paisSel  @var int $page  @var int $pages */
use App\Repositories\Settings;
$S = static fn(string $k, string $d = ''): string => Settings::get($k, $d);
$q = static fn(int $p): string => '/blog?' . http_build_query(array_filter(['pais' => $paisSel, 'page' => $p > 1 ? $p : null]));
?>
<?= \App\Core\View::partial('partials/crumbs', ['items' => [['Blog', null]]]) ?>
<section class="pagehead">
  <div class="wrap">
    <div class="kick">Blog</div>
    <h1><?= e($S('blog.titulo', 'Historias de')) ?> <em><?= e($S('blog.enfasis', 'viaje')) ?></em></h1>
    <?php if ($filtro && ($posts || $paisSel)): ?>
    <div class="chips2" role="navigation" aria-label="Filtrar por país">
      <a class="chip<?= $paisSel === null ? ' on' : '' ?>" href="/blog"<?= $paisSel === null ? ' aria-current="true"' : '' ?>>Todos</a>
      <?php foreach ($filtro as $f): ?>
      <a class="chip<?= $paisSel === $f['slug'] ? ' on' : '' ?>" href="/blog?pais=<?= e($f['slug']) ?>"<?= $paisSel === $f['slug'] ? ' aria-current="true"' : '' ?>><?= e($f['nombre']) ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if ($posts): ?>
    <?= \App\Core\View::partial('partials/post-grid', ['posts' => $posts]) ?>
    <?php if ($pages > 1): ?>
    <nav class="pager" aria-label="Paginación">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
      <a<?= $i === $page ? ' class="on" aria-current="page"' : '' ?> href="<?= e($q($i)) ?>" aria-label="Página <?= $i ?>"><?= $i ?></a>
      <?php endfor; ?>
    </nav>
    <?php endif; ?>
    <?php else: ?>
    <div class="empty">
      <p class="t"><?= e($S('blog.vacio.titulo', 'Todavía no hay historias por aquí.')) ?></p>
      <p class="s"><?= e($S('blog.vacio.texto', 'Estamos preparando los primeros artículos. Mientras tanto, cuéntanos tu idea de viaje.')) ?></p>
      <a class="btn b1" href="/presupuesto">Comienza tu aventura</a>
    </div>
    <?php endif; ?>
  </div>
</section>
