# Pendientes y decisiones

## Pendientes de definir

- [ ] Formato y firma de la constancia (por ahora, ejemplo provisional con la tipografía DejaVu de dompdf).
- [ ] Formato definitivo del folio (por ahora `EMP-AAAA-NNNN`).
- [ ] Captcha del consultorio y del newsletter (por ahora honeypot + límite por IP).
- [ ] Correo real del administrador (por ahora `admin@emprendeunam.invalid` en `comunicacion`).
- [ ] Uso de la extensión `unaccent` en PostgreSQL (por ahora el buscador quita acentos con `translate()` en ambos lados).
- [ ] Servidor SMTP institucional (por ahora `MAIL_MAILER=log`).
- [ ] Exportación de metadatos para Latindex (RNF-05 del anexo; no está en las secciones 1–11).
- [ ] Fecha de fin de los cursos (la menciona el anexo, pero `curso` no tiene columna para ella).
- [ ] Respaldo diario de la base y de los PDF (RNF-06; tarea de infraestructura).
- [ ] "Ver detalle" del artículo para el autor (RF-AUT-04): por ahora lo cubre el historial, que muestra el motivo de rechazo.
- [ ] Meta de rendimiento del buscador (RNF-07, por confirmar).
- [ ] Texto oficial y enlace del aviso de privacidad (RNF-04). Por ahora el registro muestra un texto breve provisional (el sitio público enlaza el PDF de la FCA).
- [ ] **Lineamientos para autores** (`/lineamientos`, texto oficial del sitio, sin cambios): piden archivo en Word, un resumen de 50 palabras y «hasta dos personas autoras», pero el sistema solo acepta PDF, no pide resumen (RN-06) y admite hasta 10 coautores. ¿Se actualiza el texto o se ajusta el sistema?
- [ ] Página «Envío de artículos»: se ajustaron dos lineamientos a las reglas del sistema («Formato»: PDF de hasta 7 MB; «Revisión por pares» → «Dictamen editorial», porque no hay revisores externos, RN-07) y el «Compromiso UNAM» ya no promete la fase de revisión por pares en 15 días. Confirmar redacción.
- [ ] El formulario del consultorio del sitio tenía un campo «Tema»; se retiró porque `mensaje` no tiene columna para guardarlo.
- [ ] Imágenes y PDF del sitio público: copiar las carpetas `img/` y `pdf/` del sitio original a `public/img/` y `public/pdf/` (mismas rutas). Las imágenes del newsletter ya están en `public/img/newsletter/`.
- [ ] Índice GIN para el buscador (requiere cambio en la BD; por ahora el texto completo se calcula en cada consulta, suficiente para el volumen esperado).
- [ ] Contador de visitas del sitio original (`includes/contador.php`): no se usaba en ninguna página y no se migró.

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
| 2026-10-15 | Newsletter: la ventana y la página de suscripción usan los campos de la especificación (tema de la lista de 13 temas). Toda (re)suscripción se confirma de nuevo por correo. La baja pide confirmación con un botón (los filtros antispam visitan los enlaces). |
| 2026-10-15 | Buscador: año = año de aprobación del artículo o de inicio del curso; con un tema elegido solo se muestran artículos; cada término busca también por prefijo. |
