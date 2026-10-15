# Prompt para Claude Code — Sistema de gestión de la Revista EmprendeUNAM

> Versión del 14 de octubre de 2026. Este documento es la especificación completa. Síguelo al pie de la letra; si algo no está aquí, **no lo inventes**: anótalo en `PENDIENTES.md` y pregúntame.
>
> Nota (15/10/2026): el tamaño máximo del PDF quedó confirmado en **7 MB**. Las decisiones posteriores están en `PENDIENTES.md`.

---

## 0. Rol, idioma y forma de trabajo

- Eres un desarrollador senior de **Laravel 13** y **Vue.js 3**. Vas a construir el sistema de gestión de la Revista EmprendeUNAM (UNAM · Facultad de Contaduría y Administración).
- Todo lo que vea el usuario (textos de interfaz, validaciones, correos, mensajes de error) va en **español de México**. El código usa nombres en español coherentes con la base de datos.
- **Primero el plan, después el código.** En tu primera respuesta entrega solo el plan (estructura de carpetas, lista de archivos por fase, paquetes a instalar con su versión, dudas). No escribas código hasta que yo apruebe.
- Trabaja **por fases** (sección 9). Al terminar cada fase: corre las pruebas, dime qué quedó, cómo probarlo a mano y qué decisión necesitas de mí. No pases a la siguiente fase sin mi aprobación.
- Antes de instalar cualquier paquete de Composer o npm, **verifica que sea compatible con Laravel 13 / PHP 8.3+**. Si no lo es, detente y pregúntame.
- No modifiques la base de datos (tablas, columnas, restricciones, triggers) por tu cuenta. Si crees que hace falta un cambio, propónmelo con el SQL exacto y espera.

---

## 1. Stack obligatorio (no lo cambies)

| Capa | Tecnología |
|---|---|
| Back-end | **Laravel 13** (PHP 8.3 o superior), arquitectura **MVC** |
| Base de datos | **PostgreSQL 18**, base `emprendeunam`, esquema `public` |
| Front-end | **Vue.js 3** (Composition API, `<script setup>`), montado con **Vite** sobre vistas **Blade** |
| Estilos | **Bootstrap 5** compilado con **Sass** + **CSS propio** |
| Autenticación | Sesión de Laravel (guard `web`) con un proveedor de usuarios propio (sección 5). **No** uses Breeze, Jetstream, Fortify, Inertia ni Sanctum |
| PDF de constancia | Una librería de PDF para Laravel compatible con Laravel 13 (propón cuál) |
| Código QR | Una librería de QR compatible con PHP 8.3 (propón cuál) |
| Correo | Mailables de Laravel enviados por **cola** (`QUEUE_CONNECTION=database`) |

Creación del proyecto (en la carpeta actual, que ya tiene `docs/` y `database/`):

```bash
composer create-project laravel/laravel:^13.0 app-tmp
# mueve el contenido de app-tmp a la carpeta actual y borra app-tmp
php artisan --version   # debe decir Laravel Framework 13.x
npm install vue@3 @vitejs/plugin-vue bootstrap@5 @popperjs/core sass
```

`.env` (valores base):

```
APP_NAME="Revista EmprendeUNAM"
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_TIMEZONE=America/Mexico_City   # o en config/app.php si la versión no lee esta variable
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=emprendeunam
DB_USERNAME=emprende
DB_PASSWORD=********
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
BCRYPT_ROUNDS=12
```

---

## 2. Identidad visual

### Paleta (solo estos cuatro colores, más sus variaciones de opacidad)

| Token | Color | Uso |
|---|---|---|
| `--emp-cafe` | `#664300` | Color primario: botones principales, enlaces, acentos, títulos de sección, estado activo del menú |
| `--emp-azul` | `#001328` | Color secundario/oscuro: barra de navegación, pie de página, encabezados de tablas, fondo del panel lateral del administrador |
| `--emp-blanco` | `#FFFFFF` | Fondo general, texto sobre `#001328` y sobre `#664300` |
| `--emp-negro` | `#000000` | Texto de cuerpo |

Reglas de uso (accesibilidad):

- Texto **blanco** sobre `#001328` o sobre `#664300`: permitido.
- Texto `#664300` sobre fondo blanco: permitido (contraste alto).
- **Nunca** texto `#664300` sobre `#001328` ni al revés: el contraste es insuficiente.
- Estados de artículo/mensaje: usa insignias (*badges*) con estos mismos colores y con texto, no solo color (ej. "En revisión" con borde `#664300`, "Aprobado" con fondo `#001328`, "Rechazado" con fondo negro). No introduzcas verdes ni rojos.
- Los colores de alerta de Bootstrap (`success`, `danger`, etc.) se redefinen con la paleta; no uses los colores por defecto de Bootstrap.

Implementación:

- `resources/scss/_variables.scss` define `$primary: #664300; $secondary: #001328; $dark: #000000; $light: #ffffff;` **antes** de importar Bootstrap.
- `resources/scss/app.scss` importa variables, Bootstrap y `resources/css/emprende.css` (estilos propios con las variables CSS `--emp-*`).
- Tipografías: **Playfair Display** para títulos e **Inter** para texto, instaladas localmente con npm (`@fontsource/playfair-display`, `@fontsource/inter`), no desde CDN.
- Diseño responsivo (celular, tableta, escritorio), sobrio e institucional.

---

## 3. Estructura de carpetas MVC (obligatoria)

Respeta esta estructura. Puedes agregar archivos dentro de estas carpetas, pero no cambies la organización.

