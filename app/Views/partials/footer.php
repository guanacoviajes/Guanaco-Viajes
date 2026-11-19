<?php
use App\Repositories\Settings;

$tel   = Settings::get('site.telefono');
$wa    = preg_replace('/\D+/', '', Settings::get('site.whatsapp'));
$mail  = Settings::get('site.email');
?>
<footer class="site-footer">
  <div class="wrap">
    <div class="fgrid">
      <div>
        <img class="flogo" src="/assets/img/logo/guanaco-completo-oscuro.svg" width="1000" height="470" alt="Guanaco Viajes" loading="lazy">
        <p class="lema"><?= e(Settings::get('footer.lema')) ?></p>
      </div>
      <div>
        <h2>Destinos</h2>
        <a href="/destinos/sudamerica">Sudamérica</a>
        <a href="/destinos/europa">Europa</a>
      </div>
      <div>
        <h2>Viajes</h2>
        <a href="/viajes-a-medida">Viajes a medida</a>
        <a href="/viajes-en-grupo">Viajes en grupo</a>
      </div>
      <div>
        <h2>Guanaco</h2>
        <a href="/quienes-somos">Quiénes somos</a>
        <a href="/blog">Blog</a>
        <a href="/contacto">Contacto</a>
        <a href="/presupuesto">Pide tu presupuesto</a>
      </div>
      <div>
        <h2>Contacto</h2>
        <?php if ($tel !== ''): ?><a href="tel:+<?= e($wa !== '' ? $wa : preg_replace('/\D+/', '', $tel)) ?>"><?= e($tel) ?></a><?php endif; ?>
        <?php if ($mail !== ''): ?><a href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a><?php endif; ?>
        <?= \App\Core\View::partial('partials/social') ?>
      </div>
    </div>
    <div class="legal">
      <?= e(Settings::get('footer.agencia')) ?><br>
      <a href="/aviso-legal">Aviso legal</a> · <a href="/politica-de-privacidad">Política de privacidad</a> · <a href="/politica-de-cookies">Política de cookies</a> · <a href="/condiciones-generales">Condiciones generales</a>
    </div>
  </div>
</footer>
