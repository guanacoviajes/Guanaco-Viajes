<?php
/**
 * @var array{sudamerica: list<array>, europa: list<array>} $paises
 * @var list<array> $intereses
 * @var array<string,string> $meses
 * @var array<string,mixed> $old
 * @var array<string,string> $errors
 * @var list<string> $pre
 * @var int $start
 * @var ?string $aviso
 * @var string $csrf
 * @var string $timeToken
 */
use App\Services\FormOptions;

$cont = ['sudamerica' => 'Sudamérica', 'europa' => 'Europa'];
$o    = $old;
$list = static fn(string $k): array => is_array($o[$k] ?? null) ? $o[$k] : [];
$val  = static fn(string $k, string $d = ''): string => is_string($o[$k] ?? null) ? $o[$k] : $d;
$destSel = $o ? $list('destinos') : $pre;
$mesSel  = $list('meses');
$intSel  = $list('intereses');
$adultos = $val('adultos', '2');
$menores = $val('menores', '0');
$canal   = $val('canal', 'correo');
$durSel  = $val('duracion');
$flex    = !empty($o['flexible']);
$cons    = !empty($o['consentimiento']);
$err = static function (string $k) use ($errors): string {
    return isset($errors[$k])
        ? '<p class="err on" role="alert" id="e-' . e($k) . '">' . e($errors[$k]) . '</p>'
        : '<p class="err" role="alert" id="e-' . e($k) . '"></p>';
};
$bad = static fn(string $k): string => isset($errors[$k]) ? ' aria-invalid="true"' : '';
$steps = [1 => 'Destino', 2 => 'Fechas', 3 => 'Viajeros', 4 => 'Tu idea', 5 => 'Contacto'];
?>
<noscript><style>.pr-card .step{display:block !important}.pr-card .pr-nav .btn-next,.pr-card .pr-nav .back,.pr-card .seg,.pr-card .cnt{display:none !important}.pr-card .chip input,.pr-card .tg input{position:static;opacity:1;width:auto;height:auto;margin:0 8px 0 0}.pr-card .chk input{position:static;opacity:1;width:18px;height:18px;margin:2px 10px 0 0}.pr-card .chk i,.pr-card .tg i{display:none}.pr-card .step+.step{margin-top:44px}.pr-card .btn-send{display:inline-block !important;margin-left:0}.pr-card .sp button{display:none}</style></noscript>

