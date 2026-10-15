@extends('layouts.admin')

@section('titulo', 'Artículos')

@section('contenido')
    <h1 class="emp-titulo-seccion">Artículos</h1>

    <form method="GET" action="{{ route('admin.articulos.index') }}" class="row g-2 align-items-end mb-4">
        <div class="col-8 col-md-4">
            <label for="filtro-estatus" class="form-label">Estatus</label>
            <select id="filtro-estatus" name="estatus" class="form-select">
                <option value="">Todos</option>
                @foreach (\App\Enums\EstatusArticulo::cases() as $opcion)
                    <option value="{{ $opcion->value }}" @selected($estatus === $opcion->value)>{{ $opcion->etiqueta() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-4 col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
        </div>
    </form>

    @if ($articulos->isEmpty())
        <p>No hay artículos con esos criterios.</p>
    @else
        <div class="table-responsive">
            <table class="table emp-tabla align-middle">
                <caption class="visually-hidden">Artículos de todos los autores</caption>
                <thead>
                    <tr>
                        <th scope="col">Título</th>
                        <th scope="col">Autor principal</th>
                        <th scope="col">Tema</th>
                        <th scope="col">Enviado</th>
                        <th scope="col">Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articulos as $articulo)
                        <tr>
                            <td><a href="{{ route('admin.articulos.show', $articulo) }}">{{ $articulo->arti_titulo }}</a></td>
                            <td>{{ $articulo->autores->first()?->persona?->nombre_completo }}</td>
                            <td>{{ $articulo->tema->tema_nombre }}</td>
                            <td class="text-nowrap">{{ $articulo->arti_fecha_registro->format('d/m/Y') }}</td>
                            <td><x-insignia :estatus="$articulo->arti_status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $articulos->links() }}
    @endif
@endsection
