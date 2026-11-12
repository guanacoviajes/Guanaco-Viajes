<?php /** @var list<array{path:string,time:int,size:int,use:list<string>}> $files @var string $csrf */ ?>
<div class="page-head"><div><h1>Imágenes</h1><p class="sub">Las fotos subidas desde el panel. Las que no se usan en ningún sitio se pueden borrar para liberar espacio.</p></div></div>
<?php if ($files): ?>
<div class="thumbs">
  <?php foreach ($files as $f): ?>
  <div class="thumb">
    <img src="/<?= e($f['path']) ?>" alt="" loading="lazy">
    <div class="b">
      <?php if ($f['use']): ?><span class="tag pub">En uso</span> <span class="sm"><?= e(implode(' · ', $f['use'])) ?></span>
      <?php else: ?><span class="tag warn">Sin usar</span>
      <form method="post" action="/admin/imagenes/borrar" data-confirm="¿Borrar esta imagen? No se puede deshacer." style="margin-top:8px"><?= $csrf ?><input type="hidden" name="path" value="<?= e($f['path']) ?>"><button class="btn danger sm" type="submit">Borrar</button></form>
      <?php endif; ?>
      <div class="sm" style="margin-top:6px"><?= e(date('d/m/Y', $f['time'])) ?> · <?= e(number_format($f['size'] / 1024, 0, ',', '.')) ?> KB</div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div class="empty">Todavía no has subido ninguna imagen desde el panel. Las fotos de muestra de la web no aparecen aquí.</div>
<?php endif; ?>
