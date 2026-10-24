@extends('layouts.admin')

@section('titulo', 'Manuscritos y diseños')

@php
    $disenadosEnLista = $articulos->filter->tieneDiseno()->count();
    $consulta = $todos ? ['ver' => 'todos'] : [];
@endphp

@section('contenido')
    <x-encabezado titulo="Manuscritos y diseños"
                  descripcion="Los artículos aprobados pasan a la mesa de diseño. Aquí descargas los originales y los PDF diseñados para armar el número."
                  :volver="route('admin.ediciones.index')" volver-texto="Portada del sitio">
        <x-slot:acciones>
            <a href="{{ route('admin.ediciones.manuscritos.originales', $consulta) }}" @class(['btn btn-secondary', 'disabled' => $articulos->isEmpty()])
               @if ($articulos->isEmpty()) aria-disabled="true" tabindex="-1" @endif>
                <x-icono nombre="descargar" :tamano="14" /> Originales (.zip)
            </a>
            <a href="{{ route('admin.ediciones.manuscritos.disenos', $consulta) }}" @class(['btn btn-primary', 'disabled' => $disenadosEnLista === 0])
               @if ($disenadosEnLista === 0) aria-disabled="true" tabindex="-1" @endif>
                <x-icono nombre="descargar" :tamano="14" /> PDF diseñados (.zip)
                @if ($disenadosEnLista > 0)
                    <span class="emp-num emp-boton-cuenta">{{ $disenadosEnLista }}</span>
                @endif
            </a>
        </x-slot:acciones>
    </x-encabezado>

    @if ($conteos['total'] > 0)
        <x-avance-diseno :conteos="$conteos" />
    @endif

    <nav class="emp-segmentos" aria-label="Qué artículos mostrar">
        <a href="{{ route('admin.ediciones.manuscritos') }}" @class(['emp-segmentos__opcion', 'is-activo' => ! $todos]) @unless ($todos) aria-current="page" @endunless>
            Sin número
        </a>
        <a href="{{ route('admin.ediciones.manuscritos', ['ver' => 'todos']) }}" @class(['emp-segmentos__opcion', 'is-activo' => $todos]) @if ($todos) aria-current="page" @endif>
            Todos los aprobados <span class="emp-segmentos__cuenta emp-num">{{ $conteos['total'] }}</span>
        </a>
    </nav>

    @if ($articulos->isEmpty())
        <div class="emp-vacio-grande">
            <span class="emp-vacio-grande__icono" aria-hidden="true"><x-icono nombre="check" :tamano="22" /></span>
            <h2 class="emp-vacio-grande__titulo">{{ $todos ? 'Aún no hay artículos aprobados' : 'Todos ya están en un número' }}</h2>
            <p class="emp-vacio-grande__texto">
                {{ $todos
                    ? 'Cuando apruebes un artículo, la mesa de diseño recibe un correo y aquí verás su avance.'
                    : 'No hay artículos aprobados pendientes de publicar. Revisa «Todos los aprobados» para descargar los de números anteriores.' }}
            </p>
        </div>
    @else
        <ol class="emp-mesa">
            @foreach ($articulos as $articulo)
                <x-articulo-mesa :articulo="$articulo">
                    <a href="{{ route('admin.articulos.pdf', $articulo) }}" class="btn btn-quieto btn-sm">
                        <x-icono nombre="descargar" :tamano="14" /> Original<span class="visually-hidden"> de «{{ $articulo->arti_titulo }}»</span>
                    </a>
                    @if ($articulo->tieneDiseno())
                        <a href="{{ route('admin.ediciones.manuscritos.diseno', $articulo) }}" class="btn btn-secondary btn-sm">
                            <x-icono nombre="descargar" :tamano="14" /> PDF diseñado<span class="visually-hidden"> de «{{ $articulo->arti_titulo }}»</span>
                        </a>
                    @endif
                </x-articulo-mesa>
            @endforeach
        </ol>
    @endif
@endsection
