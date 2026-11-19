# Guanaco Viajes · Fase 5: páginas interiores y formulario (aprobada)

Prototipos en `design/prototipos/`. Son maquetas, no la aplicación.

## Páginas diseñadas

Ficha de país (`pais.html`) · Viajes a medida · Viajes en grupo · Destinos (con pestañas Sudamérica / Europa) · Quiénes somos · Contacto · Blog (con estado vacío) · Artículo · Plantilla de página legal (`aviso-legal.html`) · 404 · Formulario de presupuesto.

## Decisiones de diseño

- **Menú:** en las páginas interiores es una barra sólida de carbón. La transparencia con línea fina existe solo sobre el carrusel de la home.
- **Ficha de país:** foto grande con bloque sólido (nombre y resumen genérico, sin «Cuándo ir»), galería opcional (0 a 6 fotos; con una sola, no aparece), salidas en grupo de ese país y artículos ligados (ambos solo si existen), bloque de presupuesto con el país ya marcado y «Más destinos» del mismo continente.
- **Viajes a medida:** cabecera, introducción con textos propios, dos situaciones («Ya sé adónde quiero ir» → «Comienza tu viaje»; «Quiero descubrir algo distinto» → «Cuéntanos tu idea»), destinos y «Cómo funciona» con el primer paso del formulario. Título de la sección: «Hay dos maneras de empezar tu viaje».
- **Viajes en grupo:** cabecera, introducción, tres huecos «Próximamente» (transparencia sobre marfil, sin degradado) y cierre hacia viajes a medida. Tarjeta de salida publicada: foto, destino, fechas, «Grupo de 10 a 12 personas», «Desde XXXX €». Los viajes en grupo llevan una pequeña señal al reservar. Nada de esto aparece en el texto de la página hasta que haya salidas.
- **Destinos / continentes:** una sola plantilla con pestañas; cada continente tiene su URL con la pestaña activa.
- **Contacto:** WhatsApp y teléfono (mismo número), correo y formulario corto con consentimiento que nombra a Nadidú como responsable.
- **Blog:** rejilla con filtros por país y paginación; estado vacío «Todavía no hay historias por aquí». Artículos con título, fecha, portada, subtítulos, citas y relacionados.
- **Páginas legales:** plantilla con índice lateral, aviso de que Guanaco Viajes es marca de Daniel Conti, agente colaborador de NADIDU SL, y texto adaptado de Nadidú pendiente de su revisión.

## Formulario de presupuesto (`/presupuesto`)

Cinco pasos, sin guardar nada si se abandona. Consentimiento solo en el último paso.

1. **Destino:** países por continente, selección múltiple, y «Aún no lo sé».
2. **Fechas:** meses próximos (varios), duración aproximada (una opción) e interruptor «Soy flexible».
   - **Regla:** si hay algún país de Sudamérica seleccionado en el paso 1, no se ofrece «Un fin de semana largo» y se deselecciona si estaba marcado. Con solo destinos europeos o «Aún no lo sé», se ofrece.
3. **Viajeros:** adultos (mínimo 1) y menores con botones + / −; con menores aparece el campo de edades.
4. **Tu idea:** opciones editables desde el panel (por defecto: Naturaleza, Cultura, Gastronomía, Ciudades, Playas, Montaña, Descanso, Fuera de lo habitual) y texto libre. Todo opcional.
5. **Contacto:** nombre y correo obligatorios, teléfono opcional, canal preferido (correo, WhatsApp, llamada), consentimiento RGPD (responsable: NADIDU SL, registro C.IAN-118115-2).

- **Sin pregunta de presupuesto:** el cliente recibe después un presupuesto a medida.
- **Entradas:** «Comienza tu viaje» abre el paso 1; «Cuéntanos tu idea» abre con «Aún no lo sé» marcado y empieza por el paso 4; la ficha de país llega con ese país marcado; el primer paso también se muestra en la home y se arrastra a `/presupuesto` solo en el navegador.
- **Errores:** mensajes propios junto a cada campo, que se quitan al corregir. Pantalla final con resumen y sin promesa de plazo.
- **Sin burbuja de contacto** en esta página.

## Para el panel de administración (Fase 7)

Editable: opciones del paso 4 · redes sociales · salidas en grupo (estado Próximamente/Publicada, destino, fechas, precio «desde», plazas, señal, foto) · galería de cada país · resumen de cada país · artículos (ligados a país) · textos de las páginas · ajuste de tono del menú (claro/oscuro) por diapositiva del carrusel.

## Pendientes

- Titulares y textos que escribí yo como borrador: «Historias de viaje», «Cada país es un punto de partida…», «Parece que te has salido del camino», texto de «Quiénes somos», resúmenes de país.
- Revisión de Nadidú de los cuatro textos legales; datos pendientes: titular del sitio web, número de calle, Registro Mercantil.
- Destino de la foto de las dunas; foto de Daniel viajando.
- Envío real, guardado, correo de aviso, acuse de recibo y antispam del formulario (Fase 8).
