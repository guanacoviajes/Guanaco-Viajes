<?php /** @var string $csrf @var array $old */ ?>
<div class="auth-card">
  <img class="auth-logo" src="/assets/img/logo/guanaco-completo-claro.svg" width="1000" height="470" alt="Guanaco Viajes">
  <h1>Crear el acceso al panel</h1>
  <p class="hint">Esta pantalla solo existe hasta que se crea el primer usuario. Necesitas el <b>código de instalación</b> que pusiste en <code>config/config.php</code>.</p>
  <form method="post" action="/admin/instalar" novalidate>
    <?= $csrf ?>
    <label class="f">Código de instalación<input type="password" name="token" autocomplete="off" required></label>
    <label class="f">Tu correo<input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" autocomplete="username" required></label>
    <label class="f">Contraseña <small>(mínimo 12 caracteres; mejor una frase larga)</small><input type="password" name="password" autocomplete="new-password" minlength="12" required></label>
    <label class="f">Repite la contraseña<input type="password" name="password2" autocomplete="new-password" minlength="12" required></label>
    <button class="btn" type="submit">Crear acceso</button>
  </form>
</div>
