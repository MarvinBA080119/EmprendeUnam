{{-- Plantilla de acceso: iniciar sesión, registro de autores y contraseña.
     Encabezado azul con los logotipos y, debajo, dos mitades: a la izquierda
     una nota editorial sobre fondo hueso (en el registro, el proceso de
     publicación) y a la derecha el formulario sobre blanco, sin cajas. --}}
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

    <x-cabecera :inicio="route('inicio')" />

    <div @class(['emp-acceso__marco', 'emp-acceso__marco--registro' => $ancho])>
        <aside class="emp-acceso__nota" aria-label="Revista EmprendeUNAM">
            <div class="emp-acceso__nota-interior">
                @if ($ancho)
                    <h2 class="emp-acceso__lema">Publica tu investigación sobre emprendimiento.</h2>
                    <p class="emp-acceso__texto">
                        La revista de la Facultad de Contaduría y Administración recibe artículos de académicos e
                        investigadores. Todo el proceso se sigue desde tu panel.
                    </p>
                    <ol class="emp-pasos">
                        <li><strong>Regístrate</strong><span>El equipo editorial revisa y activa tu cuenta.</span></li>
                        <li><strong>Envía tu artículo</strong><span>En PDF, con resumen de hasta 50 palabras y cinco palabras clave.</span></li>
                        <li><strong>Recibe el dictamen</strong><span>Te avisamos por correo; si no se aprueba, te decimos por qué.</span></li>
                        <li><strong>Descarga tu constancia</strong><span>Con un folio que cualquiera puede verificar en línea.</span></li>
                    </ol>
                @else
                    <h2 class="emp-acceso__lema">Revista EmprendeUNAM</h2>
                    <p class="emp-acceso__texto">
                        Envío, dictamen y constancias de artículos sobre emprendimiento de la Facultad de
                        Contaduría y Administración.
                    </p>
                @endif
                <p class="emp-acceso__pie">&copy; {{ now()->year }} Universidad Nacional Autónoma de México</p>
            </div>
        </aside>

        <main id="contenido" class="emp-acceso__principal">
            <div class="emp-acceso__barra">
                @auth
                    <a class="emp-acceso__volver" href="{{ app(\App\Services\AccesoService::class)->rutaInicio(auth()->user()) }}">
                        <x-icono nombre="atras" :tamano="14" /> Ir a mi panel
                    </a>
                @else
                    <a class="emp-acceso__volver" href="{{ route('inicio') }}">
                        <x-icono nombre="atras" :tamano="14" /> Volver al sitio
                    </a>
                @endauth
            </div>

            <div @class(['emp-acceso__columna', 'emp-acceso__columna--ancha' => $ancho])>
                <x-alertas />
                @if ($errors->count() > 1)
                    <div class="alert alert-danger" role="alert">
                        <span class="alert-texto">Revisa los {{ $errors->count() }} campos marcados.</span>
                    </div>
                @endif
                @yield('contenido')
            </div>
        </main>
    </div>
</body>
</html>
