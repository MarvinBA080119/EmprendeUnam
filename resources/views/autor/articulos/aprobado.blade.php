@extends('layouts.autor')

@section('titulo', 'Artículo aprobado')

@section('contenido')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card emp-tarjeta text-center">
                <div class="card-body p-4 p-md-5">
                    <h1 class="emp-titulo-seccion border-0">¡Gracias por tu contribución!</h1>
                    <p class="lead">Tu artículo <strong>«{{ $articulo->arti_titulo }}»</strong> fue aprobado para su publicación en la Revista EmprendeUNAM.</p>
                    @if ($articulo->constancia)
                        <p>Folio de tu constancia: <strong>{{ $articulo->constancia->cons_folio }}</strong>
                            (emitida el {{ $articulo->constancia->cons_fecha_emision->format('d/m/Y') }}).</p>
                        <a href="{{ route('autor.articulos.constancia', $articulo) }}" class="btn btn-primary btn-lg">Descargar constancia</a>
                        <p class="small mt-3 mb-0">Puedes descargarla de nuevo cuando quieras desde tu historial.</p>
                    @endif
                </div>
            </div>
            <p class="mt-3"><a href="{{ route('autor.panel') }}">Volver a mis artículos</a></p>
        </div>
    </div>
@endsection
