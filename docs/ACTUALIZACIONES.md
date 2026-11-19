# Actualizar la web en Hostinger (hPanel) · guía paso a paso

> **Actualización «Destinos en una fila» (solo archivos, sin SQL):** los carriles de países de **Destinos** y **Viajes a medida** ya no muestran el texto resumen bajo el nombre (se cortaba) y los países se deslizan en una sola fila. En cada ficha de país, «Más destinos en…» pasa de rejilla de varias filas a la misma fila deslizable. El inicio no cambia. No hay que importar nada. Archivos: `app/Views/partials/destinos-rail.php`, `app/Views/pages/destinos.php`, `a-medida.php`, `pais.php` y `public_html/assets/css/app.css`.

> **Actualización «Quiénes somos» (solo archivos, sin SQL):** título centrado, fotos cuadradas, texto más grande y justificado (en móvil, alineado a la izquierda), cierre «Viaja con curiosidad…» centrado con botón y **sin la franja inferior** de esta página. El botón pasa a decir «Cuéntanos tu idea» salvo que hayas escrito otro texto en *Textos y ajustes → Quiénes somos → Cierre: texto del botón*. No hay que importar nada y tus textos y fotos no se tocan. Archivos: `app/Views/pages/quienes.php`, `app/Controllers/PagesController.php`, `app/Admin/SettingsRegistry.php` y `public_html/assets/css/app.css`.

> **Actualización de diseño de noviembre (solo archivos, sin SQL):** foto en cada tarjeta de zona (máximo 4 zonas), título y subtítulo de las zonas editables, formulario sin los 3 pasos, «Quiénes somos» con foto + texto y texto + foto, texto de «Tipos de viaje» sin título repetido y carrusel más pequeño. **No hay nada que importar** y tus textos y fotos (que viven en la base de datos y en `uploads`) no se tocan: el ZIP solo cambia código, estilos y las fotos de ejemplo. Después de subirlo:
> 1. *Destinos → Chile*: si tenías 5 zonas, la web ya solo enseña las 4 primeras. Al guardar el país desde el panel, la quinta («Patagonia chilena») se elimina para siempre.
> 2. *Destinos → cada país → Zonas*: sube la foto de cada zona (opcional).
> 3. *Textos y ajustes → Fichas de país*: cambia el título y el subtítulo de las zonas (escribe `{País}` donde quieras el nombre).
> 4. *Textos y ajustes → Quiénes somos*: sube la segunda foto (y su descripción). La primera sigue siendo «Tu foto viajando» de la página de inicio.
>
> Archivos que cambian: `app/Views/pages/home.php`, `pais.php`, `quienes.php`, `app/Views/partials/como.php`, `app/Services/CountryContent.php`, `app/Admin/FormKit.php`, `CrudController.php`, `CountriesController.php`, `MediaController.php`, `SettingsRegistry.php`, `app/Core/Text.php` y `public_html/assets/css/app.css`, `admin.css`.

> **Actualización de seguimiento (solo archivos, sin SQL):** la ficha de país pasó a tener un único bloque «Zonas y cuándo ir» (cada zona lleva su época) y se quitó el texto de entrada. **No hay que importar nada.** Se sube el ZIP (pasos 1 a 3) y se comprueba (paso 5). Archivos: `app/Services/CountryContent.php`, `app/Repositories/Countries.php`, `app/Controllers/DestinosController.php`, `app/Admin/CountriesController.php`, `app/Views/pages/pais.php`, `public_html/assets/css/app.css` y `public_html/assets/css/admin.css`. Lo que hubieras escrito en «Entrada» o en el antiguo «Cuándo ir» sigue guardado en la base de datos pero ya no se muestra: pasa la época a cada zona.

Esta guía sirve para **cualquier actualización** y está escrita con la de **2026-11-17** (plantilla ampliada de las páginas de país y artículos con varios países) como ejemplo.

> **Regla de oro:** no se toca nada sin las dos copias de seguridad hechas (base de datos y archivos). Con ellas, cualquier cosa se puede deshacer.

**Tiempo:** unos 25 minutos, en un momento tranquilo (no hace falta avisar a nadie: la web sigue funcionando durante todo el proceso).

## Lo que trae esta actualización

