<?php /** @var string $csrf */ ?>
<div class="auth-card">
  <h1>Verificación en dos pasos</h1>
  <p class="hint">Escribe el código de 6 dígitos que muestra tu aplicación de verificación.</p>
  <form method="post" action="/admin/verificar" novalidate>
    <?= $csrf ?>
    <label class="f">Código<input class="code" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="12" required autofocus></label>
    <button class="btn" type="submit">Entrar</button>
  </form>
  <details class="more"><summary>¿No tienes el móvil?</summary>
    <p class="hint">Puedes escribir aquí uno de tus códigos de recuperación (formato <code>ABCDE-FGHIJ</code>). Cada uno vale una sola vez.</p>
  </details>
  <p class="hint"><a href="/admin/entrar">Volver</a></p>
</div>
