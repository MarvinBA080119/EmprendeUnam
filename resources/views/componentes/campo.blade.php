{{-- Campo de formulario con etiqueta, ayuda y error de validación.
     - opcional: muestra «(opcional)» junto a la etiqueta (los obligatorios no se marcan).
     - $accion: enlace opcional a la derecha de la etiqueta (p. ej. «¿La olvidaste?»).
     - tipo="password": agrega el botón Mostrar/Ocultar. --}}
@props([
    'nombre',
    'etiqueta',
    'tipo' => 'text',
    'valor' => null,
    'requerido' => false,
    'opcional' => false,
    'ayuda' => null,
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
