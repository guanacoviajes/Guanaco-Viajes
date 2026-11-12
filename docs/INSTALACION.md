# Guanaco Viajes · Instalación, mantenimiento y actualización (Hostinger)

Guía pensada para tu plan de Hostinger con **acceso SSH**. Si algún día no puedes usar SSH, al final hay una versión solo con el gestor de archivos.

Tiempo estimado: 30–40 minutos la primera vez.

---

## 0. Cómo queda la web en el servidor

El ZIP se descomprime **dentro de la carpeta de tu dominio**, al lado de `public_html`, y no dentro:

```
~/domains/guanacoviajes.es/
├── public_html/      ← lo único visible en internet (index.php, css, js, imágenes, uploads)
├── app/              ← el código (no se ve desde internet)
├── config/           ← tu configuración y contraseñas (no se ve desde internet)
├── storage/          ← registros de errores y correos
├── database/         ← instalacion.sql
├── docs/             ← estas guías
├── comprobar.php     ← comprueba que el servidor está bien
└── README.md
```

Así, aunque algo fallara, nadie puede descargar tu `config.php`. Además, cada carpeta interna lleva su propio bloqueo.

## 1. Antes de empezar (en hPanel)

1. **Dominio y SSL**: `guanacoviajes.es` apuntando a Hostinger y con el certificado SSL activo (candado verde).
2. **Versión de PHP**: *Avanzado → Configuración de PHP* → **8.3** (la de tu captura sirve; mínimo 8.2).
3. **Extensiones** (misma pantalla, pestaña *Extensiones*): comprueba que están activas `pdo_mysql`, `mbstring`, `gd`, `fileinfo`, `sodium`, `openssl`, `iconv`, `exif`. Casi todas lo están por defecto; **`iconv` suele estar desactivada y la necesita el QR del panel**.
4. **Opciones de PHP** (pestaña *Opciones*): sube `upload_max_filesize` a **16M** y `post_max_size` a **24M**. Sin esto, las fotos grandes del panel se rechazan.
5. **Base de datos**: *Bases de datos → Administración de MySQL* → crea una base nueva. Hostinger le pone un prefijo, por ejemplo `u123456789_guanaco`. **Apunta los tres datos**: nombre de la base, usuario y contraseña.
6. **Buzón de correo**: `hola@guanacoviajes.es` ya lo tienes. Necesitas su contraseña (solo la escribirás tú en el paso 4).
7. **Acceso SSH**: *Avanzado → Acceso SSH* → actívalo y apunta IP, puerto (en Hostinger suele ser **65002**) y usuario (`u123456789`).

## 2. Subir el ZIP

Desde tu ordenador, con el ZIP en la carpeta actual (cambia usuario e IP por los tuyos):

```bash
scp -P 65002 guanaco-viajes-AAAA-MM-DD.zip u123456789@IP_DEL_SERVIDOR:domains/guanacoviajes.es/
```

(También vale subirlo con el *Administrador de archivos* de hPanel a la carpeta `domains/guanacoviajes.es/`.)

## 3. Descomprimir

```bash
ssh -p 65002 u123456789@IP_DEL_SERVIDOR
```

```bash
cd ~/domains/guanacoviajes.es
unzip -o guanaco-viajes-*.zip
rm guanaco-viajes-*.zip
rm -f public_html/default.php
ls
```

Debes ver `app  comprobar.php  config  database  docs  public_html  README.md  storage`.

## 4. Configurar

```bash
cp config/config.example.php config/config.php
chmod 600 config/config.php
```

Genera dos cadenas aleatorias (una para `secret`, otra para `install_token`):

```bash
openssl rand -hex 32
openssl rand -hex 16
```

Edita el archivo:

```bash
nano config/config.php
```

Rellena **solo** estos campos (guardar: `Ctrl+O`, `Enter`; salir: `Ctrl+X`):

| Campo | Qué poner |
|---|---|
| `app.url` | `https://guanacoviajes.es` (sin barra final) |
| `app.secret` | La primera cadena de `openssl` (64 caracteres) |
| `admin.install_token` | La segunda cadena (32 caracteres). Solo sirve para crear tu acceso la primera vez |
| `db.name`, `db.user`, `db.pass` | Los tres datos del paso 1.5. `db.host` se queda en `localhost` |
| `mail.pass` | La contraseña del buzón `hola@guanacoviajes.es` |

