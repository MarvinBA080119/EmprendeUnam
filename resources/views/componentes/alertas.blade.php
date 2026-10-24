{{-- Mensajes flash de la sesión (exito, aviso, error) como notificaciones en
     la esquina. resources/js/avisos.js las cierra solas (los errores se quedan
     hasta que se cierran) y agrega aquí las que se generan en la página. --}}
@php
    $titulos = ['exito' => 'Listo', 'aviso' => 'Aviso', 'error' => 'No se pudo completar'];
    $duraciones = ['exito' => 6000, 'aviso' => 9000, 'error' => 0];
    $mensajes = collect($titulos)->map(fn ($titulo, $clave) => session($clave))->filter()->all();
    // Envío demasiado pesado: llega en la URL porque se rechazó antes de abrir la sesión.
    if (request()->query('aviso') === \App\Support\AvisoArchivo::PARAMETRO && ! isset($mensajes['error'])) {
        $mensajes['error'] = \App\Support\AvisoArchivo::mensaje();
    }
@endphp
<div class="emp-avisos" aria-live="polite">
    @foreach ($titulos as $clave => $titulo)
        @if (! empty($mensajes[$clave]))
            <div class="emp-aviso emp-aviso--{{ $clave }}" role="{{ $clave === 'error' ? 'alert' : 'status' }}"
                 data-duracion="{{ $duraciones[$clave] }}">
                <span class="emp-aviso__marca" aria-hidden="true"></span>
                <div class="emp-aviso__cuerpo">
                    <p class="emp-aviso__titulo">{{ $titulo }}</p>
                    <p class="emp-aviso__texto">{{ $mensajes[$clave] }}</p>
                </div>
                <button type="button" class="emp-aviso__cerrar" aria-label="Cerrar aviso">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false">
                        <path d="M3.72 3.72a.75.75 0 0 1 1.06 0L8 6.94l3.22-3.22a.75.75 0 1 1 1.06 1.06L9.06 8l3.22 3.22a.75.75 0 1 1-1.06 1.06L8 9.06l-3.22 3.22a.75.75 0 0 1-1.06-1.06L6.94 8 3.72 4.78a.75.75 0 0 1 0-1.06Z"/>
                    </svg>
                </button>
            </div>
        @endif
    @endforeach
</div>
