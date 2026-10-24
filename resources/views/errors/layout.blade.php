{{-- Páginas de error: autónomas (sin Vite, base de datos ni sesión) para que se
     muestren aunque falle otra parte del sistema. Mismo encabezado azul con
     los logotipos y la misma tipografía del sistema. --}}
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('titulo') · {{ config('app.name') }}</title>
    <style>
        @font-face { font-family: Geist; font-weight: 100 900; font-display: swap; src: url('{{ asset('fonts/geist/geist-latin-wght-normal.woff2') }}') format('woff2'); }
        @font-face { font-family: Newsreader; font-weight: 200 800; font-display: swap; src: url('{{ asset('fonts/newsreader/newsreader-latin-opsz-normal.woff2') }}') format('woff2'); }
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; flex-direction: column;
               font-family: Geist, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
               font-size: 15px; line-height: 1.6; background: #ffffff; color: #001328;
               -webkit-font-smoothing: antialiased; }
        header { display: flex; align-items: center; gap: 1.25rem; height: 6.5rem; padding: 0 1.5rem; background: #001328; }
        header img { display: block; width: auto; height: 5rem; }
        header img + img { height: 4.75rem; }
        @media (max-width: 767.98px) { header { height: 5rem; gap: 1rem; } header img { height: 3.75rem; } header img + img { height: 3.5rem; } }
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 4rem 1.5rem; }
        .caja { max-width: 32rem; text-align: center; }
        .codigo { margin: 0 0 1rem; font-size: 13px; color: #56626e; font-variant-numeric: tabular-nums; }
        h1 { margin: 0 0 0.75rem; font-family: Newsreader, Georgia, serif; font-size: 40px; font-weight: 420;
             line-height: 1.08; letter-spacing: -0.022em; font-optical-sizing: auto; }
        p { margin: 0 0 2rem; color: #46535f; }
        a.boton { display: inline-block; background: #664300; color: #ffffff; text-decoration: none;
                  padding: 0.625rem 1.125rem; border-radius: 10px; font-weight: 500;
                  transition: background-color 160ms ease; }
        a.boton:hover { background: #553800; }
        a.boton:active { transform: scale(0.97); }
        a.boton:focus-visible { outline: 2px solid #664300; outline-offset: 2px; }
        .acciones { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        a.boton--secundario { background: #fff; color: #001328; box-shadow: inset 0 0 0 1px #e2dcd2; }
        a.boton--secundario:hover { background: #f7f5f1; }
        footer { padding: 1.25rem 1.5rem; font-size: 12px; color: #56626e; text-align: center; }
    </style>
</head>
<body>
    <header>
        <img src="{{ asset('img/sistema/unam.png') }}" width="99" height="112" alt="Universidad Nacional Autónoma de México">
        <img src="{{ asset('img/sistema/fca.png') }}" width="132" height="112" alt="Facultad de Contaduría y Administración">
    </header>
    <main>
        <div class="caja">
            <p class="codigo">Error @yield('codigo')</p>
            <h1>@yield('titulo')</h1>
            <p>@yield('mensaje')</p>
            @php
                // Regresar a la página de la que se vino (si es de este sitio) en lugar de mandar al inicio.
                $previa = request()->headers->get('referer');
                $previa = $previa && parse_url($previa, PHP_URL_HOST) === request()->getHost() && $previa !== request()->fullUrl() ? $previa : null;
            @endphp
            <div class="acciones">
                @if ($previa)
                    <a class="boton" href="{{ $previa }}">Regresar</a>
                    <a class="boton boton--secundario" href="{{ url('/') }}">Ir al inicio</a>
                @else
                    <a class="boton" href="{{ url('/') }}">Ir al inicio</a>
                @endif
            </div>
        </div>
    </main>
    <footer>&copy; {{ now()->year }} Universidad Nacional Autónoma de México · Revista EmprendeUNAM</footer>
</body>
</html>
