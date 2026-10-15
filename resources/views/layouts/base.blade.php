{{-- Estructura común de las tres plantillas (publico, autor, admin). --}}
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('titulo')@yield('titulo') · @endif{{ config('app.name') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <a class="emp-saltar" href="#contenido">Saltar al contenido</a>

    <nav class="navbar navbar-expand-lg emp-navbar" aria-label="Navegación principal">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand" href="{{ route('inicio') }}">
                Revista EmprendeUNAM
                <span class="emp-marca-sub">UNAM · Facultad de Contaduría y Administración</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu-principal"
                    aria-controls="menu-principal" aria-expanded="false" aria-label="Mostrar menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menu-principal">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    @yield('menu')
                    @auth
                        <li class="nav-item">
                            <span class="nav-link disabled text-white-50" aria-disabled="true">
                                {{ auth()->user()->usua_nombre_usuario }}
                            </span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link">Cerrar sesión</button>
                            </form>
                        </li>
                    @else
                        <x-enlace-menu ruta="login">Iniciar sesión</x-enlace-menu>
                        <x-enlace-menu ruta="registro">Registro de autores</x-enlace-menu>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @yield('cuerpo')

    <footer class="emp-footer py-4">
        <div class="container small">
            <p class="mb-1">Revista EmprendeUNAM · Facultad de Contaduría y Administración, UNAM</p>
            <p class="mb-0">&copy; {{ now()->year }} Universidad Nacional Autónoma de México</p>
        </div>
    </footer>
</body>
</html>
