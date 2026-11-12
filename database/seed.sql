-- Guanaco Viajes · contenido inicial (solo para una instalación nueva)
-- Importar DESPUÉS de schema.sql. Todo lo de aquí se edita luego desde el panel.
SET NAMES utf8mb4;

INSERT INTO settings (clave, valor) VALUES
('site.nombre',            'Guanaco Viajes'),
('site.telefono',          '+34 600 53 88 85'),
('site.whatsapp',          '34600538885'),
('site.whatsapp_mensaje',  'Hola, me gustaría información sobre un viaje.'),
('site.email',             'hola@guanacoviajes.es'),
('site.cta_url',            '/contacto'),
('site.email_reservas',    'reservas@guanacoviajes.es'),
('social.instagram',       ''),
('social.facebook',        ''),
('social.x',               ''),
('footer.lema',            'Viaja con curiosidad. Muévete con libertad. Vuelve con historias.'),
('footer.agencia',         'Guanaco Viajes es la marca comercial de Daniel Conti, agente de viajes colaborador de NADIDU SL (agencia de viajes · registro C.IAN-118115-2).'),
('seo.home.title',         'Guanaco Viajes · Viajes a medida y en grupo'),
('seo.home.description',   'Diseñamos viajes a medida y en grupo por Sudamérica y Europa. Cuéntanos tu idea y la convertimos en un viaje pensado para ti.'),
('home.tipos.kicker',      'Tipos de viaje'),
('home.tipos.titulo',      'El viaje,'),
('home.tipos.enfasis',     'a tu manera'),
('home.destinos.kicker',   'Destinos'),
('home.destinos.titulo',   'Elige por dónde'),
('home.destinos.enfasis',  'empezar'),
('home.destinos.boton',    'Ver todos los destinos'),
('home.como.kicker',       'Cómo funciona'),
('home.como.titulo',       'Cuéntanos tu idea.'),
('home.como.enfasis',      'Del resto, nos encargamos.'),
('home.paso1.titulo',      'Cuéntanos tu idea'),
('home.paso1.texto',       'Un destino, una inquietud o una simple intuición.'),
('home.paso2.titulo',      'Diseñamos tu viaje a medida'),
('home.paso2.texto',       'Una propuesta pensada para ti, con espacio para lo inesperado.'),
('home.paso3.titulo',      'Tú viajas'),
('home.paso3.texto',       'Y vuelves con historias.'),
('home.quien.kicker',      'Quién está detrás'),
('home.quien.texto',       'Soy Daniel Conti. Pasé seis meses recorriendo Sudamérica con mi pareja y viajé por Europa mientras trabajaba en Madrid.'),
('home.quien.boton',       'Conoce nuestra historia'),
('home.quien.foto',        ''),
('home.blog.kicker',       'Blog'),
('home.blog.titulo',       'Historias de'),
('home.blog.enfasis',      'viaje'),
('franja.texto',           '¿Tienes una idea de viaje?'),
('franja.enfasis',         'Cuéntanosla.'),
('franja.boton',            'Comienza tu aventura'),
('home.carrusel.segundos', '4'),
('form.paso1.pregunta',    '¿A dónde te gustaría ir?'),
('form.paso1.ayuda',       'Puedes elegir más de uno.');

INSERT INTO countries (slug, nombre, continente, orden, resumen, foto, foto_pos, foto_alt) VALUES
('chile',     'Chile',     'sudamerica', 1, 'Desiertos de sal y roca roja en el norte, volcanes, lagos y bosques hacia el sur, y una costa interminable frente al Pacífico.', 'assets/img/seed/foto-10.jpg', '50% 60%', 'Persona caminando junto a una laguna rodeada de roca rojiza y volcanes'),
('peru',      'Perú',      'sudamerica', 2, 'Montañas andinas, valles, selva amazónica y una costa desértica. Un país donde conviven ruinas milenarias y una gastronomía que se cuenta aparte.', 'assets/img/seed/foto-14.jpg', '50% 50%', 'Ruinas de Machu Picchu entre montañas con nubes'),
('argentina', 'Argentina', 'sudamerica', 3, 'De los glaciares y lagos de la Patagonia a las cataratas del norte, pasando por viñedos al pie de los Andes y una capital intensa y nocturna.', 'assets/img/seed/foto-7.jpg', '30% 50%', 'Frente del glaciar Perito Moreno sobre un lago turquesa'),
('brasil',    'Brasil',    'sudamerica', 4, 'Selvas, playas, sierras y ciudades con mucho ritmo. Un país tan grande que cada región parece un destino distinto.', 'assets/img/seed/foto-9.jpg', '50% 50%', 'Cristo Redentor sobre una montaña cubierta de vegetación entre la bruma'),
('uruguay',   'Uruguay',   'sudamerica', 5, 'Costa atlántica de playas largas y pueblos tranquilos, ciudades de ritmo pausado y campo abierto. Un país pequeño para recorrer sin prisa.', 'assets/img/seed/foto-15.jpg', '50% 50%', 'Palacio Salvo entre palmeras en una plaza de Montevideo'),
('francia',   'Francia',   'europa',     1, 'Aldeas de piedra, castillos, viñedos, costas y montañas, y ciudades con siglos de historia.', 'assets/img/seed/foto-12.jpg', '50% 66%', 'Mont-Saint-Michel al amanecer sobre una llanura con un pequeño cauce'),
('alemania',  'Alemania',  'europa',     2, 'Bosques y pueblos de casas con entramado de madera, castillos sobre ríos y ciudades con mucha historia y mucha vida cultural.', 'assets/img/seed/foto-6.jpg', '50% 60%', 'Calle empedrada con casas de entramado de madera y una torre con arco'),
('italia',    'Italia',    'europa',     3, 'Montañas de roca clara en el norte, pueblos colgados sobre el mar, ciudades que son museos abiertos y una mesa que lo envuelve todo.', 'assets/img/seed/foto-13.jpg', '50% 40%', 'Pueblo de casas de colores sobre un acantilado junto al mar'),
('bosnia-y-herzegovina', 'Bosnia y Herzegovina', 'europa', 4, 'Ciudades donde se mezclan Oriente y Occidente, puentes de piedra sobre ríos de agua clara, y montañas y valles verdes.', 'assets/img/seed/foto-8.jpg', '50% 55%', 'Puente de piedra sobre un río en Mostar al atardecer'),
('croacia',   'Croacia',   'europa',     5, 'Costa de islas, pueblos de piedra frente al mar y parques naturales de lagos y cascadas.', 'assets/img/seed/foto-11.jpg', '50% 50%', 'Faro de piedra en una punta con una barca y montañas al fondo');

