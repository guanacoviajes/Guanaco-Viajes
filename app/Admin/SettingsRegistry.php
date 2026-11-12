<?php
declare(strict_types=1);

namespace App\Admin;

/**
 * Catálogo de los textos y ajustes editables de la web, agrupados por pantalla.
 * Cada entrada: clave => [etiqueta, tipo (text|textarea|url|image|number), valor por defecto, ayuda, obligatorio].
 * Si un texto se deja en blanco, la web usa el valor por defecto de aquí.
 */
final class SettingsRegistry
{
    /** @return array<string, array{title: string, intro: string, items: array<string, array>}> */
    public static function groups(): array
    {
        $t = static fn(string $label, string $def = '', string $help = '', bool $req = false): array => [$label, 'text', $def, $help, $req];
        $a = static fn(string $label, string $def = '', string $help = '', bool $req = false): array => [$label, 'textarea', $def, $help, $req];
        $u = static fn(string $label, string $def = '', string $help = ''): array => [$label, 'url', $def, $help, false];

        return [
            'contacto' => ['title' => 'Contacto y redes sociales', 'intro' => 'Los datos que ven los clientes en el pie, en Contacto y en la burbuja flotante.', 'items' => [
                'site.telefono'         => $t('Teléfono visible', '+34 600 53 88 85', 'Tal como quieres que se vea.', true),
                'site.whatsapp'         => $t('Número de WhatsApp', '34600538885', 'Solo números, con el prefijo del país y sin el +. Ejemplo: 34600538885.', true),
                'site.whatsapp_mensaje' => $t('Mensaje de WhatsApp ya escrito', 'Hola, me gustaría información sobre un viaje.', 'Aparece escrito cuando alguien abre WhatsApp desde la web.'),
                'site.email'            => $t('Correo de contacto', 'hola@guanacoviajes.es', 'El que ve el cliente en la web.', true),
                'site.email_reservas'   => $t('Correo de reservas', 'reservas@guanacoviajes.es', 'Para después de contratar. Ahora la web no lo usa.'),
                'social.instagram'      => $u('Instagram', '', 'Dirección completa del perfil (empieza por https://). Si lo dejas vacío, el icono no aparece.'),
                'social.facebook'       => $u('Facebook', '', 'Dirección completa de la página. Vacío = sin icono.'),
                'social.x'              => $u('X', '', 'Dirección completa del perfil. Vacío = sin icono.'),
            ]],
            'botones' => ['title' => 'Botones y franja inferior', 'intro' => 'A dónde llevan los botones principales y qué dice la franja que cierra las páginas.', 'items' => [
                'site.cta_url'    => $u('Destino de «Pide tu presupuesto» (menú, carrusel, Quiénes somos)', '/presupuesto', 'Normalmente /presupuesto. También puede ser /contacto.'),
                'franja.url'      => $u('Destino del botón de la franja inferior', '/contacto', 'Ahora lleva a Contacto.'),
                'franja.texto'    => $t('Franja inferior: texto', '¿Tienes una idea de viaje?'),
                'franja.enfasis'  => $t('Franja inferior: parte en cursiva', 'Cuéntanosla.'),
                'franja.boton'    => $t('Franja inferior: texto del botón', 'Comienza tu aventura'),
            ]],
            'inicio' => ['title' => 'Página de inicio', 'intro' => 'Los títulos y textos de cada bloque. El carrusel y los dos tipos de viaje se editan en «Inicio y carrusel».', 'items' => [
                'home.carrusel.segundos' => ['Segundos entre diapositivas (ordenador y tableta grande)', 'number', '3', 'Entre 2 y 15. Recomendado: 3.', false],
                'home.carrusel.segundos_movil' => ['Segundos entre diapositivas (móvil)', 'number', '4', 'Entre 2 y 15. Recomendado: 4: en el móvil también se puede deslizar con el dedo.', false],
                'home.tipos.kicker'      => $t('Tipos de viaje: etiqueta pequeña', 'Tipos de viaje'),
                'home.tipos.titulo'      => $t('Tipos de viaje: título', 'El viaje,'),
                'home.tipos.enfasis'     => $t('Tipos de viaje: parte en cursiva', 'a tu manera'),
                'home.destinos.kicker'   => $t('Destinos: etiqueta pequeña', 'Destinos'),
                'home.destinos.titulo'   => $t('Destinos: título', 'Elige por dónde'),
                'home.destinos.enfasis'  => $t('Destinos: parte en cursiva', 'empezar'),
                'home.destinos.boton'    => $t('Destinos: texto del botón', 'Ver todos los destinos'),
                'home.como.kicker'       => $t('Cómo funciona: etiqueta pequeña', 'Cómo funciona'),
                'home.como.titulo'       => $t('Cómo funciona: título', 'Cuéntanos tu idea.'),
                'home.como.enfasis'      => $t('Cómo funciona: parte en cursiva', 'Del resto, nos encargamos.'),
                'home.paso1.titulo'      => $t('Paso 1: título', 'Cuéntanos tu idea'),
                'home.paso1.texto'       => $t('Paso 1: texto', 'Un destino, una inquietud o una simple intuición.'),
                'home.paso2.titulo'      => $t('Paso 2: título', 'Diseñamos tu viaje a medida'),
                'home.paso2.texto'       => $t('Paso 2: texto', 'Una propuesta pensada para ti, con espacio para lo inesperado.'),
                'home.paso3.titulo'      => $t('Paso 3: título', 'Tú viajas'),
                'home.paso3.texto'       => $t('Paso 3: texto', 'Y vuelves con historias.'),
                'home.quien.kicker'      => $t('Quién está detrás: etiqueta pequeña', 'Quién está detrás'),
                'home.quien.texto'       => $a('Quién está detrás: frase', '', 'Una o dos frases sobre ti.'),
                'home.quien.boton'       => $t('Quién está detrás: texto del botón', 'Conoce nuestra historia'),
                'home.quien.foto'        => ['Tu foto viajando', 'image', '', 'Sale en «Quién está detrás» y en «Quiénes somos». Mejor vertical y solo tú.', false],
                'home.blog.kicker'       => $t('Blog en la home: etiqueta pequeña', 'Blog'),
                'home.blog.titulo'       => $t('Blog en la home: título', 'Historias de'),
                'home.blog.enfasis'      => $t('Blog en la home: parte en cursiva', 'viaje'),
            ]],
            'medida' => ['title' => 'Viajes a medida', 'intro' => 'Los textos de la página. El título y la foto de cabecera se editan en «Inicio y carrusel» (tipos de viaje).', 'items' => [
                'medida.intro'        => $a('Introducción (frase grande)', 'Creemos en los viajes que nacen de una inquietud, de una conversación, de un lugar que llevas tiempo queriendo descubrir o de una idea que todavía no sabes muy bien cómo convertir en realidad.'),
                'medida.intro2'       => $a('Introducción (frase pequeña)', 'No se trata de viajar sin pensar ni de renunciar a una buena planificación. Se trata de que el viaje esté pensado para ti, y no tú para el viaje.'),
                'medida.sit.kicker'   => $t('Dos maneras: etiqueta pequeña', '¿Cuál es tu caso?'),
                'medida.sit.titulo'   => $t('Dos maneras: título', 'Hay dos maneras de'),
                'medida.sit.enfasis'  => $t('Dos maneras: parte en cursiva', 'empezar tu viaje'),
                'medida.sit1.titulo'  => $t('Primera: título', 'Ya sé adónde quiero ir'),
                'medida.sit1.texto'   => $a('Primera: texto', 'Tienes el destino. Lo que buscas es recorrerlo a tu manera, sin la ruta de siempre.'),
                'medida.sit1.boton'   => $t('Primera: texto del botón', 'Comienza tu viaje'),
                'medida.sit2.titulo'  => $t('Segunda: título', 'Quiero descubrir algo distinto'),
                'medida.sit2.texto'   => $a('Segunda: texto', 'Viajar sin prisas, cambiar de rumbo si aparece algo inesperado y dejar espacio para lo que no se puede reservar con antelación.'),
                'medida.sit2.boton'   => $t('Segunda: texto del botón', 'Cuéntanos tu idea'),
            ]],
            'grupo' => ['title' => 'Viajes en grupo', 'intro' => 'Las salidas concretas se gestionan en «Viajes en grupo».', 'items' => [
                'grupo.intro'          => $a('Introducción', 'Creamos viajes en grupo para quienes disfrutan compartiendo el camino con otras personas. Propuestas que buscan ir un poco más allá de lo que todo el mundo hace.'),
                'grupo.px.sub'         => $t('Texto de los huecos «Próximamente»', 'Destino y fechas por confirmar'),
                'grupo.cierre.texto'   => $t('Cierre: texto', '¿Prefieres un viaje solo para ti?'),
                'grupo.cierre.enfasis' => $t('Cierre: parte en cursiva', 'Lo diseñamos a medida.'),
                'grupo.cierre.boton'   => $t('Cierre: texto del botón', 'Ver viajes a medida'),
            ]],
            'quienes' => ['title' => 'Quiénes somos (cabecera y cierre)', 'intro' => 'El texto largo de la página se edita en «Páginas y legales».', 'items' => [
                'quienes.titular'        => $t('Titular', 'Hay animales que parecen hechos para vivir bajo unas reglas. Y luego está'),
                'quienes.enfasis'        => $t('Titular: parte en cursiva', 'el guanaco.'),
                'quienes.cierre'         => $t('Cierre: texto', 'Viaja con curiosidad. Muévete con libertad.'),
                'quienes.cierre.enfasis' => $t('Cierre: parte en cursiva', 'Vuelve con historias.'),
                'quienes.boton'          => $t('Cierre: texto del botón', 'Cuéntanos tu viaje'),
            ]],
            'paginas' => ['title' => 'Títulos de Destinos, Contacto y Blog', 'intro' => '', 'items' => [
                'destinos.titulo'   => $t('Destinos: título', 'Elige por dónde'),
                'destinos.enfasis'  => $t('Destinos: parte en cursiva', 'empezar'),
                'destinos.lead'     => $a('Destinos: frase de apoyo', 'Cada país es un punto de partida. El viaje lo diseñamos contigo.'),
                'contacto.titulo'   => $t('Contacto: título', '¿Tienes una duda?'),
                'contacto.enfasis'  => $t('Contacto: parte en cursiva', 'Escríbenos.'),
                'contacto.lead'     => $a('Contacto: frase de apoyo', 'Para una pregunta rápida, por el canal que prefieras. Si quieres que te diseñemos un viaje, pide tu presupuesto.'),
                'blog.titulo'       => $t('Blog: título', 'Historias de'),
                'blog.enfasis'      => $t('Blog: parte en cursiva', 'viaje'),
                'blog.vacio.titulo' => $t('Blog sin artículos: título', 'Todavía no hay historias por aquí.'),
                'blog.vacio.texto'  => $a('Blog sin artículos: texto', 'Estamos preparando los primeros artículos. Mientras tanto, cuéntanos tu idea de viaje.'),
            ]],
            'formulario' => ['title' => 'Primer paso del formulario', 'intro' => 'La pregunta que se ve en la home y en las fichas de país.', 'items' => [
                'form.paso1.pregunta' => $t('Pregunta', '¿A dónde te gustaría ir?'),
                'form.paso1.ayuda'    => $t('Ayuda', 'Puedes elegir más de uno.'),
            ]],
            'seo' => ['title' => 'Buscadores (SEO) de las páginas principales', 'intro' => 'Lo que aparece en Google al buscar la web. Cada país, artículo y página tiene el suyo en su propia ficha. Consejo: título de 50 a 60 caracteres y descripción de 120 a 155; la expresión «viajes a medida» al principio de los más importantes.', 'items' => [
                'seo.home.title'       => $t('Inicio · título', 'Viajes a medida por Sudamérica y Europa · Guanaco Viajes', '', true),
                'seo.home.description' => $a('Inicio · descripción', 'Diseñamos viajes a medida y en grupo por Sudamérica y Europa. Cuéntanos tu idea y la convertimos en un viaje pensado para ti.'),
                'seo.medida.title'       => $t('Viajes a medida · título', 'Viajes personalizados a medida · Guanaco Viajes'),
                'seo.medida.description' => $a('Viajes a medida · descripción', 'Diseñamos viajes a medida por Sudamérica y Europa. Tú traes la idea, aunque sea solo una inquietud, y la convertimos en un viaje pensado para ti.'),
                'seo.grupo.title'       => $t('Viajes en grupo · título', 'Viajes en grupo con personalidad · Guanaco Viajes'),
                'seo.grupo.description' => $a('Viajes en grupo · descripción', 'Salidas en grupo con propuestas diferentes, cuidadas y con personalidad, para compartir el camino con otras personas.'),
                'seo.destinos.title'       => $t('Destinos · título', 'Destinos: Sudamérica y Europa a medida · Guanaco Viajes'),
                'seo.destinos.description' => $a('Destinos · descripción', 'Destinos de Sudamérica y Europa para viajar a medida. Elige un país y diseñamos el viaje contigo.'),
                'seo.blog.title'       => $t('Blog · título', 'Blog de viajes: historias e ideas · Guanaco Viajes'),
                'seo.blog.description' => $a('Blog · descripción', 'Historias, ideas y rincones de Sudamérica y Europa para viajar con curiosidad, contados por quien los ha recorrido.'),
                'seo.presupuesto.title'       => $t('Presupuesto · título', 'Pide tu presupuesto de viaje a medida · Guanaco Viajes'),
                'seo.presupuesto.description' => $a('Presupuesto · descripción', 'Cuéntanos tu idea en cinco pasos cortos y diseñamos tu viaje a medida por Sudamérica o Europa.'),
                'seo.contacto.title'       => $t('Contacto · título', 'Contacto · Guanaco Viajes'),
                'seo.contacto.description' => $a('Contacto · descripción', 'Escríbenos por WhatsApp o correo, o déjanos un mensaje. Para un viaje a medida, pide tu presupuesto.'),
                'seo.google_verification' => $t('Código de verificación de Google Search Console', '', 'Solo el valor del campo «content» de la etiqueta meta que te da Google (no hace falta si verificas por DNS en Hostinger).'),
                'seo.bing_verification'   => $t('Código de verificación de Bing Webmaster Tools', '', 'Solo el valor «content» de la etiqueta msvalidate.01. Opcional.'),
            ]],
            'pie' => ['title' => 'Pie de página', 'intro' => '', 'items' => [
                'footer.lema'    => $t('Lema', 'Viaja con curiosidad. Muévete con libertad. Vuelve con historias.'),
                'footer.agencia' => $a('Línea sobre la agencia (obligatoria)', 'Guanaco Viajes es la marca comercial de Daniel Conti, agente de viajes colaborador de NADIDU SL (agencia de viajes · registro C.IAN-118115-2).', 'Indica que eres agente colaborador de Nadidú. Cámbiala solo si Nadidú te lo indica.', true),
            ]],
        ];
    }

    /** @return array<string,array> clave => definición, de todos los grupos */
    public static function flat(): array
    {
        $out = [];
        foreach (self::groups() as $g) {
            foreach ($g['items'] as $k => $d) {
                $out[$k] = $d;
            }
        }
        return $out;
    }
}
