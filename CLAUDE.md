# Revista EmprendeUNAM — guía para agentes

La especificación completa está en `docs/especificacion.md`. Lo que no esté
definido ahí va a `PENDIENTES.md` y se pregunta antes de implementarlo.

## Stack
- Laravel 13 · PHP 8.3+ (el lock está fijado a `platform.php = 8.3.6`)
- PostgreSQL 18 (base `emprendeunam`, esquema `public`)
- Vue 3 (`<script setup>`) como "islas" montadas con Vite sobre vistas Blade
- Bootstrap 5 + Sass + CSS propio (`resources/css/emprende.css`)
- MVC con Services: controladores delgados; lógica y transacciones en `app/Services`

## Dos diseños
- **Sistema** (autores y administrador, login/registro): paleta de abajo, `layouts/publico|autor|admin`.
- **Sitio público** (inicio, histórico, consultorio, newsletter…): diseño propio del sitio original,
  `layouts/sitio.blade.php`, CSS/JS en `resources/css/sitio` y `resources/js/sitio`, contenido en
  `config/sitio/*.php`. No se rediseña.

## Paleta (sistema)
- `#664300` primario (café), `#001328` secundario (azul), blanco y negro. Nada más.
- **Nunca** texto `#664300` sobre `#001328` ni al revés.
- Estados con insignias de la paleta y con texto (sin verdes ni rojos).

## La base de datos es la fuente de verdad
- No se crean, alteran ni borran tablas del modelo. Las migraciones son solo
  para las tablas técnicas de Laravel. Nada de `migrate:fresh` ni `RefreshDatabase`.
- Identificadores en minúsculas; cada columna lleva el prefijo de su tabla.
- Todos los modelos usan `public $timestamps = false;`.
- La PK de `articulo` es `arti_id_autor` y es el **id del artículo**.
- `usuario` no tiene correo: está en `comunicacion` (tipo Correo).
  La contraseña está en `usua_clave_acceso` (bcrypt).
- persona + comunicacion (Correo) + usuario, y la suscripción al newsletter
  (persona + comunicacion + tema_news), siempre en **una** transacción:
  los triggers diferidos se revisan al COMMIT.
- No escribir la contraseña del administrador en ningún archivo, seeder ni prueba.

## Comandos
```bash
scripts/crear_bd.sh               # crea y carga emprendeunam (lee DB_PASSWORD de .env)
scripts/preparar_bd_pruebas.sh    # recrea emprendeunam_test (+ migraciones técnicas)
php artisan migrate               # solo tablas técnicas
npm run build                     # o npm run dev
php artisan test                  # PHPUnit sobre emprendeunam_test
```

## Pruebas
- `tests/TestCase.php` usa `DatabaseTransactions` y se niega a correr contra
  una base cuyo nombre no termine en `_test`.
- Para validar triggers diferidos: `DB::statement('SET CONSTRAINTS ALL IMMEDIATE')`.
