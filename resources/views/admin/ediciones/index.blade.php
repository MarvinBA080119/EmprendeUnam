@extends('layouts.admin')

@section('titulo', 'Portada del sitio')

@section('contenido')
    <x-encabezado titulo="Portada del sitio"
                  descripcion="Números de la revista. El que está en portada aparece en el inicio del sitio con los 5 artículos que elijas.">
        <x-slot:acciones>
            <a href="{{ route('admin.ediciones.create') }}" class="btn btn-primary"><x-icono nombre="mas" :tamano="14" /> Registrar número</a>
        </x-slot:acciones>
    </x-encabezado>

    <div class="emp-marco-tabla">
        @if ($numeros->isEmpty())
            <p class="emp-vacio">
                Aún no hay números registrados. Mientras tanto, el inicio del sitio muestra el contenido fijo de la versión anterior.
            </p>
        @else
            <div class="table-responsive">
                <table class="table emp-tabla align-middle">
                    <caption class="visually-hidden">Números de la revista</caption>
                    <thead>
                        <tr>
                            <th scope="col">Número</th>
                            <th scope="col">Periodo</th>
                            <th scope="col">Artículos</th>
                            <th scope="col">Estado</th>
                            <th scope="col"><span class="visually-hidden">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($numeros as $numero)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.ediciones.show', $numero) }}">No. {{ $numero->revi_num_edicion }} · {{ $numero->revi_titulo }}</a>
                                </td>
                                <td class="text-nowrap">{{ $numero->revi_mes }} {{ $numero->revi_anio }}</td>
                                <td class="text-nowrap">
                                    <span class="emp-num">{{ $numero->articulos_count }}</span>
                                    <span class="text-body-secondary">· {{ $numero->en_indice_count }} en el índice</span>
                                </td>
                                <td class="text-nowrap">
                                    @if ($numero->revi_en_portada)
                                        <span class="emp-insignia emp-insignia-aprobado">En portada</span>
                                    @elseif ($numero->revi_visible)
                                        <span class="emp-insignia emp-insignia-revision">Visible</span>
                                    @else
                                        <span class="emp-insignia emp-insignia-rechazado">Oculto</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.ediciones.show', $numero) }}" class="btn btn-secondary btn-sm">
                                            Artículos e índice<span class="visually-hidden"> del número {{ $numero->revi_num_edicion }}</span>
                                        </a>
                                        @if ($numero->revi_visible && ! $numero->revi_en_portada)
                                            <form method="POST" action="{{ route('admin.ediciones.portada', $numero) }}"
                                                  data-confirmar="¿Poner el número {{ $numero->revi_num_edicion }} en portada? Sustituye al que está ahora en el inicio del sitio."
                                                  data-confirmar-boton="Poner en portada">
                                                @csrf
                                                <button type="submit" class="btn btn-quieto btn-sm">
                                                    Poner en portada<span class="visually-hidden"> el número {{ $numero->revi_num_edicion }}</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
