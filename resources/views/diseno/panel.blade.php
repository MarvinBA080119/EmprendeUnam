@extends('layouts.diseno')

@section('titulo', 'Mesa de diseño')

@php
    $pendientes = $conteos['pendientes'];
    $total = $conteos['total'];
    $recien = (int) session('recien');
    $maxDiseno = round(config('emprende.pdf_diseno_max_kb') / 1024);
@endphp

@section('contenido')
    <header class="emp-saludo">
        <div class="emp-saludo__texto">
            <p class="emp-saludo__fecha">{{ \App\Support\Fecha::hoyLargo() }}</p>
            <h1 class="emp-saludo__titulo">Mesa de diseño</h1>
            <p class="emp-saludo__resumen">
                @if ($total === 0)
                    Aún no hay artículos aprobados. Cuando la dirección editorial apruebe uno, aparecerá aquí y te llegará un correo.
                @elseif ($pendientes === 0)
                    Todo está diseñado. Cuando se apruebe otro artículo, aparecerá aquí y te llegará un correo.
                @else
                    {{ $pendientes === 1 ? 'Tienes 1 artículo por diseñar' : "Tienes {$pendientes} artículos por diseñar" }}.
                    Descarga el texto original, diséñalo y sube aquí el PDF final: se liga solo a su artículo.
                @endif
            </p>
        </div>
        @if ($pendientes > 0)
            <div class="emp-saludo__acciones">
                <a href="{{ route('diseno.originales') }}" class="btn btn-primary">
                    <x-icono nombre="descargar" :tamano="14" />
                    {{ $pendientes === 1 ? 'Descargar el texto' : "Descargar los {$pendientes} textos" }} (.zip)
                </a>
            </div>
        @endif
    </header>

    @if ($total > 0)
        <x-avance-diseno :conteos="$conteos" />

        <nav class="emp-segmentos" aria-label="Filtrar artículos">
            @foreach (\App\Services\DisenoService::FILTROS as $clave => $rotulo)
                @php $cuenta = ['pendientes' => $pendientes, 'disenados' => $conteos['disenados'], 'todos' => $total][$clave]; @endphp
                <a href="{{ route('diseno.panel', ['ver' => $clave]) }}" @class(['emp-segmentos__opcion', 'is-activo' => $filtro === $clave])
                   @if ($filtro === $clave) aria-current="page" @endif>
                    {{ $rotulo }} <span class="emp-segmentos__cuenta emp-num">{{ $cuenta }}</span>
                </a>
            @endforeach
        </nav>
    @endif

    @if ($articulos->isEmpty())
        <div class="emp-vacio-grande">
            <span class="emp-vacio-grande__icono" aria-hidden="true"><x-icono nombre="{{ $total === 0 ? 'pluma' : 'check' }}" :tamano="22" /></span>
            <h2 class="emp-vacio-grande__titulo">
                {{ $total === 0 ? 'La mesa está vacía' : ($filtro === 'disenados' ? 'Todavía no hay diseños' : 'Nada pendiente') }}
            </h2>
            <p class="emp-vacio-grande__texto">
                @if ($total === 0)
                    Aquí verás cada artículo aprobado con su texto original para descargar y el lugar para subir su PDF diseñado.
                @elseif ($filtro === 'disenados')
                    Cuando subas el PDF de un artículo, aparecerá en esta lista.
                @else
                    Todos los artículos aprobados ya tienen su PDF diseñado.
                @endif
            </p>
            @if ($total > 0)
                <a href="{{ route('diseno.panel', ['ver' => $filtro === 'disenados' ? 'pendientes' : 'disenados']) }}" class="btn btn-secondary">
                    {{ $filtro === 'disenados' ? 'Ver los que faltan' : 'Ver los diseñados' }}
                </a>
            @endif
        </div>
    @else
        <p class="emp-mesa__ayuda" data-ayuda-arrastre>
            <x-icono nombre="subir" :tamano="14" />
            También puedes arrastrar el PDF diseñado sobre su artículo. Hasta {{ $maxDiseno }} MB por archivo.
        </p>

        <ol class="emp-mesa" data-mesa data-max-kb="{{ config('emprende.pdf_diseno_max_kb') }}">
            @foreach ($articulos as $articulo)
                @php $id = $articulo->arti_id_autor; @endphp
                <x-articulo-mesa :articulo="$articulo" :recien="$recien === $id" data-titulo="{{ $articulo->arti_titulo }}">
                    <a href="{{ route('diseno.articulos.original', $articulo) }}" class="btn btn-quieto btn-sm">
                        <x-icono nombre="descargar" :tamano="14" /> Original<span class="visually-hidden"> de «{{ $articulo->arti_titulo }}»</span>
                    </a>
                    @if ($articulo->tieneDiseno())
                        <a href="{{ route('diseno.articulos.diseno', $articulo) }}" class="btn btn-quieto btn-sm" target="_blank" rel="noopener">
                            <x-icono nombre="ojo" :tamano="14" /> Ver PDF<span class="visually-hidden"> diseñado de «{{ $articulo->arti_titulo }}»</span>
                        </a>
                    @endif
                    <form method="POST" action="{{ route('diseno.articulos.subir', $articulo) }}" enctype="multipart/form-data"
                          class="emp-mesa__formulario" data-subir novalidate>
                        @csrf
                        <input type="hidden" name="ver" value="{{ $filtro }}">
                        <input id="pdf-{{ $id }}" name="pdf" type="file" accept="application/pdf,.pdf" class="emp-mesa__archivo" required
                               aria-describedby="pdf-{{ $id }}-estado">
                        <label for="pdf-{{ $id }}" @class(['btn btn-sm', 'btn-subir' => ! $articulo->tieneDiseno(), 'btn-secondary' => $articulo->tieneDiseno()])>
                            <x-icono nombre="subir" :tamano="14" />
                            {{ $articulo->tieneDiseno() ? 'Reemplazar' : 'Subir PDF diseñado' }}<span class="visually-hidden"> de «{{ $articulo->arti_titulo }}»</span>
                        </label>
                        <button type="submit" class="btn btn-secondary btn-sm" data-sin-js>Enviar</button>
                    </form>

                    <x-slot:pie>
                        <div class="emp-subida" id="pdf-{{ $id }}-estado" data-subida aria-live="polite" hidden>
                            <span class="emp-subida__nombre" data-subida-nombre></span>
                            <span class="emp-subida__porcentaje emp-num" data-subida-porcentaje></span>
                            <span class="emp-subida__barra" aria-hidden="true"><span data-subida-barra></span></span>
                        </div>
                    </x-slot:pie>
                </x-articulo-mesa>
            @endforeach
        </ol>
    @endif
@endsection
