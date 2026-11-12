<?php
/** @var int $nuevas @var int $mes @var int $total @var list<array> $recent @var list<array> $todo @var int $paises @var int $posts */
use App\Core\Text;
use App\Repositories\Requests;
?>
<div class="page-head"><div><h1>Panel</h1><p class="sub">Resumen de la web y de las solicitudes que llegan.</p></div></div>

<div class="grid c4" style="margin-bottom:22px">
  <a class="stat<?= $nuevas ? ' alert' : '' ?>" href="/admin/solicitudes?estado=nueva"><b><?= $nuevas ?></b><span>Solicitudes nuevas</span></a>
  <a class="stat" href="/admin/solicitudes"><b><?= $mes ?></b><span>En los últimos 30 días</span></a>
  <a class="stat" href="/admin/solicitudes"><b><?= $total ?></b><span>Solicitudes en total</span></a>
  <a class="stat" href="/admin/destinos"><b><?= $paises ?></b><span>Destinos publicados</span></a>
</div>

<?php if ($todo): ?>
<div class="card">
  <h2>Pendiente</h2>
  <ul style="margin:0;padding-left:20px">
    <?php foreach ($todo as [$text, $href]): ?><li style="margin-bottom:6px"><a href="<?= e($href) ?>"><?= e($text) ?></a></li><?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<h2>Últimas solicitudes</h2>
<?php if ($recent): ?>
<div class="table-wrap"><table class="t">
  <thead><tr><th>Fecha</th><th>Quién</th><th>Qué</th><th>Estado</th></tr></thead>
  <tbody>
  <?php foreach ($recent as $x): ?>
    <tr>
      <td class="sm"><?= e(date('d/m/Y H:i', strtotime($x['created_at']))) ?></td>
      <td><a class="row" href="/admin/solicitudes/<?= (int) $x['id'] ?>"><?= e($x['nombre']) ?></a><div class="sm"><?= e($x['email']) ?></div></td>
      <td><?= $x['origen'] === 'presupuesto' ? 'Presupuesto' : 'Contacto' ?></td>
      <td><span class="tag <?= e($x['estado']) ?>"><?= e(Requests::ESTADOS[$x['estado']]) ?></span></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<p><a href="/admin/solicitudes">Ver todas</a></p>
<?php else: ?>
<div class="empty">Todavía no ha llegado ninguna solicitud.</div>
<?php endif; ?>
