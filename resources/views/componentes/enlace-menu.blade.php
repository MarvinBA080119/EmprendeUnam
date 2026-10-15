@props(['ruta'])
@php($activa = request()->routeIs($ruta))
<li class="nav-item">
    <a @class(['nav-link', 'active' => $activa]) @if ($activa) aria-current="page" @endif href="{{ route($ruta) }}">{{ $slot }}</a>
</li>
