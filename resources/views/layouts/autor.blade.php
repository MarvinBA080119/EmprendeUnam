{{-- Plantilla del autor: encabezado azul con los logotipos a la izquierda y,
     a la derecha, sus dos secciones y el menú de la cuenta. --}}
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

    <x-cabecera :inicio="route('autor.panel')" menu="menu-autor" menu-etiqueta="Menú de secciones">
        <nav class="offcanvas-lg offcanvas-end emp-nav" tabindex="-1" id="menu-autor" aria-label="Secciones del autor">
            <div class="emp-nav__cabeza d-lg-none">
                <span class="emp-nav__titulo">Secciones</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#menu-autor" aria-label="Cerrar menú"></button>
            </div>
            <ul class="emp-nav__lista">
                <x-enlace-cabecera ruta="autor.panel" activa="autor.panel" icono="articulos">Mis artículos</x-enlace-cabecera>
                <x-enlace-cabecera ruta="autor.articulos.create" activa="autor.articulos.create" icono="mas">Enviar artículo</x-enlace-cabecera>
            </ul>
        </nav>
        <x-menu-usuario rol="Autor" />
    </x-cabecera>

    <main id="contenido" class="emp-area emp-area--autor">
        <x-alertas />
        @yield('contenido')
    </main>

    <footer class="emp-pie">
        <div class="emp-pie__fila emp-pie__fila--autor">
            <span>&copy; {{ now()->year }} Universidad Nacional Autónoma de México · Facultad de Contaduría y Administración</span>
            <a href="{{ route('inicio') }}">Ver el sitio de la revista</a>
        </div>
    </footer>
</body>
</html>
