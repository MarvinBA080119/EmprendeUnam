{{-- Campo de formulario con etiqueta, ayuda y error de validación.
     - opcional: muestra «(opcional)» junto a la etiqueta (los obligatorios no se marcan).
     - $accion: enlace opcional a la derecha de la etiqueta (p. ej. «¿La olvidaste?»).
     - tipo="password": agrega el botón Mostrar/Ocultar.
     - tipo="select": lista con $opciones (valor = texto) y, si se da, una
       primera opción vacía con el texto $vacio. --}}
@props([
    'nombre',
    'etiqueta',
    'tipo' => 'text',
    'valor' => null,
    'requerido' => false,
    'opcional' => false,
    'ayuda' => null,
    'opciones' => [],
    'vacio' => null,
])
@php
    $id = 'campo-'.str_replace(['[', ']', '.'], '-', $nombre);
    $describe = trim(($ayuda ? $id.'-ayuda ' : '').($errors->has($nombre) ? $id.'-error' : ''));
@endphp
<div class="mb-3">
    <div class="emp-etiqueta-fila">
        <label for="{{ $id }}" class="form-label">
            {{ $etiqueta }}@if ($opcional) <span class="emp-opcional">(opcional)</span>@endif
        </label>
        @isset($accion)
            {{ $accion }}
        @endisset
    </div>
    @if ($tipo === 'password')
        <div class="emp-clave">
    @endif
    @if ($tipo === 'select')
        <select
            id="{{ $id }}"
            name="{{ $nombre }}"
            @if ($requerido) required @endif
            @error($nombre) aria-invalid="true" @enderror
            @if ($describe !== '') aria-describedby="{{ $describe }}" @endif
            {{ $attributes->class(['form-select', 'is-invalid' => $errors->has($nombre)]) }}
        >
            @if ($vacio !== null)
                <option value="">{{ $vacio }}</option>
            @endif
            @foreach ($opciones as $opcion)
                <option value="{{ $opcion }}" @selected(old($nombre, $valor) === $opcion)>{{ $opcion }}</option>
            @endforeach
        </select>
    @else
        <input
            id="{{ $id }}"
            name="{{ $nombre }}"
            type="{{ $tipo }}"
            @if ($tipo !== 'password') value="{{ old($nombre, $valor) }}" @endif
            @if ($requerido) required @endif
            @error($nombre) aria-invalid="true" @enderror
            @if ($describe !== '') aria-describedby="{{ $describe }}" @endif
            {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($nombre)]) }}
        >
    @endif
    @if ($tipo === 'password')
            <button type="button" class="emp-clave__ver" data-mostrar-clave="{{ $id }}" aria-controls="{{ $id }}" aria-pressed="false">Mostrar</button>
        </div>
    @endif
    @if ($ayuda)
        <div id="{{ $id }}-ayuda" class="form-text">{{ $ayuda }}</div>
    @endif
    @error($nombre)
        <div id="{{ $id }}-error" class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
