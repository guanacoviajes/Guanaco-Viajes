<?php /** @var list<string> $codes */ ?>
<div class="page-head"><h1>Códigos de recuperación</h1></div>
<div class="card">
  <p><b>Guárdalos ahora en un lugar seguro</b> (por ejemplo, un gestor de contraseñas o impresos y guardados). No volverán a mostrarse.</p>
  <p>Cada código sirve <b>una sola vez</b> para entrar si pierdes el móvil.</p>
  <ul class="codes" id="codes"><?php foreach ($codes as $c): ?><li><?= e($c) ?></li><?php endforeach; ?></ul>
  <p><button class="btn ghost" type="button" data-copy="#codes">Copiar los códigos</button>
     <a class="btn" href="/admin">Ya los he guardado</a></p>
</div>
