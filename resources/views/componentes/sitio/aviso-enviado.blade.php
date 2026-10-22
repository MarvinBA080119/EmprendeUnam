{{-- Ventana de confirmación tras enviar un formulario del sitio (consultorio,
     suscripción), con el diseño del sitio. resources/js/sitio/envios.js la
     abre al cargar la página; sin JavaScript queda el aviso junto al
     formulario. --}}
@props(['id', 'etiqueta', 'titulo'])
<dialog class="aviso-lux" id="{{ $id }}" data-abrir-al-cargar aria-labelledby="{{ $id }}-titulo"
        aria-describedby="{{ $id }}-texto">
    <div class="aviso-lux__cuerpo">
        <span class="aviso-lux__marca" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="square" focusable="false"><path pathLength="1" d="M4.5 12.5l5 5 10-11"/></svg>
        </span>
        <p class="aviso-lux__eti">{{ $etiqueta }}</p>
        <h2 class="aviso-lux__tit" id="{{ $id }}-titulo">{{ $titulo }}</h2>
        <div class="aviso-lux__texto" id="{{ $id }}-texto">{{ $slot }}</div>
        <form method="dialog">
            <button type="submit" class="btn-enviar aviso-lux__boton" autofocus>Entendido</button>
        </form>
    </div>
</dialog>
