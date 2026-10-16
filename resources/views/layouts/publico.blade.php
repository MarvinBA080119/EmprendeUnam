{{-- Plantilla de acceso: iniciar sesión, registro de autores y contraseña.
     Columna del formulario a la izquierda y, en escritorio, el proceso de
     publicación sobre azul a la derecha. --}}
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

    <div class="emp-acceso__marco">
        <div class="emp-acceso__principal">
            <header class="emp-acceso__barra">
                <a class="emp-marca" href="{{ route('inicio') }}">
                    <span class="emp-marca__nombre">Revista EmprendeUNAM</span>
                    <span class="emp-marca__sub">UNAM · Facultad de Contaduría y Administración</span>
                </a>
                @auth
                    <a class="emp-acceso__volver" href="{{ app(\App\Services\AccesoService::class)->rutaInicio(auth()->user()) }}">Mi panel</a>
                @else
                    <a class="emp-acceso__volver" href="{{ route('inicio') }}">Volver al sitio</a>
                @endauth
            </header>

            <main id="contenido" @class(['emp-acceso__contenido', 'emp-acceso__contenido--ancho' => View::hasSection('ancho')])>
                <x-alertas />
                @yield('contenido')
            </main>

            <footer class="emp-acceso__pie">&copy; {{ now()->year }} Universidad Nacional Autónoma de México</footer>
        </div>

        <aside class="emp-acceso__panel" aria-label="Cómo publicar en la revista">
            <div class="emp-panel__fijo">
                <div>
                    <h2 class="emp-panel__titulo">Publica tu investigación sobre emprendimiento</h2>
                    <p class="emp-panel__texto">
                        La revista de la Facultad de Contaduría y Administración recibe artículos de académicos e
                        investigadores. Todo el proceso se sigue desde tu panel.
                    </p>
                </div>

                <ol class="emp-pasos">
                    <li><strong>Regístrate</strong><span>El equipo editorial revisa y activa tu cuenta.</span></li>
                    <li><strong>Envía tu artículo</strong><span>En PDF, con resumen de hasta 50 palabras y cinco palabras clave.</span></li>
                    <li><strong>Recibe el dictamen</strong><span>Te avisamos por correo; si no se aprueba, te decimos por qué.</span></li>
                    <li><strong>Descarga tu constancia</strong><span>Con folio que cualquiera puede verificar en línea.</span></li>
                </ol>

                <div>
                    <div class="emp-constancia-muestra">
                        <span class="emp-constancia-muestra__tipo">Constancia de aprobación · ejemplo</span>
                        <span class="emp-constancia-muestra__folio">EMP-2026-0042</span>
                        <span class="emp-insignia emp-insignia-aprobado emp-constancia-muestra__estado">Verificable</span>
                    </div>
                    <p class="emp-panel__pie">UNAM · Facultad de Contaduría y Administración</p>
                </div>
            </div>
        </aside>
    </div>
</body>
</html>
