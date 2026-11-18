<?php
/** @var array $pagina */
use App\Core\Text;
use App\Repositories\Settings;
$S = static fn(string $k, string $d = ''): string => Settings::get($k, $d);
$foto = $S('home.quien.foto');
$foto2 = $S('quienes.foto2');
// El texto se reparte en dos bloques: la primera sección con la primera foto (a la izquierda) y el resto con la segunda (a la derecha).
$secciones = Text::sections((string) $pagina['cuerpo']);
$bloque1 = $secciones[0] ?? '';
$bloque2 = implode("\n\n", array_slice($secciones, 1));
?>
<?= \App\Core\View::partial('partials/crumbs', ['items' => [['Quiénes somos', null]]]) ?>
<section class="qhero">
  <div class="wrap">
    <div class="kick">Quiénes somos</div>
    <h1><?= e($S('quienes.titular', 'Hay animales que parecen hechos para vivir bajo unas reglas. Y luego está')) ?> <em><?= e($S('quienes.enfasis', 'el guanaco.')) ?></em></h1>
  </div>
</section>
<section class="sec sec-tight qsec">
  <div class="wrap split<?= $foto === '' ? ' sin-foto' : '' ?>">
    <?php if ($foto !== ''): ?><div class="foto-real"><?= img($foto, 'Daniel Conti viajando', ['sizes' => '(max-width: 820px) 100vw, 45vw']) ?></div><?php endif; ?>
    <div class="qtext"><?= Text::render($bloque1) ?></div>
  </div>
</section>
<?php if ($bloque2 !== ''): ?>
<section class="sec sec-tight qsec">
  <div class="wrap split split-inv<?= $foto2 === '' ? ' sin-foto' : '' ?>">
    <div class="qtext"><?= Text::render($bloque2) ?></div>
    <?php if ($foto2 !== ''): ?><div class="foto-real"><?= img($foto2, $S('quienes.foto2.alt', 'Daniel Conti viajando'), ['sizes' => '(max-width: 820px) 100vw, 45vw']) ?></div><?php endif; ?>
  </div>
</section>
<?php endif; ?>
<section class="signoff">
  <div class="wrap">
    <p><?= e($S('quienes.cierre', 'Viaja con curiosidad. Muévete con libertad.')) ?> <em><?= e($S('quienes.cierre.enfasis', 'Vuelve con historias.')) ?></em></p>
    <a class="btn b1" href="<?= e($S('site.cta_url', '/presupuesto')) ?>"><?= e($S('quienes.boton', 'Cuéntanos tu idea')) ?></a>
  </div>
</section>
