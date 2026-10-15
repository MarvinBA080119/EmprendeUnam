{{-- Campo de formulario con etiqueta, ayuda y error de validación. --}}
@props([
    'nombre',
    'etiqueta',
    'tipo' => 'text',
    'valor' => null,
    'requerido' => false,
    'ayuda' => null,
])
@php($id = 'campo-'.str_replace(['[', ']', '.'], '-', $nombre))
<div class="mb-3">
    <label for="{{ $id }}" class="form-label">
        {{ $etiqueta }}@if ($requerido) <span aria-hidden="true">*</span>@endif
    </label>
    <input
        id="{{ $id }}"
        name="{{ $nombre }}"
        type="{{ $tipo }}"
        @if ($tipo !== 'password') value="{{ old($nombre, $valor) }}" @endif
        @if ($requerido) required @endif
        @error($nombre) aria-invalid="true" @enderror
        @if ($ayuda || $errors->has($nombre)) aria-describedby="{{ $id }}-ayuda {{ $id }}-error" @endif
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($nombre)]) }}
    >
    @if ($ayuda)
        <div id="{{ $id }}-ayuda" class="form-text">{{ $ayuda }}</div>
    @endif
    @error($nombre)
        <div id="{{ $id }}-error" class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
