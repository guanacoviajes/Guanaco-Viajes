<?php use App\Repositories\Settings; ?>
<div class="franja">
  <div class="wrap">
    <p><?= e(Settings::get('franja.texto', '¿Tienes una idea de viaje?')) ?> <em><?= e(Settings::get('franja.enfasis', 'Cuéntanosla.')) ?></em></p>
    <a class="btn b1" href="<?= e(Settings::get('site.cta_url', '/contacto')) ?>"><?= e(Settings::get('franja.boton', 'Comienza tu aventura')) ?></a>
  </div>
</div>
