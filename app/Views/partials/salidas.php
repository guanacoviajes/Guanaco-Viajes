<?php
/** @var list<array> $salidas */
use App\Core\Text;
?>
<div class="proximo pub">
  <?php foreach ($salidas as $d): ?>
  <article class="px">
    <?php if ($d['foto'] !== ''): ?><div class="ph"><?= img($d['foto'], $d['foto_alt']) ?></div><?php endif; ?>
    <h3><?= e($d['titulo']) ?></h3>
    <div class="meta">
      <?php $r = Text::rangeEs($d['fecha_inicio'], $d['fecha_fin']); ?>
      <?= e($r) ?><?= ($r !== '' && $d['plazas_texto'] !== '') ? ' · ' : '' ?><?= e($d['plazas_texto']) ?>
    </div>
    <?php if ($d['descripcion'] !== ''): ?><p class="dsc"><?= e($d['descripcion']) ?></p><?php endif; ?>
    <?php if ($d['precio_desde'] !== ''): ?><div class="precio">Desde <?= e($d['precio_desde']) ?></div><?php endif; ?>
    <a class="btn b2" href="/presupuesto?pais=<?= e($d['pais_slug'] ?? '') ?>">Consultar esta salida</a>
  </article>
  <?php endforeach; ?>
</div>
