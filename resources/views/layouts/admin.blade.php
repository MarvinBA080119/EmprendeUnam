{{-- Plantilla del administrador: encabezado azul con los logotipos a la
     izquierda y, a la derecha, las secciones con sus pendientes ($pendientes
     llega por el view composer de AppServiceProvider) y el menú de la cuenta.
     En pantallas angostas las secciones van en un panel desplegable. --}}
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('titulo')@yield('titulo') · @endif{{ config('app.name') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="emp-app">
    <a class="emp-saltar" href="#contenido">Saltar al contenido</a>

    <x-cabecera class="emp-cab--muchas" :inicio="route('admin.panel')" menu="menu-admin" menu-etiqueta="Menú de secciones">
        <nav class="offcanvas-lg offcanvas-end emp-nav" tabindex="-1" id="menu-admin" aria-label="Secciones del administrador">
            <div class="emp-nav__cabeza d-lg-none">
                <span class="emp-nav__titulo">Secciones</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#menu-admin" aria-label="Cerrar menú"></button>
            </div>
            <ul class="emp-nav__lista">
                <x-enlace-cabecera ruta="admin.panel" activa="admin.panel" icono="inicio">Pendientes</x-enlace-cabecera>
                <x-enlace-cabecera ruta="admin.articulos.index" activa="admin.articulos.*" icono="articulos"
                                   :cuenta="$pendientes['articulos_en_revision']" cuenta-texto="en revisión">Artículos</x-enlace-cabecera>
                <x-enlace-cabecera ruta="admin.autores.index" activa="admin.autores.*" icono="autores"
                                   :cuenta="$pendientes['autores_pendientes']" cuenta-texto="por activar">Autores</x-enlace-cabecera>
                <x-enlace-cabecera ruta="admin.consultorio.index" activa="admin.consultorio.*" icono="consultorio"
                                   :cuenta="$pendientes['mensajes_pendientes']" cuenta-texto="sin responder">Consultorio</x-enlace-cabecera>
                <x-enlace-cabecera ruta="admin.cursos.index" activa="admin.cursos.*" icono="cursos">Cursos</x-enlace-cabecera>
                <x-enlace-cabecera ruta="admin.newsletter.index" activa="admin.newsletter.*" icono="newsletter">Newsletter</x-enlace-cabecera>
            </ul>
        </nav>
        <x-menu-usuario rol="Administración" />
    </x-cabecera>

    <main id="contenido" class="emp-area">
        <x-alertas />
        @yield('contenido')
    </main>

    <footer class="emp-pie">
        <div class="emp-pie__fila">
            <span>&copy; {{ now()->year }} Universidad Nacional Autónoma de México · Facultad de Contaduría y Administración</span>
            <a href="{{ route('inicio') }}" target="_blank" rel="noopener">Ver el sitio de la revista</a>
        </div>
    </footer>
</body>
</html>
