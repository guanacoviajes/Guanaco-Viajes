<?php
use App\Repositories\Settings;

$tel   = Settings::get('site.telefono');
$wa    = preg_replace('/\D+/', '', Settings::get('site.whatsapp'));
$mail  = Settings::get('site.email');
$redes = [
    'Instagram' => Settings::get('social.instagram'),
    'Facebook'  => Settings::get('social.facebook'),
    'X'         => Settings::get('social.x'),
];
$iconos = [
    'Instagram' => '<path d="M12 2.2c3.2 0 3.6 0 4.8.1 3.3.1 4.8 1.7 4.9 4.9.1 1.3.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 3.2-1.7 4.8-4.9 4.9-1.3.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-3.3-.1-4.8-1.7-4.9-4.9C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.8C2.4 3.9 4 2.4 7.2 2.3 8.4 2.2 8.8 2.2 12 2.2zM12 0C8.7 0 8.3 0 7.1.1 2.7.3.3 2.7.1 7.1 0 8.3 0 8.7 0 12s0 3.7.1 4.9c.2 4.4 2.6 6.8 7 7C8.3 24 8.7 24 12 24s3.7 0 4.9-.1c4.4-.2 6.8-2.6 7-7 .1-1.2.1-1.6.1-4.9s0-3.7-.1-4.9c-.2-4.4-2.6-6.8-7-7C15.7 0 15.3 0 12 0zm0 5.8a6.2 6.2 0 100 12.4 6.2 6.2 0 000-12.4zM12 16a4 4 0 110-8 4 4 0 010 8zm6.4-11.8a1.4 1.4 0 100 2.9 1.4 1.4 0 000-2.9z"/>',
    'Facebook'  => '<path d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07c0 6.02 4.39 11.02 10.13 11.93v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.69.24 2.69.24v2.97h-1.51c-1.49 0-1.96.93-1.96 1.88v2.26h3.33l-.53 3.49h-2.8V24C19.61 23.09 24 18.09 24 12.07z"/>',
    'X'         => '<path d="M18.9 1.15h3.68l-8.04 9.19L24 22.85h-7.4l-5.8-7.58-6.63 7.58H.49l8.6-9.83L0 1.15h7.59l5.24 6.93 6.07-6.93zm-1.29 19.5h2.04L6.48 3.24H4.3l13.31 17.41z"/>',
];
$redes = array_filter($redes, static fn($u) => preg_match('#^https?://#i', $u) === 1);
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
        <?php if ($redes): ?>
        <div class="social">
          <?php foreach ($redes as $nombre => $u): ?>
          <a href="<?= e($u) ?>" rel="noopener" target="_blank" aria-label="<?= e($nombre) ?> (se abre en una pestaña nueva)"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><?= $iconos[$nombre] ?></svg></a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="legal">
      <?= e(Settings::get('footer.agencia')) ?><br>
      <a href="/aviso-legal">Aviso legal</a> · <a href="/politica-de-privacidad">Política de privacidad</a> · <a href="/politica-de-cookies">Política de cookies</a> · <a href="/condiciones-generales">Condiciones generales</a>
    </div>
  </div>
</footer>
