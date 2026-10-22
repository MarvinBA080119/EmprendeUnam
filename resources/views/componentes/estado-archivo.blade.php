{{-- Estado de un archivo del número: nombre, insignia con texto (nunca solo
     color) y detalle. estado: listo (azul), falta (café, requerido) u
     opcional (neutro). --}}
@props(['nombre', 'estado' => 'falta', 'detalle' => null])
@php
    $insignia = ['listo' => ['emp-insignia-aprobado', 'Listo'], 'falta' => ['emp-insignia-revision', 'Falta'], 'opcional' => ['emp-insignia-rechazado', 'Opcional']][$estado];
@endphp
<div {{ $attributes->class(['emp-archivo-estado', "is-{$estado}"]) }}>
    <span class="emp-archivo-estado__nombre">{{ $nombre }}</span>
    <span class="emp-insignia {{ $insignia[0] }}" data-estado-insignia>{{ $insignia[1] }}</span>
    @if ($detalle || isset($slot) && trim($slot) !== '')
        <span class="emp-archivo-estado__detalle" data-estado-detalle>{{ $detalle }}{{ $slot }}</span>
    @endif
</div>