```
emprendeunam/
├── app/
│   ├── Models/                         ← M: un modelo Eloquent por tabla (sección 4.3)
│   ├── Http/
│   │   ├── Controllers/                ← C: delgados, solo orquestan
│   │   │   ├── Controller.php
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegistroAutorController.php
│   │   │   │   └── RecuperarClaveController.php
│   │   │   ├── Publico/
│   │   │   │   ├── InicioController.php
│   │   │   │   ├── BuscadorController.php
│   │   │   │   ├── ConsultorioController.php
│   │   │   │   ├── CursoController.php
│   │   │   │   ├── NewsletterController.php
│   │   │   │   └── VerificarConstanciaController.php
│   │   │   ├── Autor/
│   │   │   │   ├── PanelController.php
│   │   │   │   ├── ArticuloController.php
│   │   │   │   └── ConstanciaController.php
│   │   │   └── Admin/
│   │   │       ├── PanelController.php
│   │   │       ├── AutorController.php
│   │   │       ├── ArticuloController.php
│   │   │       ├── ConsultorioController.php
│   │   │       ├── CursoController.php
│   │   │       └── NewsletterController.php
│   │   ├── Requests/                   ← validación (Form Requests), una carpeta por módulo
│   │   │   ├── Auth/  ├── Publico/  ├── Autor/  └── Admin/
│   │   └── Middleware/
│   │       └── TienePrivilegio.php     ← autoriza por privilegio leído de la BD
│   ├── Services/                       ← lógica de negocio y transacciones
│   │   ├── RegistroAutorService.php
│   │   ├── ArticuloService.php
│   │   ├── PalabraClaveService.php
│   │   ├── ConstanciaService.php
│   │   ├── ConsultorioService.php
│   │   ├── NewsletterService.php
│   │   └── BuscadorService.php
│   ├── Policies/                       ← ArticuloPolicy, ConstanciaPolicy
│   ├── Auth/
│   │   └── UsuarioProvider.php         ← proveedor de usuarios propio (sección 5)
│   ├── Mail/                           ← un Mailable por correo (sección 7)
│   ├── Enums/
│   │   ├── EstatusArticulo.php         ← EN_REVISION, APROBADO, RECHAZADO
│   │   └── EstatusMensaje.php          ← PENDIENTE, CONTESTADO, RECHAZADO
│   └── Providers/
│       └── AppServiceProvider.php      ← registra UsuarioProvider, policies y rate limiters
├── bootstrap/app.php                   ← registra rutas por módulo y el middleware 'privilegio'
├── config/
│   └── emprende.php                    ← tamaño máximo del PDF, prefijo de folio, etc.
├── database/
│   ├── EMP_FINAL_corregido.sql         ← esquema (ya existe; no lo modifiques)
│   ├── datos_iniciales.sql             ← datos iniciales (ya existe)
│   └── migrations/                     ← SOLO tablas técnicas de Laravel (sección 4.4)
├── resources/
│   ├── views/                          ← V: Blade
│   │   ├── layouts/                    ← publico.blade.php, autor.blade.php, admin.blade.php
│   │   ├── componentes/                ← componentes Blade reutilizables
│   │   ├── auth/  ├── publico/  ├── autor/  └── admin/
│   │   ├── correos/                    ← plantillas de correo
│   │   └── pdf/constancia.blade.php
│   ├── js/
│   │   ├── app.js                      ← monta "islas" Vue por id
│   │   └── components/
│   │       ├── comun/  ├── publico/  ├── autor/  └── admin/
│   ├── scss/  (app.scss, _variables.scss)
│   └── css/emprende.css
├── routes/
│   ├── web.php                         ← solo incluye los archivos de abajo
│   ├── publico.php
│   ├── auth.php
│   ├── autor.php                       ← prefijo /autor, middleware auth + privilegio
│   └── admin.php                       ← prefijo /admin, middleware auth + privilegio
├── storage/app/private/
│   ├── articulos/                      ← PDF de artículos (fuera de public/)
│   └── constancias/                    ← PDF de constancias
├── tests/
│   ├── Feature/  (Auth/, Autor/, Admin/, Publico/)
│   └── Unit/
├── docs/                               ← requerimientos y este prompt
├── CLAUDE.md
└── PENDIENTES.md
```

Reglas MVC:

- **Modelos**: solo mapeo, relaciones, *casts* y *scopes*. Sin lógica de negocio.
- **Controladores**: reciben el Form Request, llaman a un Service y devuelven una vista Blade o JSON. Nada de consultas complejas ni transacciones en el controlador.
- **Services**: toda la lógica de negocio y todas las transacciones (`DB::transaction`).
- **Vistas**: Blade arma la página; Vue se usa para las partes interactivas (formulario de artículo con coautores y 5 palabras clave, buscador con filtros, tablas del administrador). Cada componente Vue se monta en un `<div id="...">` y recibe datos por *props* (`data-*` o JSON en la vista). Las peticiones de Vue van a rutas de `routes/*.php` que devuelven JSON y usan la misma sesión y el token CSRF.

---

## 4. Base de datos (fuente de verdad)

### 4.1 Cómo se carga

Te paso dos scripts SQL (en `database/`). Con ellos **tú creas y cargas la base**: instala PostgreSQL (idealmente 18; si el entorno solo permite otra versión, dime cuál), crea la base `emprendeunam` y el usuario `emprende`, y ejecuta con `psql`, en este orden:

1. `database/EMP_FINAL_corregido.sql` — 50 tablas, llaves, UNIQUE, CHECK y 4 triggers.
2. `database/datos_iniciales.sql` — catálogos y usuario administrador (sección 4.6).

Tras cargarlos, comprueba que existan 50 tablas, 13 filas en `tema`, 4 en `rol`, 12 en `privilegio` y el usuario `EmprendeUnam`. Si algún script da error, detente y muéstramelo; no lo corrijas por tu cuenta.

**No generes migraciones que creen, alteren o borren estas tablas.** No uses `migrate:fresh` ni `RefreshDatabase` contra esta base.

### 4.2 Nombres

- Los identificadores se crearon sin comillas, así que en PostgreSQL existen **en minúsculas**: usa siempre `usuario`, `usua_id_usuario`, etc. en Eloquent y en SQL.
- Cada tabla tiene su propio id `<prefijo>_id_<tabla>` y cada columna lleva el prefijo de su tabla.
- **Atención:** la PK de `articulo` se llama `arti_id_autor` aunque es el **id del artículo** (herencia del modelo). Todas las FK que apuntan a un artículo también se llaman `arti_id_autor`. No la confundas con el autor.
- Ninguna tabla tiene `created_at`/`updated_at`: en todos los modelos usa `public $timestamps = false;`.

### 4.3 Tablas que usa el sistema (20 de 50) y su modelo

Columnas exactas (tipo · nulo). Usa estas longitudes como `max:` en las validaciones.

**`persona`** → `App\Models\Persona` (PK `pers_id_persona`)
`pers_nombre` varchar(100) no nulo · `pers_primer_apellido` varchar(100) no nulo · `pers_segundo_apellido` varchar(100) nulo · `pers_subs_newsletter` bool def. false · `pers_acepta_aviso` bool def. false · `pers_fecha_suscripcion` timestamp nulo · `pers_token_confirmacion` varchar(64) nulo, único · `pers_fecha_confirmacion` timestamp nulo · `pers_fecha_baja` timestamp nulo.

**`usuario`** → `App\Models\Usuario` (extiende `Authenticatable`; PK `usua_id_usuario`)
`pers_id_persona` int no nulo (FK persona) · `usua_nombre_usuario` varchar(50) no nulo, único sin importar mayúsculas · `usua_clave_acceso` varchar(255) no nulo (hash bcrypt) · `usua_fecha_registro` date no nulo · `usua_ultimo_login` date nulo · `usua_activo` bool no nulo · `usua_correo_verificado` bool def. false · `usua_fecha_verificacion` timestamp nulo.
**`usuario` no tiene correo ni teléfono**: están en `comunicacion`.

