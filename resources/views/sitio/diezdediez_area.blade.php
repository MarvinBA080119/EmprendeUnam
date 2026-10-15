@extends('layouts.sitio')

@php
    $tituloPagina = '10 de 10: '.$coleccion['titulo_tematica'];
    $paginaId = '10de10';
    $recursos = ['resources/css/sitio/premium_sections.css'];
@endphp

@section('contenido')
<main id="contenido">

    <!-- ══════════════════ ENCABEZADO ══════════════════ -->
    <header class="head-solo">
        <div class="container">

            <nav aria-label="Ruta de navegación" class="miga">
                <a href="{{ route('diezdediez') }}">10 de 10</a>
                <span aria-hidden="true">/</span>
                <span class="miga__actual">{{ $coleccion['titulo_tematica'] }}</span>
            </nav>

            <h1>{{ $coleccion['titulo_tematica'] }}</h1>

            @if (!empty($coleccion['descripcion_tematica']))
                <p class="head-solo__entrada">{{ $coleccion['descripcion_tematica'] }}</p>
            @endif

        </div>
    </header>

    <div class="container pb-5">

        @if (!empty($coleccion['articulos']))

            <ol class="lista-diez">

                @foreach ($coleccion['articulos'] as $index => $articulo)

                    <li class="lista-diez__item">

                        <span class="lista-diez__num" aria-hidden="true">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>

                        <div class="lista-diez__cuerpo">

                            <h2 class="lista-diez__titulo">{{ $articulo['titulo'] }}</h2>

                            <p class="lista-diez__desc">{{ $articulo['descripcion'] }}</p>

                            @if (!empty($articulo['autor']))
                                <p class="art-mini__autor">{{ $articulo['autor'] }}</p>
                            @endif

                            <a href="{{ asset($articulo['pdf_url']) }}"
                               class="link-pdf" target="_blank" rel="noopener">
                                Leer en PDF <span aria-hidden="true">&rarr;</span>
                            </a>

                        </div>

                    </li>

                @endforeach

            </ol>

        @else

            <p class="tem-vacio">
                Próximamente se publicarán los 10 artículos elegidos para esta categoría.
            </p>

        @endif

        <p class="volver">
            <a href="{{ route('diezdediez') }}" class="post__more">Volver a 10 de 10</a>
        </p>

    </div>

</main>
@endsection
