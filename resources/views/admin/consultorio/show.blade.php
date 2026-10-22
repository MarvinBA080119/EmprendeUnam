@extends('layouts.admin')

@section('titulo', $mensaje->mens_asunto)

@section('contenido')
    <x-encabezado :titulo="$mensaje->mens_asunto" :volver="route('admin.consultorio.index')" volver-texto="Consultorio">
        <p class="emp-encabezado__meta">
            <x-insignia :estatus="$mensaje->mens_status" />
            <span>Recibido el <span class="emp-num">{{ $mensaje->mens_fecha_envio->format('d/m/Y H:i') }}</span></span>
        </p>
    </x-encabezado>

    @error('mensaje')
        <div class="alert alert-danger" role="alert"><span class="alert-texto">{{ $message }}</span></div>
    @enderror

    <div class="emp-detalle emp-detalle--con-lateral">
        <div class="emp-detalle__principal">
            <section class="emp-seccion" aria-labelledby="sec-mensaje">
                <h2 id="sec-mensaje" class="emp-seccion__titulo">Mensaje</h2>
                <dl class="emp-datos emp-datos--compactos">
                    <div>
                        <dt>De</dt>
                        <dd>{{ $mensaje->mens_remitente }} <span class="text-body-secondary text-break">&lt;{{ $mensaje->mens_correo }}&gt;</span></dd>
                    </div>
                    @if ($mensaje->mens_lugar)
                        <div>
                            <dt>Lugar</dt>
                            <dd>{{ $mensaje->mens_lugar }}</dd>
                        </div>
                    @endif
                </dl>
                <p class="emp-lectura emp-texto-libre">{{ $mensaje->mens_descripcion }}</p>
            </section>

            @if ($mensaje->respuesta)
                <section class="emp-seccion" aria-labelledby="sec-respuesta">
                    <h2 id="sec-respuesta" class="emp-seccion__titulo">Respuesta</h2>
                    <div class="emp-respuesta">
                        <p class="emp-lectura emp-texto-libre mb-0">{{ $mensaje->respuesta->mens_descripcion }}</p>
                    </div>
                </section>
            @endif
        </div>

        @if ($pendiente)
            <aside class="emp-detalle__lateral" aria-labelledby="titulo-responder">
                <div class="emp-accion">
                    <h2 id="titulo-responder" class="emp-accion__titulo">Responder</h2>
                    <p class="emp-accion__texto">La respuesta se envía al correo del lector y el mensaje se publica en el consultorio, sin su correo.</p>
                    <form method="POST" action="{{ route('admin.consultorio.responder', $mensaje) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="respuesta" class="form-label">Respuesta</label>
                            <textarea id="respuesta" name="respuesta" rows="8" maxlength="700" required
                                      aria-describedby="respuesta-ayuda @error('respuesta') respuesta-error @enderror"
                                      @class(['form-control', 'is-invalid' => $errors->has('respuesta')])>{{ old('respuesta') }}</textarea>
                            <div id="respuesta-ayuda" class="form-text">Máximo 700 caracteres.</div>
                            @error('respuesta')
                                <div id="respuesta-error" class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Enviar respuesta</button>
                    </form>

                    <div class="emp-accion__separador"><span>o</span></div>

                    <form method="POST" action="{{ route('admin.consultorio.rechazar', $mensaje) }}"
                          data-confirmar="¿Rechazar este mensaje? No se responderá ni se publicará."
                          data-confirmar-boton="Rechazar mensaje" data-destructivo>
                        @csrf
                        <button type="submit" class="btn btn-secondary w-100">Rechazar mensaje</button>
                        <p class="form-text mb-0">Un mensaje rechazado no se responde ni se publica.</p>
                    </form>
                </div>
            </aside>
        @else
            <aside class="emp-detalle__lateral" aria-labelledby="titulo-estado">
                <div class="emp-accion">
                    <h2 id="titulo-estado" class="emp-accion__titulo">Estado</h2>
                    <dl class="emp-registro">
                        <div>
                            <dt>Resultado</dt>
                            <dd><x-insignia :estatus="$mensaje->mens_status" /></dd>
                        </div>
                        @if ($mensaje->respuesta)
                            <div>
                                <dt>Respondido</dt>
                                <dd class="emp-num">{{ $mensaje->respuesta->mens_fecha_envio->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt>Por</dt>
                                <dd>{{ $mensaje->respuesta->mens_remitente }}</dd>
                            </div>
                        @endif
                    </dl>
                    @if ($mensaje->respuesta)
                        <a href="{{ route('consultorio') }}" class="emp-accion__enlace" target="_blank" rel="noopener">
                            Ver en el consultorio público <x-icono nombre="externo" :tamano="12" />
                        </a>
                    @else
                        <p class="emp-accion__texto mb-0">Un mensaje rechazado no se responde ni se publica.</p>
                    @endif
                </div>
            </aside>
        @endif
    </div>
@endsection
