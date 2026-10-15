{{-- Insignia de estatus (EstatusArticulo o EstatusMensaje): color de la paleta + texto. --}}
@props(['estatus'])
<span class="emp-insignia {{ $estatus->claseInsignia() }}">{{ $estatus->etiqueta() }}</span>
