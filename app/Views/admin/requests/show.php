<?php
/** @var array $r @var array<string,string> $resumen @var string $csrf */
use App\Repositories\Requests;
?>
<div class="page-head">
  <div><h1>Solicitud n.º <?= (int) $r['id'] ?></h1>
    <p class="sub"><?= $r['origen'] === 'presupuesto' ? 'Petición de presupuesto' : 'Mensaje de contacto' ?> · <?= e(date('d/m/Y H:i', strtotime($r['created_at']))) ?></p></div>
  <div class="actions"><a class="btn ghost" href="/admin/solicitudes">← Volver</a></div>
</div>

<div class="grid c2" style="align-items:start">
  <div class="card">
    <h2>Contacto</h2>
    <dl class="detail">
      <dt>Nombre</dt><dd><?= e($r['nombre']) ?></dd>
      <dt>Correo</dt><dd><a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a></dd>
      <?php if ($r['telefono'] !== ''): $tel = preg_replace('/[^0-9+]/', '', $r['telefono']); ?>
      <dt>Teléfono</dt><dd><a href="tel:<?= e($tel) ?>"><?= e($r['telefono']) ?></a> · <a href="https://wa.me/<?= e(ltrim($tel, '+')) ?>" target="_blank" rel="noopener">WhatsApp</a></dd>
      <?php endif; ?>
      <dt>Prefiere</dt><dd><?= e(['correo' => 'Correo', 'whatsapp' => 'WhatsApp', 'llamada' => 'Llamada'][$r['canal']] ?? '') ?></dd>
    </dl>
    <h2 style="margin-top:22px">Lo que ha contado</h2>
    <dl class="detail">
      <?php foreach ($resumen as $k => $v): ?><dt><?= e($k) ?></dt><dd class="pre"><?= e($v) ?></dd><?php endforeach; ?>
      <?php if (!$resumen && $r['mensaje']): ?><dt>Mensaje</dt><dd class="pre"><?= e($r['mensaje']) ?></dd><?php endif; ?>
    </dl>
  </div>

  <div>
    <div class="card">
      <h2>Estado</h2>
      <form method="post" action="/admin/solicitudes/<?= (int) $r['id'] ?>/estado" class="actions">
        <?= $csrf ?>
        <select name="estado" aria-label="Estado" style="max-width:220px">
          <?php foreach (Requests::ESTADOS as $k => $l): ?><option value="<?= e($k) ?>"<?= $r['estado'] === $k ? ' selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?>
        </select>
        <button class="btn" type="submit">Guardar</button>
      </form>
    </div>
    <div class="card">
      <h2>Notas internas</h2>
      <form method="post" action="/admin/solicitudes/<?= (int) $r['id'] ?>/notas">
        <?= $csrf ?>
        <label class="f"><span class="hint">Solo las ves tú. El cliente no las recibe.</span>
          <textarea name="notas" maxlength="4000"><?= e((string) $r['notas']) ?></textarea></label>
        <button class="btn" type="submit">Guardar notas</button>
      </form>
    </div>
    <div class="card">
      <h2>Correos</h2>
      <p>Aviso para ti: <b><?= $r['aviso_enviado'] ? 'enviado' : 'no enviado' ?></b> · Acuse al cliente: <b><?= $r['acuse_enviado'] ? 'enviado' : 'no enviado' ?></b></p>
      <?php if (!$r['aviso_enviado'] || !$r['acuse_enviado']): ?>
      <form method="post" action="/admin/solicitudes/<?= (int) $r['id'] ?>/reenviar"><?= $csrf ?><button class="btn ghost" type="submit">Reenviar los pendientes</button></form>
      <?php endif; ?>
    </div>
    <div class="card">
      <h2>Datos personales</h2>
      <p class="hint">Consentimiento dado el <?= e(date('d/m/Y H:i', strtotime($r['consent_at']))) ?>:<br>«<?= e($r['consent_texto']) ?>»</p>
      <form method="post" action="/admin/solicitudes/<?= (int) $r['id'] ?>/eliminar" data-confirm="¿Eliminar esta solicitud para siempre? No se puede deshacer.">
        <?= $csrf ?><button class="btn danger" type="submit">Eliminar esta solicitud</button>
      </form>
    </div>
  </div>
</div>
