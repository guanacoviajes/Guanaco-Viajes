<?php
/** @var array $paises  @var string $activo */
use App\Repositories\Settings;
$S = static fn(string $k, string $d = ''): string => Settings::get($k, $d);
?>
<?= \App\Core\View::partial('partials/crumbs', ['items' => [['Destinos', null]]]) ?>
<section class="pagehead" style="padding-bottom:0">
  <div class="wrap">
    <div class="kick">Destinos</div>
    <h1><?= e($S('destinos.titulo', 'Elige por dónde')) ?> <em><?= e($S('destinos.enfasis', 'empezar')) ?></em></h1>
    <p><?= e($S('destinos.lead', 'Cada país es un punto de partida. El viaje lo diseñamos contigo.')) ?></p>
  </div>
</section>
<section class="sec" style="padding-top:48px">
  <div class="wrap"><?= \App\Core\View::partial('partials/destinos-rail', ['paises' => $paises, 'activo' => $activo]) ?></div>
</section>
