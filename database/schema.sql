-- Guanaco Viajes · esquema de la base de datos
-- MySQL 5.7+ / MariaDB 10.3+ · codificación utf8mb4
-- Importar PRIMERO este archivo y después seed.sql (solo en una instalación nueva).

SET NAMES utf8mb4;

-- Ajustes y textos editables (clave → valor)
CREATE TABLE IF NOT EXISTS settings (
  clave       VARCHAR(100)  NOT NULL,
  valor       TEXT          NOT NULL,
  updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Países (destinos)
CREATE TABLE IF NOT EXISTS countries (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  slug        VARCHAR(60)   NOT NULL,
  nombre      VARCHAR(80)   NOT NULL,
  continente  ENUM('sudamerica','europa') NOT NULL,
  orden       SMALLINT      NOT NULL DEFAULT 0,
  resumen     TEXT          NOT NULL,
  foto        VARCHAR(255)  NOT NULL DEFAULT '',
  foto_pos    VARCHAR(30)   NOT NULL DEFAULT '50% 50%',
  foto_alt    VARCHAR(200)  NOT NULL DEFAULT '',
  seo_title   VARCHAR(160)  NULL,
  seo_desc    VARCHAR(255)  NULL,
  publicado   TINYINT(1)    NOT NULL DEFAULT 1,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_countries_slug (slug),
  KEY idx_countries_cont (continente, publicado, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Galería de cada país (0 a 6 fotos)
CREATE TABLE IF NOT EXISTS country_photos (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  country_id  INT UNSIGNED  NOT NULL,
  archivo     VARCHAR(255)  NOT NULL,
  alt         VARCHAR(200)  NOT NULL DEFAULT '',
  orden       SMALLINT      NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_cphotos_country (country_id, orden),
  CONSTRAINT fk_cphotos_country FOREIGN KEY (country_id) REFERENCES countries (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Diapositivas del carrusel de inicio
CREATE TABLE IF NOT EXISTS carousel_slides (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  orden            SMALLINT     NOT NULL DEFAULT 0,
  kicker           VARCHAR(80)  NOT NULL DEFAULT '',
  titular          VARCHAR(120) NOT NULL,
  titular_enfasis  VARCHAR(80)  NOT NULL DEFAULT '',
  foto             VARCHAR(255) NOT NULL,
  foto_pos         VARCHAR(30)  NOT NULL DEFAULT '50% 50%',
  foto_alt         VARCHAR(200) NOT NULL DEFAULT '',
  tono_menu        ENUM('oscuro','claro') NOT NULL DEFAULT 'oscuro',
  cta_texto        VARCHAR(60)  NOT NULL DEFAULT '',
  cta_url          VARCHAR(255) NOT NULL DEFAULT '',
  activo           TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  KEY idx_slides (activo, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tipos de viaje (a medida / en grupo)
CREATE TABLE IF NOT EXISTS trip_types (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug         VARCHAR(60)  NOT NULL,
  titulo       VARCHAR(80)  NOT NULL,
  descripcion  TEXT         NOT NULL,
  cta_texto    VARCHAR(60)  NOT NULL DEFAULT '',
  cta_url      VARCHAR(255) NOT NULL DEFAULT '',
  foto         VARCHAR(255) NOT NULL DEFAULT '',
  foto_pos     VARCHAR(30)  NOT NULL DEFAULT '50% 50%',
  foto_alt     VARCHAR(200) NOT NULL DEFAULT '',
  orden        SMALLINT     NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_trip_types_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Artículos del blog
CREATE TABLE IF NOT EXISTS posts (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  slug          VARCHAR(120)  NOT NULL,
  titulo        VARCHAR(200)  NOT NULL,
  extracto      TEXT          NOT NULL,
  cuerpo        MEDIUMTEXT    NOT NULL,
  portada       VARCHAR(255)  NOT NULL DEFAULT '',
  portada_alt   VARCHAR(200)  NOT NULL DEFAULT '',
  country_id    INT UNSIGNED  NULL,
  publicado     TINYINT(1)    NOT NULL DEFAULT 0,
  publicado_en  DATETIME      NULL,
  seo_title     VARCHAR(160)  NULL,
  seo_desc      VARCHAR(255)  NULL,
  created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_posts_slug (slug),
  KEY idx_posts_pub (publicado, publicado_en),
  KEY idx_posts_country (country_id),
  CONSTRAINT fk_posts_country FOREIGN KEY (country_id) REFERENCES countries (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Opciones del paso «Tu idea» del formulario de presupuesto (editables)
CREATE TABLE IF NOT EXISTS interests (
  id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre  VARCHAR(60)  NOT NULL,
  orden   SMALLINT     NOT NULL DEFAULT 0,
  activo  TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
