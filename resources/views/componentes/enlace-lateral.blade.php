{{-- Enlace de la barra lateral con ícono y, si hay pendientes, su número
     (el texto oculto lo lee el lector de pantalla). --}}
@props(['ruta', 'activa', 'icono' => null, 'cuenta' => 0, 'cuentaTexto' => 'pendientes'])
@if (Route::has($ruta))
    @php($esActiva = request()->routeIs($activa))
    <li>
        <a @class(['emp-nav__enlace', 'is-activo' => $esActiva]) @if ($esActiva) aria-current="page" @endif href="{{ route($ruta) }}">
            @if ($icono)
                <x-icono :nombre="$icono" class="emp-nav__icono" />
            @endif
            <span class="emp-nav__texto">{{ $slot }}</span>
            @if ($cuenta > 0)
                <span class="emp-cuenta">{{ $cuenta }}<span class="visually-hidden"> {{ $cuentaTexto }}</span></span>
            @endif
        </a>
    </li>
@endif
