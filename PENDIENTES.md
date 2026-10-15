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
- [ ] Texto oficial y enlace del aviso de privacidad (RNF-04). Por ahora el registro muestra un texto breve provisional.

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
