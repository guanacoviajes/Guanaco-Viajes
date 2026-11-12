# SEO de Guanaco Viajes y guía para escribir el blog

Objetivo: que Guanaco Viajes aparezca cuando alguien busca **«viajes a medida»** (y sus variantes por país), y que el blog empuje esa posición en vez de ir por libre.

---

## 1. Estrategia en una frase

La web tiene tres capas, cada una con su búsqueda:

| Capa | Páginas | Qué busca la gente | Ejemplos de búsqueda |
|---|---|---|---|
| **Comercial (la que vende)** | Inicio, Viajes a medida, Destinos, ficha de cada país | Quiere contratar | «viajes a medida», «agencia de viajes a medida», «viaje a medida a Chile», «viajes personalizados Sudamérica» |
| **Informativa (la que atrae)** | Artículos del blog | Está planificando, aún no decide | «qué ver en Perú en 15 días», «ruta por la Patagonia», «cómo organizar un viaje a medida» |
| **Confianza** | Quiénes somos, legales, contacto | Quiere saber quién hay detrás | «Guanaco Viajes opiniones» |

El blog **no compite** con las páginas de país: las alimenta. Cada artículo enlaza a su ficha de país (o a Viajes a medida) y cierra con una invitación a pedir presupuesto.

## 2. Qué se ha trabajado (y ya está en la web)

**Títulos y descripciones**
- Inicio: «Viajes a medida por Sudamérica y Europa · Guanaco Viajes».
- Viajes a medida: «Viajes personalizados a medida · Guanaco Viajes».
- Cada país: «Viajes a medida a **{País}** · Guanaco Viajes», con descripción que empieza por «Diseñamos tu viaje a medida a {País}…».
- Cada continente: «Viajes a medida por Sudamérica / Europa».
- Todos únicos (comprobado: ninguna página repite título ni descripción) y dentro de longitud (título ≤ 70, descripción 70–165).
- **Los de las páginas principales los puedes cambiar tú** en *Textos y ajustes → Buscadores (SEO)*. Los de cada país y artículo, en su propia ficha.

**Datos estructurados (lo que entiende Google)**
- En todas las páginas: la agencia (`TravelAgency`: nombre, logo, correo, teléfono, redes si las rellenas, fundador) y el sitio web.
- Viajes a medida: servicio ofrecido en Sudamérica y Europa.
- Cada país: `TouristDestination`.
- Cada artículo: `BlogPosting` con autor (Daniel Conti), fecha de publicación, fecha de modificación e imagen.
- Migas de pan (`BreadcrumbList`) en todas las páginas interiores.

**Compartir y rastreo**
- Al compartir en WhatsApp, Instagram, X o Facebook sale título, descripción y foto correctos (incluye foto propia por página y tipo «artículo» en el blog).
- `sitemap.xml` automático con todas las páginas, países y artículos, con fecha de última modificación y fotos (ayuda a Google Imágenes).
- **Feed RSS del blog** (`/blog/feed.xml`): ayuda a que los artículos nuevos se descubran antes.
- `robots.txt` bloquea el panel y los envíos de formulario. Las páginas de blog filtradas o paginadas llevan `noindex` para no competir con el blog principal.
- Una sola dirección por página: si alguien entra por `www.guanacoviajes.es`, se redirige (301) a la dirección principal. Evita contenido duplicado.
- Etiquetas de verificación de **Google Search Console** y **Bing** editables desde el panel (no necesitas tocar código).

**Rendimiento y accesibilidad (también posicionan)**
- Sin librerías pesadas, fuentes propias, imágenes con tamaño reservado, `srcset` y carga diferida; solo un `h1` por página; todas las imágenes con `alt`.

**Lo que NO se ha hecho a propósito**
- No hay etiquetas de «preguntas frecuentes» (FAQ) ni valoraciones con estrellas: Google ya no las muestra para webs como esta, y las estrellas sin opiniones reales serían un riesgo.
- No se ha puesto dirección postal en los datos estructurados: la dirección legal es de NADIDU SL y no de Guanaco Viajes. Si Nadidú quiere ficha de Google Business, que la gestione ella.
- No hay analítica con cookies (evita el banner). Search Console te da lo importante sin cookies.

## 3. Lo que tienes que hacer tú al publicar (30 minutos, una sola vez)