- **Archivos nuevos o cambiados:** el código de la web (páginas de país, blog, panel), los estilos y el JavaScript. Todo viene en el ZIP.
- **Un SQL de actualización** (`database/actualizaciones/2026-11-17-paises-plantilla.sql`) que **solo añade**: 4 columnas a los países y una tabla para los artículos con varios países. No borra ni modifica nada de lo que ya tienes.
- **Un SQL para volver atrás** (`…-VOLVER-ATRAS.sql`), por si hiciera falta.

### El orden de subida NO importa
La web nueva está preparada para funcionar **con o sin** el SQL: si falta, las secciones nuevas simplemente no se muestran y el panel te avisa en amarillo. Y la web anterior también funciona con el SQL ya importado. Puedes subir los archivos primero y el SQL después, o al revés, sin que se rompa nada de lo que ve el público. (Está comprobado en las dos direcciones.)

---

## Paso 1 · Copia de seguridad de la base de datos

Elige **una** de las dos formas.

**A. Con phpMyAdmin (sin SSH)**
1. hPanel → **Sitios web** → tu web → **Bases de datos** → **phpMyAdmin** → *Entrar*.
2. En la columna izquierda, haz clic en el nombre de **tu base de datos** (la de la web, la que empieza por `u952513423_`).
3. Pestaña **Exportar** → elige **Personalizado** → marca **«Añadir sentencia DROP TABLE / VIEW / PROCEDURE…»** → deja el formato en **SQL** → **Exportar**.
4. Se descarga un archivo `.sql`. **Renómbralo** con la fecha (por ejemplo `copia-bd-2026-11-17.sql`) y **guárdalo en tu ordenador**.
5. Comprueba que pesa más de unos 100 KB y ábrelo con un editor de texto: debe empezar por `-- phpMyAdmin SQL Dump` y contener líneas `CREATE TABLE`.

**B. Por SSH**
```bash
mysqldump -u USUARIO_BD -p NOMBRE_BD > ~/copia-bd-$(date +%F).sql
ls -lh ~/copia-bd-*.sql
```

## Paso 2 · Copia de seguridad de los archivos

Incluye tu configuración (`config.php`) y las fotos que hayas subido, que **no están en el ZIP** y no se pueden recuperar de otro sitio.

**A. Por SSH (recomendado)** — sustituye la ruta por la carpeta de tu web si es otra:
```bash
cd ~/domains/guanacoviajes.es
zip -rq ~/copia-archivos-$(date +%F).zip . -x "*.log"
ls -lh ~/copia-archivos-*.zip
```
La copia queda en tu carpeta personal (`~`), **fuera** de la web. Descárgala a tu ordenador:
```bash
scp -P 65002 u952513423@IP_DEL_SERVIDOR:copia-archivos-*.zip .
```

**B. Con el Administrador de archivos de hPanel**
1. hPanel → **Archivos** → **Administrador de archivos** → entra en la carpeta de tu web.
2. Selecciona todo (**Seleccionar todo**) → **Comprimir** → formato **ZIP** → nombre `copia-archivos-2026-11-17`.
3. **Descarga** ese ZIP a tu ordenador y, después, **bórralo del servidor** (para que no quede accesible).

**Además (cómodo y gratis):** hPanel → **Sitios web** → tu web → **Copias de seguridad** → *Generar copia de seguridad* (si tu plan lo permite). Es una tercera red de seguridad.

> **Comprobación:** tienes en tu ordenador **dos archivos** (el `.sql` y el `.zip`) con la fecha de hoy y un tamaño razonable. Si falta alguno, no sigas.

## Paso 3 · Subir los archivos

El ZIP trae la web entera. **Subirlo encima no pisa** tu `config.php` (el ZIP no lo incluye), tus fotos ni tu base de datos.

**Primero, localiza dónde está `index.php` en tu servidor.** Hay dos formas de tener la web montada y la subida es distinta:

| Tu servidor | Cómo subir |
|---|---|
| **Instalación recomendada:** junto a `public_html` hay carpetas `app`, `config`, `storage`, `database` | Descomprime el ZIP en esa carpeta superior (la que contiene `public_html`). Es lo que hace `unzip -o`. |
| **Todo dentro de `public_html`** (es tu caso si desde internet `tuweb.es/config/config.php` da «403»): `index.php`, `app`, `config`, `storage`… están en la misma carpeta | Del ZIP, la carpeta **`public_html/` se vuelca en tu carpeta de la web**, y `app/`, `database/`, `docs/` también van allí. Es decir: mismos nombres de carpeta, sobrescribiendo. |

