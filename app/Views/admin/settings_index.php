<?php /** @var array<string,array> $groups */ ?>
<div class="page-head"><div><h1>Textos y ajustes</h1><p class="sub">Todos los textos de la web. Si dejas uno en blanco, la web usa el texto por defecto.</p></div></div>
<div class="grid c3">
  <?php foreach ($groups as $slug => $g): ?>
  <a class="stat" href="/admin/ajustes/<?= e($slug) ?>" style="text-decoration:none"><h3 style="margin-bottom:6px"><?= e($g['title']) ?></h3><span><?= $g['intro'] !== '' ? e($g['intro']) : count($g['items']) . ' textos' ?></span></a>
  <?php endforeach; ?>
</div>
