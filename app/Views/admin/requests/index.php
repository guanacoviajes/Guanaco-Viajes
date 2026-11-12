<?php
/** @var list<array> $rows @var int $total @var array<string,string> $f @var int $page @var int $pages @var string $csrf */
use App\Repositories\Requests;

$q = static fn(array $over = []): string => '?' . http_build_query(array_filter(array_merge($f, $over), static fn($v) => $v !== null && $v !== ''));
?>
<div class="page-head">
  <div><h1>Solicitudes</h1><p class="sub"><?= (int) $total ?> en total<?= $f ? ' con estos filtros' : '' ?>.</p></div>
  <div class="actions"><a class="btn ghost" href="/admin/solicitudes/exportar.csv<?= e($q()) ?>">Descargar en Excel (CSV)</a></div>
</div>
<form class="filters" method="get" action="/admin/solicitudes">
  <label class="f">Estado<select name="estado"><option value="">Todos</option>
    <?php foreach (Requests::ESTADOS as $k => $l): ?><option value="<?= e($k) ?>"<?= ($f['estado'] ?? '') === $k ? ' selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></label>
  <label class="f">Origen<select name="origen"><option value="">Todos</option>
    <option value="presupuesto"<?= ($f['origen'] ?? '') === 'presupuesto' ? ' selected' : '' ?>>Presupuesto</option>
    <option value="contacto"<?= ($f['origen'] ?? '') === 'contacto' ? ' selected' : '' ?>>Contacto</option></select></label>
  <label class="f">Buscar<input type="search" name="q" value="<?= e($f['q'] ?? '') ?>" placeholder="Nombre, correo, destino…"></label>
  <button class="btn" type="submit">Filtrar</button>
  <?php if ($f): ?><a class="btn ghost" href="/admin/solicitudes">Quitar filtros</a><?php endif; ?>
</form>
<?php if ($rows): ?>
<div class="table-wrap"><table class="t">
  <thead><tr><th>n.º</th><th>Fecha</th><th>Quién</th><th>Qué</th><th>Estado</th><th>Correos</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $x): $dest = json_decode((string) $x['destinos'], true) ?: []; ?>
    <tr>
      <td class="sm"><?= (int) $x['id'] ?></td>
      <td class="sm"><?= e(date('d/m/Y H:i', strtotime($x['created_at']))) ?></td>
      <td><a class="row" href="/admin/solicitudes/<?= (int) $x['id'] ?>"><?= e($x['nombre']) ?></a><div class="sm"><?= e($x['email']) ?></div></td>
      <td><?= $x['origen'] === 'presupuesto' ? 'Presupuesto' : 'Contacto' ?><?php if ($dest): ?><div class="sm"><?= e(implode(', ', array_map(static fn($s) => $s === 'aun-no-lo-se' ? 'por decidir' : ucfirst(str_replace('-', ' ', $s)), $dest))) ?></div><?php endif; ?></td>
      <td><span class="tag <?= e($x['estado']) ?>"><?= e(Requests::ESTADOS[$x['estado']]) ?></span></td>
      <td><?= $x['aviso_enviado'] && $x['acuse_enviado'] ? '<span class="sm">enviados</span>' : '<span class="tag warn">pendientes</span>' ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php if ($pages > 1): ?>
<nav class="pager" aria-label="Paginación">
  <?php for ($i = 1; $i <= $pages; $i++): ?><a<?= $i === $page ? ' class="on" aria-current="page"' : '' ?> href="<?= e($q(['page' => $i > 1 ? $i : null])) ?>"><?= $i ?></a><?php endfor; ?>
</nav>
<?php endif; ?>
<?php else: ?>
<div class="empty"><?= $f ? 'No hay solicitudes con estos filtros.' : 'Todavía no ha llegado ninguna solicitud.' ?></div>
<?php endif; ?>
