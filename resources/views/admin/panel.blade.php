@extends('layouts.admin')

@section('titulo', 'Panel del administrador')

@section('contenido')
    <h1 class="emp-titulo-seccion">Panel del administrador</h1>

    @php($tarjetas = [
        ['cifra' => $contadores['autores_pendientes'], 'texto' => 'Autores pendientes de activar', 'enlace' => route('admin.autores.index', ['estatus' => 'pendiente']), 'accion' => 'Revisar autores'],
        ['cifra' => $contadores['articulos_en_revision'], 'texto' => 'Artículos en revisión', 'enlace' => route('admin.articulos.index', ['estatus' => 'EN_REVISION']), 'accion' => 'Dictaminar artículos'],
        ['cifra' => $contadores['mensajes_pendientes'], 'texto' => 'Mensajes del consultorio pendientes', 'enlace' => route('admin.consultorio.index', ['estatus' => 'PENDIENTE']), 'accion' => 'Atender consultorio'],
        ['cifra' => $contadores['suscriptores_activos'], 'texto' => 'Suscriptores activos del newsletter', 'enlace' => route('admin.newsletter.index', ['estado' => 'activo']), 'accion' => 'Ver suscriptores'],
    ])

    <div class="row g-3">
        @foreach ($tarjetas as $tarjeta)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card emp-tarjeta h-100">
                    <div class="card-body d-flex flex-column">
                        <p class="emp-cifra mb-1">{{ $tarjeta['cifra'] }}</p>
                        <p class="flex-grow-1">{{ $tarjeta['texto'] }}</p>
                        <a href="{{ $tarjeta['enlace'] }}" class="btn btn-outline-primary btn-sm align-self-start">{{ $tarjeta['accion'] }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.cursos.create') }}" class="btn btn-primary">Publicar un curso</a>
    </div>
@endsection
