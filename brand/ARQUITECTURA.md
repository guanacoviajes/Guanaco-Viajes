# Guanaco Viajes · Arquitectura de información (Fase 3, aprobada)

## Mapa del sitio

```
/                                   Inicio
/viajes-a-medida                    Tipo de viaje
/viajes-en-grupo                    Tipo de viaje (tres huecos «Próximamente», editables)
/destinos                           Todos los destinos (pestañas Sudamérica / Europa)
/destinos/sudamerica                Cinco tarjetas de país
/destinos/sudamerica/{pais}         Ficha de país (argentina, chile, brasil, uruguay, peru)
/destinos/europa                    Cinco tarjetas de país
/destinos/europa/{pais}             Ficha de país (francia, alemania, italia, bosnia-y-herzegovina, croacia)
/quienes-somos
/blog  ·  /blog/{articulo}
/contacto
/presupuesto                        Formulario por pasos
/aviso-legal · /politica-de-privacidad · /politica-de-cookies · /condiciones-generales
```

## Menú

Inicio · Tipos de viaje ▾ (Viajes a medida, Viajes en grupo) · Destinos ▾ (Sudamérica y Europa, con sus países, lista sin fotos) · Quiénes somos · Blog · Contacto · botón «Pide tu presupuesto».
«Tipos de viaje» es solo etiqueta del menú, sin página propia. En móvil: panel a pantalla completa en carbón, con acordeón, botón de presupuesto abajo.

## Página de inicio (orden aprobado)

1. Carrusel de tres slides.
2. Tipos de viaje: a medida y en grupo, con explicación según lo que busque el viajero. «A medida» → botón a Pide tu presupuesto. «En grupo» → botón a los viajes en grupo disponibles.
3. Destinos: pestañas Sudamérica / Europa con tarjetas de país.
4. Cómo funciona, en tres pasos (cuéntanos tu idea, diseñamos tu viaje a medida, tú viajas) y **primer paso del formulario** («¿A dónde te gustaría ir?», con tarjetas de país). La respuesta se arrastra a /presupuesto solo en el navegador.
5. Quién está detrás: una frase de «Quiénes somos», botón, y foto de Daniel viajando (solo él).
6. Blog: últimos artículos; se oculta mientras no haya ninguno.
7. Franja fina «Pide tu presupuesto» sobre el pie de página.

## Fichas de país

Foto grande con el nombre · resumen corto y genérico de lo que se puede descubrir (sin rutas típicas, sin «Cuándo ir») · salidas en grupo de ese país si las hay · botón «Pide tu presupuesto» con el país marcado · artículos del blog ligados a ese país, si los hay.

## Viajes en grupo

Página con explicación de cómo funcionan y tres huecos «Próximamente» (transparencia sobre marfil, no degradado), más botón a presupuesto. Cada salida tiene estado «Próximamente» o «Publicada» y se edita desde el panel. Siempre hay mínimo tres huecos; al publicar una salida sustituye a un hueco; si hay más de tres, la rejilla crece.

## Elementos globales

- **Burbuja flotante de contacto** (abajo a la derecha), solo WhatsApp (con mensaje inicial ya escrito) o correo. En todas las páginas públicas salvo /presupuesto. **Sin agente de IA** (descartado).
- **Pie de página:** logo completo, columnas Destinos / Guanaco / Legal, contacto (`+34 600538885`, `hola@guanacoviajes.es`), redes sociales y la identificación de Nadidú (agente de viajes colaborador, licencia C.IAN-118115-2).
- **Redes sociales:** Instagram, Facebook y X, **editables desde el panel**; cada icono solo aparece cuando tiene URL. TikTok se añade el día que exista.
- **Nadidú** solo se menciona en el pie de página y en las páginas legales (y, por obligación legal de información al recoger datos, en el último paso del formulario). No aparece en «Quiénes somos».

## Textos legales

Se basan en los de NADIDU SL (aviso legal, política de privacidad, política de compra y condiciones generales), quitando lo que solo aplica a su plataforma de agentes (áreas privadas, suscripciones, comisiones) y añadiendo que Guanaco Viajes es la marca comercial de Daniel Conti, agente de viajes colaborador de NADIDU SL. Nadidú es responsable del tratamiento. Deben ser revisados y aprobados por Nadidú antes de publicar.

## Pendientes

- Quién figura como titular del sitio web guanacoviajes.es en el aviso legal.
- Número de calle y datos del Registro Mercantil de NADIDU SL.
- Destino de la foto de las dunas.
- Redacción de «Cómo funciona», explicaciones de tipos de viaje, los 10 resúmenes de país y «Quiénes somos» (Fase 5, borradores para corregir).
