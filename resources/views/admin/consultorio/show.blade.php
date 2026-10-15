@extends('layouts.admin')

@section('titulo', $mensaje->mens_asunto)

@section('contenido')
    <nav aria-label="Ruta de navegación">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.consultorio.index') }}">Consultorio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mensaje</li>
        </ol>
    </nav>

    <h1 class="emp-titulo-seccion">{{ $mensaje->mens_asunto }}</h1>

    @error('mensaje')
        <div class="alert alert-danger" role="alert">{{ $message }}</div>
    @enderror

    <div class="row g-4">
        <div class="col-12 col-xl-7">
            <div class="card emp-tarjeta mb-4">
                <div class="card-body">
                    <dl class="row mb-3">
                        <dt class="col-sm-3">Estatus</dt>
                        <dd class="col-sm-9"><x-insignia :estatus="$mensaje->mens_status" /></dd>
                        <dt class="col-sm-3">De</dt>
                        <dd class="col-sm-9">{{ $mensaje->mens_remitente }} &lt;{{ $mensaje->mens_correo }}&gt;</dd>
                        @if ($mensaje->mens_lugar)
                            <dt class="col-sm-3">Lugar</dt>
                            <dd class="col-sm-9">{{ $mensaje->mens_lugar }}</dd>
                        @endif
                        <dt class="col-sm-3">Recibido</dt>
                        <dd class="col-sm-9">{{ $mensaje->mens_fecha_envio->format('d/m/Y H:i') }}</dd>
                    </dl>
                    <p class="mb-0" style="white-space: pre-line">{{ $mensaje->mens_descripcion }}</p>
                </div>
            </div>

            @if ($mensaje->respuesta)
                <h2 class="h4">Respuesta</h2>
                <div class="card emp-tarjeta">
                    <div class="card-body">
                        <p class="small mb-2">{{ $mensaje->respuesta->mens_remitente }} · {{ $mensaje->respuesta->mens_fecha_envio->format('d/m/Y H:i') }}</p>
                        <p class="mb-0" style="white-space: pre-line">{{ $mensaje->respuesta->mens_descripcion }}</p>
                    </div>
                </div>
            @endif
        </div>

        @if ($pendiente)
            <div class="col-12 col-xl-5">
                <div class="card emp-tarjeta mb-4">
                    <div class="card-body">
                        <h2 class="h5">Responder</h2>
                        <p class="small">La respuesta se envía al correo del lector y el mensaje se publica en el consultorio (sin su correo).</p>
                        <form method="POST" action="{{ route('admin.consultorio.responder', $mensaje) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="respuesta" class="form-label">Respuesta <span aria-hidden="true">*</span></label>
                                <textarea id="respuesta" name="respuesta" rows="8" maxlength="700" required
                                          aria-describedby="respuesta-ayuda @error('respuesta') respuesta-error @enderror"
                                          @class(['form-control', 'is-invalid' => $errors->has('respuesta')])>{{ old('respuesta') }}</textarea>
                                <div id="respuesta-ayuda" class="form-text">Máximo 700 caracteres.</div>
                                @error('respuesta')
                                    <div id="respuesta-error" class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar respuesta</button>
                        </form>
                    </div>
                </div>

                <div class="card emp-tarjeta">
                    <div class="card-body">
                        <h2 class="h5">Rechazar</h2>
                        <p class="small">Un mensaje rechazado no se responde ni se publica.</p>
                        <form method="POST" action="{{ route('admin.consultorio.rechazar', $mensaje) }}"
                              onsubmit="return confirm('¿Rechazar este mensaje? No se responderá ni se publicará.');">
                            @csrf
                            <button type="submit" class="btn btn-dark">Rechazar mensaje</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
