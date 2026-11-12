<?php
/** @var array $c @var ?array $row @var string $html @var string $extra @var string $csrf */
$action = $row ? $c['path'] . '/' . (int) $row['id'] : $c['path'] . '/nuevo';
?>
<div class="page-head">
  <div><h1><?= $row ? 'Editar ' . e($c['singular']) : e($c['create'] ?? 'Nuevo') ?></h1></div>
  <div class="actions"><a class="btn ghost" href="<?= e($c['path']) ?>">← Volver</a><?php if ($row && !empty($c['viewUrl'])): ?><a class="btn ghost" href="<?= e($c['viewUrl']($row)) ?>" target="_blank" rel="noopener">Ver en la web ↗</a><?php endif; ?></div>
</div>
<?php if (!empty($c['help'])): ?><div class="help"><?= $c['help'] ?></div><?php endif; ?>
<form class="card" method="post" action="<?= e($action) ?>" enctype="multipart/form-data" data-once novalidate>
  <?= $csrf ?>
  <?= $html ?>
  <div class="actions" style="margin-top:8px">
    <button class="btn" type="submit"><?= $row ? 'Guardar cambios' : 'Crear' ?></button>
    <a class="btn ghost" href="<?= e($c['path']) ?>">Cancelar</a>
  </div>
</form>
<?= $extra ?>
<?php if ($row && !empty($c['delete'])): ?>
<form class="card" method="post" action="<?= e($c['path']) ?>/<?= (int) $row['id'] ?>/eliminar" data-confirm="<?= e($c['deleteConfirm'] ?? '¿Eliminar definitivamente? No se puede deshacer.') ?>">
  <?= $csrf ?>
  <h3>Eliminar</h3>
  <p class="hint"><?= e($c['deleteHint'] ?? 'Esta acción no se puede deshacer.') ?></p>
  <button class="btn danger" type="submit">Eliminar <?= e($c['singular']) ?></button>
</form>
<?php endif; ?>
