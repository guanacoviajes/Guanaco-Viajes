<?php
/** @var array $pagina */
use App\Core\Text;
use App\Repositories\Settings;
$S = static fn(string $k, string $d = ''): string => Settings::get($k, $d);
$foto = $S('home.quien.foto');
?>
<?= \App\Core\View::partial('partials/crumbs', ['items' => [['Quiénes somos', null]]]) ?>
<section class="qhero">
  <div class="wrap">
    <div class="kick">Quiénes somos</div>
    <h1><?= e($S('quienes.titular', 'Hay animales que parecen hechos para vivir bajo unas reglas. Y luego está')) ?> <em><?= e($S('quienes.enfasis', 'el guanaco.')) ?></em></h1>
  </div>
</section>
<section class="sec sec-tight">
  <div class="wrap split<?= $foto === '' ? ' sin-foto' : '' ?>">
    <?php if ($foto !== ''): ?><div class="foto-real"><?= img($foto, 'Daniel Conti viajando') ?></div><?php endif; ?>
    <div class="qtext"><?= Text::render($pagina['cuerpo']) ?></div>
  </div>
</section>
<section class="signoff">
  <div class="wrap">
    <p><?= e($S('quienes.cierre', 'Viaja con curiosidad. Muévete con libertad.')) ?> <em><?= e($S('quienes.cierre.enfasis', 'Vuelve con historias.')) ?></em></p>
    <a class="btn b1" href="/presupuesto"><?= e($S('quienes.boton', 'Comienza tu aventura')) ?></a>
  </div>
</section>
