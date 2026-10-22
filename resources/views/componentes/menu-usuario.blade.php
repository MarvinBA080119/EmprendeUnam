{{-- Cuenta al pie de la barra lateral: inicial, nombre y papel; al abrirla,
     el sitio de la revista y cerrar sesión. --}}
@props(['rol'])
@php($usuario = auth()->user())
@php($inicial = mb_strtoupper(mb_substr($usuario->usua_nombre_usuario, 0, 1)))
<div class="dropup emp-cuenta-menu">
    <button type="button" class="emp-cuenta-menu__boton" data-bs-toggle="dropdown" data-bs-offset="0,8"
            aria-expanded="false">
        <span class="emp-cuenta-menu__avatar" aria-hidden="true">{{ $inicial }}</span>
        <span class="emp-cuenta-menu__datos">
            <span class="emp-cuenta-menu__nombre">{{ $usuario->usua_nombre_usuario }}</span>
            <span class="emp-cuenta-menu__rol">{{ $rol }}</span>
        </span>
        <svg class="emp-cuenta-menu__flecha" width="12" height="12" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false">
            <path fill-rule="evenodd" d="M3.646 9.646a.5.5 0 0 1 .708 0L8 13.293l3.646-3.647a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 0-.708m0-3.292a.5.5 0 0 0 .708 0L8 2.707l3.646 3.647a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 0 0 0 .708"/>
        </svg>
        <span class="visually-hidden">Menú de la cuenta</span>
    </button>
    <div class="dropdown-menu emp-cuenta-menu__panel">
        <a class="dropdown-item" href="{{ route('inicio') }}" target="_blank" rel="noopener">
            <x-icono nombre="externo" :tamano="14" /> Ver el sitio de la revista
        </a>
        <form method="POST" action="{{ route('logout') }}" data-cierra-sesion>
            @csrf
            <button type="submit" class="dropdown-item" data-texto-enviando="Cerrando sesión…">
                <x-icono nombre="salir" :tamano="14" /> Cerrar sesión
            </button>
        </form>
    </div>
</div>
