#!/usr/bin/env bash
# Genera el ZIP de instalación para Hostinger:  bash tools/build-zip.sh
# Resultado: dist/guanaco-viajes-AAAA-MM-DD.zip  (sin contraseñas, sin pruebas, sin dependencias de desarrollo)
set -euo pipefail
cd "$(dirname "$0")/.."

OUT=dist
STAGE=$(mktemp -d)
NAME="guanaco-viajes-$(date +%F)"
trap 'rm -rf "$STAGE"' EXIT

mkdir -p "$STAGE/app" "$STAGE/config" "$STAGE/database" "$STAGE/docs" "$STAGE/storage/logs" "$STAGE/storage/cache" "$STAGE/public_html"

# Código de la aplicación (incluye las librerías de correo y QR, ya integradas)
cp -r app/. "$STAGE/app/"
# Configuración: solo el ejemplo
cp config/config.example.php config/.htaccess "$STAGE/config/"
# Base de datos: un único archivo, importable en phpMyAdmin o por SSH
{
  cat <<'SQL'
-- Guanaco Viajes · instalación de la base de datos (esquema + contenido inicial)
-- Importar UNA sola vez en una base de datos vacía (phpMyAdmin → Importar, o por SSH).
-- Si lo importas dos veces, la segunda se detiene sola para no duplicar contenido ni borrar tus cambios.
SQL
  cat database/schema.sql
  cat <<'SQL'

-- Marca de instalación: si esta tabla ya existe, el script se detiene aquí (protege tu contenido).
CREATE TABLE instalacion_inicial (id TINYINT UNSIGNED NOT NULL PRIMARY KEY, creada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
INSERT INTO instalacion_inicial (id) VALUES (1);

SQL
  cat database/seed.sql
} > "$STAGE/database/instalacion.sql"
cp database/.htaccess "$STAGE/database/"
[ -d database/actualizaciones ] && cp -r database/actualizaciones "$STAGE/database/"
# Documentación
cp docs/INSTALACION.md docs/SEO-Y-BLOG.md docs/REVISION-LEGAL-NADIDU.md docs/.htaccess "$STAGE/docs/"
cp README.md comprobar.php "$STAGE/"
# Carpetas de trabajo vacías pero protegidas
cp storage/.htaccess "$STAGE/storage/"; touch "$STAGE/storage/logs/.gitkeep" "$STAGE/storage/cache/.gitkeep"
# Carpeta pública (en Hostinger es public_html). Sin las fotos subidas durante el desarrollo.
rsync -a --exclude='uploads/*' public/ "$STAGE/public_html/"
mkdir -p "$STAGE/public_html/uploads"
cp public/uploads/.htaccess "$STAGE/public_html/uploads/"; touch "$STAGE/public_html/uploads/.gitkeep"

# ---- Comprobaciones de seguridad del paquete ----
fail() { echo "ERROR: $1" >&2; exit 1; }
[ ! -e "$STAGE/config/config.php" ] || fail "config.php no debe ir en el ZIP"
find "$STAGE" \( -name '*.log' -o -name '.env*' -o -name '*.pem' -o -name '*.key' -o -name 'phpunit*' -o -name 'composer.*' -o -name '.git*' -a ! -name '.gitkeep' \) | grep -q . && fail "hay archivos que no deben ir en el ZIP"
[ -z "$(find "$STAGE" -type d \( -name tests -o -name vendor -o -name node_modules -o -name qa \) -not -path '*/app/Vendor*' -print -quit)" ] || fail "hay carpetas de desarrollo en el ZIP"
grep -rIl --exclude-dir=Vendor -E "dev-only-local|dev-secret-solo|guanaco_dev" "$STAGE" | grep -q . && fail "quedan datos de desarrollo"
find "$STAGE/public_html/uploads" -type f ! -name '.htaccess' ! -name '.gitkeep' | grep -q . && fail "uploads no está vacío"

mkdir -p "$OUT"
rm -f "$OUT/$NAME.zip"
(cd "$STAGE" && zip -qr -X "$OLDPWD/$OUT/$NAME.zip" . -x '*.DS_Store')
echo "OK  $OUT/$NAME.zip  ($(du -h "$OUT/$NAME.zip" | cut -f1), $(unzip -Z1 "$OUT/$NAME.zip" | wc -l) archivos)"
