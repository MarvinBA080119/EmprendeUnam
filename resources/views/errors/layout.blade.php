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
        .codigo { display: inline-block; font-size: 12px; font-weight: 500; color: #664300; background: #f6f4f0;
                  border: 1px solid #e7e1d6; border-radius: 999px; padding: 0.15rem 0.6rem; margin: 0 0 1rem;
                  font-variant-numeric: tabular-nums; }
        h1 { font-size: 24px; font-weight: 600; letter-spacing: -0.025em; margin: 0 0 0.5rem; }
        p { margin: 0 0 1.75rem; color: #616d7a; }
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
            <p class="codigo">Error @yield('codigo')</p>
            <h1>@yield('titulo')</h1>
            <p>@yield('mensaje')</p>
            <a class="boton" href="{{ url('/') }}">Ir al inicio</a>
        </div>
    </main>
    <footer>Revista EmprendeUNAM</footer>
</body>
</html>