El resto (`env = production`, `debug = false`, servidor `smtp.hostinger.com`, puerto 465, SSL) ya viene bien. **No pegues estas contraseñas en ningún chat ni correo**, ni siquiera a Claude.

## 5. Crear las tablas y el contenido inicial

```bash
mysql -u u123456789_usuario -p u123456789_guanaco < database/instalacion.sql
```

Te pedirá la contraseña de la base de datos. Si no muestra nada, ha ido bien. (Si lo ejecutas una segunda vez se detiene sola con un error: es a propósito, para no duplicar ni pisar tu contenido.)

## 6. Permisos y comprobación

```bash
chmod -R u+rwX,go+rX storage public_html/uploads
php comprobar.php
```

Debe terminar con **«0 fallos»**. Los avisos de `upload_max_filesize` y `post_max_size` desde SSH pueden salir distintos a los de la web; vale lo que pusiste en hPanel. Si hay algún fallo, el propio mensaje te dice qué hacer.

## 7. Crear tu acceso al panel (con verificación en dos pasos)

1. Abre `https://guanacoviajes.es/admin/instalar`.
2. Escribe el **código de instalación** (`install_token` del paso 4), tu correo y una contraseña de **12 caracteres o más** (usa un gestor de contraseñas).
3. En la pantalla siguiente verás un **código QR**: escanéalo con una app de verificación (Google Authenticator, Microsoft Authenticator, 2FAS, Aegis…) y escribe el código de 6 dígitos.
4. **Guarda los 8 códigos de recuperación** (en tu gestor de contraseñas o impresos). Son tu salvavidas si pierdes el móvil. Solo se muestran una vez.
5. Cuando termines, esa pantalla de instalación **deja de existir**. Puedes borrar `install_token` de `config.php` si quieres.

## 8. Primera revisión (15 minutos)

- [ ] La portada carga con candado verde. `http://guanacoviajes.es` y `https://www.guanacoviajes.es` te llevan a `https://guanacoviajes.es`.
- [ ] **Pide un presupuesto de prueba** desde `/presupuesto` con tu propio correo: debe llegarte el aviso a `hola@guanacoviajes.es` y el acuse a tu correo, y aparecer en *Panel → Solicitudes*. Si no llega el correo, mira el paso «Problemas habituales».
- [ ] Haz lo mismo desde `/contacto`.
- [ ] En el panel, **sube tu foto** en *Quiénes somos* y repasa los textos de *Ajustes*. **Los iconos de Instagram, Facebook y X solo aparecen cuando escribes la dirección completa (con `https://`) de cada perfil en *Ajustes → Contacto y redes*;** si los dejas vacíos no se muestran, a propósito, para no enlazar a perfiles que no existen.
- [ ] Sube una foto de ≥ 5 MB a un destino para comprobar los límites de PHP.
- [ ] Abre `/sitemap.xml` y `/robots.txt`.
- [ ] Textos legales: **no los des por buenos** hasta que Nadidú los revise (ver `docs/REVISION-LEGAL-NADIDU.md`). Cuando lo hagan, marca la casilla «revisado» en *Páginas*.
- [ ] Borra la solicitud de prueba.
- [ ] Sigue `docs/SEO-Y-BLOG.md` (Search Console y sitemap).

---

## Mantenimiento

### Copias de seguridad
- **Base de datos** (la más importante: solicitudes, textos, artículos). Antes de cualquier cambio y una vez al mes:
  ```bash
  cd ~/domains/guanacoviajes.es
  mysqldump -u USUARIO_BD -p NOMBRE_BD > ~/copia-$(date +%F).sql
  ```
- **Fotos subidas**: carpeta `public_html/uploads`. Hostinger también hace copias automáticas (hPanel → *Copias de seguridad*), pero no dependas solo de ellas.
- Descárgalas a tu ordenador con `scp -P 65002 u123456789@IP:copia-*.sql .`

