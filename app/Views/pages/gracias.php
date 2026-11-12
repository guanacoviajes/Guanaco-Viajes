<?php
/** @var array{nombre:string,canal:string,rows:array<string,string>,falso?:bool} $g */
use App\Services\FormOptions;
?>
<section class="sec thanks-sec">
  <div class="wrap thanks">
    <div class="kick">Recibido</div>
    <h1>Gracias, <?= e($g['nombre']) ?>. <em>Ya tenemos tu idea.</em></h1>
    <p>Te escribiremos por <b><?= e(mb_strtolower(FormOptions::CANALES[$g['canal']] ?? 'correo')) ?></b> para empezar a darle forma. También te hemos enviado un correo con lo que nos has contado.</p>
    <?php if (!empty($g['rows'])): ?>
    <dl class="resumen">
      <?php foreach ($g['rows'] as $k => $v): ?><div><dt><?= e($k) ?></dt><dd><?= nl2br(e($v), false) ?></dd></div><?php endforeach; ?>
    </dl>
    <?php endif; ?>
    <a class="btn b2" href="/">Volver al inicio</a>
  </div>
</section>
