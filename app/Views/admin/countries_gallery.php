<?php /** @var array $row @var list<array> $photos @var string $csrf */ ?>
<div class="card" id="galeria">
  <h2>Galería de fotos (opcional)</h2>
  <p class="hint">Hasta 6 fotos. Si hay menos de 2, la galería no aparece en la ficha del país. Hoy hay <b><?= count($photos) ?></b>.</p>
  <?php if ($photos): ?>
  <div class="thumbs" style="margin:16px 0">
    <?php foreach ($photos as $p): ?>
    <div class="thumb">
      <img src="/<?= e($p['archivo']) ?>" alt="<?= e($p['alt']) ?>" loading="lazy">
      <div class="b"><?= e($p['alt'] ?: '(sin descripción)') ?>
        <form method="post" action="/admin/destinos/<?= (int) $row['id'] ?>/fotos/<?= (int) $p['id'] ?>/eliminar" data-confirm="¿Quitar esta foto de la galería?" style="margin-top:8px"><?= $csrf ?><button class="btn danger sm" type="submit">Quitar</button></form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <?php if (count($photos) < 6): ?>
  <form method="post" action="/admin/destinos/<?= (int) $row['id'] ?>/fotos" enctype="multipart/form-data" data-once>
    <?= $csrf ?>
    <div class="row2">
      <label class="f">Foto<input type="file" name="foto" accept="image/jpeg,image/png,image/webp" required></label>
      <label class="f">Descripción de la foto<input type="text" name="alt" maxlength="200"></label>
    </div>
    <label class="check"><input type="hidden" name="grade" value="0"><input type="checkbox" name="grade" value="1" checked> Aplicar el tratamiento de color de la marca</label>
    <button class="btn" type="submit">Añadir a la galería</button>
  </form>
  <?php endif; ?>
</div>
