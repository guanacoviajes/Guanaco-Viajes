<?php /** @var array $u @var int $left @var string $csrf */ ?>
<div class="page-head"><div><h1>Seguridad</h1><p class="sub">Tu acceso al panel: <?= e($u['email']) ?></p></div></div>
<div class="grid c2" style="align-items:start">
  <div class="card">
    <h2>Cambiar la contraseña</h2>
    <form method="post" action="/admin/seguridad/contrasena" data-once>
      <?= $csrf ?>
      <label class="f">Contraseña actual<input type="password" name="actual" autocomplete="current-password" required></label>
      <label class="f">Contraseña nueva <small>(mínimo 12 caracteres; mejor una frase larga)</small><input type="password" name="nueva" autocomplete="new-password" minlength="12" required></label>
      <label class="f">Repite la nueva<input type="password" name="nueva2" autocomplete="new-password" minlength="12" required></label>
      <button class="btn" type="submit">Cambiar contraseña</button>
      <p class="hint">Al cambiarla se cierran todas las sesiones abiertas.</p>
    </form>
  </div>
  <div>
    <div class="card">
      <h2>Verificación en dos pasos</h2>
      <p>Activa. Te quedan <b><?= (int) $left ?></b> código(s) de recuperación.</p>
      <form method="post" action="/admin/seguridad/recuperacion" data-confirm="Se anularán los códigos de recuperación actuales y se crearán otros nuevos. ¿Continuar?">
        <?= $csrf ?>
        <label class="f">Contraseña actual<input type="password" name="actual" autocomplete="current-password" required></label>
        <button class="btn ghost" type="submit">Generar códigos de recuperación nuevos</button>
      </form>
      <hr style="border:0;border-top:1px solid var(--line);margin:20px 0">
      <form method="post" action="/admin/seguridad/reiniciar-2fa" data-confirm="La aplicación de verificación actual dejará de funcionar y tendrás que configurarla de nuevo. ¿Continuar?">
        <?= $csrf ?>
        <label class="f">Contraseña actual<input type="password" name="actual" autocomplete="current-password" required></label>
        <button class="btn ghost" type="submit">Cambiar de móvil (reconfigurar la verificación)</button>
      </form>
    </div>
    <div class="card">
      <h2>Cerrar todas las sesiones</h2>
      <p class="hint">Útil si has entrado desde un ordenador que no es tuyo.</p>
      <form method="post" action="/admin/seguridad/cerrar-sesiones" data-confirm="¿Cerrar todas las sesiones, incluida esta?"><?= $csrf ?><button class="btn danger" type="submit">Cerrar todas las sesiones</button></form>
    </div>
  </div>
</div>
