<?php /** @var array $c @var list<array> $rows @var bool $revisado @var string $csrf */ ?>
<div class="page-head"><div><h1><?= e($c['plural']) ?></h1><p class="sub"><?= e($c['intro']) ?></p></div></div>
<div class="table-wrap"><table class="t">
  <thead><tr><th>Página</th><th>Última edición</th><th></th></tr></thead>
  <tbody><?php foreach ($rows as $row): ?>
    <tr><td><a class="row" href="/admin/paginas/<?= (int) $row['id'] ?>"><?= $c['columns'][0][1]($row) ?></a></td><td class="sm"><?= $c['columns'][1][1]($row) ?></td><td class="num"><a class="btn ghost sm" href="/admin/paginas/<?= (int) $row['id'] ?>">Editar</a></td></tr>
  <?php endforeach; ?></tbody>
</table></div>
<div class="card">
  <h2>Revisión de los textos legales</h2>
  <p>Los cuatro textos legales parten de los de NADIDU SL y <b>deben ser revisados por Nadidú (y sus abogados) antes de publicar la web</b>. Mientras no lo marques, el Panel te lo recordará.</p>
  <form method="post" action="/admin/paginas/revision">
    <?= $csrf ?>
    <input type="hidden" name="revisado" value="0">
    <label class="check"><input type="checkbox" name="revisado" value="1"<?= $revisado ? ' checked' : '' ?>> Nadidú ha revisado y aprobado los textos legales</label>
    <button class="btn" type="submit">Guardar</button>
  </form>
</div>
