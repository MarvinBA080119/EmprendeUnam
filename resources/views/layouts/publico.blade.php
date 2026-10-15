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
        <div class="container">
            <a class="navbar-brand" href="{{ route('inicio') }}">
                Revista EmprendeUNAM
                <span class="emp-marca-sub">UNAM · Facultad de Contaduría y Administración</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu-publico"
                    aria-controls="menu-publico" aria-expanded="false" aria-label="Mostrar menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menu-publico">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a @class(['nav-link', 'active' => request()->routeIs('inicio')])
                           @if (request()->routeIs('inicio')) aria-current="page" @endif
                           href="{{ route('inicio') }}">Inicio</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main id="contenido" class="py-4">
        <div class="container">
            <x-alertas />
            @yield('contenido')
        </div>
    </main>

    <footer class="emp-footer py-4 mt-4">
        <div class="container small">
            <p class="mb-1">Revista EmprendeUNAM · Facultad de Contaduría y Administración, UNAM</p>
            <p class="mb-0">&copy; {{ now()->year }} Universidad Nacional Autónoma de México</p>
        </div>
    </footer>
</body>
</html>
