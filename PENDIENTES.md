# Pendientes y decisiones

## Por aprobar: cambios a la base (propuesta del 2026-10-22, versión 3)

SQL exacto en `database/propuestas/2026-10-22_perfil_cesion_edicion.sql` (no
aplicado; probado en una base nueva creada desde cero). No toca `autor` ni
`articulo`.

- [ ] `constancia.cons_tipo` (`CONSTANCIA`, `AGRADECIMIENTO`, `CESION`) y `cons_folio` nulo: la carta de cesión no lleva folio; la constancia y la carta de agradecimiento sí. Un documento de cada tipo por artículo.
- [ ] `revista.revi_url_flipbook` y `revista.revi_en_portada` (un solo número en portada, visible). `revi_mes` guarda el periodo («julio-agosto», se amplía a 20); la portada es `revi_url_portada`.
- [ ] Tabla nueva `articulo_revista`: todos los artículos del número; `arre_posicion_index` (1 a 5) marca los 5 que se ven en el índice y `arre_fecha_index` cuándo se mostraron, para rotarlos hasta mostrar todos.
- [ ] Tabla nueva `articulo_curso`: cursos que el administrador elige (o descarta) para cada artículo del índice.

### Preguntas abiertas de esa propuesta

- [ ] **Carta de agradecimiento**: ¿la descarga el autor desde su panel cuando su artículo sale en el número? ¿Firma fija (Dra. Clotilde Hernández Garnica) y fecha de emisión?
- [ ] **Carta de cesión**: ¿se descarga en blanco o prellenada con el nombre y el título? El número y el bimestre aún no se conocen al enviar.

## Pendientes de definir

- [ ] Formato y firma de la constancia (por ahora, ejemplo provisional con la tipografía DejaVu de dompdf).
- [ ] Formato definitivo del folio (por ahora `EMP-AAAA-NNNN`).
- [ ] Captcha del consultorio y del newsletter (por ahora honeypot + límite por IP).
- [ ] Correo real del administrador (por ahora `admin@emprendeunam.invalid` en `comunicacion`).
- [ ] Uso de la extensión `unaccent` en PostgreSQL (por ahora el buscador quita acentos con `translate()` en ambos lados).
- [ ] Servidor SMTP institucional (por ahora `MAIL_MAILER=log`). Al tenerlo: datos en `.env` y `php artisan emprende:probar-correo` (ver README, «Correo (SMTP)»).
- [ ] Exportación de metadatos para Latindex (RNF-05 del anexo; no está en las secciones 1–11).
- [ ] Fecha de fin de los cursos (la menciona el anexo, pero `curso` no tiene columna para ella).
- [ ] Respaldo diario de la base y de los PDF (RNF-06; tarea de infraestructura).
- [ ] "Ver detalle" del artículo para el autor (RF-AUT-04): por ahora lo cubre el historial, que muestra el motivo de rechazo.
- [ ] Meta de rendimiento del buscador (RNF-07, por confirmar).
- [ ] Texto oficial y enlace del aviso de privacidad (RNF-04). Por ahora el registro muestra un texto breve provisional (el sitio público enlaza el PDF de la FCA).
- [ ] Revisión por pares: los lineamientos y la página de envío conservan el texto original («Revisión por pares», notificación en 15 días hábiles), aunque el sistema no tiene revisores externos (RN-07). Se revisará en el futuro.
- [ ] El formulario del consultorio del sitio tenía un campo «Tema»; se retiró porque `mensaje` no tiene columna para guardarlo.
- [ ] Imágenes y PDF del sitio público: copiar las carpetas `img/` y `pdf/` del sitio original a `public/img/` y `public/pdf/` (mismas rutas). Las imágenes del newsletter ya están en `public/img/newsletter/`.
- [ ] Índice GIN para el buscador (requiere cambio en la BD; por ahora el texto completo se calcula en cada consulta, suficiente para el volumen esperado).
- [ ] Contador de visitas del sitio original (`includes/contador.php`): no se usaba en ninguna página y no se migró.

### De la revisión de seguridad (fase F)

