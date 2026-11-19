<?php
/** @var array $c @var list<array> $rows @var string $csrf */
?>
<div class="page-head">
  <div><h1><?= e($c['plural']) ?></h1><?php if (!empty($c['intro'])): ?><p class="sub"><?= e($c['intro']) ?></p><?php endif; ?></div>
  <?php if (!empty($c['create'])): ?><div class="actions"><a class="btn" href="<?= e($c['path']) ?>/nuevo"><?= e($c['create']) ?></a></div><?php endif; ?>
</div>
<?php if ($rows): ?>
<div class="table-wrap"><table class="t">
  <thead><tr><?php foreach ($c['columns'] as [$label]): ?><th><?= e($label) ?></th><?php endforeach; ?><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $row): ?>
    <tr>
      <?php foreach ($c['columns'] as $i => [$label, $cell]): ?>
      <td><?php if ($i === 0): ?><a class="row" href="<?= e($c['path']) ?>/<?= (int) $row['id'] ?>"><?= $cell($row) ?></a><?php else: ?><?= $cell($row) ?><?php endif; ?></td>
      <?php endforeach; ?>
      <td class="num"><a class="btn ghost sm" href="<?= e($c['path']) ?>/<?= (int) $row['id'] ?>">Editar</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php else: ?>
<div class="empty"><?= e($c['empty'] ?? 'Todavía no hay nada aquí.') ?><?php if (!empty($c['create'])): ?><p><a class="btn" href="<?= e($c['path']) ?>/nuevo"><?= e($c['create']) ?></a></p><?php endif; ?></div>
<?php endif; ?>