INSERT INTO carousel_slides (orden, kicker, titular, titular_enfasis, foto, foto_pos, foto_alt, tono_menu, cta_texto, cta_url) VALUES
(1, 'Viajes a medida',  'Viaja con',     'curiosidad', 'assets/img/seed/foto-5.jpg',  '40% 58%', 'Senderista caminando por una duna al atardecer con montañas al fondo', 'oscuro', 'Pide tu presupuesto',  '/contacto'),
(2, 'Sudamérica',       'Muévete con',   'libertad',   'assets/img/seed/foto-4.jpg',  '50% 62%', 'Carretera recta entre campos dorados con volcanes al fondo',         'oscuro', 'Pide tu presupuesto',  '/contacto'),
(3, 'Viajes en grupo',  'Vuelve con',    'historias',  'assets/img/seed/foto-27.jpg', '50% 46%', 'Pico de roca iluminado por el sol del atardecer en los Dolomitas',    'oscuro', 'Ver viajes en grupo',  '/viajes-en-grupo');

INSERT INTO trip_types (slug, titulo, descripcion, cta_texto, cta_url, foto, foto_pos, foto_alt, orden) VALUES
('viajes-a-medida', 'Viajes a medida', 'Tú traes la idea, aunque sea solo una inquietud. Diseñamos un viaje pensado para ti, y no tú para el viaje.', 'Cuéntanos tu idea', '/presupuesto', 'assets/img/seed/foto-29.jpg', '50% 45%', 'Persona de espaldas con chaqueta roja frente a una montaña nevada', 1),
('viajes-en-grupo', 'Viajes en grupo', 'Salidas con propuestas diferentes, cuidadas y con personalidad, para compartir el camino con otras personas.', 'Ver viajes en grupo', '/viajes-en-grupo', 'assets/img/seed/foto-3.jpg', '50% 60%', 'Proa de una barca de madera sobre un lago de montaña', 2);

INSERT INTO interests (nombre, orden) VALUES
('Naturaleza', 1), ('Cultura', 2), ('Gastronomía', 3), ('Ciudades', 4),
('Playas', 5), ('Montaña', 6), ('Descanso', 7), ('Fuera de lo habitual', 8);