**A. Por SSH (instalación recomendada)**
```bash
scp -P 65002 guanaco-viajes-2026-11-17.zip u952513423@IP_DEL_SERVIDOR:domains/guanacoviajes.es/
ssh -p 65002 u952513423@IP_DEL_SERVIDOR
cd ~/domains/guanacoviajes.es
unzip -o guanaco-viajes-2026-11-17.zip
rm guanaco-viajes-2026-11-17.zip
```

**B. Con el Administrador de archivos (cualquiera de las dos instalaciones)**
1. En tu ordenador, descomprime el ZIP en una carpeta.
2. En hPanel → Administrador de archivos, ve a la carpeta de tu web.
3. Sube y **sobrescribe** estas carpetas (acepta «reemplazar»): `app`, y el contenido de `public_html` (sobre todo `assets`, `index.php`). Sube también `database/actualizaciones` y `docs`.
4. **No subas ni sobrescribas** `config/config.php` ni la carpeta `uploads`.
5. Si tu instalación es «todo dentro de `public_html`», el contenido de la carpeta `public_html` del ZIP va **directamente** en tu carpeta de la web, no dentro de otra `public_html`.

### Archivos que cambian en esta actualización
Si prefieres subir solo lo necesario, estos son (rutas del ZIP):

```
app/Core/Schema.php                         (nuevo)
app/Core/Seo.php
app/Services/CountryContent.php             (nuevo)
app/Repositories/Countries.php
app/Repositories/Posts.php
app/Controllers/DestinosController.php
app/Controllers/BlogController.php
app/Admin/CountriesController.php
app/Admin/PostsController.php
app/Admin/FormKit.php
app/Admin/CrudController.php
app/Views/pages/pais.php
app/Views/pages/articulo.php
app/Views/partials/post-grid.php
public_html/assets/css/app.css
public_html/assets/css/admin.css
public_html/assets/js/admin.js
database/actualizaciones/2026-11-17-paises-plantilla.sql            (nuevo)
database/actualizaciones/2026-11-17-paises-plantilla-VOLVER-ATRAS.sql (nuevo)
```

## Paso 4 · Importar el SQL de actualización

**A. Con phpMyAdmin**
1. phpMyAdmin → clic en el nombre de tu base de datos (columna izquierda).
2. Pestaña **Importar** → **Seleccionar archivo** → elige `2026-11-17-paises-plantilla.sql` (está en la carpeta `database/actualizaciones` del ZIP en tu ordenador).
3. No cambies nada más → **Importar** (abajo del todo).
4. Debe salir un cuadro verde: **«La importación se ha completado correctamente»**.

**B. Por SSH**
```bash
cd ~/domains/guanacoviajes.es
mysql -u USUARIO_BD -p NOMBRE_BD < database/actualizaciones/2026-11-17-paises-plantilla.sql
```
No muestra nada si va bien.

**Cómo comprobar que se aplicó** (phpMyAdmin): en la tabla `countries` → pestaña *Estructura* deben aparecer las columnas `entrada`, `zonas`, `cuando_ir` y `faqs`, y en la lista de tablas debe existir `post_countries`.

**Si sale un error `#1060 Duplicate column name`:** significa que **ya estaba importado**. No ha pasado nada ni se ha duplicado nada. Sigue con el paso 5.
**Importarlo dos veces es seguro:** se detiene en el primer paso sin tocar tu contenido.

Los artículos que ya tenías conservan su país: el script lo copia a la tabla nueva.

## Paso 5 · Comprobar que todo funciona (10 minutos)

Recarga con `Ctrl+Shift+R` (y en el móvil, cierra y abre la pestaña).

**La web pública (lo primero, y lo más importante)**
- [ ] La portada carga bien.
- [ ] `https://guanacoviajes.es/destinos/sudamerica/chile` carga **sin errores y sin secciones vacías ni títulos sueltos**. (Aún no tienes contenido nuevo, así que se verá como antes.)
- [ ] Mira también un país de Europa y `https://guanacoviajes.es/blog`.
- [ ] El formulario de presupuesto se abre y funciona.

