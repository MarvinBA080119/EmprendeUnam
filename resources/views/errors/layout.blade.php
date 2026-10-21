{{-- Páginas de error: autónomas (sin Vite, base de datos ni sesión) para que se
     muestren aunque falle otra parte del sistema. Mismo encabezado azul y
     fondo marfil del sistema. --}}
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
               font-size: 14px; line-height: 1.5; background: #f1eee8; color: #001328;
               -webkit-font-smoothing: antialiased; }
        header { display: flex; align-items: center; gap: 0.875rem; min-height: 4.5rem; padding: 0.625rem 1.5rem;
                 background: #001328; color: #ffffff; }
        header img { display: block; width: auto; height: 2.75rem; }
        header div { padding-left: 0.875rem; border-left: 1px solid rgba(255, 255, 255, 0.18); line-height: 1.2; }
        header strong { display: block; font-size: 15px; font-weight: 600; letter-spacing: -0.015em; }
        header span { font-size: 11px; color: rgba(255, 255, 255, 0.66); }
        @media (max-width: 575.98px) { header div { display: none; } }
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 3rem 1.25rem; }
        .caja { max-width: 30rem; text-align: center; padding: 2.5rem 2rem; border-radius: 20px; background: #ffffff;
                box-shadow: 0 0 0 1px rgba(0, 19, 40, 0.05), 0 2px 4px rgba(0, 19, 40, 0.04), 0 16px 32px -16px rgba(0, 19, 40, 0.14); }
        h1 { font-size: 24px; font-weight: 600; letter-spacing: -0.025em; margin: 0 0 0.5rem; }
        p { margin: 0 0 1.75rem; color: #616d7a; }
        .codigo { font-variant-numeric: tabular-nums; }
        a.boton { display: inline-block; background: #664300; color: #ffffff; text-decoration: none;
                  padding: 0.625rem 1.125rem; border-radius: 10px; font-weight: 600;
                  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.14), 0 1px 2px rgba(0, 19, 40, 0.16), 0 6px 16px -8px rgba(102, 67, 0, 0.55);
                  transition: background-color 180ms ease; }
        a.boton:hover { background: #573900; }
        a.boton:focus-visible { outline: 2px solid #664300; outline-offset: 2px; }
        footer { border-top: 1px solid #e5dfd4; padding: 1rem 1.5rem; font-size: 12px; color: #616d7a; text-align: center; }
    </style>
</head>
<body>
    <header>
        <img src="{{ asset('img/sistema/unam.png') }}" width="99" height="112" alt="Universidad Nacional Autónoma de México">
        <img src="{{ asset('img/sistema/fca.png') }}" width="132" height="112" alt="Facultad de Contaduría y Administración">
        <div>
            <strong>Revista EmprendeUNAM</strong>
            <span>Facultad de Contaduría y Administración</span>
        </div>
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
