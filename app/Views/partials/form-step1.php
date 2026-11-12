<?php
/**
 * Primer paso del formulario de presupuesto (se usa en la home, fichas y tipos de viaje).
 * @var array{sudamerica: list<array>, europa: list<array>} $paises
 * @var list<string> $pre  slugs de países marcados de salida
 */
use App\Repositories\Settings;

$pre = $pre ?? [];
?>
<form class="form" id="home-form" action="/presupuesto" method="get" novalidate>
  <div class="seg" aria-hidden="true"><i class="on"></i><i></i><i></i><i></i><i></i></div>
  <fieldset class="fs">
    <legend class="q"><?= e(Settings::get('form.paso1.pregunta', '¿A dónde te gustaría ir?')) ?></legend>
    <div class="hint" id="hf-hint"><?= e(Settings::get('form.paso1.ayuda', 'Puedes elegir más de uno.')) ?></div>
    <div class="chips" role="group" aria-describedby="hf-hint">
      <?php foreach (['sudamerica', 'europa'] as $key): foreach ($paises[$key] as $p): $on = in_array($p['slug'], $pre, true); ?>
      <button type="button" class="chip" aria-pressed="<?= $on ? 'true' : 'false' ?>" data-pais="<?= e($p['slug']) ?>"><?= e($p['nombre']) ?></button>
      <?php endforeach; endforeach; ?>
      <button type="button" class="chip n" aria-pressed="false" data-pais="aun-no-lo-se">Aún no lo sé</button>
    </div>
  </fieldset>
  <p class="err" id="hf-err" role="alert">Elige al menos una opción para continuar.</p>
  <button type="submit" class="btn b1">Continuar</button>
</form>
