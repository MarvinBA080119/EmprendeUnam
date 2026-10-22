{{-- Miniatura de la portada de un número (proporción de revista, 7:9). --}}
@props(['revista'])
<span {{ $attributes->class(['emp-portada']) }}>
    @if ($revista->revi_url_portada)
        <img src="{{ route('edicion.portada', $revista) }}" alt="Portada del número {{ $revista->revi_num_edicion }}" loading="lazy">
    @else
        <span class="emp-portada__vacia">Sin portada</span>
    @endif
</span>
