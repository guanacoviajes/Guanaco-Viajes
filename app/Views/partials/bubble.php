<?php
use App\Repositories\Settings;

$wa   = preg_replace('/\D+/', '', Settings::get('site.whatsapp'));
$mail = Settings::get('site.email');
$msg  = Settings::get('site.whatsapp_mensaje', 'Hola, me gustaría información sobre un viaje.');
?>
<div class="bubble-wrap" id="bubble">
  <div class="bubble-menu" id="bubble-menu" hidden>
    <p class="bubble-t">¿Hablamos?</p>
    <?php if ($wa !== ''): ?>
    <a href="https://wa.me/<?= e($wa) ?>?text=<?= e(rawurlencode($msg)) ?>" target="_blank" rel="noopener">WhatsApp</a>
    <?php endif; ?>
    <?php if ($mail !== ''): ?>
    <a href="mailto:<?= e($mail) ?>">Correo electrónico</a>
    <?php endif; ?>
  </div>
  <button type="button" class="bubble" aria-label="Escríbenos por WhatsApp o correo" aria-expanded="false" aria-controls="bubble-menu">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v11H9l-5 4z"/></svg>
  </button>
</div>
