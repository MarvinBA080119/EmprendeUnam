{{--
    Plantilla del sitio público (diseño propio del sitio, distinto del sistema).
    Convertida de includes/header.php e includes/footer.php del sitio original.

    Variables de la vista:
      $tituloPagina (string)   título de la pestaña
      $paginaId     (string)   clave del menú para marcar la opción activa
      $recursos     (string[]) hojas o scripts adicionales de la sección (Vite)

    ESCRITORIO (>= 992px): bloque de logos estático + navbar sticky.
    MÓVIL (< 992px): logos y botón hamburguesa en una sola fila (CSS sobre .cabecera).
--}}
@php
    $tituloPagina = $tituloPagina ?? 'Edición actual';
    $paginaId = $paginaId ?? 'inicio';
    $recursos = $recursos ?? [];

    /* Menú plano de ocho secciones; "Inicio" abre un submenú con las tres páginas
       de entrada. El enlace de la pestaña sigue llevando al inicio: el submenú se
       despliega al pasar el cursor (escritorio) o dentro del acordeón (móvil). */
    $menu = [
        'inicio' => ['label' => 'Inicio', 'i18n' => 'nav.inicio', 'url' => route('inicio'), 'submenu' => [
            ['label' => 'Inicio', 'i18n' => 'nav.inicio', 'url' => route('inicio')],
            ['label' => 'Acerca de', 'i18n' => 'nav.acercade', 'url' => route('acerca')],
            ['label' => 'Nuestra historia', 'i18n' => 'nav.historia', 'url' => route('historia')],
        ]],
        'historico' => ['label' => 'Histórico', 'i18n' => 'nav.historico', 'url' => route('historico')],
        'tematicas' => ['label' => 'Temáticas', 'i18n' => 'nav.tematicas', 'url' => route('tematicas')],
        'podcasts' => ['label' => 'Podcasts', 'i18n' => 'nav.podcasts', 'url' => route('podcasts')],
        '10de10' => ['label' => '10 de 10', 'i18n' => 'nav.diezdediez', 'url' => route('diezdediez')],
        'webinar' => ['label' => 'Webinar', 'i18n' => 'nav.webinar', 'url' => route('webinar')],
        'pyme-hablan' => ['label' => 'Las Pyme Hablan', 'i18n' => 'nav.pymehablan', 'url' => route('pyme-hablan')],
        'consultorio' => ['label' => 'Consultorio', 'i18n' => 'nav.consultorio', 'url' => route('consultorio')],
    ];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tituloPagina }} | FCA UNAM</title>
    <meta name="description" content="Revista académica de divulgación de la Facultad de Contaduría y Administración de la UNAM.">
    <meta name="theme-color" content="#0A0A0A">
    @vite(array_merge(['resources/js/sitio.js'], $recursos))
</head>
<body data-page="{{ $paginaId }}">

<a href="#contenido" class="visually-hidden-focusable btn btn-dark m-2">Ir al contenido</a>

