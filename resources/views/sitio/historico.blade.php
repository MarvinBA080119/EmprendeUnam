@extends('layouts.sitio')

@php
    $tituloPagina = 'Histórico';
    $paginaId = 'historico';
    $recursos = ['resources/css/sitio/premium_sections.css', 'resources/js/sitio/premium_ui.js'];
@endphp

@section('contenido')
<main id="contenido">

    <!-- ══════════════════ ENCABEZADO ══════════════════ -->
    <header class="head-solo">
        <div class="container">
            <h1>Histórico</h1>
        </div>
    </header>

    <div class="container pb-5">

        <!-- ══════════════════ FILTROS DESPLEGABLES ══════════════════ -->
        <section class="filtros">

            <!-- Año -->
            <div class="filtro" data-grupo="anio" data-etiqueta="Año">
                <button type="button" class="filtro__boton" aria-expanded="false" aria-haspopup="true">
                    <span class="filtro__texto">Año</span>
                </button>
                <div class="filtro__panel" role="menu">
                    <button type="button" class="filtro__opcion activo" data-valor="todos" role="menuitem">Todos los años</button>
                    @foreach ($anios as $anio)
                        <button type="button" class="filtro__opcion" data-valor="{{ $anio }}" role="menuitem">{{ $anio }}</button>
                    @endforeach
                </div>
            </div>

            <!-- Tema -->
            <div class="filtro" data-grupo="tema" data-etiqueta="Tema">
                <button type="button" class="filtro__boton" aria-expanded="false" aria-haspopup="true">
                    <span class="filtro__texto">Tema</span>
                </button>
                <div class="filtro__panel" role="menu">
                    <button type="button" class="filtro__opcion activo" data-valor="todos" role="menuitem">Todos los temas</button>
                    @foreach ($temas as $slug => $nombre)
                        <button type="button" class="filtro__opcion" data-valor="{{ $slug }}" role="menuitem">{{ $nombre }}</button>
                    @endforeach
                </div>
            </div>

            <p class="filtro__contador" id="contadorEdiciones" role="status" aria-live="polite"></p>

        </section>

        <!-- ══════════════════ GRID DE PORTADAS ══════════════════ -->
        <div class="row row-cols-2 row-cols-md-4 g-4" id="gridHistorico">

            {{-- Datos de cada edición preparados en SitioService::historico():
                 portada, PDF, eBook y áreas (separadas por barra vertical, porque
                 el filtro comprueba pertenencia: un número toca varias áreas). --}}
            @foreach ($ediciones as $ed)

                <div class="col"
                     data-ed-anio="{{ $ed['anio'] }}"
                     data-ed-temas="{{ $ed['attr_temas'] }}">

                    <div class="portada">

                        {{-- Cascada de respaldo: local → servidor de la revista → marcador --}}
                        <img src="{{ asset($ed['portada']) }}"
                             alt="Portada de {{ $ed['titulo'] }}"
                             loading="lazy"
                             data-respaldo="{{ $ed['remoto'] }}"
                             onerror="if(this.dataset.respaldo){this.src=this.dataset.respaldo;this.removeAttribute('data-respaldo');}else{this.onerror=null;this.src='https://placehold.co/600x800/F8F9FA/0A0A0A?text=No.+{{ $ed['numero'] }}';}">

                        <div class="portada__capa">
                            <a href="{{ asset($ed['pdf']) }}"
                               class="btn-capa" target="_blank" rel="noopener"
                               aria-label="Abrir el número {{ $ed['numero'] }} en PDF">Abrir PDF</a>

                            <a href="{{ $ed['ebook'] }}"
                               class="btn-capa" target="_blank" rel="noopener"
                               aria-label="Abrir el número {{ $ed['numero'] }} en eBook">Abrir eBook</a>
                        </div>

                    </div>

                    <p class="edicion__numero">No. {{ $ed['numero'] }}</p>
                    <p class="edicion__meta">{{ $ed['periodo'] }}</p>

                    {{-- El título se muestra sólo cuando el acervo lo tiene registrado --}}
                    @if ($ed['tiene_titulo'])
                        <p class="edicion__titulo">{{ $ed['titulo'] }}</p>
                    @endif

                    {{-- Las áreas del número, en lugar de la etiqueta única
                             anterior. Se listan las tres primeras: la ficha de
                             una portada no admite once líneas de rótulo. --}}
                    @if ($ed['nombres_temas'])
                        <p class="edicion__tema">
                            {{ implode(' · ', array_slice($ed['nombres_temas'], 0, 3)) }}@if (count($ed['nombres_temas']) > 3)<span class="edicion__tema-mas">+{{ count($ed['nombres_temas']) - 3 }}</span>@endif
                        </p>
                    @endif

                    {{-- Accesos permanentes: no dependen del hover (táctil y teclado) --}}
                    <p class="edicion__accesos">
                        <a href="{{ asset($ed['pdf']) }}" class="edicion__acceso" target="_blank" rel="noopener">PDF</a>
                        <a href="{{ $ed['ebook'] }}" class="edicion__acceso" target="_blank" rel="noopener">eBook</a>
                    </p>

                </div>

            @endforeach

        </div>

        <p class="sin-resultados" id="sinResultados">
            No hay ediciones que coincidan con esta combinación de filtros.
        </p>

        <!-- Aviso de hemeroteca (contenido original del sitio) -->
        <p class="aviso-acervo">
            ¿El número que busca no está en nuestro sitio?<br>
            Consulte nuestro catálogo en hemeroteca física o escríbanos a
            <a href="mailto:revistaemprendedores@fca.unam.mx">revistaemprendedores@fca.unam.mx</a>
        </p>

    </div>

</main>
@endsection
