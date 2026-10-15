@extends('layouts.publico')

@section('titulo', 'Inicio')

@section('contenido')
    <h1 class="emp-titulo-seccion">Temas de la revista</h1>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="card emp-tarjeta">
                <div class="card-body">
                    {{-- Isla Vue: filtra la lista sin recargar la página --}}
                    <div id="lista-temas" data-props='@json(['temas' => $temas])'>
                        {{-- Contenido sin JavaScript --}}
                        <ul class="list-group">
                            @foreach ($temas as $tema)
                                <li class="list-group-item">{{ $tema['nombre'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card emp-tarjeta">
                <div class="card-body">
                    <h2 class="h5">Paleta institucional</h2>
                    <p class="mb-3">Muestra de botones e insignias con los colores de la revista.</p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button type="button" class="btn btn-primary">Botón principal</button>
                        <button type="button" class="btn btn-secondary">Botón secundario</button>
                        <button type="button" class="btn btn-outline-primary">Contorno</button>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach (\App\Enums\EstatusArticulo::cases() as $estatus)
                            <span class="emp-insignia {{ $estatus->claseInsignia() }}">{{ $estatus->etiqueta() }}</span>
                        @endforeach
                    </div>
                    <table class="table emp-tabla mb-0">
                        <thead>
                            <tr><th scope="col">Columna</th><th scope="col">Valor</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Temas en la base</td><td>{{ count($temas) }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
