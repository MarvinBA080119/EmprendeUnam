{{-- Enlace del panel lateral; si la ruta aún no existe (fases siguientes) no se muestra. --}}
@props(['ruta', 'activa'])
@if (Route::has($ruta))
    @php($esActiva = request()->routeIs($activa))
    <li class="nav-item">
        <a @class(['nav-link', 'active' => $esActiva]) @if ($esActiva) aria-current="page" @endif href="{{ route($ruta) }}">{{ $slot }}</a>
    </li>
@endif
