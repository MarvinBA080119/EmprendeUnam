@extends('layouts.sitio')

@php
    $tituloPagina = '10 de 10';
    $paginaId = '10de10';
    $recursos = ['resources/css/sitio/premium_sections.css', 'resources/js/sitio/premium_ui.js'];
@endphp

@section('contenido')
<main id="contenido">

    <!-- ══════════════════ ENCABEZADO ══════════════════ -->
    <header class="head-solo">
        <div class="container">
            <h1>10 de 10</h1>
        </div>
    </header>

    <div class="container pb-5">

        @if (count($areas) === 0)

            <p class="sin-resultados visible">
                No fue posible cargar la selección. Verifique el archivo config/sitio/diezdediez.php.
            </p>

        @else

        <!-- ══════════════════ PESTAÑAS DE ÁREA ══════════════════ -->
        <nav class="areas-tabs" role="tablist" aria-label="Áreas de la selección">
            @foreach ($areas as $i => $area)
                <button type="button"
                        @class(['area-tab', 'activo' => $i === 0])
                        id="tab-{{ $area['id'] }}"
                        data-panel="panel-{{ $area['id'] }}"
                        data-orden="{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}"
                        role="tab"
                        aria-controls="panel-{{ $area['id'] }}"
                        aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                        tabindex="{{ $i === 0 ? '0' : '-1' }}">
                    {{ $area['label'] }}
                </button>
            @endforeach
        </nav>

        <!-- ══════════════════ PANELES ══════════════════ -->
        @foreach ($areas as $i => $area)

            <div @class(['area-panel', 'activo' => $i === 0])
                 id="panel-{{ $area['id'] }}"
                 role="tabpanel"
                 aria-labelledby="tab-{{ $area['id'] }}"
                 @if ($i !== 0) hidden @endif>

                @if (count($area['items']) === 0)

                    <p class="tem-vacio">Aún no hay artículos registrados en esta área.</p>

                @else

                    @php($principal = $area['items'][0])

                    <!-- ── El número uno de la selección ── -->
                    <article class="top-uno">
                        <div class="row g-5 align-items-center">

                            <div class="col-lg-6">
                                <figure class="top-uno__figure">
                                    <img src="{{ asset($principal['imagen']) }}"
                                         alt="{{ $principal['titulo'] }}"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='https://placehold.co/1000x750/F8F9FA/0A0A0A?text=FCA+UNAM';">
                                    <span class="top-uno__sello" aria-hidden="true">01</span>
                                </figure>
                            </div>

                            <div class="col-lg-6">
                                <span class="top-uno__eyebrow">Lo más leído · {{ $area['label'] }}</span>

                                <h2 class="top-uno__titulo">
                                    <a href="{{ asset($principal['pdf_url']) }}" target="_blank" rel="noopener">{{ $principal['titulo'] }}</a>
                                </h2>

                                <p class="top-uno__desc">{{ $principal['descripcion'] }}</p>

                                @if (!empty($principal['autor']))
                                    <p class="art-mini__autor">{{ $principal['autor'] }}</p>
                                @endif

                                <a href="{{ asset($principal['pdf_url']) }}" class="link-pdf" target="_blank" rel="noopener">
                                    Leer en PDF <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>

                        </div>
                    </article>

                    <!-- ── Del 02 en adelante ── -->
                    <p class="seleccion-label">Completan la selección</p>

                    <div class="row row-cols-1 row-cols-md-2 g-5">

                        @foreach ($area['items'] as $n => $art)
                            @continue($n === 0)

                            <div class="col">
                                <article class="art-mini">

                                    <figure class="art-mini__figure">
                                        <img src="{{ asset($art['imagen']) }}"
                                             alt="{{ $art['titulo'] }}"
                                             loading="lazy"@if (!empty($art['enfoque']))
                                             style="object-position: {{ $art['enfoque'] }};"@endif
                                             onerror="this.onerror=null; this.src='https://placehold.co/600x400/F8F9FA/0A0A0A?text=FCA';">
                                    </figure>

                                    <div class="art-mini__body">

                                        <span class="art-mini__num" aria-hidden="true">{{ str_pad($n + 1, 2, '0', STR_PAD_LEFT) }}</span>

                                        <h3 class="art-mini__titulo">
                                            <a href="{{ asset($art['pdf_url']) }}" target="_blank" rel="noopener">{{ $art['titulo'] }}</a>
                                        </h3>

                                        <p class="art-mini__desc">{{ $art['descripcion'] }}</p>

                                        @if (!empty($art['autor']))
                                            <p class="art-mini__autor">{{ $art['autor'] }}</p>
                                        @endif

                                        <a href="{{ asset($art['pdf_url']) }}" class="link-pdf" target="_blank" rel="noopener">
                                            Leer en PDF <span aria-hidden="true">&rarr;</span>
                                        </a>

                                    </div>

                                </article>
                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        @endforeach

        @endif

    </div>

</main>
@endsection
