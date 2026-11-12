# Guanaco Viajes · Dirección creativa (Fase 2, aprobada)

Registro de decisiones aprobadas por Daniel. Lo que no está aquí no está decidido.

## Concepto

Una editorial de viajes hecha con carbón, marfil y terracota, donde las fotos mandan y el texto nunca compite con ellas.
Tono: asilvestrado pero cuidado. Una marca que invita a salirse un poco de lo establecido, sin vender aventura extrema.
Frase de cierre: *Viaja con curiosidad. Muévete con libertad. Vuelve con historias.*

## Logo

Archivos en `brand/logo/`. El animal es el trazado original recibido; solo se ha cambiado el wordmark (Newsreader 600 para «guanaco», Manrope 600 en minúsculas y espaciado para «viajes», este último ampliado de 22 a 30 unidades para que se lea en el menú), convertido a curvas.

| Ancho del logo | Versión |
|---|---|
| 60 px o más | Completa, con «viajes» |
| Menos de 60 px | Compacta: animal + «guanaco» centrado sobre el cuerpo, sin «viajes» |
| Favicon / avatar | Solo el animal |

Con «viajes» ampliado, el logo completo se lee bien desde unos 150 px de ancho (≈ 5 px de altura de letra). Logo del menú: 70 px de alto en escritorio y 60 px en móvil (≈ 150 y 128 px de ancho).

## Color

| Token | Valor | Uso | Contraste sobre marfil |
|---|---|---|---|
| Carbón verdoso | `#1F2D28` | Texto, botón principal | 12,4:1 |
| Terracota | `#B86F50` | Titulares grandes, filetes, iconos, hover | 3,3:1 (solo texto grande) |
| Marfil | `#F3EEE4` | Fondo | n/a |
| Oliva suave | `#7A8064` | Detalles, fondos | 3,6:1 |
| Terracota funcional | `#8F4A32` | **Solo** texto pequeño de acento (kickers) | 5,7:1 |

## Tipografía

- Titulares: **Newsreader** 400 / 600, cursiva para el énfasis.
- Texto y navegación: **Manrope** 400 / 500 / 600.
- Kicker: Manrope 600, 11-12 px, mayúsculas, espaciado .14em, `#8F4A32`.
- Fuentes alojadas en el propio servidor (no en el CDN de Google, por RGPD).

## Componentes

- **Botón principal:** carbón con texto marfil. Hover: terracota con texto carbón, 19 px (3,7:1, cumple texto grande).
- **Botón secundario:** borde carbón; hover relleno carbón.
- **Menú:** transparente con línea fina sobre el carrusel; en el resto de la web, barra sólida de carbón que aparece al subir el scroll y se oculta al bajar (0,3 s; el fondo transiciona en 0,5 s). Debe mostrarse también al recibir foco de teclado y respetar `prefers-reduced-motion`.
- **Color del texto del menú sobre el carrusel:** claro u oscuro por diapositiva, elegido en el panel. Los puntos de paginación siguen la misma regla.
- **Texto sobre foto:** nunca directamente. Bloque sólido de carbón.
- **Tarjeta de destino:** vertical, nombre debajo de la foto en Newsreader. En hover crece al doble y muestra una línea descriptiva (transición suave, sin rebote). En móvil la línea está siempre visible y las tarjetas se deslizan en horizontal.
- Sin degradados, cenefas ni diagonales. Radio de esquina pequeño.

## Fotografía

Tratamiento único, aplicado una vez al preparar las imágenes (no en el navegador):

- Saturación general ×0,66.
- Saturación de cianes y azules (tono 150–270°) ×0,55.
- Sombras mezcladas hacia el carbón `#1F2D28` (peso 0,30, curva (1−L)^1,6).
- Luces mezcladas hacia el marfil `#F3EEE4` (peso 0,14, curva L²).
- Canal azul ×0,97.

### Carrusel de inicio (aprobado)

| # | Foto | Titular |
|---|---|---|
| 1 | Dunas con senderista | Viaja con curiosidad |
| 2 | Carretera de Chile | Muévete con libertad |
| 3 | Dolomitas al atardecer | Vuelve con historias |

Todas pasan con texto de menú carbón (peor zona: 6,2:1, 5,2:1 y 7,8:1). Descartadas por destinos que no están en el menú: valle brumoso (EE. UU.), luna y yucas (EE. UU.), isla con pasarela (Grecia). El senderista con el Cervino (Suiza) queda para una cabecera interior.

## Formulario de presupuesto

- Por pasos, conversacional, con barra de progreso y botón Anterior.
- **No se guarda nada si la persona abandona.** El consentimiento RGPD va en el último paso, justo antes de enviar.
- Destino con tarjetas seleccionables (selección múltiple + «aún no lo sé»), fechas por mes aproximado con opción «soy flexible», viajeros con selector + / −, mensajes de error propios. Presupuesto, opcional (por decidir).
- Sin «Mi cuenta» ni área de cliente.

## Pendientes conocidos

- Destino de la foto de las dunas.
- Número de calle del domicilio de NADIDU SL y datos del Registro Mercantil, para el aviso legal.
- Quién es el responsable del tratamiento de datos (Nadidú o ambos) y cómo se presenta Guanaco Viajes (marca o agente colaborador).
- Viajes en grupo: sección lista para publicarlos, sin salidas definidas todavía.
