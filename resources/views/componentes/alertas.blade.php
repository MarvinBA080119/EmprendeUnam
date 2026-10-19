{{-- Mensajes flash de la sesión: exito, error, aviso --}}
@foreach (['exito' => 'success', 'aviso' => 'warning', 'error' => 'danger'] as $clave => $tipo)
    @if (session($clave))
        <div class="alert alert-{{ $tipo }} alert-dismissible fade show" role="{{ $tipo === 'success' ? 'status' : 'alert' }}">
            <span class="alert-texto">{{ session($clave) }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
@endforeach
