@extends('layouts.publico')

@section('titulo', 'Verificación de constancia')

@section('contenido')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="emp-titulo-seccion">Verificación de constancia</h1>

            @if ($constancia)
                <div class="card emp-tarjeta">
                    <div class="card-body">
                        <p class="mb-3"><span class="emp-insignia emp-insignia-aprobado">Constancia válida</span></p>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Folio</dt>
                            <dd class="col-sm-8">{{ $constancia->cons_folio }}</dd>
                            <dt class="col-sm-4">Artículo</dt>
                            <dd class="col-sm-8">{{ $constancia->articulo->arti_titulo }}</dd>
                            <dt class="col-sm-4">Fecha de emisión</dt>
                            <dd class="col-sm-8">{{ $constancia->cons_fecha_emision->locale('es')->translatedFormat('j \d\e F \d\e Y') }}</dd>
                        </dl>
                    </div>
                </div>
                <p class="small mt-3">La Revista EmprendeUNAM emitió esta constancia por la aprobación del artículo indicado.</p>
            @else
                <div class="card emp-tarjeta">
                    <div class="card-body">
                        <p class="mb-2"><span class="emp-insignia emp-insignia-rechazado">Folio no encontrado</span></p>
                        <p class="mb-0">No existe una constancia con el folio <strong>{{ $folio }}</strong>. Revisa que esté escrito correctamente.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