**El panel**
- [ ] *Destinos → Chile*: aparecen **Zonas (con «Cuándo ir» en cada zona) y Preguntas frecuentes**. Si en su lugar sale un aviso amarillo diciendo que faltan campos, el SQL no se importó: repite el paso 4.
- [ ] Escribe una **zona de prueba** (nombre, descripción, días) y guarda. Abre la ficha pública de Chile: debe aparecer «Zonas y cuándo ir a Chile» con la tarjeta, en el orden correcto (zonas → preguntas → formulario → ideas → más destinos).
- [ ] Prueba una **pregunta frecuente**: se abre y se cierra con el ratón y con el teclado.
- [ ] *Blog → un artículo*: marca **uno o dos países** (el primero que marques será el principal). Guarda y comprueba que el artículo aparece en «Ideas para tu viaje» de esa ficha.
- [ ] Mira la web **en el móvil**: las zonas se deslizan hacia los lados y se ve asomar la siguiente tarjeta.
- [ ] `https://guanacoviajes.es/sitemap.xml` incluye los artículos.
- [ ] Por SSH (si puedes): `tail -n 20 ~/domains/guanacoviajes.es/storage/logs/php-error.log` (o la ruta equivalente de tu instalación). No debe haber líneas nuevas de hoy.

**Limpieza:** borra el contenido de prueba que hayas escrito (o déjalo si te sirve).

Opcional: pega la dirección de una ficha con preguntas en [la prueba de resultados enriquecidos de Google](https://search.google.com/test/rich-results) para ver que los datos de «Preguntas frecuentes» se leen sin errores.

---

## Si algo falla: cómo volver atrás

Empieza por el nivel más ligero. **Después de cada paso, comprueba la web.**

### Nivel 1 · Volver a los archivos anteriores (lo más habitual)
Si la web se ve mal o da error después de subir los archivos:
```bash
cd ~/domains/guanacoviajes.es
unzip -o ~/copia-archivos-2026-11-17.zip
```
Con el Administrador de archivos: sube tu copia de archivos (el ZIP del paso 2), descomprímela sobre la carpeta de la web y acepta sobrescribir.
**La web anterior funciona perfectamente con el SQL ya importado**, así que no hace falta tocar la base de datos para esto.

### Nivel 2 · Deshacer también el SQL
Solo si quieres dejar la base de datos exactamente como estaba. **Se pierde lo que hayas escrito en los campos nuevos** (zonas, preguntas) y la relación de artículos con varios países. Los artículos conservan su país principal.
- phpMyAdmin → tu base de datos → **Importar** → archivo `2026-11-17-paises-plantilla-VOLVER-ATRAS.sql`.
- O por SSH: `mysql -u USUARIO_BD -p NOMBRE_BD < database/actualizaciones/2026-11-17-paises-plantilla-VOLVER-ATRAS.sql`

Haz este nivel **solo después** de haber vuelto a los archivos anteriores (nivel 1) o junto con él.

### Nivel 3 · Restaurar la copia completa de la base de datos
Si por cualquier motivo el contenido de la base de datos quedó mal:
1. phpMyAdmin → tu base de datos → **Importar** → elige tu `copia-bd-2026-11-17.sql` → **Importar**.
2. La copia hecha con *«Añadir sentencia DROP TABLE»* (paso 1) **sustituye** las tablas por las de ese momento.
3. Si no marcaste esa opción y da errores de «tabla ya existe»: en phpMyAdmin, **Seleccionar todo** en la lista de tablas → *Para los elementos marcados:* **Eliminar** (con las dos copias a salvo en tu ordenador) y vuelve a importar la copia.

### Nivel 4 · Copia de Hostinger
hPanel → **Copias de seguridad** → *Restaurar* la copia de antes de la actualización (archivos y/o base de datos).

### Qué NO hacer
- No borres la carpeta `uploads` ni el archivo `config.php`.
- No restaures la copia de archivos **y** de base de datos de momentos distintos (podría haber fotos sin su dato).
- Si dudas, **para y escríbeme el mensaje de error**: del registro (`storage/logs/php-error.log`) saco enseguida qué pasa.

---

## Resumen en una frase por paso
1. **Copia de la base de datos** (`.sql` a tu ordenador). 2. **Copia de los archivos** (`.zip` a tu ordenador). 3. **Sube el ZIP** y sobrescribe (no toques `config.php` ni `uploads`). 4. **Importa el SQL** de actualización. 5. **Comprueba** la web, el panel y el móvil. Si algo falla, **nivel 1**: restaura tu copia de archivos.
