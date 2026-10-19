@extends('layouts.sitio')

@php
    $tituloPagina = 'Podcasts';
    $paginaId = 'podcasts';
    $recursos = ['resources/css/sitio/secciones_vivas.css', 'resources/js/sitio/interacciones.js', 'resources/js/sitio/reproductor.js'];
@endphp

@section('contenido')
<main id="contenido">

    <!-- ══════════════════ ENCABEZADO ══════════════════ -->
    <header class="head-solo">
        <div class="container">
            <h1>Podcasts</h1>
        </div>
    </header>

    <div class="container pb-5">

        <!-- ══════════════════ EPISODIO DESTACADO ══════════════════ -->
        <section class="yt-destacado">
            <div class="row g-4 g-lg-5 align-items-center">

                <div class="col-lg-7">

                    {{-- Imagen alusiva al episodio --}}
                    <div class="ep-portada">
                        <img src="{{ asset('img/Propuesta_min-1.jpg') }}" alt="Aprender y Emprender">
                    </div>

                </div>

                <div class="col-lg-5">

                    <span class="player__eyebrow">Episodio destacado</span>

                    <h2 class="player__titulo">{{ $destacado['titulo'] }}</h2>

                    @if ($destacado['autor'] !== '')
                        <p class="player__invitado">{{ $destacado['autor'] }}</p>
                    @endif

                    @if ($destacado['descripcion'] !== '')
                        <p class="player__desc">{{ $destacado['descripcion'] }}</p>
                    @endif

                    @if ($meta_ep)
                        <p class="yt-meta">{{ implode(' · ', $meta_ep) }}</p>
                    @endif

                    {{-- ───────── REPRODUCTOR DE SPOTIFY ─────────
                             El iframe de Spotify ya trae sus propios controles
                             de play, pausa, volumen y avance. Se usa
                             loading="lazy" para no bloquear la carga inicial. --}}
                    @if ($spotify_id !== '')

                        <div class="ep-spotify">
                            <iframe
                                src="https://open.spotify.com/embed/episode/{{ $spotify_id }}?theme=0"
                                width="100%" height="232" frameborder="0"
                                allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                loading="lazy"
                                title="Reproductor de Spotify: {{ $destacado['titulo'] }}">
                            </iframe>
                        </div>

                    @else

                        <p class="yt-nota">
                            El audio de este episodio estará disponible en breve.
                        </p>

                    @endif

                    {{-- Nota editorial cuando el episodio aún no tiene datos --}}
                    @if ($destacado['descripcion'] === '' && $destacado['autor'] === '')
                        <p class="yt-nota">
                            El primer episodio de la nueva etapa está en preparación.
                            Se publicará un audio cada mes.
                        </p>
                    @endif

                    {{-- Enlace a YouTube como opción secundaria, por si el
                             episodio también está publicado como video. --}}
                    @if ($id_destacado !== '')
                        <a class="yt-enlace"
                           href="https://www.youtube.com/watch?v={{ $id_destacado }}"
                           target="_blank" rel="noopener">Ver también en YouTube</a>
                    @endif

                </div>

            </div>
        </section>

        <!-- ══════════════════ EPISODIOS DE LA SERIE ══════════════════ -->
        @if (count($podcasts) > 1)

        <p class="bloque-titulo">Episodios de la serie</p>

        <div class="row row-cols-1 row-cols-md-3 g-4">

            @foreach ($podcasts as $ep)
                @php($id_ep = $ep['id_youtube'])

                <div class="col">
                    <article class="ep-card h-100">

                        @if ($id_ep !== '')
                            <div class="yt-marco ep-card__figure" data-yt="{{ $id_ep }}"
                                 role="button" tabindex="0"
                                 aria-label="Reproducir: {{ $ep['titulo'] }}">
                                <img src="{{ asset($ep['portada']) }}"
                                     alt="{{ $ep['titulo'] }}" loading="lazy"
                                     onerror="this.onerror=null; this.src='https://i.ytimg.com/vi/{{ $id_ep }}/hqdefault.jpg';">
                                <span class="yt-play yt-play--chico" aria-hidden="true"><i class="bi bi-play-fill"></i></span>
                            </div>
                        @else
                            <div class="yt-marco ep-card__figure yt-marco--vacio">
                                <img src="{{ asset($ep['imagen']) }}"
                                     alt="{{ $ep['titulo'] }}" loading="lazy"
                                     onerror="this.onerror=null; this.src='https://placehold.co/640x360/0A0A0A/FFFFFF?text=Episodio';">
                                <span class="yt-pendiente yt-pendiente--chico">Por publicar</span>
                            </div>
                        @endif

                        <div class="p-4">
                            <h3 class="ep-card__titulo">{{ $ep['titulo'] }}</h3>
                            <p class="ep-card__autor">{{ $ep['autor'] }}</p>
                            <p class="ep-card__desc">{{ $ep['descripcion'] }}</p>
                            <p class="ep-card__meta">
                                {{ $ep['fecha'] }}
                                &nbsp;·&nbsp;
                                {{ $ep['duracion'] }}
                            </p>
                        </div>

                    </article>
                </div>

            @endforeach

        </div>

        @endif

    </div>

</main>
@endsection
