# Revista EmprendeUNAM

Sistema de gestión de la Revista EmprendeUNAM (UNAM · Facultad de Contaduría y
Administración): envío y dictamen de artículos, constancias con QR, consultorio,
cursos y newsletter, integrado con el sitio público de la revista.

Stack: Laravel 13 · PHP 8.3+ · PostgreSQL 18 · Vue 3 + Vite · Bootstrap 5 + Sass.

La especificación está en `docs/especificacion.md` y los puntos abiertos y las
decisiones tomadas, en `PENDIENTES.md`.

- [Puesta en marcha (desarrollo)](#puesta-en-marcha-desarrollo)
- [Instalación en AlmaLinux (producción)](#instalación-en-almalinux-producción)
- [Actualizar una instalación](#actualizar-una-instalación)
- [Sitio público](#sitio-público)
- [Pruebas](#pruebas)
- [Errores y registros](#errores-y-registros)

## Puesta en marcha (desarrollo)

Requisitos: PHP 8.3+ (con `pdo_pgsql`, `mbstring`, `xml`, `gd`), Composer 2,
Node.js 20.19+ o 22.12+ y PostgreSQL 18.

```bash
composer install
npm install
cp .env.example .env          # captura DB_PASSWORD
php artisan key:generate

scripts/crear_bd.sh           # crea el usuario 'emprende' y la base 'emprendeunam' y carga los SQL
php artisan migrate           # solo tablas técnicas de Laravel (sesiones, caché, colas)
php artisan emprende:clave-admin   # contraseña del administrador (se pide oculta)

npm run build                 # o: npm run dev
php artisan serve
php artisan queue:work        # en otra terminal: envía los correos en cola
```

Con `MAIL_MAILER=log` los correos se escriben en `storage/logs/laravel.log`.

## Instalación en AlmaLinux (producción)

Guía para **AlmaLinux 9.6** y **AlmaLinux 10**. Se asume la ruta
`/var/www/emprendeunam`, Nginx con PHP-FPM y el dominio
`revista.ejemplo.unam.mx` (cámbialo por el real).

El código, PHP-FPM, la cola y las tareas programadas corren con un usuario de
sistema propio, **`emprende`**; Nginx solo lee `public/`. Los comandos con
`sudo` se ejecutan desde tu cuenta de administración; los que van después de
`sudo -iu emprende`, como el usuario `emprende`.

### 1. Paquetes del sistema

```bash
sudo dnf -y update
sudo dnf -y install epel-release
sudo dnf -y install git unzip nginx policycoreutils-python-utils
```

**PHP 8.3 y Node.js 22**

```bash
# AlmaLinux 9: activar los flujos de módulo (AlmaLinux 10 ya trae PHP 8.3 y Node 22)
sudo dnf -y module reset php nodejs nginx
sudo dnf -y module enable php:8.3 nodejs:22 nginx:1.24

# AlmaLinux 9 y 10
sudo dnf -y install php-cli php-fpm php-common php-pgsql php-mbstring php-xml \
    php-gd php-intl php-bcmath php-opcache php-process php-zip nodejs

php -v      # 8.3.x
node -v     # v22.x
```

**Composer 2**

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
composer --version
```

### 2. PostgreSQL 18 (repositorio oficial PGDG)

```bash
# AlmaLinux 9 (en AlmaLinux 10 cambia EL-9 por EL-10)
sudo dnf -y install https://download.postgresql.org/pub/repos/yum/reporpms/EL-9-x86_64/pgdg-redhat-repo-latest.noarch.rpm
sudo dnf -qy module disable postgresql      # solo AlmaLinux 9
sudo dnf -y install postgresql18-server postgresql18

sudo /usr/pgsql-18/bin/postgresql-18-setup initdb
sudo systemctl enable --now postgresql-18
```

Revisa que `/var/lib/pgsql/18/data/pg_hba.conf` pida contraseña para las
conexiones locales por TCP:

```
host    all    all    127.0.0.1/32    scram-sha-256
host    all    all    ::1/128         scram-sha-256
```

Si lo cambias: `sudo systemctl reload postgresql-18`. PostgreSQL solo debe
escuchar en `localhost` (valor por defecto de `listen_addresses`).

### 3. Usuario `emprende` y código de la aplicación

```bash
sudo useradd --system --create-home --shell /bin/bash emprende
sudo mkdir -p /var/www/emprendeunam
sudo chown emprende:emprende /var/www/emprendeunam

sudo -iu emprende
git clone <URL-del-repositorio> /var/www/emprendeunam
cd /var/www/emprendeunam
composer install --no-dev --optimize-autoloader
npm ci
npm run build
cp .env.example .env
chmod 600 .env
php artisan key:generate
exit
```

### 4. Archivo `.env`

Edita `/var/www/emprendeunam/.env` (como `emprende`) y ajusta al menos estos
valores. La contraseña de la base es nueva: elige una larga y aleatoria, por
ejemplo con `openssl rand -base64 24`.

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://revista.ejemplo.unam.mx

DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=emprendeunam
DB_USERNAME=emprende
DB_PASSWORD=<contraseña-de-la-base>

SESSION_SECURE_COOKIE=true
LOG_LEVEL=warning

MAIL_MAILER=smtp
MAIL_HOST=<servidor-smtp-institucional>
MAIL_PORT=587
MAIL_SCHEME=smtp
MAIL_USERNAME=<usuario-smtp>
MAIL_PASSWORD=<contraseña-smtp>
MAIL_FROM_ADDRESS=<correo-remitente>
MAIL_FROM_NAME="Revista EmprendeUNAM"
```

Ver «Correo (SMTP)» para Gmail, Microsoft 365 y la prueba de envío.

`APP_DEBUG` **siempre** en `false` en producción: con `true` las páginas de
error muestran la configuración y el código.

### 5. Base de datos

```bash
cd /var/www/emprendeunam
sudo bash scripts/crear_bd.sh                       # usuario 'emprende' en PostgreSQL y base 'emprendeunam'
sudo -iu emprende php /var/www/emprendeunam/artisan migrate --force   # solo tablas técnicas de Laravel
sudo -iu emprende php /var/www/emprendeunam/artisan emprende:clave-admin   # contraseña del administrador
```

`crear_bd.sh` lee `DB_PASSWORD` de `.env`, carga
`database/EMP_FINAL_corregido.sql` y `database/datos_iniciales.sql` y verifica
los conteos (52 tablas, 13 temas, 4 roles, 12 privilegios y el usuario
`EmprendeUnam`). Si la base ya existe, se niega a continuar; `--forzar` la
borra y la recrea (**se pierden los datos**).

El administrador es el usuario **`EmprendeUnam`**, cargado por
`datos_iniciales.sql`. El hash que trae ese archivo está en el repositorio, así
que **al instalar hay que cambiar la contraseña** con
`php artisan emprende:clave-admin`: la pide dos veces sin mostrarla (12 a 72
caracteres), guarda solo el hash, cierra las sesiones abiertas del
administrador y lo anota en `storage/logs/admin-*.log`. Sirve también para
cambiarla después. Su correo provisional es `admin@emprendeunam.invalid`
(ver `PENDIENTES.md`).

### 6. SELinux y permisos

```bash
# Nginx necesita atravesar el directorio personal: el código está fuera de él.
sudo chmod 755 /var/www/emprendeunam

# SELinux: Nginx/PHP-FPM leen el código y escriben solo en storage y bootstrap/cache
sudo semanage fcontext -a -t httpd_sys_content_t "/var/www/emprendeunam(/.*)?"
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/emprendeunam/storage(/.*)?"
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/emprendeunam/bootstrap/cache(/.*)?"
sudo restorecon -Rv /var/www/emprendeunam

# PHP-FPM se conecta a PostgreSQL por 127.0.0.1
sudo setsebool -P httpd_can_network_connect_db on

# PHP-FPM puede conectarse a los puertos de correo (ver «Correo (SMTP)»)
sudo setsebool -P httpd_can_sendmail on
```

Los PDF de los artículos y las constancias se guardan en
`storage/app/private` y solo se descargan a través de la aplicación (tras
revisar permisos). Nginx sirve únicamente `public/`.

### 7. PHP-FPM

Crea un grupo de procesos propio en `/etc/php-fpm.d/emprendeunam.conf`
(el PDF admite 7 MB):

```ini
[emprendeunam]
user = emprende
group = emprende
listen = /run/php-fpm/emprendeunam.sock
listen.owner = nginx
listen.group = nginx
listen.mode = 0660

pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6

php_admin_value[upload_max_filesize] = 8M
php_admin_value[post_max_size] = 10M
php_admin_value[memory_limit] = 256M
php_admin_value[date.timezone] = America/Mexico_City
php_admin_flag[expose_php] = off
```

Y en `/etc/php.d/99-emprendeunam.ini`:

```ini
expose_php = Off
opcache.enable = 1
opcache.validate_timestamps = 0
```

Con `opcache.validate_timestamps = 0` hay que recargar PHP-FPM en cada
actualización del código (ver [Actualizar una instalación](#actualizar-una-instalación)).

```bash
sudo systemctl enable --now php-fpm
```

### 8. Nginx

`/etc/nginx/conf.d/emprendeunam.conf`:

```nginx
server {
    listen 80;
    server_name revista.ejemplo.unam.mx;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name revista.ejemplo.unam.mx;

    ssl_certificate     /etc/pki/tls/certs/emprendeunam.crt;
    ssl_certificate_key /etc/pki/tls/private/emprendeunam.key;

    root /var/www/emprendeunam/public;
    index index.php;
    charset utf-8;
    client_max_body_size 10m;
    server_tokens off;

    add_header Strict-Transport-Security "max-age=31536000" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location /build/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/run/php-fpm/emprendeunam.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Ningún otro .php se ejecuta (las direcciones .php del sitio anterior las
    # redirige Laravel), ni se sirven archivos ocultos.
    location ~ \.php$ { try_files /index.php?$query_string =404; }
    location ~ /\.(?!well-known).* { deny all; }
}
```

El certificado lo emite la institución (o `certbot` con el paquete
`certbot python3-certbot-nginx` de EPEL). Después:

```bash
sudo nginx -t
sudo systemctl enable --now nginx
sudo firewall-cmd --permanent --add-service=http --add-service=https
sudo firewall-cmd --reload
```

Si el servidor queda detrás de un proxy o balanceador de la UNAM, configura
sus direcciones como proxies de confianza en `bootstrap/app.php`
(`$middleware->trustProxies(at: [...])`) para que la aplicación detecte HTTPS
y la IP real (los límites de intentos van por IP).

### 9. Caché de configuración

```bash
sudo -iu emprende
cd /var/www/emprendeunam
php artisan optimize        # config, rutas, vistas y eventos en caché
exit
```

Tras cualquier cambio en `.env`, vuelve a ejecutar `php artisan optimize`
(como `emprende`) y `sudo systemctl reload php-fpm`.

### 10. Cola de correos (systemd)

Todos los correos (cuentas, dictámenes, consultorio, newsletter) se envían en
cola. `/etc/systemd/system/emprendeunam-cola.service`:

```ini
[Unit]
Description=Revista EmprendeUNAM - cola de correos
After=network.target postgresql-18.service

[Service]
User=emprende
Group=emprende
WorkingDirectory=/var/www/emprendeunam
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --backoff=60 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now emprendeunam-cola
```

Los correos que fallan tres veces quedan en la tabla `failed_jobs`:
`php artisan queue:failed` los lista y `php artisan queue:retry all` los
reintenta.

### 11. Tareas programadas (systemd timer)

`/etc/systemd/system/emprendeunam-tareas.service`:

```ini
[Unit]
Description=Revista EmprendeUNAM - tareas programadas

[Service]
Type=oneshot
User=emprende
Group=emprende
WorkingDirectory=/var/www/emprendeunam
ExecStart=/usr/bin/php artisan schedule:run
```

`/etc/systemd/system/emprendeunam-tareas.timer`:

```ini
[Unit]
Description=Revista EmprendeUNAM - tareas programadas cada minuto

[Timer]
OnCalendar=*-*-* *:*:00
Persistent=true

[Install]
WantedBy=timers.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now emprendeunam-tareas.timer
```

Por ahora la única tarea borra los enlaces de recuperación de contraseña
vencidos (`routes/console.php`).

### 12. Respaldo (sugerido)

El respaldo diario está pendiente de definir con infraestructura
(`PENDIENTES.md`). Como mínimo hay que respaldar la base y los PDF:

```bash
sudo -u postgres pg_dump -Fc emprendeunam | sudo tee /respaldos/emprendeunam-$(date +%F).dump > /dev/null
sudo tar -czf /respaldos/pdf-$(date +%F).tar.gz -C /var/www/emprendeunam/storage/app private
```

### 13. Comprobación

1. `https://revista.ejemplo.unam.mx/` muestra el sitio público.
2. `/iniciar-sesion` con el usuario `EmprendeUnam` entra al panel `/admin`.
3. Un registro de autor llega al correo (`systemctl status emprendeunam-cola`).
4. `/up` responde 200 (comprobación de salud).

## Actualizar una instalación

```bash
sudo -iu emprende
cd /var/www/emprendeunam
php artisan down
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force           # solo tablas técnicas
php artisan optimize
php artisan queue:restart             # la cola toma el código nuevo
exit

sudo restorecon -R /var/www/emprendeunam
sudo systemctl reload php-fpm
sudo -iu emprende php /var/www/emprendeunam/artisan up
```

Los cambios al esquema del modelo **no** se hacen con migraciones: se
publican como scripts SQL en `database/cambios/` y se aplican a mano, en orden
de fecha, con el usuario de la aplicación (pide la contraseña de `DB_PASSWORD`):

```bash
psql -h 127.0.0.1 -U emprende -d emprendeunam -f database/cambios/2026-10-15_arti_resumen.sql
```

| Script | Cambio |
|---|---|
| `2026-10-15_arti_resumen.sql` | Columna `arti_resumen varchar(500)` en `articulo` (resumen de hasta 50 palabras). |

Una base creada con `scripts/crear_bd.sh` después de la fecha de un script ya
incluye ese cambio.

## Correo (SMTP)

Todos los correos salen por SMTP cifrado (Symfony Mailer, incluido en
Laravel) y en cola. Si «no llegan», casi siempre es una de estas tres cosas:

1. **`MAIL_MAILER=log`** en `.env`: los correos solo se escriben en
   `storage/logs/laravel.log`. Debe decir `smtp`.
2. **El trabajador de la cola no está corriendo** (o no se reinició después de
   cambiar `.env`: guarda la configuración en memoria).
3. **Credenciales o puerto**: Gmail y Microsoft 365 exigen una *contraseña de
   aplicación*; 587 va con STARTTLS y 465 con TLS directo.

### Datos en `.env`

```ini
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com          # o smtp.office365.com, o el servidor institucional
MAIL_PORT=587
MAIL_SCHEME=smtp                  # 587: STARTTLS automático · 465: smtps (TLS directo)
MAIL_USERNAME=cuenta@dominio.mx
MAIL_PASSWORD="contraseña de aplicación"
MAIL_FROM_ADDRESS=cuenta@dominio.mx   # la misma cuenta o un alias autorizado
MAIL_FROM_NAME="Revista EmprendeUNAM"
```

Las credenciales viven solo en `.env` (que no está en git y debe tener
permisos `600` o `640` del usuario de la aplicación).

### Después de cambiar `.env`

```bash
php artisan optimize                       # o php artisan config:clear
sudo systemctl restart emprendeunam-cola   # el trabajador vuelve a leer .env
php artisan emprende:probar-correo tu@correo.com
php artisan emprende:probar-correo tu@correo.com --cola   # prueba también el trabajador
```

`emprende:probar-correo` muestra la configuración que está usando la
aplicación (sin la contraseña), avisa si hay correos atorados en la cola o
fallidos, envía la prueba en el momento y, si falla, dice la causa probable.

### SELinux y firewall

El trabajador de la cola corre como servicio de systemd y SELinux no lo
limita. Si algún correo se envía directamente desde PHP-FPM (por ejemplo con
`QUEUE_CONNECTION=sync`), hay que permitir a Apache/Nginx conectarse a los
puertos de correo (25, 465 y 587 ya están etiquetados como `smtp_port_t`):

```bash
getsebool httpd_can_sendmail               # ¿está en on?
sudo setsebool -P httpd_can_sendmail on
```

firewalld solo filtra lo que entra; la salida a 587/465 está permitida. Para
comprobar que la red llega al servidor de correo:

```bash
curl -v telnet://smtp.gmail.com:587        # «Connected to …» = hay red (Ctrl+C para salir)
openssl s_client -starttls smtp -connect smtp.gmail.com:587 -quiet   # prueba TLS (si hay openssl)
sudo ausearch -m avc -ts recent | grep -i smtp                       # bloqueos de SELinux recientes
```

### Revisar la cola

```bash
sudo systemctl status emprendeunam-cola
journalctl -u emprendeunam-cola -n 50
php artisan queue:failed                   # correos que fallaron 3 veces
php artisan queue:retry all                # reintentarlos ya corregido el problema
```

## Sitio público

El sitio público conserva el diseño del sitio original de la revista:

- Plantilla: `resources/views/layouts/sitio.blade.php`; páginas en `resources/views/sitio/`.
- Contenido editable (edición actual, histórico, temáticas, podcasts, 10 de 10…): `config/sitio/*.php`.
- Estilos y scripts propios: `resources/css/sitio/` y `resources/js/sitio/` (compilados con Vite).
- Imágenes y PDF: copiar las carpetas `img/` y `pdf/` del sitio original a `public/img/` y `public/pdf/`.

Si se edita algún archivo de `config/sitio/` en producción, hay que ejecutar
`php artisan optimize` para que se vea el cambio.

## Pruebas

```bash
scripts/preparar_bd_pruebas.sh   # recrea emprendeunam_test
php artisan test
```

Las pruebas corren solo contra una base cuyo nombre termine en `_test`, dentro
de transacciones que se revierten (`DatabaseTransactions`); nunca tocan la base
de producción.

## Errores y registros

- Las reglas de la base (correo repetido, máximo de palabras clave, registros
  en uso…) se traducen a mensajes claros en `App\Support\ErrorBaseDatos`
  (SQLSTATE 23505, 23514, 23001 y 23503) y se registran como aviso.
- Las páginas de error (403, 404, 419, 429, 500…) están en
  `resources/views/errors/` y no dependen de la base ni de Vite.
- `storage/logs/laravel.log`: errores de la aplicación.
- `storage/logs/admin-AAAA-MM-DD.log`: acciones del administrador (activar
  autores, aprobar y rechazar artículos, responder el consultorio, cursos y
  avisos del newsletter). Se conservan 365 días (`LOG_ADMIN_DAYS`).