**`autor`** → `App\Models\Autor` (PK `auto_id_autor`)
`auto_id_coautor` int nulo (FK recursiva a autor; **déjala en NULL**, los coautores se ligan por `autor_articulo`) · `pers_id_persona` int nulo (FK persona) · `auto_institucion` varchar(100) nulo · `auto_grado_academico` varchar(100) nulo.

**`rol`** → `App\Models\Rol` (PK `rol_id_rol`): `rol_nombre` varchar(50) · `rol_descripcion` varchar(100).
**`privilegio`** → `App\Models\Privilegio` (PK `priv_id_privilegio`): `priv_nombre` varchar(50) · `priv_descripcion` varchar(100).
**`rol_usuario`** → `App\Models\RolUsuario` (PK `rous_id_rol_usuario`): `usua_id_usuario` · `rol_id_rol`; par único.
**`rol_privilegio`** → `App\Models\RolPrivilegio` (PK `ropr_id_rol_privilegio`): `rol_id_rol` · `ropr_id_privilegio` (FK a `privilegio.priv_id_privilegio`); par único.

**`tema`** → `App\Models\Tema` (PK `tema_id_tema`): `tema_nombre` varchar(50) único · `tema_descripcion` varchar(100).

**`articulo`** → `App\Models\Articulo` (PK `arti_id_autor` = id del artículo)
`tema_id_tema` int no nulo (FK tema) · `arti_titulo` varchar(200) no nulo · `arti_url_pdf` varchar(256) no nulo (ruta relativa en `storage/app/private`) · `arti_status` varchar(50) no nulo, CHECK `EN_REVISION | APROBADO | RECHAZADO` · `arti_fecha_registro` date · `arti_fecha_modificacion` date · `arti_comentario_rechazo` varchar(350) nulo, **obligatorio si el estatus es RECHAZADO** (CHECK).

**`autor_articulo`** → `App\Models\AutorArticulo` (PK `auar_id_autor_articulo`)
`auar_id_autor` (FK `autor.auto_id_autor`) · `arti_id_autor` (FK articulo) · `auar_principal` bool def. false. Par (autor, artículo) único; **solo un autor principal por artículo** (índice único parcial).

**`palabra`** → `App\Models\Palabra` (PK `palabra_id_palabra`): `pala_descripcion` varchar(100), única sin importar mayúsculas.
**`palabra_articulo`** → `App\Models\PalabraArticulo` (PK `paar_id_palabra_articulo`): `palabra_id_palabra` · `arti_id_autor`; par único; **máximo 5 por artículo** (trigger).
**`curso`** → `App\Models\Curso` (PK `curs_id_curso`): `curs_nombre_curso` varchar(100) · `curs_url` varchar(256) · `curs_institucion` varchar(150) · `curs_fecha` date (nulo en BD, **obligatorio en el formulario**). No tiene tema.
**`palabra_curso`** → `App\Models\PalabraCurso` (PK `pacu_id_palabra_curso`): `curs_id_curso` · `palabra_id_palabra`; par único; máximo 5 (validación en la app).

**`constancia`** → `App\Models\Constancia` (PK `cons_id_constancia`)
`arti_id_autor` no nulo, **único** (una por artículo) · `cons_fecha_emision` date · `cons_url_archivo` varchar(256) · `cons_folio` varchar(30) no nulo, **único**.

**`mensaje`** → `App\Models\Mensaje` (PK `mens_id_mensaje`)
`usua_id_usuario` int nulo (NULL si lo envía un lector; el id del administrador en las respuestas) · `men_mens_id_mensaje` int nulo (FK recursiva: en una respuesta apunta al mensaje original) · `mens_remitente` varchar(75) · `mens_lugar` varchar(100) nulo · `mens_correo` varchar(254) con CHECK de formato · `mens_asunto` varchar(**50**) · `mens_descripcion` varchar(700) · `mens_status` varchar(75), CHECK `PENDIENTE | CONTESTADO | RECHAZADO` · `mens_fecha_envio` timestamp def. now().

**`tipo_comunicacion`** → `App\Models\TipoComunicacion` (PK `comu_id_tipo_comunicacion`): `comu_nombre` varchar(25). Valores cargados: `Correo`, `Teléfono`.
**`comunicacion`** → `App\Models\Comunicacion` (PK `comu_id_comunicacion`)
`pers_id_persona` int (FK persona) · `comu_id_tipo_comunicacion` int no nulo (FK) · `comu_valor_comunicacion` varchar(254) · `comu_extension` varchar(5) nulo · `comu_etiqueta` varchar(50) no nulo (ej. `Principal`, `Newsletter`) · `comu_activo` bool no nulo. El par (tipo, valor en minúsculas) es **único**: un correo no puede repetirse.

**`tema_news`** → `App\Models\TemaNews` (PK `tene_id_tema_news`): `pers_id_persona` no nulo · `tene_tema_interes` varchar(256) no vacío.

`notificacion` existe pero **no se usa en esta versión** (no hay campanita). Las otras 29 tablas (alumnos, pagos, facturación, revista, páginas, favoritos, historiales) **no se tocan**.

Relaciones Eloquent mínimas: Persona hasOne Usuario, hasOne Autor, hasMany Comunicacion, hasMany TemaNews · Usuario belongsTo Persona, belongsToMany Rol (via `rol_usuario`) · Rol belongsToMany Privilegio (via `rol_privilegio`, llaves `rol_id_rol` / `ropr_id_privilegio`) · Articulo belongsTo Tema, belongsToMany Autor (via `autor_articulo`, pivote `auar_principal`), belongsToMany Palabra (via `palabra_articulo`), hasOne Constancia · Curso belongsToMany Palabra (via `palabra_curso`) · Mensaje belongsTo Mensaje como `original` y hasOne Mensaje como `respuesta`.

### 4.4 Tablas técnicas de Laravel

