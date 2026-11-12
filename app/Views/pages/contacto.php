<?php
use App\Repositories\Settings;
$S = static fn(string $k, string $d = ''): string => Settings::get($k, $d);
$tel = $S('site.telefono'); $wa = preg_replace('/\D+/', '', $S('site.whatsapp')); $mail = $S('site.email');
$msg = $S('site.whatsapp_mensaje', 'Hola, me gustaría información sobre un viaje.');
?>
<?= \App\Core\View::partial('partials/crumbs', ['items' => [['Contacto', null]]]) ?>
<section class="pagehead">
  <div class="wrap">
    <div class="kick">Contacto</div>
    <h1><?= e($S('contacto.titulo', '¿Tienes una duda?')) ?> <em><?= e($S('contacto.enfasis', 'Escríbenos.')) ?></em></h1>
    <p><?= e($S('contacto.lead', 'Para una pregunta rápida, por el canal que prefieras. Si quieres que te diseñemos un viaje, pide tu presupuesto.')) ?></p>
    <div class="cts">
      <?php if ($tel !== ''): ?>
      <div class="ct"><div class="k">WhatsApp y teléfono</div>
        <a class="v" href="tel:+<?= e($wa !== '' ? $wa : preg_replace('/\D+/', '', $tel)) ?>"><?= e($tel) ?></a>
        <p><a href="https://wa.me/<?= e($wa) ?>?text=<?= e(rawurlencode($msg)) ?>" rel="noopener" target="_blank">Escríbenos por WhatsApp</a> o llámanos.</p></div>
      <?php endif; ?>
      <?php if ($mail !== ''): ?>
      <div class="ct"><div class="k">Correo</div>
        <a class="v" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a>
        <p>Para cualquier consulta.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>
<section class="sec sec-tight">
  <div class="wrap">
    <div class="cierre">
      <p>¿Quieres que diseñemos un viaje para ti? <em>Cuéntanos tu idea.</em></p>
      <a class="btn b1" href="/presupuesto">Pide tu presupuesto</a>
    </div>
  </div>
</section>
