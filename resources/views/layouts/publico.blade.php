@extends('layouts.base')

@section('menu')
    <x-enlace-menu ruta="inicio">Inicio</x-enlace-menu>
    @auth
        @php($inicio = app(\App\Services\AccesoService::class)->rutaInicio(auth()->user()))
        <li class="nav-item"><a class="nav-link" href="{{ $inicio }}">Mi panel</a></li>
    @endauth
@endsection

@section('cuerpo')
    <main id="contenido" class="py-4">
        <div class="container">
            <x-alertas />
            @yield('contenido')
        </div>
    </main>
@endsection
