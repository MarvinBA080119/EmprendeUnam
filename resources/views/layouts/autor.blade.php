{{-- Plantilla del autor: barra superior con la navegación (dos secciones) y
     contenido centrado. --}}
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

    <header class="emp-sup">
        <div class="emp-sup__fila">
            <a class="emp-marca" href="{{ route('autor.panel') }}">
                <span class="emp-marca__nombre">Revista EmprendeUNAM</span>
                <span class="emp-marca__sub">UNAM · Facultad de Contaduría y Administración</span>
            </a>

            <nav class="emp-sup__nav" aria-label="Secciones del autor">
                <a @class(['emp-sup__enlace', 'is-activo' => request()->routeIs('autor.panel')])
                   @if (request()->routeIs('autor.panel')) aria-current="page" @endif
                   href="{{ route('autor.panel') }}">Mis artículos</a>
                @if (Route::has('autor.articulos.create'))
                    <a @class(['emp-sup__enlace', 'is-activo' => request()->routeIs('autor.articulos.create')])
                       @if (request()->routeIs('autor.articulos.create')) aria-current="page" @endif
                       href="{{ route('autor.articulos.create') }}">Enviar artículo</a>
                @endif
            </nav>

            <div class="emp-usuario emp-sup__usuario">
                <span class="emp-usuario__avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->usua_nombre_usuario, 0, 1)) }}</span>
                <span class="emp-usuario__datos">
                    <span class="emp-usuario__nombre">{{ auth()->user()->usua_nombre_usuario }}</span>
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
    </header>

    <main id="contenido" class="emp-autor">
        <x-alertas />
        @yield('contenido')
    </main>

    <footer class="emp-autor__pie">
        <div class="emp-autor__pie-fila">
            <span>&copy; {{ now()->year }} Universidad Nacional Autónoma de México</span>
            <a href="{{ route('inicio') }}">Ver el sitio de la revista</a>
        </div>
    </footer>
</body>
</html>
