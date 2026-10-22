@extends('layouts.admin')

@php
    $total = $articulos->count();
    $mostrados = $articulos->whereNotNull('arre_fecha_index')->count();
    $requeridos = min(\App\Models\ArticuloRevista::POSICIONES, $total);
@endphp

@section('titulo', 'Número '.$revista->revi_num_edicion)

@section('contenido')
    <x-encabezado :titulo="'No. '.$revista->revi_num_edicion.' · '.$revista->revi_titulo" :volver="route('admin.ediciones.index')" volver-texto="Portada del sitio">
        <p class="emp-encabezado__meta">
            @if ($revista->revi_en_portada)
                <span class="emp-insignia emp-insignia-aprobado">En portada</span>
            @elseif ($revista->revi_visible)
                <span class="emp-insignia emp-insignia-revision">Visible</span>
            @else
                <span class="emp-insignia emp-insignia-rechazado">Oculto</span>
            @endif
            <span>{{ $revista->revi_mes }} {{ $revista->revi_anio }}</span>
            <span>Publicado el <span class="emp-num">{{ $revista->revi_fecha_publicacion->format('d/m/Y') }}</span></span>
        </p>
        <x-slot:acciones>
            <a href="{{ route('admin.ediciones.edit', $revista) }}" class="btn btn-secondary">Editar número</a>
            <a href="{{ route('admin.ediciones.articulos.create', $revista) }}" class="btn btn-primary">
                <x-icono nombre="mas" :tamano="14" /> Agregar artículo
            </a>
        </x-slot:acciones>
    </x-encabezado>

    @error('revista')
        <div class="alert alert-danger" role="alert"><span class="alert-texto">{{ $message }}</span></div>
    @enderror

    @if (! $revista->revi_en_portada)
        <div class="alert alert-info" role="status">
            <span class="alert-texto">
                @if ($revista->revi_visible)
                    Este número no está en portada: sus artículos no se ven en el inicio del sitio.
                @else
                    Este número está oculto. Márcalo como visible (Editar número) para poder ponerlo en portada.
                @endif
            </span>
            @if ($revista->revi_visible)
                <form method="POST" action="{{ route('admin.ediciones.portada', $revista) }}" class="ms-auto"
                      data-confirmar="¿Poner este número en portada? Sustituye al que está ahora en el inicio del sitio."
                      data-confirmar-boton="Poner en portada">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm">Poner en portada</button>
                </form>
            @endif
        </div>
    @endif

    <section class="emp-seccion" aria-labelledby="titulo-indice">
        <h2 id="titulo-indice" class="emp-seccion__titulo">Artículos del número e índice del inicio</h2>
        <p class="emp-seccion__texto">
            Elige {{ $requeridos === 5 ? 'los 5 artículos' : 'los artículos' }} que se ven en el inicio y su posición
            (la 1 es el artículo principal). Cuando quieras rotarlos, cambia algunos por los que aún no se han mostrado.
            @if ($total > 0)
                Se han mostrado <strong>{{ $mostrados }} de {{ $total }}</strong>.
            @endif
        </p>

        @error('posiciones')
            <div class="alert alert-danger" role="alert"><span class="alert-texto">{{ $message }}</span></div>
        @enderror

        @if ($articulos->isEmpty())
            <p class="emp-vacio">Este número aún no tiene artículos. <a href="{{ route('admin.ediciones.articulos.create', $revista) }}">Agrega el primero</a>.</p>
        @else
            <form id="form-indice" method="POST" action="{{ route('admin.ediciones.indice', $revista) }}">
                @csrf
                <div class="emp-marco-tabla">
                    <div class="table-responsive">
                        <table class="table emp-tabla align-middle">
                            <caption class="visually-hidden">Artículos del número {{ $revista->revi_num_edicion }}</caption>
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 10.5rem;">Posición</th>
                                    <th scope="col">Artículo</th>
                                    <th scope="col">En el inicio</th>
                                    <th scope="col">Cursos</th>
                                    <th scope="col"><span class="visually-hidden">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($articulos as $entrada)
                                    @php($id = $entrada->arre_id_articulo_revista)
                                    <tr>
                                        <td>
                                            <label for="posicion-{{ $id }}" class="visually-hidden">Posición de «{{ $entrada->arre_titulo }}»</label>
                                            <select id="posicion-{{ $id }}" name="posiciones[{{ $id }}]" class="form-select form-select-sm">
                                                <option value="">No se muestra</option>
                                                @for ($p = 1; $p <= \App\Models\ArticuloRevista::POSICIONES; $p++)
                                                    <option value="{{ $p }}" @selected((int) old("posiciones.$id", $entrada->arre_posicion_index) === $p)>
                                                        {{ $p }}{{ $p === 1 ? ' · principal' : '' }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </td>
                                        <td>
                                            <span class="d-block fw-medium">{{ $entrada->arre_titulo }}</span>
                                            <span class="text-body-secondary small">
                                                {{ $entrada->arre_seccion ? $entrada->arre_seccion.' · ' : '' }}{{ $entrada->arre_autores }}
                                                @if ($entrada->arti_id_autor) · artículo del sistema @endif
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            @if ($entrada->arre_posicion_index)
                                                <span class="emp-insignia emp-insignia-aprobado">Desde {{ $entrada->arre_fecha_index->format('d/m/Y') }}</span>
                                            @elseif ($entrada->arre_fecha_index)
                                                <span class="emp-insignia emp-insignia-revision">Mostrado el {{ $entrada->arre_fecha_index->format('d/m/Y') }}</span>
                                            @else
                                                <span class="emp-insignia emp-insignia-rechazado">Pendiente</span>
                                            @endif
                                        </td>
                                        <td class="small">
                                            @forelse ($cursos[$id] as $curso)
                                                <span class="d-block">{{ $curso->curs_nombre_curso }}</span>
                                            @empty
                                                <span class="text-body-secondary">Sin cursos</span>
                                            @endforelse
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.ediciones.articulos.edit', [$revista, $entrada]) }}" class="btn btn-secondary btn-sm">
                                                    Editar<span class="visually-hidden"> «{{ $entrada->arre_titulo }}»</span>
                                                </a>
                                                <button type="submit" form="quitar-{{ $id }}" class="btn btn-quieto btn-sm">
                                                    Quitar<span class="visually-hidden"> «{{ $entrada->arre_titulo }}»</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="emp-formulario__acciones">
                    <button type="submit" class="btn btn-primary">Guardar índice</button>
                </div>
            </form>

            {{-- Formularios de «Quitar» fuera del formulario del índice (no se anidan). --}}
            @foreach ($articulos as $entrada)
                <form id="quitar-{{ $entrada->arre_id_articulo_revista }}" method="POST" hidden
                      action="{{ route('admin.ediciones.articulos.destroy', [$revista, $entrada]) }}"
                      data-confirmar="¿Quitar «{{ $entrada->arre_titulo }}» del número? Se borran su PDF, su imagen y sus cursos."
                      data-confirmar-boton="Quitar artículo" data-destructivo>
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        @endif
    </section>

    <section class="emp-seccion" aria-labelledby="titulo-eliminar">
        <h2 id="titulo-eliminar" class="emp-seccion__titulo">Eliminar el número</h2>
        <p class="emp-seccion__texto">Se borran el número, su portada, su PDF y todos sus artículos. No se puede deshacer.</p>
        <form method="POST" action="{{ route('admin.ediciones.destroy', $revista) }}"
              data-confirmar="¿Eliminar el número {{ $revista->revi_num_edicion }}? Se borran su portada, su PDF y sus artículos. Esta acción no se puede deshacer."
              data-confirmar-boton="Eliminar número" data-destructivo>
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-quieto">Eliminar número</button>
        </form>
    </section>
@endsection
