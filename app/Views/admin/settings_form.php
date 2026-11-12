<?php /** @var array $g @var string $slug @var string $html @var string $csrf */ ?>
<div class="page-head">
  <div><h1><?= e($g['title']) ?></h1><?php if ($g['intro'] !== ''): ?><p class="sub"><?= e($g['intro']) ?></p><?php endif; ?></div>
  <div class="actions"><a class="btn ghost" href="/admin/ajustes">← Todos los ajustes</a></div>
</div>
<div class="help">Lo que ves <b>en gris dentro del campo</b> es el texto por defecto: si lo dejas vacío, se usa ese.</div>
<form class="card" method="post" action="/admin/ajustes/<?= e($slug) ?>" enctype="multipart/form-data" data-once novalidate>
  <?= $csrf ?>
  <?= $html ?>
  <div class="actions"><button class="btn" type="submit">Guardar cambios</button></div>
</form>
