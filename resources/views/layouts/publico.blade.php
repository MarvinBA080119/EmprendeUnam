{{-- Plantilla de acceso: iniciar sesión, registro de autores y contraseña.
     Encabezado azul global y, debajo, el formulario en una tarjeta centrada.
     Con @section('ancho') (registro) la tarjeta se acompaña, en escritorio,
     del proceso de publicación a la izquierda. --}}
@php($ancho = View::hasSection('ancho'))
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('titulo')@yield('titulo') · @endif{{ config('app.name') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="emp-acceso">
    <a class="emp-saltar" href="#contenido">Saltar al contenido</a>

    <x-cabecera :inicio="route('inicio')">
        <nav class="emp-cab__acciones" aria-label="Acceso">
            @auth
                <a class="emp-cab__boton" href="{{ app(\App\Services\AccesoService::class)->rutaInicio(auth()->user()) }}">Mi panel</a>
            @else
                <a class="emp-cab__enlace" href="{{ route('inicio') }}">Volver al sitio</a>
                @if (request()->routeIs('registro'))
                    <a class="emp-cab__boton" href="{{ route('login') }}">Iniciar sesión</a>
                @else
                    <a class="emp-cab__boton" href="{{ route('registro') }}">Regístrate</a>
                @endif
            @endauth
        </nav>
    </x-cabecera>

    <main id="contenido" @class(['emp-acceso__escena', 'emp-acceso__escena--ancha' => $ancho])>
        @if ($ancho)
            <aside class="emp-acceso__intro" aria-label="Cómo publicar en la revista">
                <h2 class="emp-acceso__lema">Publica tu investigación sobre emprendimiento</h2>
                <p class="emp-acceso__texto">
                    La revista de la Facultad de Contaduría y Administración recibe artículos de académicos e
                    investigadores. Todo el proceso se sigue desde tu panel.
                </p>

                <ol class="emp-pasos">
                    <li><strong>Regístrate</strong><span>El equipo editorial revisa y activa tu cuenta.</span></li>
                    <li><strong>Envía tu artículo</strong><span>En PDF, con resumen de hasta 50 palabras y cinco palabras clave.</span></li>
                    <li><strong>Recibe el dictamen</strong><span>Te avisamos por correo; si no se aprueba, te decimos por qué.</span></li>
                    <li><strong>Descarga tu constancia</strong><span>Con folio que cualquiera puede verificar en línea.</span></li>
                </ol>

                <div class="emp-constancia-muestra">
                    <span class="emp-constancia-muestra__tipo">Constancia de aprobación · ejemplo</span>
                    <span class="emp-constancia-muestra__folio">EMP-2026-0042</span>
                    <span class="emp-insignia emp-insignia-aprobado emp-constancia-muestra__estado">Verificable</span>
                </div>
            </aside>
        @endif

        <div class="emp-tarjeta-acceso">
            <x-alertas />
            @if ($errors->count() > 1)
                <div class="alert alert-danger" role="alert">
                    <span class="alert-texto">Revisa los {{ $errors->count() }} campos marcados.</span>
                </div>
            @endif
            @yield('contenido')
        </div>
    </main>

    <footer class="emp-acceso__pie">&copy; {{ now()->year }} Universidad Nacional Autónoma de México · Facultad de Contaduría y Administración</footer>
</body>
</html>
