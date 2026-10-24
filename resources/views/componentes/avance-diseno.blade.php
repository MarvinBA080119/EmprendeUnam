{{-- Avance de la mesa de diseño: una tira con un pliego por artículo aprobado
     (azul = diseñado, hueso con hilo café = falta) y el conteo en texto. Con
     muchos artículos la tira se vuelve una barra continua. --}}
@props(['conteos'])
@php
    $total = $conteos['total'];
    $listos = $conteos['disenados'];
    $porPliego = $total <= 40;
@endphp
<section {{ $attributes->class(['emp-avance']) }} aria-label="Avance del diseño">
    <p class="emp-avance__texto">
        <strong class="emp-num">{{ $listos }} de {{ $total }}</strong>
        {{ $total === 1 ? 'artículo diseñado' : 'artículos diseñados' }}
    </p>
    @if ($porPliego)
        <span class="emp-avance__tira" aria-hidden="true">
            @for ($i = 0; $i < $total; $i++)
                <span @class(['emp-avance__pliego', 'is-listo' => $i < $listos])></span>
            @endfor
        </span>
    @else
        <span class="emp-avance__barra" aria-hidden="true"><span style="width: {{ $total ? round($listos * 100 / $total, 1) : 0 }}%"></span></span>
    @endif
</section>
