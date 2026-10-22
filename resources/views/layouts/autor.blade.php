{{-- Plantilla del autor: encabezado azul con los logotipos, barra lateral con
     sus dos secciones y la cuenta, y la hoja de trabajo. En pantallas angostas
     la barra lateral se vuelve una franja de secciones. --}}
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

    <x-cabecera :inicio="route('autor.panel')" />

    <div class="emp-escritorio">
        <aside class="emp-lateral">
            <nav class="emp-nav" aria-label="Secciones del autor">
                <p class="emp-nav__grupo">Revista EmprendeUNAM</p>
                <ul class="emp-nav__lista">
                    <x-enlace-lateral ruta="autor.panel" activa="autor.panel" icono="articulos">Mis artículos</x-enlace-lateral>
                    <x-enlace-lateral ruta="autor.articulos.create" activa="autor.articulos.create" icono="mas">Enviar artículo</x-enlace-lateral>
                </ul>
            </nav>
            <x-menu-usuario rol="Autor" />
        </aside>

        <main id="contenido" class="emp-hoja">
            <div class="emp-hoja__interior emp-hoja__interior--lectura">
                @yield('contenido')
            </div>
        </main>
    </div>

    <x-sesion-inactiva />
</body>
</html>