Laravel puede crear **solo** sus tablas técnicas con migraciones: `migrations`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens`. Edita la migración por defecto `0001_01_01_000000_create_users_table.php` para que **no cree la tabla `users`** (deja solo `password_reset_tokens` y `sessions`). Ninguna de estas tablas choca con el esquema.

### 4.5 Reglas que impone la base (la app debe respetarlas y traducir el error a un mensaje claro)

| Regla | Mecanismo en la BD | Qué hace la app |
|---|---|---|
| Máximo 5 palabras clave por artículo | Trigger `tr_max_palabras_articulo` (SQLSTATE 23514) | El formulario exige **exactamente 5**, distintas |
| Rechazo con motivo | CHECK `ck_arti_rechazo` | Comentario obligatorio al rechazar |
| Un autor principal por artículo | Índice único parcial `uq_autor_principal` | El autor que envía es el principal; coautores con `auar_principal = false` |
| Una constancia por artículo, folio único | `uq_constancia_articulo`, `uq_constancia_folio` | Generar la constancia una sola vez |
| Correo con formato válido | Trigger `tr_comunicacion_correo` (cuando el tipo es Correo) | Validar `email` en el Form Request |
| Correo irrepetible | `uq_comunicacion_valor` | Mensaje "Este correo ya está registrado" |
| **Todo usuario tiene un correo activo** | Trigger diferido `tr_usuario_con_correo` (revisa al COMMIT) | Insertar `persona`, `comunicacion` y `usuario` **en la misma transacción** |
| **Suscripción completa** | CHECK `ck_pers_newsletter` + trigger diferido `tr_newsletter_completa` | Insertar `persona`, `comunicacion` (Correo) y `tema_news` en la misma transacción |
| Nombre y apellido no vacíos | CHECK `ck_pers_nombre` | `required` + `trim` |
| Borrados protegidos | Todas las FK con `ON DELETE RESTRICT` (SQLSTATE 23001 en PG18) | No borrar registros con dependencias; mostrar mensaje |

Los triggers buscan el tipo de comunicación cuyo nombre empieza con **"Correo"** (`comu_nombre ILIKE 'correo%'`). No renombres ese registro.

### 4.6 Datos que ya están cargados (`datos_iniciales.sql`)

**Temas (13):** Habilidades directivas · Procesos creativos · Diagnóstico de problemas · Dirección de talento · Escenarios y estrategia competitiva · Cultura organizacional · Análisis de mercados · Comunicación · Economía y sociedad · Relación con el consumidor · Negocios internacionales · Toma de decisiones y resolución de problemas · Emprendimiento social. (`tema_descripcion` = mismo nombre.)

**Roles (4) y privilegios (12):**

| Privilegio (`priv_nombre`) | Administrador | Autor | Lector | Suscriptor_newsletter |
|---|:-:|:-:|:-:|:-:|
| `ENVIAR_ARTICULO` | ✔ | ✔ | | |
| `CONSULTAR_ARTICULOS_PROPIOS` | ✔ | ✔ | | |
| `DESCARGAR_CONSTANCIA` | ✔ | ✔ | | |
| `CONSULTAR_CONTENIDO_PUBLICO` | ✔ | | ✔ | ✔ |
| `ENVIAR_MENSAJE_CONSULTORIO` | ✔ | | ✔ | |
| `SUSCRIBIRSE_NEWSLETTER` | ✔ | | | ✔ |
| `ACTIVAR_AUTOR` | ✔ | | | |
| `DICTAMINAR_ARTICULO` | ✔ | | | |
| `CONSULTAR_TODOS_ARTICULOS` | ✔ | | | |
| `GESTIONAR_CONSULTORIO` | ✔ | | | |
| `GESTIONAR_CURSOS` | ✔ | | | |
| `CONSULTAR_SUSCRIPTORES` | ✔ | | | |

Los nombres de rol exactos son `Administrador`, `Autor`, `Lector` y `Suscriptor_newsletter`. Lector y Suscriptor **no tienen cuenta** (no existen en `usuario`): sus privilegios corresponden a las rutas públicas, que no requieren sesión.

**Tipos de comunicación:** `Correo`, `Teléfono`.

**Administrador (única cuenta):** `usua_nombre_usuario = 'EmprendeUnam'`, contraseña guardada como hash bcrypt `$2y$12$…` en `usua_clave_acceso`, `usua_activo = true`, rol `Administrador`, persona "Administrador EmprendeUNAM", correo en `comunicacion` (tipo Correo, etiqueta `Principal`). La contraseña no está en el código: **no la escribas en ningún archivo, seeder ni prueba**. No crees otros administradores ni una pantalla para crearlos.

Si necesitas datos de ejemplo para desarrollo, créalos en un seeder `DatosDemoSeeder` que **solo** se ejecute con `APP_ENV=local` y que nunca toque al administrador ni los catálogos.

---

## 5. Autenticación y autorización

1. `App\Models\Usuario`: `$table = 'usuario'`, `$primaryKey = 'usua_id_usuario'`, `$timestamps = false`. Sobrescribe `getAuthPasswordName()` para devolver `'usua_clave_acceso'` y `getAuthPassword()` para devolver su valor. Desactiva "recordarme" (`getRememberTokenName()` devuelve cadena vacía): la tabla no tiene `remember_token`.
2. `App\Auth\UsuarioProvider` (extiende `EloquentUserProvider`), registrado en `AppServiceProvider` y usado en `config/auth.php`. `retrieveByCredentials` acepta un solo campo `acceso`:
   - si tiene `@`, busca en `comunicacion` (tipo Correo, `comu_activo = true`, `lower(comu_valor_comunicacion) = lower(:acceso)`) → `persona` → `usuario`;
   - si no, busca por `lower(usua_nombre_usuario)`.
   Así el autor entra con su correo y el administrador puede entrar con `EmprendeUnam` o con su correo.
3. Login: si la contraseña es correcta pero `usua_activo = false`, no inicies sesión y muestra "Tu cuenta está pendiente de activación por el administrador". Al entrar, actualiza `usua_ultimo_login = hoy` y regenera la sesión. Limita los intentos con `RateLimiter` (5 por minuto por IP + usuario).
4. Recuperación de contraseña: usa `password_reset_tokens` con el correo de `comunicacion`. Implementa `getEmailForPasswordReset()` en `Usuario` y el envío con un Mailable propio en español. La nueva contraseña se guarda con `Hash::make()` en `usua_clave_acceso`.
5. Autorización: middleware `privilegio:NOMBRE` (`App\Http\Middleware\TienePrivilegio`) que consulta `usuario → rol_usuario → rol → rol_privilegio → privilegio` (cachea por sesión). Además, policies: `ArticuloPolicy` (un autor solo ve y descarga **sus** artículos) y `ConstanciaPolicy` (solo su constancia). El administrador puede todo.
6. Tras el login: el Administrador va a `/admin`, el Autor a `/autor`.

---

## 6. Flujos que deben quedar exactamente así

### 6.1 Registro de autor (`RegistroAutorService`)
Campos: nombre, primer apellido, segundo apellido (opcional), correo, contraseña (mínimo 8, confirmada), institución, grado académico (opcional), nombre de usuario (opcional; si no se da, genera uno a partir del correo y verifica que sea único).
En **una transacción**:
1. `persona`.
2. `comunicacion` (tipo Correo, etiqueta `Principal`, `comu_activo = true`).
3. `usuario` con `usua_activo = false`, `usua_fecha_registro = hoy`, `usua_clave_acceso = Hash::make(...)`.
4. `autor` (`pers_id_persona`, institución, grado).
5. `rol_usuario` con el rol `Autor`.
Muestra "Tu registro se recibió; el administrador activará tu cuenta". Si el correo ya existe, error claro sin crear nada.

### 6.2 Activación de cuenta (Admin → Autores)
Lista de autores con estatus Pendiente/Activa. "Activar" pone `usua_activo = true` y envía el correo "Tu cuenta fue activada".

### 6.3 Envío de artículo (`ArticuloService`, privilegio `ENVIAR_ARTICULO`)
Formulario (componente Vue): título (máx. 200), tema (lista de `tema`), **exactamente 5** palabras clave distintas (máx. 100 c/u), casilla "¿Tiene coautores?" y, si se marca, al menos 1 coautor (nombre, apellidos, correo, institución), y el PDF.
PDF: `mimetypes:application/pdf`, revisa que el archivo empiece con `%PDF`, tamaño máximo `config('emprende.pdf_max_kb')` (por ahora 10240, **pendiente de confirmar**; confirmado después en 7168).
En **una transacción**:
1. `articulo` con `arti_status = 'EN_REVISION'`, fechas = hoy.
2. Guarda el PDF en `storage/app/private/articulos/{arti_id_autor}.pdf` y su ruta relativa en `arti_url_pdf`.
3. `autor_articulo` del autor que envía con `auar_principal = true`.
4. Cada coautor: si su correo ya existe en `comunicacion`, reutiliza esa persona y su autor (créalo si no tiene); si no, crea `persona` + `comunicacion` (Correo, etiqueta `Coautor`) + `autor`. Después `autor_articulo` con `auar_principal = false`. Los coautores **no** reciben cuenta de usuario.
5. Palabras clave (`PalabraClaveService`): normaliza (minúsculas, `trim`, espacios internos simples); busca en `palabra` por `lower(pala_descripcion)`; si no existe la crea; luego `palabra_articulo`.
Si algo falla, borra el PDF guardado. Envía correo de confirmación al autor y muestra la confirmación en pantalla. Un artículo enviado **no se puede editar ni reenviar** (no existen rutas para eso).

### 6.4 Dictamen (Admin, privilegio `DICTAMINAR_ARTICULO`)
Solo artículos `EN_REVISION`. No hay estatus intermedios.
- **Rechazar**: comentario obligatorio (mín. 20, máx. 350) → `arti_status = 'RECHAZADO'`, `arti_comentario_rechazo`, `arti_fecha_modificacion = hoy` → correo al autor principal con el motivo. Es definitivo.
- **Aprobar** (`ConstanciaService`, en una transacción): `arti_status = 'APROBADO'`; folio `EMP-{AAAA}-{NNNN}` (consecutivo del año, calculado dentro de la transacción con bloqueo para evitar duplicados; el formato final es **pendiente de confirmar**); genera el PDF de la constancia desde `resources/views/pdf/constancia.blade.php` con nombre del autor y coautores, título, fecha, folio y un QR que apunta a `/constancias/verificar/{folio}`; guárdalo en `storage/app/private/constancias/{folio}.pdf`; inserta `constancia`. Envía correo al autor con el enlace de descarga. La constancia **no se regenera** con otro folio.
- El diseño de la constancia es un **ejemplo provisional** (encabezado `#001328`, acentos `#664300`, "Firma o sello institucional (por definir)").

