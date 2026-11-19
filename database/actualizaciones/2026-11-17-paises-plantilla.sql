-- Guanaco Viajes · actualización 2026-11-17: plantilla ampliada de las páginas de país y artículos con varios países.
--
-- IMPORTANTE
--  · Importar UNA sola vez, en la base de datos de la web (phpMyAdmin → Importar, o por SSH).
--  · Antes, haz una copia de seguridad de la base de datos (ver docs/ACTUALIZACIONES.md).
--  · Es solo aditiva: no borra ni modifica contenido. Si algo falla, database/actualizaciones/2026-11-17-paises-plantilla-VOLVER-ATRAS.sql la deshace.
--  · El orden no importa: la web funciona con o sin este script (las secciones nuevas se muestran cuando existe).

-- 1 · Campos nuevos de cada país (todos opcionales)
ALTER TABLE countries
  ADD COLUMN entrada   TEXT NULL AFTER resumen,
  ADD COLUMN zonas     TEXT NULL AFTER entrada,
  ADD COLUMN cuando_ir TEXT NULL AFTER zonas,
  ADD COLUMN faqs      TEXT NULL AFTER cuando_ir;

-- 2 · Artículos con varios países (el primero, por «orden», es el principal)
CREATE TABLE IF NOT EXISTS post_countries (
  post_id     INT UNSIGNED NOT NULL,
  country_id  INT UNSIGNED NOT NULL,
  orden       TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (post_id, country_id),
  KEY idx_pc_country (country_id),
  CONSTRAINT fk_pc_post    FOREIGN KEY (post_id)    REFERENCES posts (id)     ON DELETE CASCADE,
  CONSTRAINT fk_pc_country FOREIGN KEY (country_id) REFERENCES countries (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3 · Los artículos que ya tenían un país lo conservan
INSERT IGNORE INTO post_countries (post_id, country_id, orden)
  SELECT id, country_id, 0 FROM posts WHERE country_id IS NOT NULL;
