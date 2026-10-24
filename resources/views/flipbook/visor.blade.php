{{-- Visor del flipbook (StPageFlip 2.0.7, MIT). Se genera una vez por número
     con App\Services\FlipbookService y se guarda como index.html estático.
     Equivale al index.php + header.php de la implementación de referencia:
     las hojas son img/1.jpg, img/2.jpg… (con img/hd/ e img/thumbs/). --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    {{-- Las rutas relativas (css/, js/, img/) cuelgan de la carpeta del número,
         con o sin diagonal final en la dirección. --}}
    <base href="{{ $base }}">
    <title>Emprendedores &middot; N.&ordm; {{ $numero }} | FCA UNAM</title>
    <meta name="theme-color" content="#0A0A0A">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="preload" as="image" href="img/1.jpg?v={{ $version }}" fetchpriority="high">
    <link rel="stylesheet" href="css/header.css?v={{ $version }}">
    <link rel="stylesheet" href="css/prestige.css?v={{ $version }}">
</head>
<body>

    <div id="copy-shield" aria-hidden="true"></div>

    <div id="preloader">
        <div class="pl-center">
            <span class="pl-rule" aria-hidden="true"></span>
            <span class="pl-sub">Revista</span>
            <span class="pl-title">Emprendedores</span>
            <div class="pl-bar"><span class="pl-bar__fill"></span></div>
        </div>
    </div>

    <header id="hdr">
        <div class="hdr-inner">
            <div class="hdr-escudos">
                <img src="img/diseno/unam-negro.png" alt="Universidad Nacional Autónoma de México" class="hdr-logo hdr-logo--unam" draggable="false" />
                <img src="img/diseno/fca-negro.png" alt="Facultad de Contaduría y Administración" class="hdr-logo hdr-logo--fca" draggable="false" />
            </div>
            <span class="hdr-filete" aria-hidden="true"></span>
            <div class="hdr-marca">
                @if ($logo)
                    <img src="img/diseno/emprendeunam.png" alt="Emprende UNAM" class="hdr-logo hdr-logo--emp" width="357" height="150" draggable="false" />
                @else
                    <span class="pl-title" style="font-size:1.25rem">Emprende UNAM</span>
                @endif
            </div>
            <div class="hdr-folio">
                <span class="hdr-folio__rule" aria-hidden="true"></span>
                <span class="hdr-folio__etq">Núm.</span>
                <span class="hdr-folio__num">{{ $numero }}</span>
            </div>
        </div>
    </header>

    <main id="stage">
        <div id="zoom-wrap">
            <div id="magazine">
                @for ($i = 1; $i <= $paginas; $i++)
                    <div class="pg" data-pg="{{ $i }}">
                        <img src="img/{{ $i }}.jpg?v={{ $version }}" alt="Página {{ $i }}" data-hd="img/hd/{{ $i }}.jpg?v={{ $version }}"
                             class="pg-img" draggable="false" width="{{ $ancho }}" height="{{ $alto }}"
                             decoding="async" loading="{{ $i <= 3 ? 'eager' : 'lazy' }}" />
                    </div>
                @endfor
            </div>
        </div>
    </main>

    <nav id="toolbar">

        <button id="bt-prev" class="bt bt--nav" aria-label="Anterior" title="Anterior">
            <span class="bt-chevron bt-chevron--l"></span>
        </button>

        <button id="bt-first" class="bt bt--act" aria-label="Inicio" title="Primera página">
            <svg class="bt-svg" viewBox="0 0 20 20" fill="none" width="17" height="17">
                <line x1="4" y1="4" x2="4" y2="16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <polyline points="15,4 8,10 15,16" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <button id="bt-zoom" class="bt bt--act" aria-label="Zoom" title="Zoom">
            <svg class="bt-svg" viewBox="0 0 20 20" fill="none" width="17" height="17">
                <circle cx="8.5" cy="8.5" r="5.8" stroke="currentColor" stroke-width="1.4"/>
                <line x1="13" y1="13" x2="18" y2="18" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                <line x1="6" y1="8.5" x2="11" y2="8.5" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/>
                <line class="zoom-v" x1="8.5" y1="6" x2="8.5" y2="11" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/>
            </svg>
        </button>

        <div class="bt-counter">
            <span id="pg-now" class="bt-counter__n">1</span>
            <span class="bt-counter__sep">/</span>
            <span id="pg-max" class="bt-counter__n">{{ $paginas }}</span>
        </div>

        <!-- ► Marcador de página -->
        <button id="bt-bookmark" class="bt bt--act" aria-label="Marcar página" title="Marcar esta página">
            <svg class="bt-svg" viewBox="0 0 20 20" fill="none" width="17" height="17">
                <path id="bk-path" d="M5 2h10v16l-5-3.5L5 18V2z" stroke="currentColor" stroke-width="1.4" fill="none" stroke-linejoin="round"/>
            </svg>
        </button>

        <button id="bt-search" class="bt bt--act" aria-label="Buscar página" title="Ir a página">
            <svg class="bt-svg" viewBox="0 0 20 20" fill="none" width="17" height="17">
                <circle cx="8.5" cy="8.5" r="5.8" stroke="currentColor" stroke-width="1.3"/>
                <line x1="13" y1="13" x2="18" y2="18" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
        </button>

        <button id="bt-index" class="bt bt--act" aria-label="Índice" title="Índice de contenido">
            <svg class="bt-svg" viewBox="0 0 20 20" fill="none" width="17" height="17">
                <line x1="3" y1="5" x2="5" y2="5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="8" y1="5" x2="17" y2="5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                <line x1="3" y1="10" x2="5" y2="10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="8" y1="10" x2="17" y2="10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                <line x1="3" y1="15" x2="5" y2="15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="8" y1="15" x2="17" y2="15" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
            </svg>
        </button>

        <button id="bt-next" class="bt bt--nav" aria-label="Siguiente" title="Siguiente">
            <span class="bt-chevron bt-chevron--r"></span>
        </button>

    </nav>


    <div id="search-panel" class="spanel" role="dialog" aria-label="Ir a página" aria-hidden="true">
        <div class="spanel__head">
            <span class="spanel__title">Ir a página</span>
            <button id="sp-close" class="spanel__close" aria-label="Cerrar">&times;</button>
        </div>
        <div class="spanel__body">
            <div class="spanel__input-row">
                <input type="number" id="sp-input" class="spanel__input" min="1" max="{{ $paginas }}"
                       placeholder="1 – {{ $paginas }}" inputmode="numeric" />
                <button id="sp-go" class="spanel__go">Ir</button>
            </div>
        </div>
        <div class="spanel__grid-wrap">
            <div id="sp-grid" class="spanel__grid">
                @for ($i = 1; $i <= $paginas; $i++)
                    <button class="sp-thumb" data-pg="{{ $i }}" title="Página {{ $i }}">
                        <img data-src="img/thumbs/{{ $i }}.jpg?v={{ $version }}" alt="{{ $i }}" class="sp-thumb__img" loading="lazy" draggable="false" />
                        <span class="sp-thumb__num">{{ $i }}</span>
                        <span class="sp-thumb__bk" aria-hidden="true">★</span>
                    </button>
                @endfor
            </div>
        </div>
    </div>

    <div id="index-panel" class="ipanel" role="dialog" aria-label="Índice de contenido" aria-hidden="true">
        <div class="ipanel__head">
            <span class="ipanel__title">Índice · N.° {{ $numero }}</span>
            <button id="ip-close" class="ipanel__close" aria-label="Cerrar">&times;</button>
        </div>
        <div class="ipanel__list">
            @forelse ($indice as $idx => $sec)
                @php
                    $meta = 'Pág. '.$sec['pagina'];
                    if (! empty($sec['seccion'])) { $meta = $sec['seccion'].' · '.$meta; }
                    if (! empty($sec['autor'])) { $meta .= ' · '.$sec['autor']; }
                @endphp
                <div class="ipanel__item">
                    <button class="ipanel__link" data-pg="{{ min($sec['pagina'], $paginas) }}">
                        <span class="ipanel__num">{{ $idx + 1 }}</span>
                        <span class="ipanel__text">
                            <span class="ipanel__sec-title">{{ $sec['titulo'] }}</span>
                            <span class="ipanel__sec-page">{{ $meta }}</span>
                        </span>
                    </button>
                    @if ($sec['pdf'])
                        <a href="{{ $sec['pdf'] }}" class="ipanel__dl" target="_blank" rel="noopener" title="Descargar artículo">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                <path d="M8 2v9M8 11L4.5 7.5M8 11l3.5-3.5M2 14.5h12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>PDF</span>
                        </a>
                    @endif
                </div>
            @empty
                <div class="ipanel__item">
                    <span class="ipanel__text"><span class="ipanel__sec-page">Este número todavía no tiene artículos con páginas en el gestor.</span></span>
                </div>
            @endforelse
        </div>
    </div>

    <div id="panel-overlay" class="panel-overlay" aria-hidden="true"></div>

    <div id="zoom-tip" aria-hidden="true">
        <span>ZOOM ACTIVO</span>
        <small>Arrastra para explorar · doble toque para alejar</small>
    </div>

    {{-- Librerías MIT, servidas localmente (NOTICES.txt). --}}
    <script src="js/jquery.min.js?v={{ $version }}"></script>
    <script src="js/page-flip.browser.min.js?v={{ $version }}"></script>
    <script>
        var CF = { total: {{ (int) $paginas }}, num: {{ (int) $numero }} };
    </script>
    <script src="js/reader.js?v={{ $version }}"></script>
</body>
</html>
