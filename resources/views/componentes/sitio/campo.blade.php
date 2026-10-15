{{-- Campo flotante del sitio público (form-floating de Bootstrap) con su error de validación. --}}
@props([
    'nombre',
    'etiqueta',
    'tipo' => 'text',
    'requerido' => false,
])
@php($id = 'campo-'.$nombre)
<div class="form-floating">
    @if ($tipo === 'textarea')
        <textarea id="{{ $id }}" name="{{ $nombre }}" placeholder="{{ $etiqueta }}"
                  @if ($requerido) required @endif
                  @error($nombre) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
                  {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($nombre)]) }}>{{ old($nombre) }}</textarea>
    @else
        <input id="{{ $id }}" name="{{ $nombre }}" type="{{ $tipo }}" placeholder="{{ $etiqueta }}"
               value="{{ old($nombre) }}"
               @if ($requerido) required @endif
               @error($nombre) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
               {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($nombre)]) }}>
    @endif
    <label for="{{ $id }}">{{ $etiqueta }}</label>
    @error($nombre)
        <div id="{{ $id }}-error" class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
