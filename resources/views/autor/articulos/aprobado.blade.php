@extends('layouts.autor')

@section('titulo', 'Artículo aprobado')

@section('contenido')
    <div class="emp-logro">
        <span class="emp-logro__sello" aria-hidden="true"><x-icono nombre="check" :tamano="22" /></span>
        <h1 class="emp-logro__titulo">¡Gracias por tu contribución!</h1>
        <p class="emp-logro__texto">
            Tu artículo fue aprobado para su publicación en la Revista EmprendeUNAM.
        </p>

        @if ($articulo->constancia)
            <div class="emp-constancia">
                <div class="emp-constancia__cabeza">
                    <span>Constancia de aprobación</span>
                    <x-insignia :estatus="$articulo->arti_status" />
                </div>
                <p class="emp-constancia__titulo">«{{ $articulo->arti_titulo }}»</p>
                <dl class="emp-registro mb-0">
                    <div>
                        <dt>Folio</dt>
                        <dd><strong>{{ $articulo->constancia->cons_folio }}</strong></dd>
                    </div>
                    <div>
                        <dt>Emitida</dt>
                        <dd class="emp-num">{{ $articulo->constancia->cons_fecha_emision->format('d/m/Y') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="emp-logro__acciones">
                <a href="{{ route('autor.articulos.constancia', $articulo) }}" class="btn btn-primary btn-lg">
                    <x-icono nombre="descargar" :tamano="16" /> Descargar constancia
                </a>
                <a href="{{ route('constancias.verificar', $articulo->constancia->cons_folio) }}" class="emp-accion__enlace" target="_blank" rel="noopener">
                    Ver la verificación pública <x-icono nombre="externo" :tamano="12" />
                </a>
            </div>
            <p class="emp-logro__nota">Puedes descargarla de nuevo cuando quieras desde tu historial.</p>
        @endif

        <a class="emp-encabezado__volver mt-3" href="{{ route('autor.panel') }}"><x-icono nombre="atras" :tamano="14" /> Volver a mis artículos</a>
    </div>
@endsection
