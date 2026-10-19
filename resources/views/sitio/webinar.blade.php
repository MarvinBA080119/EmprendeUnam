@extends('layouts.sitio')

@php
    $tituloPagina = 'Webinars';
    $paginaId = 'webinar';
    $recursos = ['resources/css/sitio/secciones_vivas.css', 'resources/css/sitio/formularios_media.css', 'resources/js/sitio/interacciones.js'];
@endphp

@section('contenido')
<main id="contenido">

    <!-- ══════════════════ ENCABEZADO ══════════════════ -->
    {{-- El H1 aparece solo, sin subtítulo descriptivo (criterio editorial) --}}
    <header class="head-solo">
        <div class="container">
            <h1>Webinars</h1>
        </div>
    </header>

    <div class="container pb-5">

        @php($id_princ = $id_principal)

        <!-- ══════════════════ TRANSMISIÓN DESTACADA ══════════════════ -->
        {{-- La regla inferior sólo tiene sentido si debajo viene la rejilla
                 de más videos. Con una sola transmisión dejaba una línea suelta
                 seguida de un hueco vacío hasta el pie. --}}
        <section @class(['row g-5 align-items-start', 'mb-5 pb-5 border-bottom' => $mas_webinars])>

            <div class="col-lg-7">

                @if ($id_princ !== '')
                    <div class="yt-marco" data-yt="{{ $id_princ }}"
                         role="button" tabindex="0"
                         aria-label="Reproducir: {{ $webinar_principal['titulo'] }}">
                        <img src="https://i.ytimg.com/vi/{{ $id_princ }}/maxresdefault.jpg"
                             alt="{{ $webinar_principal['titulo'] }}"
                             onerror="this.onerror=null; this.src='https://i.ytimg.com/vi/{{ $id_princ }}/hqdefault.jpg';">
                        <span class="yt-play" aria-hidden="true"><i class="bi bi-play-fill"></i></span>
                    </div>
                @else
                    <div class="yt-marco yt-marco--vacio">
                        <img src="{{ asset($webinar_principal['imagen']) }}"
                             alt="{{ $webinar_principal['titulo'] }}"
                             onerror="this.onerror=null; this.src='https://placehold.co/1280x720/0A0A0A/FFFFFF?text=Webinar';">
                        <span class="yt-pendiente">Video por publicar en YouTube</span>
                    </div>
                @endif

            </div>

            <div class="col-lg-5">

                <h2 class="webinar__titulo">{{ $webinar_principal['titulo'] }}</h2>

                {{-- La duración se muestra sólo si el material la registra:
                         "Duración:" seguido de nada se leería como un fallo. --}}
                <p class="yt-meta">
                    {{ $webinar_principal['fecha'] }}@if ($webinar_principal['duracion'] !== '')
                        &nbsp;·&nbsp; Duración: {{ $webinar_principal['duracion'] }}
                    @endif
                </p>

                {{-- La descripción llega en varios párrafos y así se
                         conserva: unirlos en uno alteraría el texto. --}}
                @foreach ((array) $webinar_principal['descripcion'] as $parrafo)
                    <p class="webinar__desc">{{ $parrafo }}</p>
                @endforeach

                <p class="panel-sub">Puntos clave del video</p>
                <ul class="puntos-clave">
                    @foreach ($webinar_principal['puntos_clave'] as $punto)
                        <li>{{ $punto }}</li>
                    @endforeach
                </ul>

                <p class="panel-sub">Participantes</p>
                @foreach ($webinar_principal['participantes'] as $persona)
                    <div class="participante">
                        <strong>{{ $persona['nombre'] }}</strong>
                        <span>{{ $persona['cargo'] }}</span>
                    </div>
                @endforeach

            </div>

        </section>

        <!-- ══════════════════ MÁS VIDEOS DE LA SERIE ══════════════════ -->
        {{-- Un video al mes: con uno solo, esta rejilla quedaba como tres
                 marcos negros vacíos bajo la transmisión destacada. --}}
        @if ($mas_webinars)

        <p class="bloque-titulo">Más videos de la serie</p>

        <div class="row row-cols-1 row-cols-md-3 g-4">

            @foreach ($mas_webinars as $w)
                @php($id_w = $w['id_youtube'])

                <div class="col">
                    <article class="ep-card h-100">

                        @if ($id_w !== '')
                            <div class="yt-marco ep-card__figure" data-yt="{{ $id_w }}"
                                 role="button" tabindex="0"
                                 aria-label="Reproducir: {{ $w['titulo'] }}">
                                <img src="https://i.ytimg.com/vi/{{ $id_w }}/hqdefault.jpg"
                                     alt="{{ $w['titulo'] }}" loading="lazy">
                                <span class="yt-play yt-play--chico" aria-hidden="true"><i class="bi bi-play-fill"></i></span>
                            </div>
                        @else
                            <div class="yt-marco ep-card__figure yt-marco--vacio">
                                <img src="{{ asset($w['imagen']) }}"
                                     alt="{{ $w['titulo'] }}" loading="lazy"
                                     onerror="this.onerror=null; this.src='https://placehold.co/640x360/0A0A0A/FFFFFF?text=Webinar';">
                                <span class="yt-pendiente yt-pendiente--chico">Por publicar</span>
                            </div>
                        @endif

                        <div class="p-4">
                            <h3 class="ep-card__titulo">{{ $w['titulo'] }}</h3>
                        </div>

                    </article>
                </div>

            @endforeach

        </div>

        @endif

    </div>

</main>
@endsection
