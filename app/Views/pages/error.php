<?php /** @var int $code */ ?>
<section class="nf-wrap">
  <div class="wrap nf">
    <?php if ($code === 404): ?>
    <div class="num">404</div>
    <h1>Parece que te has salido <em>del camino.</em></h1>
    <p>La página que buscas no existe o ha cambiado de sitio. A veces pasa, y no pasa nada.</p>
    <?php else: ?>
    <div class="num"><?= (int) $code ?></div>
    <h1>Algo ha fallado <em>por nuestra parte.</em></h1>
    <p>Ya estamos al tanto. Inténtalo de nuevo en unos minutos o escríbenos.</p>
    <?php endif; ?>
    <div class="row">
      <a class="btn b1" href="/">Volver al inicio</a>
      <a class="btn b2" href="/presupuesto">Pide tu presupuesto</a>
    </div>
  </div>
</section>