### Actualizar la web
1. Haz la copia de la base de datos.
2. Sube el ZIP nuevo a `~/domains/guanacoviajes.es/` y ejecuta `unzip -o guanaco-viajes-*.zip`. **No pisa** tu `config/config.php` (el ZIP no lo lleva), tus fotos subidas ni tu base de datos.
3. Si la versión nueva trae cambios de base de datos, vendrá un archivo `database/actualizaciones/FECHA.sql` con instrucciones: se importa igual que en el paso 5, **sin tocar `instalacion.sql`**.
4. Ejecuta `php comprobar.php` y revisa la portada y el panel.

### Revisar errores
```bash
tail -n 50 ~/domains/guanacoviajes.es/storage/logs/php-error.log   # errores de la web
tail -n 50 ~/domains/guanacoviajes.es/storage/logs/mail.log        # correos que no se pudieron enviar
```
Nunca llevan datos personales de clientes. Las solicitudes **nunca se pierden** aunque falle el correo: quedan en el panel marcadas para reenviar.

### Problemas habituales

| Síntoma | Causa probable | Qué hacer |
|---|---|---|
| Página en blanco o error 500 | `config.php` mal escrito, versión de PHP antigua o permisos | `php comprobar.php` y `tail storage/logs/php-error.log` |
| «Falta config/config.php» | Paso 4 sin hacer | Repite el paso 4 |
| No llegan los correos | Contraseña del buzón mal, o SMTP | Revisa `mail.pass`; mira `mail.log`; en el panel, *Solicitudes → Reenviar* |
| Una foto grande se rechaza con mensaje de límite | `upload_max_filesize` / `post_max_size` | Sube a 16M / 24M en hPanel y espera 1–2 minutos |
| El QR no se dibuja en el panel | Falta la extensión `iconv` | Actívala en hPanel; mientras tanto el panel muestra la clave para escribirla a mano |
| Se me ha perdido el móvil | — | Usa un código de recuperación (*Seguridad* te deja generar nuevos) |
| Perdí móvil **y** códigos | — | Por SSH: `mysql -u USUARIO -p BASE -e "DELETE FROM admin_users;"`, comprueba que `admin.install_token` está en `config.php` y repite el paso 7 |
| Muchos fallos de acceso seguidos | Protección anti-fuerza-bruta (8 fallos/15 min) | Espera 15 minutos |

### Seguridad: costumbres que importan
- Contraseña del panel única y larga; no la compartas.
- No dejes `app.debug` en `true` en la web real.
- Mantén PHP en una versión con soporte (hPanel avisa).
- No subas nunca `config.php` a ningún repositorio ni lo envíes por correo.

---

## Sin SSH (solo gestor de archivos)

1. Sube el ZIP con el *Administrador de archivos* a `domains/guanacoviajes.es/` y usa «Extraer» ahí mismo. Borra el ZIP y `public_html/default.php`.
2. Copia `config/config.example.php` como `config/config.php`, edítalo con el editor del gestor y rellena los campos de la tabla del paso 4 (las cadenas aleatorias puedes generarlas con un gestor de contraseñas: 32 y 64 caracteres letras y números).
3. En hPanel → *phpMyAdmin* → selecciona tu base → *Importar* → elige `database/instalacion.sql`.
4. Comprueba que `storage/logs`, `storage/cache` y `public_html/uploads` tienen permisos 755 (clic derecho → Permisos).
5. El paso 6 (`comprobar.php`) solo se puede hacer por SSH; sustitúyelo por la lista del paso 8.
6. Sigue en el paso 7.

## Si no puedes poner la aplicación fuera de `public_html`

No hace falta, pero si en algún momento tuvieras que dejarlo todo dentro de `public_html` (por ejemplo, otro hosting), cada carpeta interna (`app`, `config`, `storage`, `database`, `docs`) ya incluye un `.htaccess` que impide verla desde internet. Tendrías que mover `index.php`, `.htaccess`, `assets`, `uploads` y `favicon.svg` a la raíz web y ajustar en `index.php` la ruta de `bootstrap.php`. Mejor evitarlo.
