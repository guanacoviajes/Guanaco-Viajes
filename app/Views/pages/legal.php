<?php
/** @var array $pagina */
use App\Core\Text;
use App\Repositories\Settings;
$headings = [];
foreach (preg_split('/\n\s*\n/', $pagina['cuerpo']) ?: [] as $b) {
    if (str_starts_with(trim($b), '## ')) { $headings[] = trim(substr(trim($b), 3)); }
}
$html = Text::render($pagina['cuerpo']);
$i = 0;
$html = preg_replace_callback('/<h2>/', static function () use (&$i): string { return '<h2 id="s' . (++$i) . '">'; }, $html) ?? $html;
?>
<?= \App\Core\View::partial('partials/crumbs', ['items' => [[$pagina['titulo'], null]]]) ?>
<section class="pagehead" style="padding-bottom:24px">
  <div class="wrap">
    <div class="kick">Legal</div>
    <h1><?= e($pagina['titulo']) ?></h1>
    <p style="font-size:16px">Última actualización: <?= e(\App\Core\Text::dateEs($pagina['updated_at'])) ?></p>
  </div>
</section>
<section class="sec" style="padding-top:32px">
  <div class="wrap lg">
    <?php if (count($headings) > 2): ?>
    <aside class="toc" aria-label="En esta página"><b>En esta página</b>
      <?php foreach ($headings as $n => $h): ?><a href="#s<?= $n + 1 ?>"><?= e($h) ?></a><?php endforeach; ?>
    </aside>
    <?php else: ?><div></div><?php endif; ?>
    <div class="doc">
      <div class="aviso"><?= e(Settings::get('footer.agencia')) ?> NADIDU SL es la agencia titular de la licencia y la responsable de la contratación y del tratamiento de datos.</div>
      <?= $html ?>
    </div>
  </div>
</section>
