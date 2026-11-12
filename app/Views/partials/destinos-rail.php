<?php
/** @var array{sudamerica: list<array>, europa: list<array>} $paises  @var string $activo */
$activo = $activo ?? 'sudamerica';
$cont = ['sudamerica' => 'Sudamérica', 'europa' => 'Europa'];
?>
<div class="tabs" role="tablist" aria-label="Continentes" data-tabs="destinos">
  <?php foreach ($cont as $key => $label): $on = $key === $activo; ?>
  <button type="button" class="tab<?= $on ? ' on' : '' ?>" role="tab" id="dtab-<?= $key ?>"
          aria-selected="<?= $on ? 'true' : 'false' ?>" aria-controls="dset-<?= $key ?>" data-s="<?= $key ?>"<?= $on ? '' : ' tabindex="-1"' ?>><?= e($label) ?></button>
  <?php endforeach; ?>
</div>
<?php foreach ($cont as $key => $label): $on = $key === $activo; ?>
<div class="set<?= $on ? ' on' : '' ?>" role="tabpanel" id="dset-<?= $key ?>" aria-labelledby="dtab-<?= $key ?>" data-s="<?= $key ?>"<?= $on ? '' : ' hidden' ?>>
  <div class="rail">
    <button type="button" class="arr l hide" aria-label="Destinos anteriores"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></button>
    <button type="button" class="arr r hide" aria-label="Más destinos"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg></button>
    <div class="cards">
      <?php foreach ($paises[$key] as $j => $p): ?>
      <a class="card<?= $j === 0 ? ' x' : '' ?>" href="/destinos/<?= e($key) ?>/<?= e($p['slug']) ?>">
        <span class="ph"><?= img($p['foto'], $p['foto_alt'], ['pos' => $p['foto_pos']]) ?></span>
        <span class="cn"><?= e($p['nombre']) ?></span>
        <span class="cp"><?= e($p['resumen']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endforeach; ?>
