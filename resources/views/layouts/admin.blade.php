{{-- Plantilla del administrador: barra lateral con las secciones y sus
     pendientes ($pendientes llega por el view composer de AppServiceProvider),
     y área de trabajo. En pantallas angostas la barra es un panel desplegable. --}}
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

    <div class="emp-app__marco">
        <aside class="offcanvas-lg offcanvas-start emp-lat" tabindex="-1" id="menu-admin" aria-labelledby="menu-admin-titulo">
            <div class="emp-lat__interior">
                <div class="emp-lat__cabeza">
                    <a class="emp-marca" href="{{ route('admin.panel') }}" id="menu-admin-titulo">
                        <span class="emp-marca__nombre">Revista EmprendeUNAM</span>
                        <span class="emp-marca__sub">UNAM · Facultad de Contaduría y Administración</span>
                    </a>
                    <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#menu-admin" aria-label="Cerrar menú"></button>
                </div>

                <nav class="emp-lat__nav" aria-label="Secciones del administrador">
                    <p class="emp-lat__grupo">Trabajo editorial</p>
                    <ul>
                        <x-enlace-lateral ruta="admin.panel" activa="admin.panel" icono="inicio">Pendientes</x-enlace-lateral>
                        <x-enlace-lateral ruta="admin.articulos.index" activa="admin.articulos.*" icono="articulos"
                                          :cuenta="$pendientes['articulos_en_revision']" cuenta-texto="en revisión">Artículos</x-enlace-lateral>
                        <x-enlace-lateral ruta="admin.autores.index" activa="admin.autores.*" icono="autores"
                                          :cuenta="$pendientes['autores_pendientes']" cuenta-texto="por activar">Autores</x-enlace-lateral>
                        <x-enlace-lateral ruta="admin.consultorio.index" activa="admin.consultorio.*" icono="consultorio"
                                          :cuenta="$pendientes['mensajes_pendientes']" cuenta-texto="sin responder">Consultorio</x-enlace-lateral>
                    </ul>
                    <p class="emp-lat__grupo">Difusión</p>
                    <ul>
                        <x-enlace-lateral ruta="admin.cursos.index" activa="admin.cursos.*" icono="cursos">Cursos</x-enlace-lateral>
                        <x-enlace-lateral ruta="admin.newsletter.index" activa="admin.newsletter.*" icono="newsletter">Newsletter</x-enlace-lateral>
                    </ul>
                </nav>

                <div class="emp-lat__pie">
                    <a class="emp-lat__externo" href="{{ route('inicio') }}" target="_blank" rel="noopener">
                        Ver el sitio público <x-icono nombre="externo" :tamano="12" />
                    </a>
                    <div class="emp-usuario">
                        <span class="emp-usuario__avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->usua_nombre_usuario, 0, 1)) }}</span>
                        <span class="emp-usuario__datos">
                            <span class="emp-usuario__nombre">{{ auth()->user()->usua_nombre_usuario }}</span>
                            <span class="emp-usuario__rol">Administración</span>
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="emp-usuario__salir" title="Cerrar sesión">
                                <x-icono nombre="salir" />
                                <span class="visually-hidden">Cerrar sesión</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="emp-app__principal">
            <header class="emp-app__barra d-lg-none">
                <button type="button" class="emp-app__menu" data-bs-toggle="offcanvas" data-bs-target="#menu-admin" aria-controls="menu-admin">
                    <x-icono nombre="menu" :tamano="20" />
                    <span class="visually-hidden">Abrir menú</span>
                </button>
                <span class="emp-marca__nombre">Revista EmprendeUNAM</span>
            </header>

            <main id="contenido" class="emp-app__contenido">
                <x-alertas />
                @yield('contenido')
            </main>
        </div>
    </div>
</body>
</html>
