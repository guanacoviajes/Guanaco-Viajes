# Guanaco Viajes

Web de **Guanaco Viajes** (viajes a medida y en grupo por Sudamérica y Europa): sitio público, formularios de presupuesto y contacto, y panel de administración propio. PHP 8.2+ y MySQL/MariaDB, **sin WordPress** ni frameworks. Pensada para Hostinger Premium.

## Instalar

Lee **`docs/INSTALACION.md`** (paso a paso con SSH, y alternativa solo con gestor de archivos). Resumen:

1. Crea la base de datos en hPanel y sube el ZIP a `~/domains/guanacoviajes.es/`.
2. `unzip -o guanaco-viajes-*.zip`
3. `cp config/config.example.php config/config.php` y rellénalo (URL, claves, base de datos, contraseña del buzón).
4. `mysql -u USUARIO -p BASE < database/instalacion.sql`
5. `php comprobar.php` (debe acabar en «0 fallos»).
6. Crea tu acceso en `https://guanacoviajes.es/admin/instalar` (contraseña + verificación en dos pasos).

## Qué lleva el ZIP

| Carpeta | Contenido |
|---|---|
| `public_html/` | Lo único visible: `index.php`, CSS, JS, fuentes, imágenes, `uploads/` (fotos que subas) |
| `app/` | Código de la aplicación, vistas y librerías incluidas (PHPMailer, QR) |
| `config/` | `config.example.php` (tu `config.php` lo creas tú) |
| `database/` | `instalacion.sql` (tablas + contenido inicial) |
| `storage/` | Registros de errores y correos (`logs/`) |
| `docs/` | Instalación y mantenimiento, SEO y guía del blog, revisión legal pendiente |
| `comprobar.php` | Comprobación del servidor por SSH |

No incluye contraseñas, claves, archivos temporales, pruebas ni dependencias de desarrollo.

## Documentación

- `docs/INSTALACION.md` · instalación, copias de seguridad, actualizaciones, problemas habituales.
- `docs/SEO-Y-BLOG.md` · qué SEO lleva la web y cómo escribir artículos que la apoyen.
- `docs/REVISION-LEGAL-NADIDU.md` · textos legales que debe revisar Nadidú antes de publicar.

## Para quien desarrolle

```bash
composer install            # solo desarrollo (PHPUnit)
vendor/bin/phpunit          # pruebas unitarias
bash tools/build-zip.sh     # genera dist/guanaco-viajes-AAAA-MM-DD.zip
```

Estructura: `public/` (carpeta pública; en Hostinger se llama `public_html`), `app/` (Core, Controllers, Admin, Repositories, Services, Views, Vendor), `config/`, `database/`, `storage/`, `tests/`. Sin framework: un controlador frontal (`public/index.php`), un enrutador propio, consultas siempre preparadas, CSRF en todos los formularios y cabeceras de seguridad (CSP estricta).
