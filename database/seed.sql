-- Guanaco Viajes · contenido inicial (solo para una instalación nueva)
-- Importar DESPUÉS de schema.sql. Todo lo de aquí se edita luego desde el panel.
SET NAMES utf8mb4;

INSERT INTO settings (clave, valor) VALUES
('site.nombre',            'Guanaco Viajes'),
('site.telefono',          '+34 600 53 88 85'),
('site.whatsapp',          '34600538885'),
('site.whatsapp_mensaje',  'Hola, me gustaría información sobre un viaje.'),
('site.email',             'hola@guanacoviajes.es'),
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
(1, 'Viajes a medida',  'Viaja con',     'curiosidad', 'assets/img/seed/foto-5.jpg',  '40% 58%', 'Senderista caminando por una duna al atardecer con montañas al fondo', 'oscuro', 'Pide tu presupuesto',  '/presupuesto'),
(2, 'Sudamérica',       'Muévete con',   'libertad',   'assets/img/seed/foto-4.jpg',  '50% 62%', 'Carretera recta entre campos dorados con volcanes al fondo',         'oscuro', 'Pide tu presupuesto',  '/presupuesto'),
(3, 'Viajes en grupo',  'Vuelve con',    'historias',  'assets/img/seed/foto-27.jpg', '50% 46%', 'Pico de roca iluminado por el sol del atardecer en los Dolomitas',    'oscuro', 'Ver viajes en grupo',  '/viajes-en-grupo');

INSERT INTO trip_types (slug, titulo, descripcion, cta_texto, cta_url, foto, foto_pos, foto_alt, orden) VALUES
('viajes-a-medida', 'Viajes a medida', 'Tú traes la idea, aunque sea solo una inquietud. Diseñamos un viaje pensado para ti, y no tú para el viaje.', 'Cuéntanos tu idea', '/presupuesto', 'assets/img/seed/foto-29.jpg', '50% 45%', 'Persona de espaldas con chaqueta roja frente a una montaña nevada', 1),
('viajes-en-grupo', 'Viajes en grupo', 'Salidas con propuestas diferentes, cuidadas y con personalidad, para compartir el camino con otras personas.', 'Ver viajes en grupo', '/viajes-en-grupo', 'assets/img/seed/foto-3.jpg', '50% 60%', 'Proa de una barca de madera sobre un lago de montaña', 2);

INSERT INTO interests (nombre, orden) VALUES
('Naturaleza', 1), ('Cultura', 2), ('Gastronomía', 3), ('Ciudades', 4),
('Playas', 5), ('Montaña', 6), ('Descanso', 7), ('Fuera de lo habitual', 8);
