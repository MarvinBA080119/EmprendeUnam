@extends('layouts.admin')

@section('titulo', 'Cursos')

@section('contenido')
    <x-encabezado titulo="Cursos" descripcion="Cursos que se muestran en el sitio público hasta su fecha de inicio.">
        <x-slot:acciones>
            <a href="{{ route('admin.cursos.create') }}" class="btn btn-primary"><x-icono nombre="mas" :tamano="14" /> Publicar curso</a>
        </x-slot:acciones>
    </x-encabezado>

    <form method="GET" action="{{ route('admin.cursos.index') }}" class="row g-2 align-items-end emp-filtros">
        <div class="col-8 col-md-4 col-xl-3">
            <label for="filtro-vigencia" class="form-label">Mostrar</label>
            <select id="filtro-vigencia" name="vigencia" class="form-select" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="vigentes" @selected($vigencia === 'vigentes')>Visibles al público</option>
                <option value="pasados" @selected($vigencia === 'pasados')>Con fecha pasada</option>
            </select>
        </div>
        <div class="col-4 col-md-auto">
            <noscript><button type="submit" class="btn btn-secondary w-100">Filtrar</button></noscript>
        </div>
    </form>

    <div class="emp-marco-tabla">
        @if ($cursos->isEmpty())
            <p class="emp-vacio">No hay cursos con esos criterios.</p>
        @else
            <div class="table-responsive">
                <table class="table emp-tabla align-middle">
                    <caption class="visually-hidden">Cursos publicados</caption>
                    <thead>
                        <tr>
                            <th scope="col">Curso</th>
                            <th scope="col">Institución</th>
                            <th scope="col">Inicio</th>
                            <th scope="col">Palabras clave</th>
                            <th scope="col">Estado</th>
                            <th scope="col"><span class="visually-hidden">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cursos as $curso)
                            @php($pasado = $curso->curs_fecha?->lt(today()))
                            <tr>
                                <td>
                                    <a href="{{ $curso->curs_url }}" target="_blank" rel="noopener noreferrer">{{ $curso->curs_nombre_curso }}</a>
                                </td>
                                <td>{{ $curso->curs_institucion }}</td>
                                <td class="text-nowrap emp-num">{{ $curso->curs_fecha?->format('d/m/Y') }}</td>
                                <td class="text-body-secondary">{{ $curso->palabras->pluck('pala_descripcion')->join(' · ') }}</td>
                                <td>
                                    @if ($pasado)
                                        <span class="emp-insignia emp-insignia-rechazado" title="La fecha de inicio ya pasó: no se muestra al público">Vencido</span>
                                    @else
                                        <span class="emp-insignia emp-insignia-aprobado" title="Se muestra en el sitio público">Visible</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.cursos.edit', $curso) }}" class="btn btn-secondary btn-sm">
                                            Editar<span class="visually-hidden"> «{{ $curso->curs_nombre_curso }}»</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.cursos.destroy', $curso) }}"
                                              data-confirmar="¿Eliminar el curso «{{ $curso->curs_nombre_curso }}»? Esta acción no se puede deshacer."
                                              data-confirmar-boton="Eliminar curso" data-destructivo>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-quieto btn-sm">
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
            @if ($cursos->hasPages())
                <div class="emp-marco-tabla__pie">{{ $cursos->links() }}</div>
            @endif
        @endif
    </div>
@endsection
