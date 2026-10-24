{{-- Plantilla del administrador: encabezado azul con los logotipos, barra
     lateral con las secciones y sus pendientes ($pendientes llega por el view
     composer de AppServiceProvider) y la hoja de trabajo. En pantallas
     angostas la barra lateral se vuelve una franja de secciones desplazable. --}}
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('titulo')@yield('titulo') · @endif{{ config('app.name') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="emp-app" data-privada>
    <a class="emp-saltar" href="#contenido">Saltar al contenido</a>
    <x-alertas />

    <x-cabecera :inicio="route('admin.panel')" />

    <div class="emp-escritorio">
        <aside class="emp-lateral">
            <nav class="emp-nav" aria-label="Secciones del administrador">
                <p class="emp-nav__grupo">Trabajo editorial</p>
                <ul class="emp-nav__lista">
                    <x-enlace-lateral ruta="admin.panel" activa="admin.panel" icono="inicio">Pendientes</x-enlace-lateral>
                    <x-enlace-lateral ruta="admin.articulos.index" activa="admin.articulos.*" icono="articulos"
                                      :cuenta="$pendientes['articulos_en_revision']" cuenta-texto="en revisión">Artículos</x-enlace-lateral>
                    <x-enlace-lateral ruta="admin.autores.index" activa="admin.autores.*" icono="autores"
                                      :cuenta="$pendientes['autores_pendientes']" cuenta-texto="por activar">Autores</x-enlace-lateral>
                    <x-enlace-lateral ruta="admin.consultorio.index" activa="admin.consultorio.*" icono="consultorio"
                                      :cuenta="$pendientes['mensajes_pendientes']" cuenta-texto="sin responder">Consultorio</x-enlace-lateral>
                </ul>
                <p class="emp-nav__grupo">Difusión</p>
                <ul class="emp-nav__lista">
                    <x-enlace-lateral ruta="admin.ediciones.index" activa="admin.ediciones.*" icono="portada">Portada del sitio</x-enlace-lateral>
                    <x-enlace-lateral ruta="admin.flipbooks.index" activa="admin.flipbooks.*" icono="libro">Flipbooks</x-enlace-lateral>
                    <x-enlace-lateral ruta="admin.cursos.index" activa="admin.cursos.*" icono="cursos">Cursos</x-enlace-lateral>
                    <x-enlace-lateral ruta="admin.newsletter.index" activa="admin.newsletter.*" icono="newsletter">Newsletter</x-enlace-lateral>
                </ul>
            </nav>
            <x-menu-usuario rol="Administración" />
        </aside>

        <main id="contenido" class="emp-hoja">
            <div class="emp-hoja__interior">
                @yield('contenido')
            </div>
        </main>
    </div>

    <x-sesion-inactiva />
</body>
</html>
