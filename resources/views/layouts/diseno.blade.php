{{-- Plantilla de la mesa de diseño: encabezado azul con los logotipos, barra
     lateral con una sola sección (y lo que falta por diseñar) y la hoja de
     trabajo. En pantallas angostas la barra lateral se vuelve una franja. --}}
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

    <x-cabecera :inicio="route('diseno.panel')" />

    <div class="emp-escritorio">
        <aside class="emp-lateral">
            <nav class="emp-nav" aria-label="Secciones de diseño">
                <p class="emp-nav__grupo">Revista EmprendeUNAM</p>
                <ul class="emp-nav__lista">
                    <x-enlace-lateral ruta="diseno.panel" activa="diseno.*" icono="pluma"
                                      :cuenta="$conteosDiseno['pendientes']" cuenta-texto="por diseñar">Mesa de diseño</x-enlace-lateral>
                </ul>
            </nav>
            <x-menu-usuario rol="Diseño" />
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
