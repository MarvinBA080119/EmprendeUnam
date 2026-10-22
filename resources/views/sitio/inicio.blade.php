@extends('layouts.sitio')

@php
    $tituloPagina = 'El más reciente';
    $paginaId = 'inicio';
    $recursos = [];
@endphp

@section('contenido')
<!-- ══════════════════ HERO · EDICIÓN ACTUAL ══════════════════ -->
<header class="hero">
    <div class="container">
        <div class="row align-items-center g-5">

            <!-- Texto (en móvil pasa debajo de la portada) -->
            <div class="col-lg-7 order-2 order-lg-1">

                <p class="eyebrow mb-0">
                    No. {{ $revista_actual['edicion'] }} &nbsp;·&nbsp;
                    {{ $revista_actual['periodo'] }}
                </p>

                <h1 class="hero__title">{{ $revista_actual['titulo'] }}</h1>

                <p class="hero__lead">
                    Consulte el contenido de nuestra edición actual
                </p>

                <div class="hero__acciones">
                    <a href="{{ asset($revista_actual['pdf_completo']) }}"
                       class="btn btn-outline-dark rounded-0 btn-lux"
                       target="_blank" rel="noopener">
                        <span>Número completo (PDF)</span>
                    </a>

                    @if (! empty($config['ebook_url']))
                    <a href="{{ asset($config['ebook_url']) }}"
                       class="btn btn-outline-dark rounded-0 btn-lux btn-lux--rojo"
                       target="_blank" rel="noopener">
                        <span>Ver en eBook</span>
                    </a>
                    @endif
                </div>

            </div>

            <!-- Portada (en móvil aparece arriba) -->
            {{-- REVELADO PROGRESIVO · sólo el número cero
                     ---------------------------------------------------------
                     La coordinación editorial pidió que la portada no aparezca
                     de golpe sino que se revele poco a poco, como gesto de
                     apertura de la nueva etapa de la revista, y que el efecto
                     quede reservado a este número.

                     No se usa un GIF: un GIF de una portada a este tamaño pesa
                     varios megabytes, se ve con bandas de color y no se puede
                     apagar para quien haya pedido menos movimiento. El mismo
                     resultado se consigue con una máscara CSS que descubre la
                     imagen de izquierda a derecha, sobre el JPG que ya se
                     descarga.

                     La condición cuelga del número de edición, no de una marca
                     manual: cuando config.php pase al número 1, el efecto
                     desaparece solo. --}}
            <div class="col-lg-5 order-1 order-lg-2">
                <div @class(['hero__portada-wrap', 'hero__portada-wrap--revelado' => $revelar_portada])>
                    {{-- El marco envuelve la imagen para poder superponerle
                             las capas del revelado —la línea que marca el borde
                             y el destello— alineadas exactamente a la portada y
                             no al contenedor de la columna. --}}
                    <span class="hero__portada-marco">
                        <img src="{{ asset($revista_actual['portada']) }}"
                             alt="Portada de la edición No. {{ $revista_actual['edicion'] }}"
                             class="hero__portada"
                             data-respaldo="{{ asset('img/portada_emprende_unam_0.jpg') }}"
                             onerror="if(this.dataset.respaldo){this.src=this.dataset.respaldo;this.removeAttribute('data-respaldo');}else{this.onerror=null;this.src='https://placehold.co/700x910/F8F9FA/0A0A0A?text=Portada';}">
                    </span>
                </div>
            </div>

        </div>
    </div>
</header>

