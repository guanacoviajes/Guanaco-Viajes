<?php
/** @var array $paises  @var array $tipo */
use App\Repositories\Settings;
$S = static fn(string $k, string $d = ''): string => Settings::get($k, $d);
?>
<?= \App\Core\View::partial('partials/crumbs', ['items' => [['Tipos de viaje', null], ['Viajes a medida', null]]]) ?>
<header class="phero">
  <?= img($tipo['foto'], $tipo['foto_alt'], ['pos' => $tipo['foto_pos'], 'eager' => true]) ?>
  <div class="blk">
    <div class="kick">Tipos de viaje</div>
    <h1>Viajes a medida</h1>
    <p><?= e($tipo['descripcion']) ?></p>
  </div>
</header>

<section class="sec">
  <div class="wrap intro">
    <p class="big"><?= e($S('medida.intro', 'Creemos en los viajes que nacen de una inquietud, de una conversación, de un lugar que llevas tiempo queriendo descubrir o de una idea que todavía no sabes muy bien cómo convertir en realidad.')) ?></p>
    <p class="small"><?= e($S('medida.intro2', 'No se trata de viajar sin pensar ni de renunciar a una buena planificación. Se trata de que el viaje esté pensado para ti, y no tú para el viaje.')) ?></p>
  </div>
</section>

<section class="sec sec-tight" aria-labelledby="sit-t">
  <div class="wrap">
    <div class="kick"><?= e($S('medida.sit.kicker', '¿Cuál es tu caso?')) ?></div>
    <h2 id="sit-t"><?= e($S('medida.sit.titulo', 'Hay dos maneras de')) ?> <em><?= e($S('medida.sit.enfasis', 'empezar tu viaje')) ?></em></h2>
    <div class="situ">
      <article><span class="n">1</span><h3><?= e($S('medida.sit1.titulo', 'Ya sé adónde quiero ir')) ?></h3><p><?= e($S('medida.sit1.texto', 'Tienes el destino. Lo que buscas es recorrerlo a tu manera, sin la ruta de siempre.')) ?></p><a class="btn b2" href="/destinos"><?= e($S('medida.sit1.boton', 'Comienza tu viaje')) ?></a></article>
      <article><span class="n">2</span><h3><?= e($S('medida.sit2.titulo', 'Quiero descubrir algo distinto')) ?></h3><p><?= e($S('medida.sit2.texto', 'Viajar sin prisas, cambiar de rumbo si aparece algo inesperado y dejar espacio para lo que no se puede reservar con antelación.')) ?></p><a class="btn b2" href="/presupuesto?pais=aun-no-lo-se"><?= e($S('medida.sit2.boton', 'Cuéntanos tu idea')) ?></a></article>
    </div>
  </div>
</section>

<section class="sec sec-tight" aria-labelledby="dest-t">
  <div class="wrap">
    <div class="kick">Destinos</div>
    <h2 id="dest-t">Elige por dónde <em>empezar</em></h2>
    <?= \App\Core\View::partial('partials/destinos-rail', ['paises' => $paises]) ?>
  </div>
</section>

<?= \App\Core\View::partial('partials/como', ['paises' => $paises]) ?>
