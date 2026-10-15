@extends('layouts.autor')

@section('titulo', 'Mis artículos')

@section('contenido')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="emp-titulo-seccion mb-0 flex-grow-1">Mis artículos</h1>
        <a href="{{ route('autor.articulos.create') }}" class="btn btn-primary">Enviar nuevo artículo</a>
    </div>

    @if ($articulos->isEmpty())
        <div class="card emp-tarjeta">
            <div class="card-body">
                <p class="mb-0">Aún no has enviado artículos. Cuando envíes uno, aquí verás su estatus.</p>
            </div>
        </div>
    @else
        <div class="table-responsive">
            <table class="table emp-tabla align-middle">
                <caption class="visually-hidden">Historial de tus artículos</caption>
                <thead>
                    <tr>
                        <th scope="col">Título</th>
                        <th scope="col">Tema</th>
                        <th scope="col">Enviado</th>
                        <th scope="col">Estatus</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articulos as $articulo)
                        <tr>
                            <td>
                                {{ $articulo->arti_titulo }}
                                @if ($articulo->arti_status === \App\Enums\EstatusArticulo::RECHAZADO)
                                    <div class="small mt-1"><strong>Motivo:</strong> {{ $articulo->arti_comentario_rechazo }}</div>
                                @endif
                            </td>
                            <td>{{ $articulo->tema->tema_nombre }}</td>
                            <td class="text-nowrap">{{ $articulo->arti_fecha_registro->format('d/m/Y') }}</td>
                            <td><x-insignia :estatus="$articulo->arti_status" /></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('autor.articulos.pdf', $articulo) }}" class="btn btn-outline-primary btn-sm">
                                        Descargar PDF<span class="visually-hidden"> de «{{ $articulo->arti_titulo }}»</span>
                                    </a>
                                    @if ($articulo->arti_status === \App\Enums\EstatusArticulo::APROBADO && $articulo->constancia)
                                        <a href="{{ route('autor.articulos.aprobado', $articulo) }}" class="btn btn-primary btn-sm">
                                            Descargar constancia<span class="visually-hidden"> de «{{ $articulo->arti_titulo }}»</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $articulos->links() }}
    @endif
@endsection