1. **Search Console** (search.google.com/search-console) → «Añadir propiedad» → *Dominio* `guanacoviajes.es`. Verifica por DNS desde Hostinger (lo más limpio) o pega el código en *Ajustes → Buscadores*.
2. En Search Console → *Sitemaps* → añade `sitemap.xml`.
3. Pide indexar a mano las 5 páginas clave (*Inspección de URL → Solicitar indexación*): inicio, viajes a medida, destinos, Chile y Perú.
4. **Bing Webmaster Tools** → «Importar desde Google Search Console» (un clic).
5. Rellena las redes en *Ajustes → Contacto y redes* en cuanto existan: se añaden solas al buscador y al pie.
6. Cada semana, 5 minutos en Search Console → *Rendimiento*: qué búsquedas te traen impresiones y a qué posición. Con eso decides qué artículo escribir después.

> Honestidad sobre plazos: una web nueva tarda semanas en indexarse y meses en posicionar una búsqueda tan competida como «viajes a medida». Lo realista es empezar a ganar antes en **«viaje a medida a {país}»** y en búsquedas largas de los artículos, y de ahí subir.

## 4. Palabras clave: dónde va cada una

- **«Viajes a medida»**: inicio y `/viajes-a-medida`. En artículos úsala como enlace natural, no la metas a la fuerza.
- **«Viaje a medida a {País}»**: solo la ficha de ese país. Un artículo sobre ese país **no** debe intentar posicionar esa misma frase (competiría con su ficha): apunta a una búsqueda informativa distinta.
- **«Viajes a medida por Sudamérica / Europa»**: páginas de continente.
- **Variantes** que puedes mezclar con naturalidad: viajes personalizados, viaje a la carta, diseñar tu viaje, agencia de viajes a medida, viaje sin rutas cerradas.

## 5. Guía para escribir cada artículo

### Lo que te diferencia (y Google premia)
Has pasado meses viajando por Sudamérica. **Eso es lo que no tiene una web genérica.** Escribe desde lo vivido: qué hiciste, qué cambiarías, qué sorprendió, dónde te equivocaste. Es lo que Google llama experiencia real, y es también lo que hace que alguien quiera que le diseñes el viaje a ti. Evita los textos de catálogo («Chile es un país de contrastes…»): hay miles iguales.

### Cómo elegir el tema (5 minutos)
1. Piensa la pregunta concreta que alguien teclearía: *«qué ver en el desierto de Atacama en 4 días»*.
2. Escríbela en Google y mira las sugerencias del desplegable y «Otras preguntas de los usuarios»: son búsquedas reales.
3. Si los primeros resultados son enormes portales, afina más: *«Atacama sin tour organizado»* tiene menos competencia y encaja con tu estilo.
4. Cada artículo responde **una** pregunta. Si son dos, son dos artículos.

### Anatomía de un artículo (qué rellenar en el panel)

| Campo del panel | Qué poner | Truco SEO |
|---|---|---|
| **Título** | La pregunta o promesa, con la búsqueda al principio | 50–60 caracteres. «Qué ver en Atacama en 4 días: ruta sin prisas» |
| **Dirección web (slug)** | Palabras clave, sin fechas | `que-ver-atacama-4-dias` (no se cambia después) |
| **País** | El país del que habla | Enlaza el artículo con su ficha y lo pinta en ella |
| **Resumen** | 1–2 frases que prometan lo que se va a encontrar | Se usa de descripción en Google: 120–155 caracteres, con la búsqueda dentro |
| **Texto** | El artículo (ver más abajo) | |
| **Foto de portada** | Una foto tuya, horizontal | Se comparte en redes |
| **Descripción de la foto** | Qué se ve, de forma literal | «Camino de tierra entre volcanes al amanecer». Ayuda a Google Imágenes y a la accesibilidad |
| **Título / descripción para buscadores** | Solo si quieres uno distinto al título y al resumen | Déjalos vacíos casi siempre |
| **Fecha** | Vacío = ahora; futura = programado | Publica con regularidad, mejor que a rachas |

### Estructura del texto
- **Primer párrafo (3–4 líneas)**: responde ya la pregunta o di qué vas a contar. Nada de «Desde tiempos inmemoriales…».
- **Secciones con `##`** (cada una será un subtítulo): usa las palabras de lo que la gente busca («Cómo llegar», «Cuánto tiempo necesitas», «Qué cambiaría si volviera»). Entre 3 y 7.
- **Extensión**: 1.000–1.800 palabras si el tema lo pide. Mejor 900 útiles que 2.000 de relleno.
- **Párrafos cortos**. Una idea por párrafo. Se lee en el móvil.
- **Citas (`>`)** para una frase tuya que quieras destacar. **Listas (`-`)** para pasos o cosas que llevar.
- **Cierre**: no hace falta que lo escribas: cada artículo termina automáticamente con el recuadro «¿Te apetece viajar a {País}?» con enlace a la ficha y al presupuesto.

