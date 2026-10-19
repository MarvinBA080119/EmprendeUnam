{{-- Páginas de error: autónomas (sin Vite, base de datos ni sesión) para que se
     muestren aunque falle otra parte del sistema. Paleta del sistema. --}}
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('titulo') · {{ config('app.name') }}</title>
    <style>
        @font-face { font-family: Inter; font-weight: 400; font-display: swap; src: url('{{ asset('fonts/inter/inter-latin-400-normal.woff2') }}') format('woff2'); }
        @font-face { font-family: Inter; font-weight: 500; font-display: swap; src: url('{{ asset('fonts/inter/inter-latin-500-normal.woff2') }}') format('woff2'); }
        @font-face { font-family: Inter; font-weight: 600; font-display: swap; src: url('{{ asset('fonts/inter/inter-latin-600-normal.woff2') }}') format('woff2'); }
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; flex-direction: column;
               font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
               font-size: 14px; line-height: 1.5; background: #ffffff; color: #001328;
               -webkit-font-smoothing: antialiased; }
        header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #e6e7ea; }
        header strong { display: block; font-size: 15px; font-weight: 600; letter-spacing: -0.015em; }
        header span { font-size: 12px; color: #616d7a; }
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 3rem 1.25rem; }
        .caja { max-width: 30rem; text-align: center; }
        h1 { font-size: 24px; font-weight: 600; letter-spacing: -0.025em; margin: 0 0 0.5rem; }
        p { margin: 0 0 1.75rem; color: #616d7a; }
        .codigo { font-variant-numeric: tabular-nums; }
        a.boton { display: inline-block; background: #664300; color: #ffffff; text-decoration: none;
                  padding: 0.5rem 0.875rem; border-radius: 6px; font-weight: 500;
                  box-shadow: 0 1px 2px rgba(0, 19, 40, 0.12); transition: background-color 160ms ease; }
        a.boton:hover { background: #573900; }
        a.boton:focus-visible { outline: 2px solid #664300; outline-offset: 2px; }
        footer { border-top: 1px solid #e6e7ea; padding: 1rem 1.5rem; font-size: 12px; color: #616d7a; text-align: center; }
    </style>
</head>
<body>
    <header>
        <strong>Revista EmprendeUNAM</strong>
        <span>UNAM · Facultad de Contaduría y Administración</span>
    </header>
    <main>
        <div class="caja">
            <h1>@yield('titulo')</h1>
            <p><span class="codigo">Error @yield('codigo')</span> · @yield('mensaje')</p>
            <a class="boton" href="{{ url('/') }}">Ir al inicio</a>
        </div>
    </main>
    <footer>&copy; {{ now()->year }} Universidad Nacional Autónoma de México</footer>
</body>
</html>
