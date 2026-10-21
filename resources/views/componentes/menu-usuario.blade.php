{{-- Menú de la cuenta en el encabezado: inicial del usuario y, al abrirlo,
     su nombre, el papel, el sitio público y cerrar sesión. --}}
@props(['rol'])
@php($usuario = auth()->user())
<div class="dropdown emp-cuenta-menu">
    <button type="button" class="emp-cuenta-menu__boton" data-bs-toggle="dropdown" data-bs-offset="0,10"
            aria-expanded="false">
        <span class="emp-cuenta-menu__avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($usuario->usua_nombre_usuario, 0, 1)) }}</span>
        <span class="emp-cuenta-menu__nombre">{{ $usuario->usua_nombre_usuario }}</span>
        <svg class="emp-cuenta-menu__flecha" width="12" height="12" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false">
            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
        </svg>
        <span class="visually-hidden">Menú de la cuenta</span>
    </button>
    <div class="dropdown-menu dropdown-menu-end emp-cuenta-menu__panel">
        <div class="emp-cuenta-menu__quien">
            <span class="emp-cuenta-menu__avatar emp-cuenta-menu__avatar--grande" aria-hidden="true">{{ mb_strtoupper(mb_substr($usuario->usua_nombre_usuario, 0, 1)) }}</span>
            <span>
                <strong>{{ $usuario->usua_nombre_usuario }}</strong>
                <span>{{ $rol }}</span>
            </span>
        </div>
        <a class="dropdown-item" href="{{ route('inicio') }}" target="_blank" rel="noopener">
            <x-icono nombre="externo" :tamano="14" /> Ver el sitio de la revista
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item">
                <x-icono nombre="salir" :tamano="14" /> Cerrar sesión
            </button>
        </form>
    </div>
</div>
