<?php /** @var string $csrf @var array $old */ ?>
<div class="auth-card">
  <img class="auth-logo" src="/assets/img/logo/guanaco-completo-claro.svg" width="1000" height="470" alt="Guanaco Viajes">
  <h1>Acceso al panel</h1>
  <form method="post" action="/admin/entrar" novalidate>
    <?= $csrf ?>
    <label class="f">Correo<input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" autocomplete="username" required autofocus></label>
    <label class="f">Contraseña<input type="password" name="password" autocomplete="current-password" required></label>
    <button class="btn" type="submit">Continuar</button>
  </form>
  <p class="hint">Después te pediremos el código de tu aplicación de verificación.</p>
</div>
