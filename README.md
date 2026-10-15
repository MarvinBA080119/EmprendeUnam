# Revista EmprendeUNAM

Sistema de gestión de la Revista EmprendeUNAM (UNAM · Facultad de Contaduría y
Administración): envío y dictamen de artículos, constancias con QR, consultorio,
cursos y newsletter.

Stack: Laravel 13 · PHP 8.3+ · PostgreSQL 18 · Vue 3 + Vite · Bootstrap 5 + Sass.

La especificación está en `docs/especificacion.md` y los puntos abiertos en
`PENDIENTES.md`. La guía de instalación completa para AlmaLinux se agrega en la
fase F.

## Puesta en marcha (desarrollo)

```bash
composer install
npm install
cp .env.example .env          # captura DB_PASSWORD
php artisan key:generate

scripts/crear_bd.sh           # crea el usuario 'emprende' y la base 'emprendeunam' y carga los SQL
php artisan migrate           # solo tablas técnicas de Laravel (sesiones, caché, colas)

npm run build                 # o: npm run dev
php artisan serve
```

## Sitio público

El sitio público conserva el diseño del sitio original de la revista:

- Plantilla: `resources/views/layouts/sitio.blade.php`; páginas en `resources/views/sitio/`.
- Contenido editable (edición actual, histórico, temáticas, podcasts, 10 de 10…): `config/sitio/*.php`.
- Estilos y scripts propios: `resources/css/sitio/` y `resources/js/sitio/` (compilados con Vite).
- Imágenes y PDF: copiar las carpetas `img/` y `pdf/` del sitio original a `public/img/` y `public/pdf/`.

## Pruebas

```bash
scripts/preparar_bd_pruebas.sh   # recrea emprendeunam_test
php artisan test
```