### 6.5 Panel del autor
Historial de **sus** artículos: título, fecha de envío, estatus (insignia), motivo si fue rechazado, descargar su PDF y, si está aprobado, pantalla de agradecimiento y botón "Descargar constancia". Botón "Enviar nuevo artículo". Nada más (no edita su perfil).

### 6.6 Consultorio
- **Público** (sin cuenta): formulario nombre (máx. 75), correo (máx. 254), lugar (opcional, máx. 100), asunto (máx. **50**), mensaje (máx. 700). Inserta `mensaje` con `usua_id_usuario = NULL`, `men_mens_id_mensaje = NULL`, `mens_status = 'PENDIENTE'`. Protección: campo *honeypot* + `RateLimiter` de 3 envíos por hora por IP (el captcha queda **pendiente**).
- **Admin** (privilegio `GESTIONAR_CONSULTORIO`): bandeja con los mensajes donde `men_mens_id_mensaje IS NULL`, filtrable por estatus.
  - **Responder**: en una transacción inserta la respuesta como `mensaje` con `men_mens_id_mensaje = {original}`, `usua_id_usuario = {admin}`, `mens_remitente = 'Consultorio EmprendeUNAM'`, `mens_correo = {correo del admin}`, `mens_asunto = 'Re: ' + asunto` recortado a 50, `mens_status = 'CONTESTADO'`; y pone el original en `CONTESTADO`. Envía la respuesta al correo del lector.
  - **Rechazar**: `mens_status = 'RECHAZADO'` (sin motivo; no se responde ni se publica).
- **Sección pública**: muestra solo los mensajes `CONTESTADO` originales con su respuesta. Nunca muestra correos de los lectores.

### 6.7 Cursos (Admin, privilegio `GESTIONAR_CURSOS`)
Crear, editar y eliminar. Campos: nombre (máx. 100), institución (máx. 150), fecha (obligatoria), enlace (URL `https`, máx. 256), hasta 5 palabras clave (mismo `PalabraClaveService`, tabla `palabra_curso`). Al eliminar, borra primero sus `palabra_curso` en la misma transacción. En el sitio público solo se muestran cursos con `curs_fecha >= hoy`.

### 6.8 Newsletter (`NewsletterService`)
- **Suscripción pública**: nombre, apellido, correo, tema de interés (lista con los 13 temas; se guarda el nombre en `tene_tema_interes`) y casilla obligatoria de aviso de privacidad. En **una transacción**:
  - Si el correo ya existe en `comunicacion` (tipo Correo): usa esa persona. Si ya está suscrita y confirmada sin baja → "Este correo ya está suscrito" (no dupliques). Si se había dado de baja → reactiva (`pers_fecha_baja = NULL`, nueva fecha y token).
  - Si no existe: crea `persona` + `comunicacion` (Correo, etiqueta `Newsletter`, activo).
  - En la persona: `pers_subs_newsletter = true`, `pers_acepta_aviso = true`, `pers_fecha_suscripcion = now()`, `pers_token_confirmacion = bin2hex(random_bytes(32))` (64 caracteres).
  - Inserta `tema_news`.
  - Envía el correo de confirmación con `/newsletter/confirmar/{token}` → `pers_fecha_confirmacion = now()`.