-- Páginas de texto: Quiénes somos y textos legales
-- Los textos legales parten de los de NADIDU SL (aviso legal, política de privacidad y política de compra)
-- sin las secciones propias de su plataforma de agentes. Deben ser revisados por Nadidú antes de publicar.
INSERT INTO pages (slug, titulo, cuerpo) VALUES
('quienes-somos', 'Quiénes somos', '## El guanaco

Vive en los paisajes abiertos de Sudamérica. Recorre montañas, valles, estepas y territorios inmensos con una libertad que resulta difícil de explicar: se mueve por donde encuentra su camino, se adapta al terreno y no parece necesitar demasiado para sentirse en casa.

No entiende de fronteras dibujadas en un mapa. Tampoco de itinerarios establecidos ni de caminos que haya que seguir porque alguien decidió que eran los correctos. Simplemente avanza, explora, se detiene donde algo despierta su curiosidad y continúa cuando siente que ha llegado el momento.

Creemos que viajar debería parecerse un poco más a eso.

## Quién está detrás

Soy Daniel Conti. He pasado seis meses recorriendo Sudamérica con mi pareja y, durante años, he viajado por Europa en escapadas cortas y medias mientras trabajaba en Madrid.

De ahí nació Guanaco Viajes: una forma de viajar menos pautada.

## Cómo pensamos los viajes

No creemos que haya una única forma de descubrir el mundo, ni que todos tengamos que visitar los mismos lugares, hacer las mismas excursiones o seguir el mismo itinerario para decir que hemos conocido un destino.

Creemos en los viajes que nacen de una inquietud, de una conversación, de un lugar que llevas tiempo queriendo descubrir o de una idea que todavía no sabes muy bien cómo convertir en realidad. Viajar sin prisas cuando apetece detenerse, cambiar de rumbo si aparece algo inesperado, conocer a quienes viven allí y dejar espacio para que sucedan cosas que no se pueden reservar con antelación.

No se trata de viajar sin pensar ni de renunciar a una buena planificación. Se trata de que el viaje esté pensado para ti, y no tú para el viaje.

Por eso diseñamos viajes a medida, y también creamos viajes en grupo para quienes disfrutan compartiendo el camino con otras personas.'),
('aviso-legal', 'Aviso legal', '## Datos identificativos

En cumplimiento de lo establecido en el artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y de Comercio Electrónico (LSSI-CE), se informa a los usuarios de los datos identificativos del titular de este sitio web:

Denominación social: NADIDU SL

Nombre comercial: NADIDÚ

CIF: B22644447

Número de registro de agencia de viajes: C.IAN-118115-2

Domicilio social: Calle Sierra de Luna, 11202 Algeciras, Cádiz, España.

Correo electrónico: info@nadidu.es

Teléfono de contacto: +34 650 68 07 95

En adelante, NADIDU SL será denominada «NADIDÚ» o «el titular».

## Objeto y ámbito de aplicación

El presente Aviso Legal regula el acceso, navegación y utilización del sitio web de NADIDÚ, así como las responsabilidades derivadas del uso de sus contenidos, funcionalidades y servicios digitales.

NADIDÚ desarrolla actividades relacionadas con el sector turístico, la intermediación y comercialización de servicios de viajes, así como la gestión de una plataforma digital destinada a agentes comerciales y profesionales vinculados al sector turístico.

A través de su sitio web, NADIDÚ podrá ofrecer, entre otros, los siguientes servicios:

Información sobre servicios turísticos, viajes y productos relacionados.

Información y solicitudes de contratación de servicios.

Registro y acceso a áreas privadas para agentes comerciales.

Herramientas de gestión de expedientes, proveedores y liquidaciones.

Formación, recursos y documentación profesional.

Servicios de suscripción y acceso a funcionalidades digitales.

Canales de comunicación, atención al cliente y soporte.

El acceso y utilización del sitio web atribuye la condición de usuario e implica la aceptación de las condiciones establecidas en este Aviso Legal, sin perjuicio de las condiciones particulares que resulten aplicables a determinados servicios.

## Condiciones de acceso y utilización

El acceso general al sitio web es libre, salvo aquellas funcionalidades que requieran registro, identificación, suscripción o contratación previa.

El usuario se compromete a utilizar el sitio web de manera lícita, responsable y conforme a la buena fe, la legislación vigente y las presentes condiciones.

Queda expresamente prohibido:

Utilizar el sitio web con fines ilícitos, fraudulentos o contrarios a los derechos de terceros.

Introducir virus, programas maliciosos o cualquier elemento susceptible de alterar el funcionamiento de los sistemas.

Acceder sin autorización a cuentas, áreas privadas, bases de datos o información de otros usuarios.

Suplantar identidades o facilitar información falsa durante los procesos de registro o contratación.

Reproducir, distribuir o explotar comercialmente contenidos sin la autorización correspondiente.

Realizar actuaciones que puedan comprometer la seguridad, disponibilidad o integridad de la plataforma.

NADIDÚ se reserva el derecho de restringir o suspender el acceso a aquellos usuarios que incumplan las presentes condiciones, respetando en todo caso la normativa aplicable y los derechos contractuales de los interesados.

## Servicios turísticos y contratación

NADIDÚ desarrolla su actividad como agencia de viajes registrada con el número C.IAN-118115-2.

La información publicada sobre destinos, viajes, servicios turísticos, precios, disponibilidad, condiciones y características de los productos tiene carácter informativo, salvo que se indique expresamente su naturaleza contractual.

La contratación de viajes combinados, servicios de viaje vinculados u otros servicios turísticos estará sometida a las condiciones particulares correspondientes y a la normativa aplicable, especialmente al Real Decreto Legislativo 1/2007, de 16 de noviembre, en lo relativo a los derechos de consumidores y usuarios.

Antes de formalizar una contratación, el cliente recibirá, cuando legalmente corresponda, la información precontractual obligatoria, incluyendo las características esenciales del servicio, el precio total, las condiciones de pago, las políticas de cancelación y los derechos que le correspondan.

Las condiciones específicas de cada servicio prevalecerán sobre las disposiciones generales de este Aviso Legal en aquellas materias que regulen expresamente.

## Propiedad intelectual e industrial

Todos los contenidos del sitio web, incluyendo, entre otros, textos, imágenes, fotografías, logotipos, marcas, diseños, elementos gráficos, vídeos, documentos, software, código fuente, estructura y demás elementos, están protegidos por la legislación española y europea en materia de propiedad intelectual e industrial.

Los derechos sobre dichos elementos pertenecen a NADIDÚ o a sus respectivos titulares, cuando se utilicen bajo licencia, autorización o cualquier otro título legítimo.

Queda prohibida su reproducción, transformación, distribución, comunicación pública o explotación comercial sin la autorización previa del titular de los derechos, salvo en los supuestos legalmente permitidos.

El acceso al sitio web no implica la cesión ni transmisión de derechos de propiedad intelectual o industrial.

## Responsabilidad del titular

NADIDÚ adoptará las medidas técnicas y organizativas razonables para garantizar el correcto funcionamiento, la disponibilidad y la seguridad del sitio web.

No obstante, no puede garantizar la ausencia absoluta de interrupciones, errores técnicos, incidencias de conectividad o situaciones ajenas a su control.

NADIDÚ no será responsable de los daños derivados de actuaciones imputables exclusivamente al usuario, del uso indebido de la plataforma o de circunstancias de fuerza mayor, sin perjuicio de las responsabilidades que legalmente le correspondan.

Asimismo, la información publicada podrá ser modificada o actualizada para reflejar cambios en los servicios, condiciones comerciales o normativa aplicable.

Ninguna disposición del presente Aviso Legal limitará los derechos irrenunciables reconocidos a consumidores y usuarios por la legislación vigente.

## Enlaces a sitios web de terceros

El sitio web podrá incluir enlaces a páginas externas pertenecientes a proveedores turísticos, plataformas de pago, empresas colaboradoras u otros terceros.

Estos enlaces se facilitan con fines informativos, operativos o comerciales.

NADIDÚ no controla necesariamente los contenidos, políticas de privacidad, condiciones de uso o medidas de seguridad de dichas páginas.

La inclusión de un enlace no implica, por sí misma, la existencia de una relación de patrocinio, aprobación o responsabilidad sobre los contenidos del sitio enlazado.

Todo ello se entiende sin perjuicio de las obligaciones y responsabilidades que puedan corresponder legalmente a NADIDÚ.

## Protección de datos personales

NADIDÚ se compromete a tratar los datos personales de los usuarios conforme a la normativa vigente en materia de protección de datos y, especialmente, a las siguientes disposiciones:

Reglamento (UE) 2016/679 del Parlamento Europeo y del Consejo, de 27 de abril de 2016, General de Protección de Datos (RGPD).

Ley Orgánica 3/2018, de 5 de diciembre, de Protección de Datos Personales y garantía de los derechos digitales (LOPDGDD).

Los datos personales facilitados mediante formularios, registros, solicitudes de información, procesos de contratación o utilización de áreas privadas serán tratados conforme a las finalidades, bases jurídicas y condiciones detalladas en la correspondiente Política de Privacidad.

Los usuarios podrán ejercer los derechos de acceso, rectificación, supresión, oposición, limitación del tratamiento y portabilidad, cuando procedan, mediante solicitud dirigida a:

info@nadidu.es

También podrán presentar una reclamación ante la Agencia Española de Protección de Datos (AEPD), a través de su sitio web oficial: www.aepd.es.

Para obtener información detallada sobre el tratamiento de datos personales, el usuario deberá consultar la Política de Privacidad publicada en este sitio web.

## Política de cookies

El sitio web podrá utilizar cookies propias y de terceros necesarias para su funcionamiento, así como otras destinadas a mejorar la experiencia de navegación, analizar el uso del sitio o desarrollar funcionalidades adicionales.

La utilización de cookies no necesarias estará sujeta a los requisitos de información y consentimiento establecidos por la normativa aplicable.

El usuario podrá consultar información detallada sobre las cookies utilizadas, sus finalidades, duración y mecanismos de configuración o retirada del consentimiento en la correspondiente Política de Cookies.

## Comunicaciones electrónicas

NADIDÚ podrá establecer comunicaciones electrónicas con los usuarios para gestionar solicitudes, contrataciones, incidencias, servicios profesionales y otras actuaciones relacionadas con la relación existente.

El envío de comunicaciones comerciales por medios electrónicos se realizará conforme a lo establecido en la LSSI-CE y demás normativa aplicable.

Cuando resulte necesario, se solicitará previamente el consentimiento del destinatario.

Los usuarios podrán oponerse o solicitar la baja de las comunicaciones comerciales mediante los mecanismos facilitados en cada comunicación o a través de info@nadidu.es.

## Modificaciones del Aviso Legal

NADIDÚ se reserva el derecho de modificar, actualizar o adaptar el presente Aviso Legal cuando resulte necesario para reflejar cambios legislativos, técnicos, organizativos o relacionados con los servicios ofrecidos.

Las modificaciones serán publicadas en el sitio web y resultarán aplicables desde su publicación, salvo que se establezca expresamente otra fecha o que la normativa exija un procedimiento adicional.

Se recomienda a los usuarios consultar periódicamente el contenido de este documento.

## Legislación aplicable y jurisdicción

El presente Aviso Legal se regirá e interpretará conforme a la legislación española y a la normativa de la Unión Europea que resulte aplicable.

Para la resolución de cualquier controversia relacionada con el acceso o utilización del sitio web, las partes se someterán a los juzgados y tribunales que resulten legalmente competentes.

Cuando el usuario tenga la condición de consumidor, se respetarán en todo caso las normas imperativas relativas a la competencia judicial y a la protección de consumidores y usuarios.

## Contacto

Para cualquier consulta, comunicación o incidencia relacionada con el presente Aviso Legal o con el funcionamiento del sitio web, los usuarios podrán dirigirse a:

NADIDU SL — NADIDÚ

Calle Sierra de Luna, 11202 Algeciras, Cádiz, España.

Correo electrónico: info@nadidu.es

Teléfono: +34 650 68 07 95

Registro de agencia de viajes: C.IAN-118115-2

*© 2026 NADIDÚ — NADIDU SL. Todos los derechos reservados.'),
('politica-de-privacidad', 'Política de privacidad', '## Responsable del tratamiento

En cumplimiento de lo establecido en el Reglamento (UE) 2016/679, General de Protección de Datos (RGPD), y en la Ley Orgánica 3/2018, de Protección de Datos Personales y garantía de los derechos digitales (LOPDGDD), se informa a los usuarios sobre el tratamiento de sus datos personales.

Los datos identificativos del responsable son:

Responsable: NADIDU SL

Nombre comercial: NADIDÚ

CIF: B22644447

Domicilio social: Calle Sierra de Luna, 11202 Algeciras, Cádiz, España.

Correo electrónico: info@nadidu.es

Teléfono: +34 650 68 07 95

Registro de agencia de viajes: C.IAN-118115-2

NADIDÚ se compromete a garantizar la privacidad y protección de los datos personales tratados mediante su sitio web, plataforma de gestión y servicios asociados.

## Ámbito de aplicación

La presente Política de Privacidad se aplica al tratamiento de datos personales realizado a través del sitio web de NADIDÚ y de las funcionalidades vinculadas a su actividad.

Comprende, entre otros, los datos de:

Visitantes y usuarios del sitio web.

Personas que solicitan información o presupuestos.

Clientes y viajeros que contratan servicios turísticos.

Agentes comerciales registrados en la plataforma.

Profesionales que solicitan incorporarse a la red de agentes.

Usuarios de las áreas privadas, cursos y herramientas de gestión.

Personas que contactan mediante formularios, correo electrónico, teléfono u otros canales habilitados.

Esta política también resulta aplicable a los datos incorporados a expedientes, reservas y demás procesos de gestión, en aquellos tratamientos en los que NADIDÚ actúe como responsable.

Cuando intervengan otras entidades como responsables independientes o encargadas del tratamiento, se aplicarán las condiciones correspondientes a cada relación.

## Datos personales que podemos tratar

En función del servicio utilizado y de la relación mantenida con NADIDÚ, podrán tratarse las siguientes categorías de datos:

Datos identificativos y de contacto

Nombre, apellidos, documento de identidad, dirección postal, correo electrónico, teléfono y otros datos necesarios para identificar o contactar con el interesado.

Datos profesionales

Información sobre actividad profesional, condición de autónomo o profesional, datos fiscales, información de facturación y datos necesarios para formalizar relaciones comerciales.

Datos de registro y acceso

Nombre de usuario, credenciales protegidas, identificadores de cuenta, fecha de registro, permisos de acceso y actividad necesaria para el funcionamiento y seguridad de la plataforma.

Datos de contratación y viajes

Información de reservas, destinos, fechas, localizadores, servicios contratados, datos de viajeros y acompañantes, documentación necesaria para las gestiones turísticas y comunicaciones relacionadas con los expedientes.

Datos económicos y de facturación

Información de pagos, suscripciones, facturas, comisiones, liquidaciones, importes y, cuando proceda, datos bancarios necesarios para gestionar las operaciones.

Datos de formación y actividad profesional

Cursos realizados, progreso formativo, resultados de evaluaciones, acreditaciones, tareas y actividad asociada a las herramientas profesionales.

Datos técnicos

Dirección IP, información del dispositivo, navegador, registros de acceso, identificadores técnicos y datos relativos al uso y seguridad de la plataforma.

Datos de comunicaciones

Consultas, solicitudes, incidencias, mensajes y documentación intercambiada mediante los canales de atención habilitados.

NADIDÚ aplicará el principio de minimización de datos, limitando su recogida a aquellos que resulten adecuados, pertinentes y necesarios para cada finalidad.

## Finalidades del tratamiento

NADIDÚ podrá tratar los datos personales para las siguientes finalidades:

4.1. Atención de consultas y solicitudes

Gestionar las consultas recibidas mediante formularios, correo electrónico, teléfono u otros canales de comunicación.

Incluye responder solicitudes de información, presupuestos, consultas comerciales y peticiones relacionadas con los servicios ofrecidos.

4.2. Gestión de servicios turísticos

Gestionar solicitudes, reservas, contrataciones y expedientes relacionados con viajes y servicios turísticos.

Esto podrá incluir:

Identificación de viajeros.

Gestión de reservas y localizadores.

Comunicación con proveedores turísticos.

Tramitación de documentación necesaria.

Coordinación de servicios contratados.

Atención de incidencias y solicitudes.

Cumplimiento de obligaciones legales.

4.7. Comunicaciones administrativas y operativas

Enviar comunicaciones relacionadas con la prestación del servicio, actualizaciones de cuentas, incidencias, pagos, cambios contractuales, documentación y otras informaciones necesarias para la relación existente.

4.8. Comunicaciones comerciales

Enviar información sobre novedades, servicios, promociones, actividades o propuestas comerciales cuando exista una base jurídica válida para ello.

Cuando resulte necesario, se solicitará previamente el consentimiento del interesado.

4.9. Seguridad y mantenimiento

Garantizar el funcionamiento y seguridad de la plataforma, prevenir accesos no autorizados, detectar usos fraudulentos, resolver incidencias y mantener la integridad de los sistemas.

4.10. Cumplimiento de obligaciones legales

Atender las obligaciones legales, fiscales, contables, administrativas y demás responsabilidades derivadas de la actividad empresarial y turística.

## Base jurídica del tratamiento

El tratamiento de los datos personales se realizará sobre alguna de las siguientes bases jurídicas, según corresponda:

Ejecución de un contrato o medidas precontractuales (artículo 6.1.b del RGPD).

Aplicable a la gestión de servicios turísticos, contratación, suscripciones, cuentas profesionales y demás actuaciones necesarias para prestar los servicios solicitados por el interesado.

Cumplimiento de obligaciones legales (artículo 6.1.c del RGPD).

Aplicable al cumplimiento de obligaciones fiscales, contables, administrativas y otras exigencias normativas.

Consentimiento del interesado (artículo 6.1.a del RGPD).

Aplicable a determinadas comunicaciones comerciales, cookies no necesarias y otros tratamientos para los que se solicite autorización.

Interés legítimo (artículo 6.1.f del RGPD).

Podrá aplicarse a determinadas actividades de seguridad, prevención del fraude, protección de los sistemas y gestión de relaciones profesionales, previa valoración de los intereses y derechos de los afectados.

El consentimiento podrá retirarse en cualquier momento, sin que ello afecte a la licitud del tratamiento realizado anteriormente.

## Carácter obligatorio de los datos

Los formularios y procesos de registro podrán identificar determinados campos como obligatorios.

La falta de cumplimentación de los datos necesarios podrá impedir la atención de una solicitud, la formalización de una contratación o la prestación de determinadas funcionalidades.

Los datos solicitados deberán ser veraces, exactos y mantenerse actualizados.

El usuario será responsable de comunicar cualquier modificación relevante de su información.

## Datos de viajeros y terceros

Para gestionar determinados servicios turísticos podrá resultar necesario tratar información de viajeros, acompañantes u otras personas vinculadas a una reserva o expediente.

Cuando un usuario o agente facilite datos personales de terceros, deberá contar con una base jurídica adecuada para dicha comunicación y cumplir las obligaciones de información que le correspondan.

NADIDÚ tratará estos datos únicamente para las finalidades legítimas relacionadas con la gestión de los servicios y conforme a la normativa aplicable.

Cuando los datos no se obtengan directamente del interesado, NADIDÚ cumplirá las obligaciones de información previstas en el artículo 14 del RGPD, salvo que resulte aplicable alguna excepción legal.

## Destinatarios de los datos personales

Los datos personales podrán comunicarse a terceros cuando resulte necesario para prestar los servicios contratados, cumplir obligaciones legales o exista otra base jurídica válida.

Entre los posibles destinatarios se encuentran:

Proveedores turísticos, alojamientos, transportistas, operadores y empresas responsables de los servicios contratados.

Entidades financieras y proveedores de servicios de pago.

Administraciones públicas y autoridades competentes cuando exista obligación legal.

Asesorías fiscales, contables y jurídicas, cuando corresponda.

Agentes comerciales y personal autorizado, dentro de sus funciones y permisos.

Otros destinatarios necesarios para la ejecución de los servicios solicitados.

Asimismo, determinados proveedores tecnológicos podrán acceder a datos personales en calidad de encargados del tratamiento, incluyendo servicios de alojamiento web, mantenimiento, correo electrónico, plataformas de gestión y soporte informático.

En estos casos, NADIDÚ formalizará los acuerdos exigidos por el artículo 28 del RGPD cuando correspondan.

No se venderán datos personales a terceros.

## Transferencias internacionales de datos

La prestación de servicios turísticos puede requerir la comunicación de datos personales a proveedores situados fuera del Espacio Económico Europeo, especialmente cuando el destino contratado se encuentre en un tercer país.

Estas comunicaciones se realizarán únicamente cuando exista una base jurídica y un mecanismo válido conforme al RGPD.

Cuando proceda, se aplicarán decisiones de adecuación, garantías apropiadas u otras excepciones legalmente previstas, incluida la necesidad de determinadas transferencias para la ejecución de contratos de viaje.

La utilización de proveedores tecnológicos establecidos fuera del Espacio Económico Europeo también estará sujeta a los requisitos aplicables a las transferencias internacionales.

Los interesados podrán solicitar información sobre las transferencias que les afecten y las garantías correspondientes mediante el correo info@nadidu.es.

## Plazos de conservación

Los datos personales se conservarán durante el tiempo necesario para cumplir las finalidades para las que fueron recogidos.

Con carácter general, se aplicarán los siguientes criterios:

Consultas y solicitudes: durante el tiempo necesario para atenderlas y, posteriormente, durante los plazos que resulten necesarios para atender posibles responsabilidades.

Clientes y expedientes turísticos: mientras se mantenga la relación contractual y durante los plazos legales de conservación o prescripción que correspondan.

Agentes comerciales: mientras permanezca activa la relación profesional y, una vez finalizada, durante los plazos legalmente exigibles.

Suscripciones, facturas y liquidaciones: durante los periodos establecidos por la normativa fiscal, contable y mercantil aplicable.

Formación: mientras sea necesario para gestionar y acreditar la formación, de acuerdo con la finalidad y las obligaciones correspondientes.

Comunicaciones comerciales: hasta que el interesado retire su consentimiento o ejerza su derecho de oposición, sin perjuicio de conservar la información mínima necesaria para acreditar dichas solicitudes.

Registros técnicos y de seguridad: durante periodos proporcionados a las necesidades de seguridad y prevención de incidentes.

Cuando proceda, los datos podrán mantenerse debidamente bloqueados durante los plazos legalmente exigidos y serán eliminados o anonimizados una vez transcurridos.

## Derechos de los interesados

Los interesados podrán ejercer los derechos reconocidos por la normativa de protección de datos.

Estos incluyen:

Acceso: conocer si se están tratando sus datos personales y obtener información sobre dicho tratamiento.

Rectificación: solicitar la corrección de datos inexactos o incompletos.

Supresión: solicitar la eliminación de sus datos cuando proceda legalmente.

Oposición: oponerse a determinados tratamientos, especialmente a los realizados con fines de mercadotecnia directa.

Limitación: solicitar la restricción del tratamiento en los supuestos previstos legalmente.

Portabilidad: recibir los datos personales facilitados en un formato estructurado y transmitirlos a otro responsable cuando se cumplan los requisitos legales.

Retirada del consentimiento: retirar las autorizaciones previamente concedidas.

Derechos frente a decisiones automatizadas: no ser objeto, en los supuestos legalmente previstos, de decisiones basadas únicamente en tratamientos automatizados que produzcan efectos jurídicos o afecten significativamente al interesado.

Para ejercer estos derechos, el interesado podrá enviar una solicitud a:

info@nadidu.es

También podrá remitirla por escrito al domicilio social de NADIDÚ.

La solicitud deberá permitir identificar al interesado y concretar el derecho que desea ejercer.

Cuando existan dudas razonables sobre su identidad, podrá solicitarse información adicional proporcionada para verificarla.

NADIDÚ responderá dentro de los plazos establecidos por la normativa aplicable.

## Reclamaciones ante la autoridad de control

Si el interesado considera que el tratamiento de sus datos personales vulnera la normativa vigente, podrá presentar una reclamación ante la Agencia Española de Protección de Datos.

Agencia Española de Protección de Datos (AEPD)

Sitio web: https://www.aepd.es

Sin perjuicio de este derecho, el interesado podrá contactar previamente con NADIDÚ mediante info@nadidu.es para plantear cualquier cuestión relacionada con el tratamiento de sus datos.

## Seguridad de la información

NADIDÚ adoptará medidas técnicas y organizativas apropiadas al riesgo para proteger los datos personales frente a accesos no autorizados, pérdidas, alteraciones, destrucción o divulgaciones indebidas.

Estas medidas podrán incluir:

Control de acceso mediante credenciales individuales.

Asignación de permisos según las funciones de cada usuario.

Protección de las comunicaciones mediante mecanismos de cifrado cuando corresponda.

Copias de seguridad y procedimientos de recuperación.

Actualización y mantenimiento de los sistemas.

Medidas de prevención y detección de incidentes.

Procedimientos de gestión de incidencias de seguridad.

Las medidas se revisarán y adaptarán conforme a la evolución de los riesgos y de los servicios prestados.

## Tratamiento de datos de menores

Los servicios profesionales y las funcionalidades de registro de agentes están dirigidos a personas con capacidad legal para establecer la relación correspondiente.

En el ámbito turístico, NADIDÚ podrá tratar datos personales de menores cuando resulte necesario para gestionar reservas, viajes o servicios contratados por sus representantes legales o por personas debidamente autorizadas.

El tratamiento se realizará conforme a la normativa aplicable, utilizando únicamente los datos necesarios y las garantías adecuadas.

Cuando un tratamiento requiera consentimiento, se respetarán las reglas legales relativas a la edad y a la intervención de quienes ejerzan la patria potestad o tutela.

## Comunicaciones comerciales

NADIDÚ podrá remitir comunicaciones relacionadas con sus servicios turísticos, actividades, novedades profesionales y ofertas comerciales cuando exista una base jurídica que lo permita.

El destinatario podrá solicitar en cualquier momento dejar de recibir comunicaciones comerciales mediante los mecanismos de baja disponibles o escribiendo a info@nadidu.es.

La oposición a comunicaciones comerciales no impedirá el envío de comunicaciones estrictamente necesarias para la ejecución de contratos, la gestión de cuentas o el cumplimiento de obligaciones legales.

## Cookies y tecnologías similares

El sitio web podrá utilizar cookies y tecnologías similares para garantizar su funcionamiento, mantener sesiones de usuario, recordar preferencias y desarrollar otras funcionalidades.

La utilización de cookies no necesarias estará sujeta a los requisitos de información y consentimiento establecidos por la normativa vigente.

Los usuarios podrán consultar información sobre las cookies utilizadas, sus finalidades, proveedores, duración y mecanismos de configuración en la Política de Cookies del sitio web.

## Decisiones automatizadas y elaboración de perfiles

Las herramientas de NADIDÚ podrán realizar operaciones automatizadas de carácter administrativo u operativo, como el cálculo de importes, seguimiento de suscripciones, asignación de estados o generación de liquidaciones.

Estas operaciones no implican necesariamente la adopción de decisiones individuales automatizadas en el sentido del artículo 22 del RGPD.

Si NADIDÚ incorporase tratamientos automatizados que produjeran efectos jurídicos o afectaran significativamente a los interesados, proporcionaría la información exigida legalmente y aplicaría las garantías correspondientes.

## Actualización de la Política de Privacidad

NADIDÚ podrá modificar esta Política de Privacidad para adaptarla a cambios normativos, técnicos, organizativos o relacionados con los servicios prestados.

La versión actualizada se publicará en el sitio web indicando su fecha de revisión.

Cuando los cambios afecten de manera sustancial al tratamiento de datos personales, se informará a los interesados y se solicitará nuevamente su consentimiento cuando sea legalmente necesario.

## Contacto en materia de protección de datos

Para cualquier consulta sobre privacidad, protección de datos personales o ejercicio de derechos, los interesados podrán contactar con:

NADIDU SL — NADIDÚ

CIF: B22644447

Calle Sierra de Luna, 11202 Algeciras, Cádiz, España.

Correo electrónico: info@nadidu.es

Teléfono: +34 650 68 07 95

© 2026 NADIDÚ — NADIDU SL. Todos los derechos reservados.'),
('condiciones-generales', 'Condiciones generales de contratación', '## Identificación del titular

Las presentes Condiciones Generales de Contratación regulan las compras, reservas y contrataciones realizadas a través del sitio web de NADIDÚ, así como la contratación de servicios digitales y suscripciones profesionales cuando estén disponibles.

Datos del titular:

Denominación social: NADIDU SL

Nombre comercial: NADIDÚ

CIF: B22644447

Registro de agencia de viajes: C.IAN-118115-2

Domicilio social: Calle Sierra de Luna, 11202 Algeciras, Cádiz, España.

Correo electrónico: info@nadidu.es

Teléfono: +34 650 68 07 95

En adelante, la entidad será denominada «NADIDÚ».

## Objeto de las condiciones

Las presentes condiciones regulan las relaciones contractuales establecidas entre NADIDÚ y los usuarios que contraten productos o servicios a través de sus canales digitales.

Entre los servicios que pueden ofrecerse se encuentran:

Reservas y contratación de viajes y servicios turísticos.

Comercialización e intermediación de productos turísticos.

Acceso a herramientas profesionales para agentes comerciales.

Suscripciones a plataformas digitales.

Formación y recursos destinados a profesionales del sector turístico.

Otros servicios complementarios relacionados con la actividad de NADIDÚ.

Las condiciones particulares de cada producto o servicio complementarán las presentes condiciones generales y prevalecerán en aquellas cuestiones que regulen específicamente, siempre dentro de los límites establecidos por la legislación vigente.

## Requisitos para contratar

Para contratar los servicios ofrecidos por NADIDÚ, el usuario deberá tener capacidad legal suficiente para obligarse contractualmente.

En caso de contratar en representación de una empresa o de otra persona, deberá disponer de la autorización correspondiente.

El usuario se compromete a proporcionar información veraz, exacta y actualizada durante los procesos de compra, registro o contratación.

NADIDÚ podrá solicitar la documentación necesaria para verificar la identidad, representación o condición profesional del contratante cuando resulte justificado.

## Proceso de compra y contratación

El proceso de contratación podrá comprender las siguientes fases:

Selección del servicio, producto turístico o suscripción.

Consulta de las características, precios y condiciones particulares.

Introducción de los datos necesarios para la contratación.

Revisión de la información y del importe total.

Aceptación expresa de las condiciones aplicables.

Selección del método de pago.

Confirmación de la contratación.

Antes de realizar el pago, el usuario tendrá la posibilidad de revisar los datos introducidos y corregir posibles errores.

Cuando la contratación implique una obligación de pago, el botón de confirmación deberá identificar claramente dicha obligación.

Una vez formalizada la contratación, NADIDÚ facilitará la confirmación correspondiente por medios electrónicos, conforme a los requisitos legales aplicables.

La recepción de una solicitud de reserva no implicará necesariamente la confirmación definitiva del servicio turístico, que dependerá de las condiciones comunicadas durante el proceso de contratación.

## Precios e impuestos

Los precios de los servicios se mostrarán en euros (€), salvo indicación expresa en contrario.

En las contrataciones dirigidas a consumidores, se informará del precio total, incluidos los impuestos y gastos adicionales aplicables, antes de finalizar la compra.

Cuando existan servicios adicionales, suplementos, gastos de gestión o conceptos no incluidos en el precio inicial, se comunicarán de forma clara antes de la contratación.

En los servicios profesionales, los precios podrán indicarse sin impuestos siempre que se informe adecuadamente de los tributos aplicables.

NADIDÚ podrá modificar los precios de sus servicios, sin que dichas modificaciones afecten retroactivamente a contratos ya formalizados, salvo en los supuestos legal o contractualmente permitidos.

## Métodos de pago

Los servicios podrán abonarse mediante los métodos de pago que se encuentren habilitados en cada momento en la plataforma.

Estos podrán incluir tarjetas bancarias, pasarelas de pago electrónicas u otros sistemas autorizados.

Los pagos electrónicos podrán procesarse mediante entidades financieras o proveedores externos especializados.

NADIDÚ no tendrá necesariamente acceso a los datos completos de las tarjetas bancarias utilizadas, que podrán ser tratados directamente por el proveedor del servicio de pago.

La contratación quedará sujeta a la correcta autorización del pago cuando este sea exigible para formalizar la operación.

Si una transacción no se completa correctamente, el usuario deberá comprobar el estado de la operación antes de repetirla para evitar posibles cargos duplicados.

## Contratación de viajes y servicios turísticos

La contratación de servicios turísticos se realizará conforme a la información precontractual y las condiciones específicas facilitadas para cada producto.

Estas condiciones podrán incluir:

Destino e itinerario.

Fechas y duración del viaje.

Servicios incluidos y excluidos.

Características del alojamiento y transporte.

Precio total.

Calendario y condiciones de pago.

Requisitos de documentación.

Condiciones de modificación y cancelación.

Información sobre seguros y garantías aplicables.

Cuando el servicio constituya un viaje combinado o un servicio de viaje vinculado, se aplicará la normativa específica prevista en el Real Decreto Legislativo 1/2007, de 16 de noviembre.

NADIDÚ facilitará la información normalizada y contractual legalmente exigida en cada supuesto.

## Disponibilidad y confirmación de reservas

La disponibilidad de alojamientos, transportes, actividades y otros servicios turísticos podrá depender de terceros proveedores.

La información publicada en el sitio web podrá estar sujeta a cambios hasta que se confirme definitivamente la contratación.

Cuando un servicio solicitado no esté disponible, NADIDÚ informará al cliente y ofrecerá, cuando resulte posible, alternativas adecuadas.

Cualquier modificación de las condiciones esenciales de una contratación ya formalizada se gestionará conforme a la normativa aplicable y a los derechos reconocidos al viajero.

## Cancelaciones, modificaciones y desistimiento en servicios turísticos

Las condiciones de cancelación y modificación dependerán de la naturaleza del servicio contratado y de la normativa aplicable.

En los viajes combinados, el viajero podrá resolver el contrato antes del inicio del viaje, de acuerdo con las condiciones y consecuencias previstas legalmente.

Podrán existir gastos de terminación adecuados y justificables, salvo en los supuestos en los que la legislación reconozca el derecho a resolver sin penalización.

En determinadas circunstancias extraordinarias e inevitables que afecten significativamente a la ejecución del viaje combinado o al transporte de pasajeros al destino, el viajero podrá tener derecho a resolver el contrato sin pagar gastos de terminación, conforme a la normativa vigente.

Los servicios de alojamiento, transporte, alquiler de vehículos, restauración y actividades de esparcimiento previstos para una fecha o periodo de ejecución específicos podrán estar exceptuados del derecho general de desistimiento de catorce días, en los términos legalmente establecidos.

Las condiciones específicas de cada reserva se comunicarán antes de formalizar la contratación.

## Reembolsos de servicios turísticos

Cuando corresponda legal o contractualmente la devolución de cantidades abonadas, NADIDÚ gestionará el reembolso conforme a las condiciones del servicio y la normativa aplicable.

En los viajes combinados, cuando proceda el reembolso por terminación del contrato, se respetará el plazo máximo legal correspondiente, que con carácter general será de catorce días naturales.

Los reembolsos se efectuarán mediante un medio de pago adecuado, respetando los derechos del consumidor y las obligaciones legalmente establecidas.

## Obligaciones de los usuarios

Los usuarios deberán utilizar los servicios contratados conforme a la legislación vigente, las presentes condiciones y las instrucciones de uso facilitadas.

En particular, deberán:

Proporcionar datos veraces y actualizados.

Proteger sus credenciales de acceso.

Respetar la confidencialidad de la información profesional.

No utilizar la plataforma con fines ilícitos o fraudulentos.

No acceder a información de otros usuarios sin autorización.

Cumplir las obligaciones económicas derivadas de sus contratos.

Respetar los derechos de propiedad intelectual e industrial.

El incumplimiento grave de estas obligaciones podrá dar lugar a las medidas legal y contractualmente procedentes.

## Responsabilidad

NADIDÚ responderá de las obligaciones que le correspondan legal y contractualmente en relación con los servicios comercializados.

En los viajes combinados se aplicará el régimen específico de responsabilidad previsto en la legislación de consumidores y usuarios.

La intervención de proveedores turísticos no excluirá las responsabilidades que correspondan legalmente a NADIDÚ.

En los servicios digitales, NADIDÚ adoptará medidas razonables para garantizar su disponibilidad y funcionamiento, sin perjuicio de las interrupciones justificadas por mantenimiento, incidencias técnicas o causas ajenas a su control.

Ninguna disposición de estas condiciones excluirá o limitará los derechos irrenunciables de consumidores y usuarios.

## Facturación

NADIDÚ emitirá las facturas y justificantes correspondientes conforme a la normativa fiscal aplicable.

El usuario deberá facilitar los datos fiscales necesarios cuando proceda.

Las facturas podrán ponerse a disposición del cliente mediante medios electrónicos, respetando los requisitos legales correspondientes.

Los usuarios podrán solicitar información sobre su facturación a través de info@nadidu.es.

## Protección de datos personales

Los datos personales facilitados durante los procesos de compra, contratación, registro o suscripción serán tratados por NADIDU SL conforme al Reglamento General de Protección de Datos y demás normativa aplicable.

Los interesados podrán consultar información detallada sobre las finalidades, bases jurídicas, destinatarios, conservación y ejercicio de derechos en la Política de Privacidad publicada en el sitio web.

Para cualquier consulta relacionada con el tratamiento de datos personales podrán contactar mediante info@nadidu.es.

## Atención al cliente y reclamaciones

Los usuarios podrán presentar consultas, incidencias, solicitudes o reclamaciones mediante los siguientes canales:

NADIDU SL — NADIDÚ

Correo electrónico: info@nadidu.es

Teléfono: +34 650 68 07 95

Dirección postal: Calle Sierra de Luna, 11202 Algeciras, Cádiz, España.

Las reclamaciones relacionadas con viajes y servicios turísticos serán atendidas conforme a la normativa sectorial y de protección de consumidores aplicable.

Los consumidores podrán acudir a los organismos de consumo y a los mecanismos de resolución de controversias que correspondan legalmente.

## Modificación de las condiciones

NADIDÚ podrá actualizar las presentes condiciones para adaptarlas a modificaciones legales, técnicas, operativas o comerciales.

Las nuevas condiciones serán publicadas en el sitio web.

Las modificaciones no afectarán retroactivamente a los contratos ya formalizados, salvo cuando exista una base legal o contractual válida para ello.

Cuando se produzcan modificaciones relevantes en servicios de prestación continuada, se respetarán las obligaciones de información y los derechos que correspondan al usuario.

## Legislación aplicable y jurisdicción

Las presentes condiciones se regirán por la legislación española y por las disposiciones de la Unión Europea que resulten aplicables.

En particular, serán de aplicación, cuando corresponda:

Ley 34/2002, de Servicios de la Sociedad de la Información y de Comercio Electrónico.

Real Decreto Legislativo 1/2007, por el que se aprueba el texto refundido de la Ley General para la Defensa de los Consumidores y Usuarios.

Normativa aplicable a viajes combinados y servicios de viaje vinculados.

Reglamento General de Protección de Datos y normativa española complementaria.

Legislación civil, mercantil y turística aplicable.

Las controversias se someterán a los juzgados y tribunales legalmente competentes.

Cuando el contratante tenga la condición de consumidor, se respetarán las normas imperativas de competencia judicial que resulten aplicables.

## Contacto

Para cualquier consulta relacionada con las presentes condiciones de compra y contratación, el usuario podrá contactar con:

NADIDU SL — NADIDÚ

CIF: B22644447

Registro de agencia de viajes: C.IAN-118115-2

Calle Sierra de Luna, 11202 Algeciras, Cádiz, España.

Correo electrónico: info@nadidu.es

Teléfono: +34 650 68 07 95

© 2026 NADIDÚ — NADIDU SL. Todos los derechos reservados.'),
('politica-de-cookies', 'Política de cookies', '## Qué son las cookies

Las cookies son pequeños archivos que un sitio web guarda en tu navegador. Sirven, por ejemplo, para recordar que has iniciado una sesión o para que un formulario funcione de forma segura.

## Qué usa este sitio

Este sitio solo utiliza cookies técnicas, necesarias para que funcione. No usamos cookies de publicidad ni de seguimiento, y no compartimos información de tu navegación con terceros.

- **gvsid**: cookie de sesión. Solo se crea cuando usas un formulario (por ejemplo, el de presupuesto o el de contacto) y sirve para protegerlo frente a envíos falsos. Caduca al cerrar el navegador.

Además, al elegir destinos en el formulario de la página de inicio, tu navegador guarda esa elección de forma temporal (almacenamiento de sesión) para continuar en la página de presupuesto. No sale de tu dispositivo y se borra al cerrar la pestaña.

Una persona que solo lee las páginas no recibe ninguna cookie.

## Cómo gestionarlas

Puedes bloquear o borrar las cookies desde la configuración de tu navegador. Si bloqueas las técnicas, algunos formularios podrían no funcionar.

## Cambios

Si en el futuro incorporamos herramientas de medición u otras cookies, actualizaremos esta página y te pediremos tu consentimiento antes de activarlas.

## Responsable

El responsable del sitio y del tratamiento de datos es NADIDU SL (CIF B22644447, registro de agencia de viajes C.IAN-118115-2), con domicilio en Calle Sierra de Luna, 11202 Algeciras, Cádiz. Puedes escribir a info@nadidu.es.');
