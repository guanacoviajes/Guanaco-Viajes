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

-- Páginas de texto editables (Quiénes somos y páginas legales)
CREATE TABLE IF NOT EXISTS pages (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  slug        VARCHAR(80)   NOT NULL,
  titulo      VARCHAR(200)  NOT NULL,
  cuerpo      MEDIUMTEXT    NOT NULL,
  seo_title   VARCHAR(160)  NULL,
  seo_desc    VARCHAR(255)  NULL,
  updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pages_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Salidas de viajes en grupo
CREATE TABLE IF NOT EXISTS departures (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  country_id    INT UNSIGNED  NULL,
  titulo        VARCHAR(160)  NOT NULL,
  descripcion   TEXT          NOT NULL,
  fecha_inicio  DATE          NULL,
  fecha_fin     DATE          NULL,
  plazas_texto  VARCHAR(80)   NOT NULL DEFAULT '',
  precio_desde  VARCHAR(40)   NOT NULL DEFAULT '',
  senal_texto   VARCHAR(160)  NOT NULL DEFAULT '',
  foto          VARCHAR(255)  NOT NULL DEFAULT '',
  foto_alt      VARCHAR(200)  NOT NULL DEFAULT '',
  publicada     TINYINT(1)    NOT NULL DEFAULT 0,
  orden         SMALLINT      NOT NULL DEFAULT 0,
  created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_dep_pub (publicada, fecha_inicio),
  KEY idx_dep_country (country_id),
  CONSTRAINT fk_dep_country FOREIGN KEY (country_id) REFERENCES countries (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Solicitudes recibidas (formulario de presupuesto y formulario de contacto)
CREATE TABLE IF NOT EXISTS requests (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  origen          ENUM('presupuesto','contacto') NOT NULL,
  estado          ENUM('nueva','leida','respondida','archivada') NOT NULL DEFAULT 'nueva',
  nombre          VARCHAR(100)  NOT NULL,
  email           VARCHAR(150)  NOT NULL,
  telefono        VARCHAR(30)   NOT NULL DEFAULT '',
  canal           ENUM('correo','whatsapp','llamada') NOT NULL DEFAULT 'correo',
  destinos        TEXT          NULL,
  meses           TEXT          NULL,
  flexible        TINYINT(1)    NOT NULL DEFAULT 0,
  duracion        VARCHAR(30)   NOT NULL DEFAULT '',
  adultos         TINYINT UNSIGNED NULL,
  menores         TINYINT UNSIGNED NULL,
  edades_menores  VARCHAR(120)  NOT NULL DEFAULT '',
  intereses       TEXT          NULL,
  mensaje         TEXT          NULL,
  consent_texto   VARCHAR(400)  NOT NULL,
  consent_at      DATETIME      NOT NULL,
  ip_hash         CHAR(64)      NOT NULL,
  aviso_enviado   TINYINT(1)    NOT NULL DEFAULT 0,
  acuse_enviado   TINYINT(1)    NOT NULL DEFAULT 0,
  notas           TEXT          NULL,
  created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_req_estado (estado, created_at),
  KEY idx_req_ip (ip_hash, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario del panel de administración
CREATE TABLE IF NOT EXISTS admin_users (
  id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  email            VARCHAR(150)  NOT NULL,
  password_hash    VARCHAR(255)  NOT NULL,
  totp_secret      VARCHAR(255)  NULL,
  totp_enabled     TINYINT(1)    NOT NULL DEFAULT 0,
  totp_last_step   BIGINT        NOT NULL DEFAULT 0,
  recovery_codes   TEXT          NULL,
  session_version  INT UNSIGNED  NOT NULL DEFAULT 1,
  last_login       DATETIME      NULL,
  created_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_admin_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Intentos de acceso (para frenar la fuerza bruta). Solo hashes, nunca contraseñas ni IPs.
CREATE TABLE IF NOT EXISTS auth_attempts (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  scope       ENUM('login','totp') NOT NULL,
  ip_hash     CHAR(64)  NOT NULL,
  subject     CHAR(64)  NOT NULL,
  ok          TINYINT(1) NOT NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_att_ip (scope, ip_hash, created_at),
  KEY idx_att_subject (scope, subject, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
