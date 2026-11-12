<?php
/** @var array $tipo  @var list<array> $salidas  @var int $huecos */
use App\Repositories\Settings;
$S = static fn(string $k, string $d = ''): string => Settings::get($k, $d);
?>
<?= \App\Core\View::partial('partials/crumbs', ['items' => [['Tipos de viaje', null], ['Viajes en grupo', null]]]) ?>
<header class="phero">
  <?= img($tipo['foto'], $tipo['foto_alt'], ['pos' => $tipo['foto_pos'], 'eager' => true]) ?>
  <div class="blk">
    <div class="kick">Tipos de viaje</div>
    <h1>Viajes en grupo</h1>
    <p><?= e($tipo['descripcion']) ?></p>
  </div>
</header>

<section class="sec">
  <div class="wrap intro">
    <p class="big"><?= e($S('grupo.intro', 'Creamos viajes en grupo para quienes disfrutan compartiendo el camino con otras personas. Propuestas que buscan ir un poco más allá de lo que todo el mundo hace.')) ?></p>
  </div>
  <div class="wrap">
    <?php if ($salidas): ?>
    <?= \App\Core\View::partial('partials/salidas', ['salidas' => $salidas]) ?>
    <?php endif; ?>
    <?php if ($huecos > 0): ?>
    <div class="proximo"<?= $salidas ? ' style="margin-top:20px"' : '' ?>>
      <?php for ($i = 0; $i < $huecos; $i++): ?>
      <div class="px"><span class="kick k">Salida <?= count($salidas) + $i + 1 ?></span><div class="t">Próximamente</div><div class="s"><?= e($S('grupo.px.sub', 'Destino y fechas por confirmar')) ?></div></div>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="sec sec-tight">
  <div class="wrap">
    <div class="cierre">
      <p><?= e($S('grupo.cierre.texto', '¿Prefieres un viaje solo para ti?')) ?> <em><?= e($S('grupo.cierre.enfasis', 'Lo diseñamos a medida.')) ?></em></p>
      <a class="btn b1" href="/viajes-a-medida"><?= e($S('grupo.cierre.boton', 'Ver viajes a medida')) ?></a>
    </div>
  </div>
</section>
