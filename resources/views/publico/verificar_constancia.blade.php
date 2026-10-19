@extends('layouts.sitio')

{{-- Verificación pública de constancias (6.10): nunca entrega el PDF. --}}
@php
    $tituloPagina = 'Verificación de constancia';
    $paginaId = '';
    $recursos = ['resources/css/sitio/premium_sections.css', 'resources/css/sitio/formularios_media.css'];
@endphp

@section('contenido')
<main id="contenido">

    <header class="head-solo">
        <div class="container">
            <p class="eyebrow mb-0">Constancias</p>
            <h1>Verificación de constancia</h1>
        </div>
    </header>

    <div class="container pb-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="form-lux">
                    @if ($constancia)
                        <p class="consulta__estado">Constancia válida</p>
                        <dl class="verificacion">
                            <dt>Folio</dt>
                            <dd>{{ $constancia->cons_folio }}</dd>
                            <dt>Artículo</dt>
                            <dd>{{ $constancia->articulo->arti_titulo }}</dd>
                            <dt>Fecha de emisión</dt>
                            <dd>{{ $constancia->cons_fecha_emision->locale('es')->translatedFormat('j \d\e F \d\e Y') }}</dd>
                        </dl>
                        <p class="form-nota">La Revista EmprendeUNAM emitió esta constancia por la aprobación del artículo indicado.</p>
                    @else
                        <p class="consulta__estado">Folio no encontrado</p>
                        <p class="consultorio__entrada">
                            No existe una constancia con el folio <strong>{{ $folio }}</strong>. Revise que esté escrito correctamente.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</main>
@endsection
