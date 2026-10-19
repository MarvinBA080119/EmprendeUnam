@extends('layouts.sitio')

@php
    $tituloPagina = 'Las Pyme Hablan';
    $paginaId = 'pyme-hablan';
    $recursos = ['resources/css/sitio/premium_sections.css'];
@endphp

@section('contenido')
<main id="contenido">

    <!-- ══════════════════ ENCABEZADO ══════════════════ -->
    <header class="head-solo">
        <div class="container">
            <h1>Las Pyme Hablan</h1>
        </div>
    </header>

    <!-- ══════════════════ GRID DE CASOS ══════════════════ -->
    <div class="container pb-5">
        <div class="row row-cols-1 row-cols-md-2 g-5">

            @foreach ($casos_pyme as $caso)

                <div class="col">
                    <article class="pyme-card h-100">

                        <figure class="pyme-card__figure">
                            <img src="{{ asset($caso['imagen']) }}"
                                 alt="{{ $caso['empresa'] . '. ' . $caso['titulo'] }}"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='https://placehold.co/800x600/0A0A0A/FFFFFF?text={{ urlencode($caso['empresa']) }}'">
                        </figure>

                        <div class="pyme-card__body">

                            <span class="pyme-card__cat">{{ $caso['categoria'] }}</span>

                            <p class="pyme-card__empresa">{{ $caso['empresa'] }}</p>

                            <h2 class="pyme-card__titulo">
                                <a href="{{ asset($caso['pdf']) }}" target="_blank" rel="noopener">
                                    {{ $caso['titulo'] }}
                                </a>
                            </h2>

                            <p class="pyme-card__desc">{{ $caso['descripcion'] }}</p>

                            <!-- Obstáculos y logros: los dos ejes del caso de estudio -->
                            <div class="pyme-card__eje">
                                <span class="pyme-card__eje-label">Obstáculos</span>
                                <p>{{ $caso['obstaculos'] }}</p>
                            </div>

                            <div class="pyme-card__eje pyme-card__eje--logros">
                                <span class="pyme-card__eje-label">Logros</span>
                                <p>{{ $caso['logros'] }}</p>
                            </div>

                            <a href="{{ asset($caso['pdf']) }}" class="pyme-card__link" target="_blank" rel="noopener">
                                Leer caso completo
                            </a>

                        </div>

                    </article>
                </div>

            @endforeach

        </div>

        <!-- ══════════════════ LLAMADA A PARTICIPAR ══════════════════ -->
        <section class="pyme-cta">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2 class="pyme-cta__titulo">¿Tienes una historia de emprendimiento que contar?</h2>
                    <p class="pyme-cta__texto">
                        Buscamos emprendimientos y Pyme que deseen compartir su experiencia con nuestros lectores.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('envio-articulos') }}" class="btn-lux btn-lux--rojo"><span>Envia tu propuesta</span></a>
                </div>
            </div>
        </section>

    </div>

</main>
@endsection
