{{-- Un artículo aprobado en la mesa de diseño (panel de diseño y manuscritos
     del administrador). La «hoja» de la izquierda dice el estado sin color:
     el tipo del original (DOCX o PDF) mientras falta diseñarlo y una palomita
     sobre azul cuando ya tiene su PDF diseñado. Las acciones van en el slot;
     $pie (opcional) ocupa el renglón completo debajo. --}}
@props(['articulo', 'recien' => false])
@php
    $disenado = $articulo->tieneDiseno();
    $extension = strtoupper(pathinfo($articulo->arti_url_pdf, PATHINFO_EXTENSION) ?: 'pdf');
    $autores = \App\Services\DisenoService::autores($articulo);
    $numero = $articulo->entradasRevista->first()?->revista;
@endphp
<li {{ $attributes->class(['emp-mesa__fila', 'is-disenado' => $disenado, 'is-recien' => $recien]) }}
    id="articulo-{{ $articulo->arti_id_autor }}" data-mesa-fila>
    <span @class(['emp-mesa__hoja', 'is-disenada' => $disenado]) aria-hidden="true">
        @if ($disenado)
            <x-icono nombre="check" :tamano="18" />
        @else
            <span class="emp-mesa__extension">{{ $extension }}</span>
        @endif
    </span>

    <div class="emp-mesa__texto">
        <h2 class="emp-mesa__titulo">{{ $articulo->arti_titulo }}</h2>
        <p class="emp-mesa__meta">
            @if ($articulo->tema)
                <span class="emp-mesa__seccion">{{ $articulo->tema->tema_nombre }}</span>
            @endif
            @if ($autores)
                <span>{{ $autores }}</span>
            @endif
        </p>
        @if ($articulo->palabras->isNotEmpty())
            <ul class="emp-etiquetas" aria-label="Palabras clave">
                @foreach ($articulo->palabras as $palabra)
                    <li class="emp-etiqueta">{{ $palabra->pala_descripcion }}</li>
                @endforeach
            </ul>
        @endif
        <p class="emp-mesa__datos">
            <span>Aprobado el <span class="emp-num">{{ $articulo->arti_fecha_modificacion->format('d/m/Y') }}</span></span>
            @if ($disenado)
                <span>Diseñado el <span class="emp-num">{{ $articulo->arti_fecha_diseno->format('d/m/Y · H:i') }}</span></span>
            @endif
            @if ($numero)
                <span>Va en el No. <span class="emp-num">{{ $numero->revi_num_edicion }}</span></span>
            @endif
        </p>
    </div>

    <div class="emp-mesa__lado">
        @if ($disenado)
            <span class="emp-insignia emp-insignia-aprobado">Diseñado</span>
        @else
            <span class="emp-insignia emp-insignia-revision">Por diseñar</span>
        @endif
        <div class="emp-mesa__acciones">{{ $slot }}</div>
    </div>

    @isset($pie)
        <div class="emp-mesa__pie">{{ $pie }}</div>
    @endisset
</li>
