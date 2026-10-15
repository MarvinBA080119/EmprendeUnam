@extends('layouts.sitio')

{{-- Buscador (6.9): isla Vue que consulta /buscar/resultados (JSON). --}}
@php
    $tituloPagina = 'Buscador';
    $paginaId = '';
    $recursos = ['resources/css/sitio/premium_sections.css', 'resources/css/sitio/formularios_media.css'];
    $props = [
        'urlResultados' => route('buscador.resultados'),
        'filtros' => $filtros,
        'temas' => $temas->map(fn ($t) => ['id' => $t->tema_id_tema, 'nombre' => $t->tema_nombre])->values(),
        'anios' => $anios,
    ];
@endphp

@section('contenido')
<main id="contenido">

    <header class="head-solo">
        <div class="container">
            <h1>Buscador</h1>
        </div>
    </header>

    <div class="container pb-5">
        <p class="consultorio__nota">
            Busque artículos aprobados por título, palabras clave, autores o tema, y cursos por nombre,
            institución o palabras clave. No distingue mayúsculas ni acentos.
        </p>

        <div id="buscador" data-props='@json($props)'>
            {{-- Sin JavaScript: formulario simple con los mismos parámetros. --}}
            <form method="GET" action="{{ route('buscador') }}" role="search" class="form-lux">
                <label for="q-sin-js" class="form-label">Buscar</label>
                <input id="q-sin-js" type="search" name="q" value="{{ $filtros['q'] ?? '' }}" class="form-control" maxlength="100">
                <noscript><p class="form-nota">Para ver los resultados necesita activar JavaScript.</p></noscript>
            </form>
        </div>
    </div>

</main>
@endsection