- **Baja**: `/newsletter/baja/{token}` (sin sesión) → `pers_subs_newsletter = false`, `pers_fecha_baja = now()`.
- **Suscriptor activo** = `pers_subs_newsletter = true` y `pers_fecha_confirmacion IS NOT NULL` y `pers_fecha_baja IS NULL`.
- **Admin** (privilegio `CONSULTAR_SUSCRIPTORES`): lista (nombre, correo, tema, fecha de alta, estado activo/baja), exportar CSV (UTF-8 con BOM para Excel) y formulario "Avisar nueva edición" (título de la edición + liga al newsletter) que encola un correo por suscriptor activo con su enlace de baja. El newsletter no se arma en el sistema y no se guarda historial de envíos (los fallidos quedan en `failed_jobs`).

### 6.9 Buscador público (`BuscadorService`)
Un campo de texto + filtros: tipo (artículo/curso), tema (solo artículos) y año. Busca en título del artículo, palabras clave, nombre de autores y coautores y tema; en cursos, nombre, institución y palabras clave. Solo artículos `APROBADO`. Usa búsqueda de texto completo de PostgreSQL con configuración `spanish`, insensible a mayúsculas y acentos. Para ignorar acentos, propón usar la extensión `unaccent` (requiere `CREATE EXTENSION`, **pregúntame antes**); mientras tanto usa `translate()` para quitar acentos en ambos lados. Paginación de 10, resaltado del término y respuesta en menos de 2 s.

### 6.10 Verificación pública de constancias
`/constancias/verificar/{folio}` muestra si el folio existe, el título y la fecha de emisión; nunca descarga el PDF.

---

## 7. Correos (Mailables en `app/Mail`, plantillas en `resources/views/correos`, todos en cola)

`CuentaRecibida` · `CuentaActivada` · `ArticuloRecibido` · `ArticuloAprobado` (con enlace a la constancia) · `ArticuloRechazado` (con el motivo) · `RespuestaConsultorio` · `ConfirmarSuscripcion` · `AvisoNuevaEdicion` (con enlace de baja) · `RecuperarClave`. Diseño con la paleta (encabezado `#001328`, botón `#664300` con texto blanco).

---

## 8. Seguridad y calidad

- Validación siempre en Form Requests; `max:` igual a la longitud de la columna.
- CSRF en todos los formularios y en las peticiones de Vue (cabecera `X-CSRF-TOKEN`).
- Escapa toda salida (`{{ }}` en Blade; nunca `v-html` con datos del usuario).
- Consultas con Eloquent o *bindings*; nunca concatenes entradas en SQL.
- Archivos solo en `storage/app/private`; se descargan por controlador tras pasar la policy.
- Captura `QueryException` y traduce los SQLSTATE `23505` (duplicado), `23514` (regla de negocio) y `23001`/`23503` (registro en uso) a mensajes claros.
- Registro en `storage/logs` de las acciones del administrador (activar, aprobar, rechazar, responder).
- **Pruebas** (PHPUnit, `tests/Feature`):
  - Usa una base aparte `emprendeunam_test` cargada con `EMP_FINAL_corregido.sql` y `datos_iniciales.sql` (crea `scripts/preparar_bd_pruebas.sh` que la recree) y configúrala en `phpunit.xml`.
  - Usa `DatabaseTransactions`, **no** `RefreshDatabase`.
  - Los triggers diferidos solo se revisan al COMMIT: en las pruebas que los validan ejecuta `DB::statement('SET CONSTRAINTS ALL IMMEDIATE')`.
  - Cubre como mínimo: login por correo y por nombre de usuario, cuenta inactiva rechazada, registro completo, envío con 5 palabras (y rechazo con 4 o 6), coautores, un autor no ve artículos ajenos, aprobar genera constancia con folio único, rechazar exige motivo, no se puede reenviar, consultorio (envío, respuesta, publicación solo de contestados), suscripción completa, suscripción duplicada, confirmación, baja, buscador solo con aprobados, acceso por rol a `/admin`.
- No hay script de pruebas de la base de datos: la base se valida con las pruebas PHPUnit de la aplicación.

---

## 9. Fases de entrega

| Fase | Contenido | Se considera terminada cuando… |
|---|---|---|
| **A. Base** | Proyecto Laravel 13, `.env`, conexión a PostgreSQL 18, Vite + Vue 3 + Bootstrap con la paleta, estructura de carpetas de la sección 3, migraciones técnicas, los 19 modelos con relaciones, `CLAUDE.md` y `PENDIENTES.md` | `php artisan --version` = 13.x; una página de prueba lista los 13 temas desde la BD con la paleta aplicada |
| **B. Acceso** | `UsuarioProvider`, login, logout, recuperación de contraseña, registro de autor, middleware `privilegio`, layouts por rol, activación de autores | El administrador entra como `EmprendeUnam`; un autor registrado no entra hasta ser activado |
| **C. Artículos** | Envío, panel del autor, dictamen, constancia PDF con QR, verificación pública | Flujo completo: enviar → aprobar → descargar constancia; y enviar → rechazar con motivo |
| **D. Administrador** | Panel con contadores (autores pendientes, artículos en revisión, mensajes pendientes, suscriptores activos), listas con filtros, cursos | Todas las secciones del panel funcionan |
| **E. Público** | Inicio, buscador, consultorio, cursos, newsletter (suscripción, confirmación, baja, aviso de edición) | Pruebas de la sección 8 en verde |
| **F. Cierre** | Correos en cola, manejo de errores, registro de acciones, revisión de seguridad, `README.md` con instalación en AlmaLinux | `php artisan test` en verde |

---

## 10. `CLAUDE.md` (créalo en la fase A con este contenido resumido)

- Stack: Laravel 13 · PHP 8.3+ · PostgreSQL 18 · Vue 3 + Vite · Bootstrap 5 + Sass · MVC con Services.
- Paleta: `#664300` primario, `#001328` secundario, blanco y negro. Nunca `#664300` sobre `#001328`.
- La BD es la fuente de verdad: no se crean ni alteran tablas del modelo; nombres en minúsculas; `$timestamps = false`; la PK de `articulo` es `arti_id_autor` (= id del artículo).
- `usuario` no tiene correo: está en `comunicacion` (tipo Correo). La contraseña está en `usua_clave_acceso`.
- Inserciones de persona+usuario+correo y de suscripción al newsletter siempre en una transacción (triggers diferidos).
- No escribir la contraseña del administrador en ningún archivo.
- Lo no definido va a `PENDIENTES.md`.

## 11. `PENDIENTES.md` (créalo con estos puntos iniciales)

- Formato y firma de la constancia (por ahora, ejemplo provisional).
- Formato definitivo del folio (por ahora `EMP-AAAA-NNNN`).
- Tamaño máximo del PDF (por ahora 10 MB). — *Resuelto: 7 MB.*
- Captcha del consultorio y del newsletter (por ahora honeypot + límite por IP).
- Correo real del administrador (por ahora `admin@emprendeunam.invalid` en `comunicacion`).
- Uso de la extensión `unaccent` en PostgreSQL.
- Servidor SMTP institucional.

