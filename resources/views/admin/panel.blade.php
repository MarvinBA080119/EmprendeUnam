@extends('layouts.admin')

@section('titulo', 'Panel del administrador')

@section('contenido')
    <h1 class="emp-titulo-seccion">Panel del administrador</h1>
    <p>Bienvenido. Desde el menú lateral puedes atender cada sección.</p>
    <div class="row g-3">
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card emp-tarjeta h-100">
                <div class="card-body">
                    <h2 class="h5">Autores</h2>
                    <p>Revisa las cuentas de autores y activa las que estén pendientes.</p>
                    <a href="{{ route('admin.autores.index', ['estatus' => 'pendiente']) }}" class="btn btn-primary">Ver pendientes</a>
                </div>
            </div>
        </div>
    </div>
@endsection
