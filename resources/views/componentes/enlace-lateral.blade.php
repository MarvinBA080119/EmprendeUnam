{{-- Enlace de la barra lateral con ícono y, si hay pendientes, su número. --}}
@props(['ruta', 'activa', 'icono' => null, 'cuenta' => 0, 'cuentaTexto' => 'pendientes'])
@if (Route::has($ruta))
    @php($esActiva = request()->routeIs($activa))
    <li>
        <a @class(['emp-lat__enlace', 'is-activo' => $esActiva]) @if ($esActiva) aria-current="page" @endif href="{{ route($ruta) }}">
            @if ($icono)
                <x-icono :nombre="$icono" />
            @endif
            <span class="emp-lat__texto">{{ $slot }}</span>
            @if ($cuenta > 0)
                <span class="emp-cuenta">{{ $cuenta }}<span class="visually-hidden"> {{ $cuentaTexto }}</span></span>
            @endif
        </a>
    </li>
@endif