- [ ] **El formulario del newsletter reactiva correos inactivos.** Si un correo está marcado como inactivo en `comunicacion`, volver a suscribirlo lo reactiva. Se dejó como está; por decidir si el formulario público debe respetar el estado inactivo.
- [ ] **Política de contenido (CSP) completa.** Por ahora solo se envía `frame-ancestors 'self'`; una CSP estricta requiere quitar los scripts en línea del sitio público.
- [ ] Enumeración de cuentas: el registro dice «Este correo ya está registrado» (lo pide la especificación, 4.5) y el newsletter distingue «ya suscrito». El inicio de sesión y la recuperación de contraseña no revelan si la cuenta existe.

## Decisiones tomadas

| Fecha | Decisión |
|---|---|
| 2026-10-15 | Tamaño máximo del PDF de artículo: **7 MB** (`config('emprende.pdf_max_kb') = 7168`). |
| 2026-10-15 | PostgreSQL **18.6** instalado desde `apt.postgresql.org` en el entorno de desarrollo. |
| 2026-10-15 | `composer.json` fija `config.platform.php = 8.3.6` para que el lock funcione con PHP 8.3 (Symfony 7.4 en lugar de 8.1, que exige PHP 8.4). |
| 2026-10-15 | No se instala Laravel Boost; `CLAUDE.md` y `AGENTS.md` siguen la sección 10 de la especificación. |
| 2026-10-15 | PDF: `barryvdh/laravel-dompdf` ^3.1.2. QR: `chillerlan/php-qrcode` ^6.0.1 (`endroid/qr-code` 6 exige PHP 8.4). |
| 2026-10-15 | Pruebas del login del administrador: cambian temporalmente su hash dentro de la transacción de la prueba, que se revierte; la contraseña real nunca se escribe. |
| 2026-10-15 | Resuscripción al newsletter: se agrega una fila en `tema_news` solo si ese tema no está ya registrado para la persona. |
| 2026-10-15 | Tipografía del PDF de la constancia: DejaVu Serif/Sans (incluidas en dompdf). |
| 2026-10-15 | El panel `/admin` exige cualquiera de los 6 privilegios exclusivos del administrador (`privilegio:A,B,…` = basta uno). |
| 2026-10-15 | La recuperación de contraseña responde lo mismo exista o no el correo, para no revelar cuentas. |
| 2026-10-15 | Si quien envía un artículo no tiene registro en `autor` (p. ej. el administrador, que tiene el privilegio `ENVIAR_ARTICULO`), se le crea uno ligado a su persona. |
| 2026-10-15 | Folio de constancia: consecutivo anual calculado dentro de la transacción con `pg_advisory_xact_lock` (no modifica el esquema). |
| 2026-10-15 | El correo de aprobación enlaza a la pantalla de agradecimiento del autor (requiere iniciar sesión), desde donde descarga la constancia. |
| 2026-10-15 | Un autor registrado que aparece como **coautor** ve el artículo en su historial (columna «Tu papel») y descarga su PDF y su constancia. Los correos de dictamen siguen yendo solo al autor principal. |
| 2026-10-15 | Confirmado: si el administrador envía un artículo, se le crea un registro de autor ligado a su persona. |
| 2026-10-15 | Tablas Vue con filtros y JSON para artículos, autores, consultorio y suscriptores; la lista de cursos queda en Blade (CRUD con formularios). |
| 2026-10-15 | La lista de suscriptores distingue un tercer estado, «Sin confirmar» (se suscribió pero no confirmó), además de activo y baja. El CSV neutraliza celdas que empiezan con = + - @. |
| 2026-10-15 | Cursos: de 1 a 5 palabras clave (la sección 6.7 dice «hasta 5» y el anexo las marca como obligatorias). |
| 2026-10-15 | Los filtros del administrador ignoran mayúsculas y acentos con `translate()` (ver `BuscadorService::sinAcentos`). |
| 2026-10-15 | El sitio público original se integró en Laravel conservando su diseño (CSS/JS propios vía Vite; Bootstrap, iconos y tipografías Barlow desde npm, sin CDN). Su contenido pasó a `config/sitio/*.php` sin cambios de texto. |
| 2026-10-15 | Las direcciones `.php` del sitio original redirigen con 301 a las nuevas (`/consultorio.php` → `/consultorio`, `/diezdediez-detalle.php?tema=x` → `/10-de-10/x`, etc.). |
| 2026-10-15 | Consultorio público: muestra nombre y lugar del lector (como el sitio original), nunca su correo. Primero las consultas contestadas en el sistema y después el archivo del sitio anterior. |
| 2026-10-15 | Newsletter: el **tema de interés es texto libre** (hasta 120 caracteres, sin etiquetas ni caracteres de control), en la ventana del newsletter y en la página de suscripción. Toda (re)suscripción se confirma de nuevo por correo. La baja pide confirmación con un botón (los filtros antispam visitan los enlaces). |
| 2026-10-15 | Buscador: año = año de aprobación del artículo o de inicio del curso; con un tema elegido solo se muestran artículos; cada término busca también por prefijo. |
| 2026-10-15 | **Resumen del artículo** (lineamientos): nueva columna `arti_resumen varchar(500) not null` en `articulo` (en `EMP_FINAL_corregido.sql`; para bases existentes, `database/cambios/2026-10-15_arti_resumen.sql`). El formulario lo exige con máximo 50 palabras. Sustituye a RN-06 («los artículos no llevan abstract»). |
| 2026-10-15 | **Máximo 1 coautor** por artículo (lineamientos: hasta dos personas autoras). |
| 2026-10-15 | El sistema sigue aceptando solo PDF aunque los lineamientos mencionen Word (indicación: ignorar Word). |
| 2026-10-16 | **Errores de la base** centralizados (`App\Support\ErrorBaseDatos`, registrado en `bootstrap/app.php`): 23505, 23514, 23001 y 23503 se traducen a mensajes claros según la restricción o el trigger; en formularios regresan con el mensaje, en JSON responden 422 y en páginas GET muestran la página 409. Se registran como aviso, sin traza. |
| 2026-10-16 | Páginas de error propias en español (403, 404, 405, 409, 413, 419, 429, 500, 503), autónomas: no dependen de la base, la sesión ni Vite. |
| 2026-10-16 | Las contraseñas (`clave`, `clave_confirmation`) nunca regresan a la sesión como datos anteriores del formulario. |
| 2026-10-16 | Seguridad: corregido un XSS en la lista de autores del administrador (`@json` con dos argumentos pierde el escape). Encabezados `X-Frame-Options`, `frame-ancestors`, `nosniff`, `Referrer-Policy` y `Permissions-Policy` en todas las páginas. Solo se aceptan peticiones al dominio de `APP_URL` (`trustHosts`) y en producción las URL de los correos se arman con `APP_URL`. |
| 2026-10-16 | Inicio de sesión: además de 5 intentos por minuto por usuario + IP, máximo 20 intentos fallidos cada 15 minutos por cuenta desde cualquier IP (un atacante podría bloquear una cuenta hasta 15 minutos). Un usuario inexistente tarda lo mismo que una contraseña equivocada. Restablecer la contraseña cierra las demás sesiones de la cuenta. |
| 2026-10-16 | Registro de acciones del administrador (`storage/logs/admin-*.log`): también altas, cambios y bajas de cursos. |
| 2026-10-16 | Cola de correos: `queue:work --tries=3 --backoff=60` (servicio systemd); tarea programada diaria `auth:clear-resets`. Instalación en AlmaLinux 9.6/10 documentada en `README.md` (usuario de sistema `emprende`, Nginx + PHP-FPM, SELinux, PostgreSQL 18 de PGDG). |
| 2026-10-16 | **Rediseño del sistema** (acceso, administrador y autor) con las skills `impeccable` y de Emil Kowalski: estándar de la categoría al nivel de Linear, Stripe y Vercel, dentro de la paleta cerrada (neutros mezclados del azul; café solo en acción principal, foco y elemento activo). Documentado en `DESIGN.md` y `.impeccable/design.json`; contexto del producto en `PRODUCT.md`. El sitio público no cambia. |
| 2026-10-16 | Seguridad — verificación de correo al registrarse: **no se implementa**. La barrera es que el administrador revise y active cada cuenta; `usua_correo_verificado` sigue sin usarse. |
| 2026-10-16 | Seguridad — correos «apartados»: si el correo de un registro pertenece a una persona **sin cuenta** (suscriptora del newsletter o coautora), la cuenta se crea sobre esa persona (se actualiza su nombre, se conserva la suscripción y su registro de autor, con lo que una coautora ve sus artículos al ser activada). Solo un correo con cuenta da «Este correo ya está registrado». |
| 2026-10-16 | Seguridad — token del newsletter: se deja como está (riesgo bajo). |
| 2026-10-16 | Seguridad — contraseña del administrador: se cambia al instalar con `php artisan emprende:clave-admin` (la pide oculta dos veces, 12 a 72 caracteres, guarda solo el hash, cierra sus sesiones y lo anota en el registro de acciones). No se escribe en ningún archivo. |
| 2026-10-21 | **Segundo rediseño** (acceso, panel del autor y panel del administrador), inspirado en Apple y Nespresso: encabezado global azul marino con los logotipos de la UNAM y la FCA a la izquierda y la navegación a la derecha (la barra lateral del administrador se sustituye por secciones en el encabezado y un panel desplegable en pantallas angostas); fondo marfil cálido, tarjetas blancas con sombras en capas, métricas en ambos paneles (el del autor cuenta sus artículos por estatus) y campos con transiciones suaves. Misma paleta cerrada y mismas reglas de contraste. |
| 2026-10-21 | **Tercer rediseño** (sustituye al segundo, que no convenció): encabezado azul que solo lleva los logotipos de la UNAM y la FCA a la izquierda; navegación del autor y del administrador en una barra lateral sobre fondo hueso (en pantallas angostas, franja horizontal desplazable) y el trabajo en una hoja blanca; contenido sin cajas, ordenado por el aire, hilos finos y tipografía (Geist para la interfaz y Newsreader en títulos grandes); los paneles abren con un saludo y una frase con lo pendiente; acceso en dos mitades (nota editorial y formulario). Misma paleta cerrada. |
| 2026-10-22 | **Sesiones**: el login no lleva `guest`; con sesión abierta avisa con qué cuenta y, al entrar con otras credenciales, cierra e invalida la anterior antes de validar. Cerrar sesión lleva al login. `Cache-Control: no-store` en páginas con sesión y de acceso. Cierre por inactividad a los **30 minutos** (`SESSION_INACTIVIDAD`), con aviso dos minutos antes en el navegador y renovación mientras haya actividad. |
| 2026-10-22 | **Avisos**: mensajes flash como notificaciones y diálogo de confirmación propio en lugar de `window.confirm`; botones con estado «Enviando…» que evitan envíos duplicados. En el sitio público, ventana de confirmación con el diseño del sitio tras enviar el consultorio o la suscripción. |
| 2026-10-22 | **Newsletter**: el enlace de confirmación vence a las 48 horas (`EMPRENDE_NEWSLETTER_HORAS`, sin cambio en la base); la suscripción queda «Pendiente de confirmación» y al suscribirse otra vez llega un enlace nuevo. Sustituye la decisión del 2026-10-16 de dejar el token sin vencimiento. El enlace de baja no vence. |
| 2026-10-22 | **Registro**: el grado académico se elige de una lista (Licenciatura, Especialidad, Maestría, Doctorado, Posdoctorado, Estudiante / Sin grado, Otro), sigue siendo opcional y se valida en el servidor. |
| 2026-10-22 | **Buscador**: también busca en el resumen del artículo y lo muestra con las coincidencias resaltadas. El buscador del inicio del sitio ya envía la búsqueda. |
| 2026-10-22 | **Correo**: `php artisan emprende:probar-correo` (configuración, cola, envío de prueba y causa probable si falla). SELinux: `httpd_can_sendmail` para envíos desde PHP-FPM. |
