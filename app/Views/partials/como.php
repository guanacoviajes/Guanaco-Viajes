<?php
/** @var array $paises  @var list<string>|null $pre  @var string|null $titulo  @var string|null $enfasis */
use App\Repositories\Settings;

$S = static fn(string $k, string $d = ''): string => Settings::get($k, $d);
$titulo  = $titulo  ?? $S('home.como.titulo', 'Cuéntanos tu idea.');
$enfasis = $enfasis ?? $S('home.como.enfasis', 'Del resto, nos encargamos.');
?>
<section class="sec como" aria-labelledby="como-t">
  <div class="wrap grid2">
    <div>
      <div class="kick"><?= e($S('home.como.kicker', 'Cómo funciona')) ?></div>
      <h2 id="como-t"><?= e($titulo) ?> <em><?= e($enfasis) ?></em></h2>
      <ol class="pasos">
        <?php for ($n = 1; $n <= 3; $n++): ?>
        <li><span class="n"><?= $n ?></span><div>
          <h3><?= e($S("home.paso$n.titulo")) ?></h3>
          <p><?= e($S("home.paso$n.texto")) ?></p>
        </div></li>
        <?php endfor; ?>
      </ol>
    </div>
    <?= \App\Core\View::partial('partials/form-step1', ['paises' => $paises, 'pre' => $pre ?? []]) ?>
  </div>
</section>
