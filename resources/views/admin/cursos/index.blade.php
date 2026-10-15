@extends('layouts.admin')

@section('titulo', 'Cursos')

@section('contenido')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="emp-titulo-seccion mb-0 flex-grow-1">Cursos</h1>
        <a href="{{ route('admin.cursos.create') }}" class="btn btn-primary">Publicar curso</a>
    </div>

    <form method="GET" action="{{ route('admin.cursos.index') }}" class="row g-2 align-items-end mb-3">
        <div class="col-8 col-md-4">
            <label for="filtro-vigencia" class="form-label">Mostrar</label>
            <select id="filtro-vigencia" name="vigencia" class="form-select" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="vigentes" @selected($vigencia === 'vigentes')>Vigentes (visibles al público)</option>
                <option value="pasados" @selected($vigencia === 'pasados')>Con fecha pasada</option>
            </select>
        </div>
        <div class="col-4 col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
        </div>
    </form>

    @if ($cursos->isEmpty())
        <p>No hay cursos con esos criterios.</p>
    @else
        <div class="table-responsive">
            <table class="table emp-tabla align-middle">
                <caption class="visually-hidden">Cursos publicados</caption>
                <thead>
                    <tr>
                        <th scope="col">Curso</th>
                        <th scope="col">Institución</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Palabras clave</th>
                        <th scope="col"><span class="visually-hidden">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cursos as $curso)
                        <tr>
                            <td>
                                <a href="{{ $curso->curs_url }}" target="_blank" rel="noopener noreferrer">{{ $curso->curs_nombre_curso }}</a>
                                @if ($curso->curs_fecha?->lt(today()))
                                    <div class="small">No visible al público (fecha pasada)</div>
                                @endif
                            </td>
                            <td>{{ $curso->curs_institucion }}</td>
                            <td class="text-nowrap">{{ $curso->curs_fecha?->format('d/m/Y') }}</td>
                            <td>{{ $curso->palabras->pluck('pala_descripcion')->join(' · ') }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.cursos.edit', $curso) }}" class="btn btn-outline-primary btn-sm">
                                        Editar<span class="visually-hidden"> «{{ $curso->curs_nombre_curso }}»</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.cursos.destroy', $curso) }}"
                                          onsubmit="return confirm('¿Eliminar este curso? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-dark btn-sm">
                                            Eliminar<span class="visually-hidden"> «{{ $curso->curs_nombre_curso }}»</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $cursos->links() }}
    @endif
@endsection