Formato admitido en el editor: `## subtítulo`, `> cita`, `- lista`, `*cursiva*`, `**negrita**`, `[texto](https://…)` y `[texto](/ruta-interna)`.

### Enlaces (lo que más se olvida)
- **Mínimo 2 enlaces internos por artículo**, con texto descriptivo (no «pulsa aquí»):
  - uno a la ficha del país: `[viaje a medida a Chile](/destinos/sudamerica/chile)`
  - uno a otro artículo relacionado, o a `[viajes a medida](/viajes-a-medida)`.
- **1 enlace externo útil** si aporta (parque nacional, transporte oficial). Sin reventar el artículo de enlaces.
- Cuando publiques un artículo nuevo, **vuelve a uno antiguo del mismo tema y enlázalo**. Es lo que mejor funciona y casi nadie lo hace.

### Qué no hacer
- **No inventes ni des como seguros**: precios, horarios, requisitos de entrada, vacunas, visados, temporadas o normas. Cambian y es responsabilidad tuya (y de Nadidú). Si das un dato, di desde cuándo es y recomienda confirmarlo. En lo vivido, cuenta *tu* experiencia («cuando fuimos nosotros…»).
- **No copies** de otras webs ni de ChatGPT: Google lo detecta y penaliza el contenido repetido. Si usas ayuda para redactar, reescribe con tu voz y tus datos.
- **No repitas la misma frase clave** a la fuerza. Si suena forzada al leerla en voz alta, quítala.
- **No pongas fotos de otros** sin permiso.

## 6. Plan de arranque: 10 artículos (propuesta, tú decides)

Mezcla de búsqueda informativa por país (donde Daniel tiene experiencia) y artículos que llevan directamente a «viajes a medida». Los títulos son ideas de partida, no textos cerrados:

**Que explican el servicio (atraen a quien está decidiendo cómo viajar)**
1. «Viaje a medida o viaje organizado: cuándo compensa cada uno»
2. «Cómo se diseña un viaje a medida, paso a paso» (cuenta vuestro proceso real)
3. «Viajar sin ruta cerrada: cómo planificar sin perder la sorpresa»

**Basados en vuestros 6 meses por Sudamérica (los de más valor)**
4. «6 meses por Sudamérica: la ruta que hicimos y lo que cambiaríamos»
5. «Qué llevar para un viaje largo por Sudamérica: lo que usamos y lo que sobró»
6. «Errores que cometimos viajando por Sudamérica (y cómo evitarlos)»
7. Un artículo por país donde hayáis estado: «Chile: [ruta/lugar concreto], contado desde dentro»

**Europa**
8. «Italia fuera de las rutas típicas: Manarola y alrededores» (solo si habéis estado)
9. «Croacia y Bosnia y Herzegovina en un mismo viaje» (solo si habéis estado o con tema bien documentado)

**De confianza**
10. «Quién está detrás de Guanaco Viajes y por qué el guanaco» (apoya Quiénes somos)

**Ritmo recomendado**: 1 artículo cada 1–2 semanas durante 3 meses vale más que 10 de golpe y nada después.

## 7. Cómo saber si funciona

- **Search Console → Rendimiento**: impresiones, clics, posición media por página y por búsqueda.
- **Señal buena**: un artículo con muchas impresiones y pocos clics → mejora el título y el resumen. Posición 8–15 → refuerza con enlaces internos y amplía el texto.
- **Señal de negocio**: en *Solicitudes* del panel mira cuántas llegan y desde qué país. Si un artículo de Chile trae solicitudes de Chile, haz más de Chile.
- Revisa los artículos antiguos cada 6 meses: corrige datos que hayan cambiado. Al guardarlos, la fecha de modificación se actualiza sola (Google lo ve).

## 8. Dos mejoras posibles más adelante (no están hechas, dime si las quieres)

- **Fotos dentro del texto del artículo**: hoy cada artículo lleva portada pero no imágenes intercaladas. Añadirlas ayudaría a Google Imágenes y a la lectura. Requiere ampliar el editor.
- **Más continentes**: ahora hay Sudamérica y Europa. Para añadir Asia, África, Norteamérica, etc., hay que ampliar la lista de continentes (menú, filtros, formulario y regla del «fin de semana largo»).
