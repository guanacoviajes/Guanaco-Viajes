<?php /** @var string $csrf @var string $secret @var ?string $qr */ ?>
<div class="auth-card wide">
  <h1>Activar la verificación en dos pasos</h1>
  <ol class="steps">
    <li>Abre una aplicación de verificación en tu móvil (Google Authenticator, Authy, 1Password, Microsoft Authenticator…).</li>
    <li>Añade una cuenta nueva <?= $qr ? 'escaneando este código' : 'con esta clave' ?>:
      <?php if ($qr): ?><div class="qr" role="img" aria-label="Código QR para la aplicación de verificación"><?= $qr ?></div>
      <p class="hint">¿No puedes escanearlo? Escribe esta clave a mano:</p><?php endif; ?>
      <code class="secret"><?= e($secret) ?></code>
    </li>
    <li>Escribe el código de 6 dígitos que aparece para confirmar.</li>
  </ol>
  <form method="post" action="/admin/activar" novalidate>
    <?= $csrf ?>
    <label class="f">Código de 6 dígitos<input class="code" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="8" required autofocus></label>
    <button class="btn" type="submit">Activar y entrar</button>
  </form>
  <p class="hint">Si el código no funciona, comprueba que la hora de tu móvil está en automático.</p>
</div>
