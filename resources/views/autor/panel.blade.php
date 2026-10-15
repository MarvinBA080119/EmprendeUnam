@extends('layouts.autor')

@section('titulo', 'Mis artículos')

@section('contenido')
    <h1 class="emp-titulo-seccion">Mis artículos</h1>
    <p>Hola, {{ auth()->user()->persona->pers_nombre }}. Aquí verás el historial de tus artículos y podrás enviar uno nuevo.</p>
    <div class="card emp-tarjeta">
        <div class="card-body">
            <p class="mb-0">Aún no hay artículos para mostrar.</p>
        </div>
    </div>
@endsection