{{-- Envoltorio que permite fusionar logos + navbar en una sola fila en móvil --}}
<div class="cabecera">

    <!-- ══════════════ BLOQUE 1 · LOGOS ══════════════ -->
    <div class="bloque-logos">
        <div class="container">
            <div class="bloque-logos__lista">

                <a href="https://www.unam.mx" target="_blank" rel="noopener">
                    <img src="{{ asset('img/unam_logo.png') }}" alt="UNAM"
                         class="logo-institucional logo-invertido"
                         onerror="this.onerror=null; this.src='https://placehold.co/255x108/FFFFFF/0A0A0A?text=UNAM'; this.className='logo-institucional';">
                </a>

                <a href="https://www.fca.unam.mx" target="_blank" rel="noopener">
                    <img src="{{ asset('img/fca-unam-logo.png') }}" alt="Facultad de Contaduría y Administración"
                         class="logo-institucional logo-invertido"
                         onerror="this.onerror=null; this.src='https://placehold.co/255x108/FFFFFF/0A0A0A?text=FCA'; this.className='logo-institucional';">
                </a>

                {{-- El logo de la revista es a color; hay tres archivos (claro, oscuro y rojo)
                     que se alternan por CSS según el estado de la cabecera. --}}
                <a href="{{ route('inicio') }}">
                    <img src="{{ asset('img/Emprendeunam_horizontal.png') }}" alt="EmprendeUNAM"
                         class="logo-institucional logo-revista logo-revista--claro"
                         onerror="this.onerror=null; this.src='https://placehold.co/255x108/FFFFFF/C8102E?text=EmprendeUNAM'">
                    <img src="{{ asset('img/logo_nuevo_oscuro.png') }}" alt="" aria-hidden="true"
                         class="logo-institucional logo-revista logo-revista--oscuro"
                         onerror="this.onerror=null; this.src='{{ asset('img/logo_nuevo.png') }}'">
                    <img src="{{ asset('img/logo_nuevo_rojo.png') }}" alt="" aria-hidden="true"
                         class="logo-institucional logo-revista logo-revista--rojo"
                         onerror="this.onerror=null; this.src='{{ asset('img/logo_nuevo.png') }}'">
                </a>

            </div>
        </div>
    </div>

    <!-- ══════════════ BLOQUE 2 · NAVBAR ══════════════ -->
    {{-- Sin bg-black de Bootstrap: aplica !important y bloquearía el cambio de color al hacer scroll. --}}
    <nav class="navbar navbar-expand-lg navbar-dark nav-lux" aria-label="Navegación principal">
        <div class="container">

            <button class="navbar-toggler ms-auto" type="button"
                    data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                    aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav mx-auto">

                    @foreach ($menu as $clave => $item)
                        @php($tieneSubmenu = ! empty($item['submenu']))
                        <li @class(['nav-item', 'dropdown' => $tieneSubmenu])>

                            <a @class(['nav-link', 'active' => $paginaId === $clave, 'dropdown-toggle' => $tieneSubmenu])
                               href="{{ $item['url'] }}"
                               @if ($paginaId === $clave) aria-current="page" @endif
                               data-i18n="{{ $item['i18n'] }}">{{ $item['label'] }}</a>

                            @if ($tieneSubmenu)
                                <ul class="dropdown-menu">
                                    @foreach ($item['submenu'] as $sub)
                                        <li>
                                            <a class="dropdown-item" href="{{ $sub['url'] }}"
                                               data-i18n="{{ $sub['i18n'] }}">{{ $sub['label'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                        </li>
                    @endforeach

                </ul>
            </div>

        </div>
    </nav>

</div><!-- /.cabecera -->

@yield('contenido')

<!-- ══════════════════ FOOTER ══════════════════ -->
<footer class="footer-lux" role="contentinfo">
    <div class="container">
        <div class="row g-4 g-lg-5 align-items-start">

            <!-- Identidad -->
            <div class="col-lg-5">
                <p class="footer-lux__title">Publicación académica</p>
                <p class="mb-3">
                    Revista de divulgación de la Facultad de Contaduría y Administración,
                    Universidad Nacional Autónoma de México. Bimestral y de acceso abierto.
                </p>
                {{-- Perfiles oficiales de la FCA: los cuatro usan el identificador FCAUNAMOFICIAL. --}}
                <div class="footer-lux__social">
                    <a href="https://www.facebook.com/FCAUNAMOFICIAL/" target="_blank" rel="noopener" aria-label="Facebook de la FCA UNAM">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://www.instagram.com/fcaunamoficial/" target="_blank" rel="noopener" aria-label="Instagram de la FCA UNAM">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="https://www.youtube.com/@FCAUNAMOFICIAL" target="_blank" rel="noopener" aria-label="YouTube de la FCA UNAM">
                        <i class="bi bi-youtube"></i>
                    </a>
                    <a href="https://x.com/FCAUNAMOFICIAL" target="_blank" rel="noopener" aria-label="X de la FCA UNAM">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                </div>

                {{-- Accesos al sistema de la revista (agregados al integrar el sitio). --}}
                <ul class="footer-lux__enlaces">
                    <li><a href="{{ route('buscador') }}">Buscador</a></li>
                    <li><a href="{{ route('cursos') }}">Cursos</a></li>
                    <li><a href="{{ route('suscripcion') }}">Suscripción</a></li>
                    <li><a href="{{ route('envio-articulos') }}">Envío de artículos</a></li>
                    <li><a href="{{ route('login') }}">Acceso para autores</a></li>
                </ul>
            </div>

            <!-- Contacto -->
            <div class="col-lg-4">
                <p class="footer-lux__title">Contacto</p>
                <p class="mb-3">
                    Circuito Exterior s/n, Ciudad Universitaria<br>
                    Coyoacán, CP 04510, Ciudad de México
                </p>
                <p class="mb-0">
                    <a href="mailto:revistaemprendedores@fca.unam.mx">revistaemprendedores@fca.unam.mx</a><br>
                    +52 (55) 5622 8332
                </p>
            </div>

            <div class="col-lg-3 footer-lux__marcas">
                <img src="{{ asset('img/unam_gran.png') }}" alt="UNAM" class="logo-footer"
                     onerror="this.style.display='none'">
                <img src="{{ asset('img/logo475-azul.jpg') }}" alt="FCA UNAM" class="logo-footer"
                     onerror="this.style.display='none'">
            </div>

        </div>

    </div>

    {{-- La franja legal ocupa todo el ancho: banda bajo el prepié, no una nota dentro de él. --}}
    <div class="footer-lux__legal">
        <div class="container">
            <p class="mb-0">
                Hecho en México. D.R. &copy; <span data-anio-actual>{{ now()->year }}</span> Universidad Nacional Autónoma de México.
                Esta página puede ser reproducida con fines no lucrativos siempre que no se mutile, se cite la fuente
                completa y su dirección electrónica. De otra forma requiere permiso previo por escrito de la institución.
                <a href="https://www.fca.unam.mx/docs/aviso_privacidad.pdf" target="_blank" rel="noopener">Aviso de privacidad</a> ·
                <a href="https://www.fca.unam.mx/docs/permanentes/seguridad.pdf" target="_blank" rel="noopener">Documento de seguridad</a> ·
                Sitio administrado por el Centro de Informática de la FCA (CIFCA).
            </p>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
