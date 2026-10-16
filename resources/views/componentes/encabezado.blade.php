{{-- Encabezado de página del área de trabajo: enlace de regreso opcional,
     título, descripción y acciones a la derecha ($acciones). --}}
@props(['titulo', 'descripcion' => null, 'volver' => null, 'volverTexto' => null])
<header class="emp-encabezado">
    @if ($volver)
        <a class="emp-encabezado__volver" href="{{ $volver }}">
            <x-icono nombre="atras" :tamano="14" /> {{ $volverTexto }}
        </a>
    @endif
    <div class="emp-encabezado__fila">
        <div class="emp-encabezado__texto">
            <h1 class="emp-titulo">{{ $titulo }}</h1>
            @if ($descripcion)
                <p class="emp-subtitulo">{{ $descripcion }}</p>
            @endif
            {{ $slot }}
        </div>
        @isset($acciones)
            <div class="emp-encabezado__acciones">{{ $acciones }}</div>
        @endisset
    </div>
</header>
