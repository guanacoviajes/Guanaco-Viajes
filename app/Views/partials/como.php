<?php
/** @var array $paises  @var list<string>|null $pre  @var string|null $titulo  @var string|null $enfasis */
use App\Repositories\Settings;

$S = static fn(string $k, string $d = ''): string => Settings::get($k, $d);
$titulo  = $titulo  ?? $S('home.como.titulo', 'Cuéntanos tu idea.');
$enfasis = $enfasis ?? $S('home.como.enfasis', 'Del resto, nos encargamos.');
?>
<section class="sec como" id="disena-tu-viaje" aria-labelledby="como-t">
  <div class="wrap grid2">
    <div>
      <div class="kick"><?= e($S('home.como.kicker', 'Cómo funciona')) ?></div>
      <h2 id="como-t"><?= e($titulo) ?> <em><?= e($enfasis) ?></em></h2>
    </div>
    <?= \App\Core\View::partial('partials/form-step1', ['paises' => $paises, 'pre' => $pre ?? []]) ?>
  </div>
</section>