<main id="contenido">
    <div class="container py-5">
        <div class="row">

            <!-- ═════════ COLUMNA PRINCIPAL · FEED ═════════ -->
            <div class="col-lg-8">

                {{-- Sin destacados elegidos todavía (gestor de portada), el número principal se muestra solo con su portada. --}}
                @if ($articulo_principal)
                <!-- ── Artículo principal ── -->
                <p class="feed-label">Artículo principal</p>

                {{-- Imagen y texto conviven a dos columnas en vez de apilarse.
                         A ancho completo la fotografía se comía media pantalla antes
                         de llegar al titular; repartida, mantiene la jerarquía de
                         artículo destacado sin desequilibrar la columna. --}}
                <article class="post post--principal">
                    <div class="row g-4 g-lg-5 align-items-center">

                        <div class="col-md-7">
                            <figure class="post__figure post__figure--destacada">
                                <img src="{{ asset($articulo_principal['imagen']) }}"
                                     alt="{{ $articulo_principal['titulo'] }}"
                                     onerror="this.onerror=null; this.src='https://placehold.co/900x600/0A0A0A/FFFFFF?text=EmprendeUNAM'">
                            </figure>
                        </div>

                        <div class="col-md-5">
                            <p class="eyebrow mb-0">{{ $articulo_principal['categoria'] }}</p>

                            <h2 class="post__title post__title--mayor">
                                <a href="{{ asset($articulo_principal['pdf']) }}" target="_blank" rel="noopener">
                                    {{ $articulo_principal['titulo'] }}
                                </a>
                            </h2>

                            <p class="post__excerpt">{{ $articulo_principal['descripcion'] }}</p>

                            <p class="post__meta"><strong>@foreach ((array) $articulo_principal['autores'] as $autor){{ $autor }}@unless ($loop->last)<br>@endunless @endforeach</strong></p>

                            <a href="{{ asset($articulo_principal['pdf']) }}"
                               class="post__more" target="_blank" rel="noopener">Leer artículo</a>
                        </div>

                    </div>

                    {{-- Cursos del artículo principal (gestor de portada); el contenido fijo anterior no los trae. --}}
                    @if (!empty($articulo_principal['cursos_fca']))
                        <aside class="curso-vip">
                            <span class="curso-vip__label">Aprenda más</span>
                            <ul class="curso-vip__lista">
                                @foreach ($articulo_principal['cursos_fca'] as $n => $curso)
                                    <li>
                                        <a href="{{ $curso['url'] }}" target="_blank" rel="noopener">
                                            <span class="curso-vip__num" aria-hidden="true">{{ str_pad($n + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                            <span class="curso-vip__nombre">{{ $curso['texto'] }}</span>
                                            <span class="curso-vip__flecha" aria-hidden="true">&rarr;</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </aside>
                    @endif
                </article>

                <!-- ── En este número ── -->
                <p class="feed-label">En este número</p>

                @foreach ($tabla_contenidos as $art)

                    {{-- Cada artículo abre su propio PDF (sólo sus páginas). Si
                         alguno no lo tuviera todavía, cae al número completo
                         para que el enlace nunca quede roto (SitioService::inicio). --}}

                    <article class="post">

                        <div class="row g-4 align-items-start">

                            <!-- Imagen izquierda -->
                            <div class="col-md-4">
                                <figure class="post__figure">
                                    <img src="{{ asset($art['imagen']) }}"
                                         alt="{{ $art['titulo'] }}"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='https://placehold.co/500x500/0A0A0A/FFFFFF?text=FCA'">
                                </figure>
                            </div>

                            <!-- Contenido derecha -->
                            <div class="col-md-8">
                                <p class="eyebrow mb-0">{{ $art['categoria'] }}</p>

                                <h2 class="post__title">
                                    <a href="{{ asset($art['pdf_art']) }}" target="_blank" rel="noopener">
                                        {{ $art['titulo'] }}
                                    </a>
                                </h2>

                                <p class="post__excerpt">{{ $art['descripcion'] }}</p>

                                <p class="post__meta">
                                    <strong>@foreach ((array) $art['autores'] as $autor){{ $autor }}@unless ($loop->last)<br>@endunless @endforeach</strong>
                                    @if (! empty($art['paginas']))&nbsp;·&nbsp; pp. {{ $art['paginas'] }}@endif
                                </p>

                                <a href="{{ asset($art['pdf_art']) }}"
                                   class="post__more" target="_blank" rel="noopener">Leer artículo</a>
                            </div>

                        </div>

                        <!-- ── Caja negra de cursos FCA/UNAM ── -->
                        {{-- Se muestran TODOS los cursos que registra datos-articulos.php,
                                 uno por línea, para no romper la contención visual. --}}
                        @if (!empty($art['cursos_fca']))

                            <aside class="curso-vip">

                                <span class="curso-vip__label">Aprenda más</span>

                                <ul class="curso-vip__lista">
                                    @foreach ($art['cursos_fca'] as $n => $curso)
                                        <li>
                                            <a href="{{ asset($curso['url']) }}" target="_blank" rel="noopener">
                                                <span class="curso-vip__num" aria-hidden="true">{{ str_pad($n + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                                <span class="curso-vip__nombre">{{ $curso['texto'] }}</span>
                                                <span class="curso-vip__flecha" aria-hidden="true">&rarr;</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                            </aside>

                        @endif

                    </article>

                @endforeach
                @endif

            </div>

            <!-- ═════════ SIDEBAR ═════════ -->
            <aside class="col-lg-4">
                <div class="sidebar-lux">

                    <!-- Búsqueda -->
                    <section class="widget">
                        <h2 class="widget__title" data-i18n="widget.busqueda">Búsqueda</h2>
                        <form id="formBusqueda" role="search" method="GET" action="{{ route('buscador') }}">
                            <div class="input-group">
                                <input type="search" id="inputBusqueda" name="q" class="form-control"
                                       placeholder="Buscar..." aria-label="Buscar en la revista">
                                <button class="btn" type="submit" aria-label="Buscar">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </section>

                    <!-- Idioma
                    <section class="widget">
                        <h2 class="widget__title" data-i18n="widget.idioma">Idioma</h2>
                        <select class="form-select" id="selectIdioma" aria-label="Seleccionar idioma del sitio">
                            @foreach ($idiomas as $codigo => $nombre)
                                <option value="{{ $codigo }}" @selected($codigo === 'es')>{{ $nombre }}</option>
                            @endforeach
                        </select>
                        <p class="idioma-aviso" id="idiomaAviso" role="status" aria-live="polite"></p>
                    </section>-->

                    {{-- NEWSLETTER ────────────────────────────────────────
                             Va segundo, justo después de la búsqueda, por
                             indicación de la coordinación editorial (orden:
                             búsqueda, newsletter, incluida en, información y
                             convocatoria).

                             Abre en la misma pestaña, no en una nueva: el
                             newsletter tiene su propio botón para volver a la
                             revista, así que la ida y la vuelta quedan dentro
                             del mismo recorrido. --}}
                    <section class="widget widget--newsletter">
                        <h2 class="widget__title" data-i18n="widget.newsletter">Newsletter</h2>
                        <p class="widget__texto">
                            Manténgase al día con las claves del mercado, las tendencias
                            y lo que ocurre en nuestra comunidad
                        </p>
                        <a href="{{ route('newsletter') }}" class="btn btn-outline-dark rounded-0 btn-lux btn-lux--bloque">
                            <i class="bi bi-newspaper" aria-hidden="true"></i>
                            <span>Leer el newsletter</span>
                        </a>
                    </section>

                    <!-- Incluida en -->
                    <section class="widget">
                        <h2 class="widget__title" data-i18n="widget.incluida">Incluida en</h2>
                        @foreach ($incluida_en as $dir)
                            <div class="directorio">
                                <a href="{{ asset($dir['url']) }}" target="_blank" rel="noopener">{{ $dir['nombre'] }}</a>
                                <span>{{ $dir['descripcion'] }}</span>
                            </div>
                        @endforeach
                    </section>

                    <!-- Información -->
                    <section class="widget">
                        <h2 class="widget__title" data-i18n="widget.informacion">Información</h2>
                        <ul class="widget__list">
                            <!--<li><a href="{{ route('acerca') }}" data-i18n="info.lectores">Para lectores</a></li>-->
                            <li><a href="{{ route('lineamientos') }}" data-i18n="info.autores">Para autores</a></li>
                        </ul>
                    </section>

                    <!-- Convocatoria -->
                    <section class="cta-convocatoria">
                        <i class="bi bi-envelope-paper"></i>
                        <h2>{{ $convocatoria['titulo'] }}</h2>
                        <p>{{ $convocatoria['descripcion'] }}</p>
                        <a href="{{ route('envio-articulos') }}" class="btn-blanco">{{ $convocatoria['cta'] }}</a>
                    </section>

                </div>
            </aside>

        </div>
    </div>
</main>
@endsection
