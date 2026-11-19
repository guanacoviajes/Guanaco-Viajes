-- Guanaco Viajes · VOLVER ATRÁS de la actualización 2026-11-17.
--
-- Úsalo solo si necesitas deshacerla. Quita las columnas y la tabla que añadió.
-- ATENCIÓN: se pierde lo que hayas escrito en los campos nuevos (entrada, zonas, cuándo ir, preguntas) y la relación
-- de artículos con varios países. Los artículos conservan su país principal (posts.country_id), que no se toca.
-- Después hay que volver también a los archivos anteriores (ver docs/ACTUALIZACIONES.md).

DROP TABLE IF EXISTS post_countries;
ALTER TABLE countries
  DROP COLUMN faqs,
  DROP COLUMN cuando_ir,
  DROP COLUMN zonas,
  DROP COLUMN entrada;