---

## Anexo — Requerimientos vigentes

Este anexo es el documento de requerimientos aprobado. Si algo del anexo choca con las secciones 1–11, **mandan las secciones 1–11** (están ajustadas a la base de datos).

### 1. Objetivo, alcance y roles

El sistema reemplaza el proceso manual por correo de la revista: los autores envían y dan seguimiento a sus artículos, el administrador los dictamina, y los lectores consultan contenido, escriben al consultorio y se suscriben al newsletter.

Se distinguen cuatro roles:

| Rol | Acceso | Qué puede hacer |
| --- | --- | --- |
| Administrador | Login (una sola cuenta, creada por el equipo de desarrollo) | Todo: activar cuentas de autores, dictaminar artículos, atender el consultorio, publicar cursos y consultar suscriptores |
| Autor | Registro y login; cuenta activada por el administrador | Solo enviar sus artículos, consultarlos y descargar su constancia |
| Lector | Sin login | Permisos mínimos: consultar contenido público, buscar y escribir al consultorio |
| Suscriptor del newsletter | Sin login | Permisos mínimos: registrarse al newsletter y recibir el aviso de cada edición |

Los permisos de cada rol se guardan en las tablas de roles y privilegios de la base de datos.

### 2. Módulo Autor

**Cuenta y acceso**

- RF-AUT-01: el autor se registra con nombre, apellidos, correo, contraseña, institución y, opcionalmente, grado académico.
- RF-AUT-02: la cuenta queda inactiva al registrarse; el administrador la activa y solo entonces el autor puede iniciar sesión y enviar artículos. El sistema avisa al autor por correo cuando su cuenta es activada.
- RF-AUT-03: inicio de sesión con correo y contraseña, y recuperación de contraseña por correo.

**Panel del autor**

- RF-AUT-04: historial de sus artículos con título, fecha de envío, estatus y acciones (ver detalle, descargar su PDF, descargar su constancia si está aprobado).
- RF-AUT-05: botón para cargar un nuevo artículo.

**Formulario de envío de artículo**

| Campo | Obligatorio | Regla |
| --- | --- | --- |
| Título | Sí | Texto, máximo 200 caracteres |
| Tema | Sí | Lista cerrada administrada por el sistema |
| Palabras clave | Sí | Exactamente 5, una por campo, sin repetidas (la base de datos rechaza más de 5) |
| ¿Tiene coautores? | Sí | Casilla; si se marca, se habilita el registro de coautores |
| Coautores | Condicional | Nombre, apellidos, correo e institución por cada uno; mínimo 1 si la casilla está marcada |
| Archivo PDF | Sí | Solo PDF, tamaño máximo 10 MB (por confirmar), validado por tipo real del archivo |

- RF-AUT-06: al enviar, el sistema guarda el artículo con estatus En revisión y muestra confirmación al autor.
- RF-AUT-07: el autor no puede editar un artículo después de enviarlo; solo el administrador cambia su estatus.
- RF-AUT-08: un artículo enviado no puede volver a enviarse ni reenviarse corregido; un artículo Rechazado es definitivo.

### 3. Ciclo de vida del artículo

Todo artículo nace En revisión y solo el administrador lo mueve a Aprobado o Rechazado; cada cambio notifica al autor.

| Estatus | Quién lo asigna | Qué ve el autor | Efecto |
| --- | --- | --- | --- |
| En revisión | Sistema, al enviar | Estatus en su historial | Notifica al administrador de un nuevo envío |
| Rechazado | Administrador | Estatus y el motivo del rechazo | Comentario obligatorio; notifica al autor por correo |
| Aprobado | Administrador | Pantalla de agradecimiento con botón para descargar su constancia | Genera la constancia; notifica al autor por correo |

- RF-EST-01: el motivo de rechazo es obligatorio (mínimo 20 caracteres, por confirmar) y queda visible en el detalle del artículo del autor.
- RF-EST-02: al aprobar, el sistema genera una constancia en PDF con folio único, nombre del autor y coautores, título, fecha y firma o sello institucional.
- RF-EST-03: la constancia incluye un código QR que lleva a una página pública de verificación del folio.
- RF-EST-05: no existen estatus intermedios; los únicos estatus son En revisión, Aprobado y Rechazado.

**Ejemplo de constancia (solo ilustrativo; el formato, el texto y la firma se definirán después)**

> **Revista EmprendeUNAM**
> Facultad de Contaduría y Administración, UNAM
>
> Se hace constar que el artículo **«Título del artículo de ejemplo»**, de **Nombre Apellido** (coautores: Nombre Apellido), fue aprobado para su publicación.
>
> Ciudad de México, 14 de octubre de 2026 · Folio: **EMP-2026-0001** · [código QR de verificación]
>
> Firma o sello institucional (por definir)

- RF-EST-04: el autor puede volver a descargar su constancia desde su historial en cualquier momento.

### 4. Módulo Administrador

El administrador inicia sesión y entra a un panel con cinco secciones. Existe una sola cuenta de administrador, creada por el equipo de desarrollo; no hay registro público de administradores.

**Autores**

- RF-ADM-14: lista de autores registrados con su estatus de cuenta (pendiente o activa).
- RF-ADM-15: activar la cuenta de un autor, lo que le permite iniciar sesión y enviar artículos.

**Artículos**

- RF-ADM-01: lista de todos los artículos de todos los autores, con filtros por estatus, tema y fecha, y búsqueda por título, autor o palabra clave.
- RF-ADM-02: detalle del artículo con palabras clave, coautores y datos del autor.
- RF-ADM-03: descargar el PDF del artículo.
- RF-ADM-04: aprobar el artículo, lo que genera la constancia (ver sección 3).
- RF-ADM-05: rechazar el artículo capturando el comentario obligatorio.

**Consultorio**

- RF-ADM-06: bandeja de mensajes enviados por lectores, con estatus Pendiente, Contestado y Rechazado.
- RF-ADM-07: responder un mensaje desde el sistema; la respuesta se envía al correo del lector, queda guardada como un mensaje ligado al original y el original pasa a Contestado (contestado equivale a aceptado).
- RF-ADM-08: rechazar un mensaje; un mensaje rechazado no se publica ni se responde.
- RF-ADM-09: un mensaje contestado se publica automáticamente en la sección pública de consultorio, junto con su respuesta.

**Cursos**

| Campo | Obligatorio | Nota |
| --- | --- | --- |
| Nombre | Sí | Texto |
| Institución | Sí | Texto |
| Fecha | Sí | Fecha de inicio; opcionalmente fecha de fin |
| Enlace | Sí | URL válida (https) |
| Palabras clave | Sí | Hasta 5, mismo criterio que en artículos; sustituyen al tema para clasificar el curso |

