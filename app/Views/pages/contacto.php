<?php
/**
 * @var array<string,mixed> $old  @var array<string,string> $errors  @var ?string $aviso
 * @var bool $enviado  @var string $csrf  @var string $timeToken
 */
use App\Repositories\Settings;

$S = static fn(string $k, string $d = ''): string => Settings::get($k, $d);
$tel = $S('site.telefono');
$wa = preg_replace('/\D+/', '', $S('site.whatsapp'));
$mail = $S('site.email');
$msg = $S('site.whatsapp_mensaje', 'Hola, me gustaría información sobre un viaje.');
$val = static fn(string $k): string => is_string($old[$k] ?? null) ? $old[$k] : '';
$err = static fn(string $k): string => isset($errors[$k])
    ? '<p class="err on" role="alert" id="e-' . e($k) . '">' . e($errors[$k]) . '</p>'
    : '';
$bad = static fn(string $k): string => isset($errors[$k]) ? ' aria-invalid="true"' : '';
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

<section class="sec sec-tight" id="formulario">
  <div class="wrap cform">
    <div>
      <div class="kick">Mensaje</div>
      <h2>Cuéntanos <em>qué necesitas</em></h2>
      <p class="lead2">Respondemos en cuanto podemos. Si lo que buscas es un viaje a medida, el formulario de presupuesto te hace las preguntas justas.</p>
      <a class="btn b2" href="/presupuesto" style="margin-top:26px">Pide tu presupuesto</a>
    </div>

    <?php if ($enviado): ?>
    <div class="sent" id="mensaje-enviado" role="status" tabindex="-1">
      <p class="t">Mensaje enviado.</p>
      <p>Gracias. Te responderemos en cuanto podamos. Te hemos enviado un correo de confirmación.</p>
    </div>
    <?php else: ?>
    <form id="cform" action="/contacto/enviar" method="post" novalidate>
      <?= $csrf ?>
      <input type="hidden" name="t" value="<?= e($timeToken) ?>">
      <div class="hp" aria-hidden="true"><label>Sitio web <input type="text" name="sitio_web" tabindex="-1" autocomplete="off"></label></div>
      <?php if ($aviso): ?><p class="aviso-form" role="alert"><?= e($aviso) ?></p><?php endif; ?>
      <div class="fld"><label for="c-nombre">Nombre</label><div class="in<?= isset($errors['nombre']) ? ' bad' : '' ?>"><input id="c-nombre" name="nombre" maxlength="100" autocomplete="name" value="<?= e($val('nombre')) ?>"<?= $bad('nombre') ?> required></div><?= $err('nombre') ?></div>
      <div class="fld"><label for="c-email">Correo electrónico</label><div class="in<?= isset($errors['email']) ? ' bad' : '' ?>"><input id="c-email" type="email" name="email" maxlength="150" autocomplete="email" value="<?= e($val('email')) ?>"<?= $bad('email') ?> required></div><?= $err('email') ?></div>
      <div class="fld"><label for="c-mensaje">Mensaje</label><div class="in ta<?= isset($errors['mensaje']) ? ' bad' : '' ?>"><textarea id="c-mensaje" name="mensaje" maxlength="2000"<?= $bad('mensaje') ?> required><?= e($val('mensaje')) ?></textarea></div><?= $err('mensaje') ?></div>
      <label class="chk"><input type="checkbox" name="consentimiento" value="1"<?= !empty($old['consentimiento']) ? ' checked' : '' ?><?= $bad('consentimiento') ?>><i></i><span>He leído y acepto la <a href="/politica-de-privacidad" target="_blank" rel="noopener">política de privacidad</a>. Responsable del tratamiento: NADIDU SL, agencia de viajes con registro C.IAN-118115-2. Mis datos se usarán para responder a mi mensaje.</span></label>
      <?= $err('consentimiento') ?>
      <button type="submit" class="btn b1" id="csend" style="margin-top:22px">Enviar mensaje</button>
    </form>
    <?php endif; ?>
  </div>
</section>
