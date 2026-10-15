@extends('layouts.base')

@section('menu')
    <x-enlace-menu ruta="autor.panel">Mis artículos</x-enlace-menu>
@endsection

@section('cuerpo')
    <main id="contenido" class="py-4">
        <div class="container">
            <x-alertas />
            @yield('contenido')
        </div>
    </main>
@endsection