<?= \App\Core\View::partial('partials/crumbs', ['items' => [['Pide tu presupuesto', null]]]) ?>
<div class="pr">
  <div class="wrap pr-grid">
    <aside class="pr-side">
      <div class="kick">Pide tu presupuesto</div>
      <h1>Cuéntanos tu idea. <em>Del resto, nos encargamos.</em></h1>
      <p>Cinco pasos cortos. Con lo que nos cuentes, diseñamos tu viaje a medida.</p>
      <ol class="vsteps" aria-hidden="true">
        <?php foreach ($steps as $n => $t): ?><li data-v="<?= $n ?>"<?= $n === 1 ? ' class="on"' : '' ?>><b><?= $n ?></b><?= e($t) ?></li><?php endforeach; ?>
      </ol>
    </aside>

    <form class="pr-card" id="pform" action="/presupuesto/enviar" method="post" novalidate data-start="<?= (int) $start ?>">
      <?= $csrf ?>
      <input type="hidden" name="t" value="<?= e($timeToken) ?>">
      <div class="hp" aria-hidden="true"><label>Sitio web <input type="text" name="sitio_web" tabindex="-1" autocomplete="off"></label></div>

      <?php if ($aviso): ?><p class="aviso-form" role="alert"><?= e($aviso) ?></p><?php endif; ?>

      <div class="seg" id="seg" aria-hidden="true"><i class="on"></i><i></i><i></i><i></i><i></i></div>
      <div class="cnt" id="cnt" aria-live="polite">Paso 1 de 5</div>

      <!-- 1 · Destino -->
      <fieldset class="step" data-s="1">
        <legend class="q">¿A dónde te gustaría ir?</legend>
        <div class="hint" id="h1">Puedes elegir más de uno.</div>
        <?php foreach ($cont as $key => $label): ?>
        <div class="grp"><?= e($label) ?></div>
        <div class="chips">
          <?php foreach ($paises[$key] as $p): ?>
          <label class="chip<?= in_array($p['slug'], $destSel, true) ? ' on' : '' ?>"><input type="checkbox" name="destinos[]" value="<?= e($p['slug']) ?>" data-cont="<?= e($key) ?>"<?= in_array($p['slug'], $destSel, true) ? ' checked' : '' ?>><span><?= e($p['nombre']) ?></span></label>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <div class="chips" style="margin-top:22px"><label class="chip n<?= in_array('aun-no-lo-se', $destSel, true) ? ' on' : '' ?>"><input type="checkbox" name="destinos[]" value="aun-no-lo-se"<?= in_array('aun-no-lo-se', $destSel, true) ? ' checked' : '' ?>><span>Aún no lo sé</span></label></div>
        <?= $err('destinos') ?>
      </fieldset>

      <!-- 2 · Fechas -->
      <fieldset class="step" data-s="2">
        <legend class="q">¿Cuándo te gustaría viajar?</legend>
        <div class="hint">Un mes aproximado nos basta. Puedes elegir más de uno.</div>
        <div class="grp">Mes</div>
        <div class="chips" id="meses">
          <?php foreach ($meses as $v => $l): ?>
          <label class="chip<?= in_array($v, $mesSel, true) ? ' on' : '' ?>"><input type="checkbox" name="meses[]" value="<?= e($v) ?>"<?= in_array($v, $mesSel, true) ? ' checked' : '' ?>><span><?= e($l) ?></span></label>
          <?php endforeach; ?>
        </div>
        <div class="grp">Duración aproximada</div>
        <div class="chips" id="duraciones">
          <?php foreach (FormOptions::DURACIONES as $v => $l): ?>
          <label class="chip<?= $durSel === $v ? ' on' : '' ?>"<?= $v === 'finde' ? ' id="chipFinde"' : '' ?>><input type="radio" name="duracion" value="<?= e($v) ?>"<?= $durSel === $v ? ' checked' : '' ?>><span><?= e($l) ?></span></label>
          <?php endforeach; ?>
        </div>
        <?= $err('duracion') ?>
        <label class="tg<?= $flex ? ' on' : '' ?>"><input type="checkbox" name="flexible" value="1"<?= $flex ? ' checked' : '' ?>><i></i>Soy flexible con las fechas</label>
        <?= $err('fechas') ?>
      </fieldset>

      <!-- 3 · Viajeros -->
      <fieldset class="step" data-s="3">
        <legend class="q">¿Quiénes viajáis?</legend>
        <div class="hint">Cuéntanos cuántos sois.</div>
        <div class="cnt-row"><label class="l" for="adultos">Adultos<small>Desde 18 años</small></label>
          <div class="sp"><button type="button" data-t="adultos" data-d="-1" aria-label="Quitar un adulto">−</button><input type="number" id="adultos" name="adultos" min="1" max="20" value="<?= e($adultos) ?>"><button type="button" data-t="adultos" data-d="1" aria-label="Añadir un adulto">+</button></div></div>
        <div class="cnt-row"><label class="l" for="menores">Menores<small>De 0 a 17 años</small></label>
          <div class="sp"><button type="button" data-t="menores" data-d="-1" aria-label="Quitar un menor">−</button><input type="number" id="menores" name="menores" min="0" max="10" value="<?= e($menores) ?>"><button type="button" data-t="menores" data-d="1" aria-label="Añadir un menor">+</button></div></div>
        <?= $err('viajeros') ?>
        <div class="fld ages" id="ages"<?= (int) $menores > 0 ? '' : ' hidden' ?>><label for="edades">Edades de los menores</label><div class="in"><input id="edades" name="edades_menores" maxlength="120" placeholder="Por ejemplo: 4 y 9 años" value="<?= e($val('edades_menores')) ?>"></div><?= $err('edades') ?></div>
      </fieldset>

      <!-- 4 · Tu idea -->
      <fieldset class="step" data-s="4">
        <legend class="q">Cuéntanos tu idea</legend>
        <div class="hint">Un lugar, una inquietud, una idea a medias. Puedes dejarlo en blanco y lo hablamos después.</div>
        <?php if ($intereses): ?>
        <div class="grp">¿Qué te apetece? (opcional)</div>
        <div class="chips">
          <?php foreach ($intereses as $i): $on = in_array((string) $i['id'], $intSel, true); ?>
          <label class="chip<?= $on ? ' on' : '' ?>"><input type="checkbox" name="intereses[]" value="<?= (int) $i['id'] ?>"<?= $on ? ' checked' : '' ?>><span><?= e($i['nombre']) ?></span></label>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <div class="fld" style="margin-top:26px"><label for="mensaje">Cuéntanos más</label><div class="in ta"><textarea id="mensaje" name="mensaje" maxlength="2000" placeholder="Escribe aquí lo que tengas en la cabeza"><?= e($val('mensaje')) ?></textarea></div><?= $err('mensaje') ?></div>
      </fieldset>

      <!-- 5 · Contacto -->
      <fieldset class="step" data-s="5">
        <legend class="q">¿Cómo te escribimos?</legend>
        <div class="hint">Para enviarte tu propuesta a medida.</div>
        <div class="fld"><label for="nombre">Nombre</label><div class="in<?= isset($errors['nombre']) ? ' bad' : '' ?>"><input id="nombre" name="nombre" maxlength="100" autocomplete="name" placeholder="Tu nombre" value="<?= e($val('nombre')) ?>"<?= $bad('nombre') ?> required></div><?= $err('nombre') ?></div>
        <div class="fld"><label for="email">Correo electrónico</label><div class="in<?= isset($errors['email']) ? ' bad' : '' ?>"><input id="email" type="email" name="email" maxlength="150" autocomplete="email" placeholder="tu@correo.com" value="<?= e($val('email')) ?>"<?= $bad('email') ?> required></div><?= $err('email') ?></div>
        <div class="fld"><label for="telefono">Teléfono (opcional)</label><div class="in<?= isset($errors['telefono']) ? ' bad' : '' ?>"><input id="telefono" type="tel" name="telefono" maxlength="20" autocomplete="tel" placeholder="+34 600 00 00 00" value="<?= e($val('telefono')) ?>"<?= $bad('telefono') ?>></div><?= $err('telefono') ?></div>
        <div class="grp" style="margin-top:6px">Prefiero que me escribáis por</div>
        <div class="chips" id="canales">
          <?php foreach (FormOptions::CANALES as $v => $l): ?>
          <label class="chip<?= $canal === $v ? ' on' : '' ?>"><input type="radio" name="canal" value="<?= e($v) ?>"<?= $canal === $v ? ' checked' : '' ?>><span><?= e($l) ?></span></label>
          <?php endforeach; ?>
        </div>
        <label class="chk" style="margin-top:26px"><input type="checkbox" name="consentimiento" value="1"<?= $cons ? ' checked' : '' ?><?= $bad('consentimiento') ?>><i></i><span>He leído y acepto la <a href="/politica-de-privacidad" target="_blank" rel="noopener">política de privacidad</a>. Responsable del tratamiento: NADIDU SL, agencia de viajes con registro C.IAN-118115-2. Mis datos se usarán para preparar mi propuesta y responderme.</span></label>
        <?= $err('consentimiento') ?>
      </fieldset>

      <div class="pr-nav" id="prnav">
        <button class="back hide" id="back" type="button">Anterior</button>
        <button class="btn b1 btn-next" id="next" type="button">Siguiente</button>
        <button class="btn b1 btn-send" id="send" type="submit">Enviar mi idea</button>
      </div>
    </form>
  </div>
</div>
