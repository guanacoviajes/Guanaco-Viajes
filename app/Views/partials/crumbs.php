<?php
/** @var list<array{0:string,1:?string}> $items  [[texto, url|null], …] */
?>
<nav class="crumbs" aria-label="Ruta de navegación">
  <ol class="crumbs-l">
    <li><a href="/">Inicio</a></li>
    <?php foreach ($items as [$txt, $href]): ?>
    <li><?php if ($href): ?><a href="<?= e($href) ?>"><?= e($txt) ?></a><?php else: ?><span aria-current="page"><?= e($txt) ?></span><?php endif; ?></li>
    <?php endforeach; ?>
  </ol>
</nav>
<?php
$base = rtrim((string) config('app.url'), '/');
$ld = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => $base . '/'],
]];
foreach ($items as $i => [$txt, $href]) {
    $ld['itemListElement'][] = ['@type' => 'ListItem', 'position' => $i + 2, 'name' => $txt] + ($href ? ['item' => $base . $href] : []);
}
?>
<script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
