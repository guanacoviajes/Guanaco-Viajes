<?php use App\Repositories\Settings; ?>
<div class="franja">
  <div class="wrap">
    <p><?= e(Settings::get('franja.texto', '¿Tienes una idea de viaje?')) ?> <em><?= e(Settings::get('franja.enfasis', 'Cuéntanosla.')) ?></em></p>
    <a class="btn b1" href="/presupuesto">Pide tu presupuesto</a>
  </div>
</div>
