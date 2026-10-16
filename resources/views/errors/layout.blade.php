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
               font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
               background: #ffffff; color: #000000; line-height: 1.5; }
        header { background: #001328; color: #ffffff; padding: 1rem 1.5rem; }
        header strong { display: block; font-size: 1.15rem; }
        header span { font-size: .85rem; opacity: .8; }
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; }
        .caja { max-width: 34rem; text-align: center; }
        .codigo { font-size: 3.5rem; font-weight: 700; color: #664300; margin: 0; line-height: 1; }
        h1 { font-size: 1.5rem; margin: .75rem 0 .5rem; color: #001328; }
        p { margin: 0 0 1.5rem; }
        a.boton { display: inline-block; background: #664300; color: #ffffff; text-decoration: none;
                  padding: .6rem 1.4rem; border-radius: .375rem; font-weight: 600; }
        a.boton:hover, a.boton:focus-visible { background: #000000; outline: 2px solid #664300; outline-offset: 2px; }
        footer { border-top: 1px solid #001328; padding: .75rem 1.5rem; font-size: .8rem; text-align: center; }
    </style>
</head>
<body>
    <header>
        <strong>Revista EmprendeUNAM</strong>
        <span>UNAM · Facultad de Contaduría y Administración</span>
    </header>
    <main>
        <div class="caja">
            <p class="codigo">@yield('codigo')</p>
            <h1>@yield('titulo')</h1>
            <p>@yield('mensaje')</p>
            <a class="boton" href="{{ url('/') }}">Ir al inicio</a>
        </div>
    </main>
    <footer>Revista EmprendeUNAM</footer>
</body>
</html>