- RF-ADM-10: crear, editar y eliminar cursos; los cursos con fecha pasada dejan de mostrarse al público de forma automática.

**Newsletter**

- RF-ADM-11: consultar la lista de suscriptores con correo, fecha de alta y estado (activo o dado de baja).
- RF-ADM-12: exportar la lista a CSV.
- RF-ADM-13: cuando se sube una nueva edición, el sistema avisa por correo a los suscriptores activos con la liga al newsletter (ver sección 5). El newsletter en sí no se arma en el sistema.

### 5. Módulo público (lectores sin login)

**Consultorio**

- RF-PUB-01: formulario con nombre, correo, asunto y mensaje; el lector no necesita cuenta.
- RF-PUB-02: el sistema guarda cada mensaje con fecha y estatus Pendiente, y confirma el envío al lector en pantalla.
- RF-PUB-03: protección contra spam (captcha o equivalente) y límite de envíos por IP.

**Suscripción al newsletter**

- RF-PUB-04: formulario con nombre, apellido, correo, tema de interés y casilla de aceptación del aviso de privacidad; todos los campos son obligatorios y no se puede suscribir quien no acepte el aviso. La base de datos no permite una suscripción sin correo ni tema de interés.
- RF-PUB-05: doble confirmación: el sistema envía un correo con un enlace para confirmar la suscripción, y solo entonces el suscriptor queda activo. Se guardan la fecha de suscripción, la de confirmación y, en su caso, la de baja.
- RF-PUB-06: cada correo del newsletter incluye un enlace de baja que funciona sin iniciar sesión.
- RF-PUB-07: un correo no puede suscribirse dos veces; si ya existe, el sistema lo informa sin duplicar el registro.
- RF-PUB-08: el sistema solo registra a los suscriptores; cuando se sube una nueva edición, les avisa por correo con la liga al newsletter.

**Buscador**

- RF-PUB-09: un solo campo de búsqueda por palabras clave que consulta título, palabras clave, autor, coautores y tema.
- RF-PUB-10: filtros por tema (solo artículos), año y tipo de contenido (artículo, curso); los cursos se encuentran por sus palabras clave.
- RF-PUB-11: resultados ordenados por relevancia, con paginación y resaltado del término encontrado.
- RF-PUB-12: el público solo ve artículos aprobados y publicados; los artículos En revisión o Rechazados nunca aparecen en resultados.
- RF-PUB-13: búsqueda insensible a mayúsculas y acentos (por ejemplo, "administracion" encuentra "administración").
- RF-PUB-14: la sección pública de consultorio muestra los mensajes ya contestados con su respuesta; los pendientes y rechazados nunca se muestran.

### 6. Reglas de negocio y notificaciones

| Evento | Notificación al autor | Notificación al administrador |
| --- | --- | --- |
| Cuenta de autor registrada | Aviso por correo cuando el administrador la activa | Aviso de cuenta pendiente de activar en su panel |
| Artículo enviado | Confirmación en pantalla y por correo | Aviso de nuevo artículo en su panel |
| Artículo aprobado | Correo con enlace a la constancia | Aviso en su panel |
| Artículo rechazado | Correo con el motivo | No aplica |
| Mensaje de consultorio recibido | No aplica | Aviso de mensaje pendiente |
| Mensaje de consultorio respondido | No aplica (el lector recibe la respuesta por correo) | No aplica |

- RN-01: un autor solo ve y descarga sus propios artículos y constancias; nunca los de otros.
- RN-02: un artículo tiene un solo autor principal; los coautores no necesitan cuenta.
- RN-03: las palabras clave se normalizan (minúsculas, sin espacios sobrantes) para que el buscador agrupe variantes.
- RN-04: cada aprobación genera un folio de constancia irrepetible; una constancia no se regenera con un folio distinto.
- RN-05: los correos del newsletter solo se envían a suscriptores con confirmación vigente y sin baja.
- RN-06: los artículos no llevan abstract; las palabras clave son máximo 5 por artículo (y por curso) y se relacionan con el artículo mediante una tabla intermedia, de modo que el buscador consulta artículo, palabra clave y palabra.
- RN-07: no hay revisores ni dictaminadores externos; el administrador decide solo sobre las cuentas de autor, los artículos y los mensajes.
- RN-08: el correo de una persona se guarda en la tabla de comunicación (tipo Correo), no en la cuenta de usuario; el autor inicia sesión con ese correo, que no puede repetirse. Todo usuario debe tener un correo activo.

### 7. Requerimientos no funcionales

- RNF-01 Seguridad: contraseñas con hash seguro (bcrypt o Argon2), nunca en texto plano; sesiones con expiración; protección contra CSRF, XSS e inyección SQL.
- RNF-02 Roles: control de acceso por rol en servidor; el panel de administrador no es accesible a autores ni a lectores.
- RNF-03 Archivos: los PDF se guardan fuera de la carpeta pública y se sirven solo a su autor o al administrador, tras validar el tipo real del archivo.
- RNF-04 Privacidad: aviso de privacidad visible en registro, consultorio y newsletter, conforme a la normatividad aplicable a la UNAM.
- RNF-05 Latindex: los metadatos de cada artículo (título, autores, palabras clave, fecha) deben estar disponibles y exportables para cumplir los requisitos digitales de Latindex.
- RNF-06 Disponibilidad de datos: respaldo diario de la base de datos y de los PDF.
- RNF-07 Rendimiento: el buscador responde en menos de 2 segundos con el volumen esperado (meta por confirmar).
- RNF-08 Usabilidad: diseño responsivo (celular, tableta, escritorio) y coherente con la identidad visual de la revista.
- RNF-09 Base de datos: PostgreSQL 18, siguiendo el modelo de EMP_BD_fisi.pdm y su convención de nombres; el buscador puede apoyarse en búsqueda de texto completo de PostgreSQL.
- RNF-10 Correo: envío mediante un servicio SMTP institucional o transaccional, con registro de envíos fallidos.
- RNF-11 Tecnología: arquitectura MVC con Laravel 13 (back-end), Vue.js (interfaz), Bootstrap y CSS (estilos) y PostgreSQL 18.

### 8. Fuera de alcance y preguntas abiertas

**Fuera de alcance (nota para una fase futura)**

- Dictamen por pares (revisores externos), rondas de correcciones y reenvío de artículos.
- Armado y diseño del newsletter dentro del sistema (solo se registran suscriptores y se avisa de cada edición).
- Publicación automática del número de la revista a partir de los artículos aprobados.
- Cobro, DOI y gestión de derechos de autor.

**Preguntas abiertas para definir**

- [ ] ¿Quién firma la constancia y qué formato institucional debe seguir?
- [x] ¿Cuál es el límite de tamaño del PDF? — 7 MB.
