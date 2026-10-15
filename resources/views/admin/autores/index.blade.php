@extends('layouts.admin')

@section('titulo', 'Autores')

@section('contenido')
    <h1 class="emp-titulo-seccion">Autores</h1>

    <form method="GET" action="{{ route('admin.autores.index') }}" class="row g-2 align-items-end mb-4" role="search">
        <div class="col-12 col-md-5">
            <label for="filtro-texto" class="form-label">Buscar</label>
            <input id="filtro-texto" type="search" name="texto" value="{{ $filtros['texto'] ?? '' }}" maxlength="100"
                   class="form-control" placeholder="Nombre, correo o usuario">
        </div>
        <div class="col-8 col-md-4">
            <label for="filtro-estatus" class="form-label">Estatus de la cuenta</label>
            <select id="filtro-estatus" name="estatus" class="form-select">
                <option value="">Todas</option>
                <option value="pendiente" @selected(($filtros['estatus'] ?? '') === 'pendiente')>Pendiente</option>
                <option value="activa" @selected(($filtros['estatus'] ?? '') === 'activa')>Activa</option>
            </select>
        </div>
        <div class="col-4 col-md-3">
            <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
        </div>
    </form>

    @if ($autores->isEmpty())
        <p>No hay autores con esos criterios.</p>
    @else
        <div class="table-responsive">
            <table class="table emp-tabla align-middle">
                <caption class="visually-hidden">Autores registrados</caption>
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Institución</th>
                        <th scope="col">Registro</th>
                        <th scope="col">Estatus</th>
                        <th scope="col"><span class="visually-hidden">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($autores as $autor)
                        <tr>
                            <td>
                                {{ $autor->persona->nombre_completo }}
                                <div class="small text-body-secondary">{{ $autor->usua_nombre_usuario }}</div>
                            </td>
                            <td class="text-break">{{ $autor->persona->correo?->comu_valor_comunicacion }}</td>
                            <td>
                                {{ $autor->persona->autor?->auto_institucion }}
                                @if ($autor->persona->autor?->auto_grado_academico)
                                    <div class="small text-body-secondary">{{ $autor->persona->autor->auto_grado_academico }}</div>
                                @endif
                            </td>
                            <td>{{ $autor->usua_fecha_registro->format('d/m/Y') }}</td>
                            <td>
                                @if ($autor->usua_activo)
                                    <span class="emp-insignia emp-insignia-aprobado">Activa</span>
                                @else
                                    <span class="emp-insignia emp-insignia-revision">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @unless ($autor->usua_activo)
                                    <form method="POST" action="{{ route('admin.autores.activar', $autor) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            Activar<span class="visually-hidden"> la cuenta de {{ $autor->persona->nombre_completo }}</span>
                                        </button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $autores->links() }}
    @endif
@endsection
