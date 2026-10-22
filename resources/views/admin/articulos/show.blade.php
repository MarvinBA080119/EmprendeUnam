@extends('layouts.admin')

@section('titulo', $articulo->arti_titulo)

@section('contenido')
    @php($enRevision = $articulo->arti_status === \App\Enums\EstatusArticulo::EN_REVISION)

    <x-encabezado :titulo="$articulo->arti_titulo" :volver="route('admin.articulos.index')" volver-texto="Artículos">
        <p class="emp-encabezado__meta">
            <x-insignia :estatus="$articulo->arti_status" />
            <span>{{ $articulo->tema->tema_nombre }}</span>
            <span>Enviado el <span class="emp-num">{{ $articulo->arti_fecha_registro->format('d/m/Y') }}</span></span>
        </p>
        <x-slot:acciones>
            <a href="{{ route('admin.articulos.pdf', $articulo) }}" class="btn btn-secondary">
                <x-icono nombre="descargar" :tamano="14" /> Descargar PDF
            </a>
        </x-slot:acciones>
    </x-encabezado>

    @error('articulo')
        <div class="alert alert-danger" role="alert"><span class="alert-texto">{{ $message }}</span></div>
    @enderror

    <div class="emp-detalle emp-detalle--con-lateral">
        <div class="emp-detalle__principal">
            <section class="emp-seccion" aria-labelledby="sec-resumen">
                <h2 id="sec-resumen" class="emp-seccion__titulo">Resumen</h2>
                <p class="emp-lectura">{{ $articulo->arti_resumen }}</p>
            </section>

            <section class="emp-seccion" aria-labelledby="sec-datos">
                <h2 id="sec-datos" class="emp-seccion__titulo">Datos del artículo</h2>
                <dl class="emp-datos">
                    <div>
                        <dt>Palabras clave</dt>
                        <dd>
                            <ul class="emp-etiquetas">
                                @foreach ($articulo->palabras as $palabra)
                                    <li class="emp-etiqueta">{{ $palabra->pala_descripcion }}</li>
                                @endforeach
                            </ul>
                        </dd>
                    </div>
                    <div>
                        <dt>Última modificación</dt>
                        <dd class="emp-num">{{ $articulo->arti_fecha_modificacion->format('d/m/Y') }}</dd>
                    </div>
                </dl>
            </section>

            <section class="emp-seccion" aria-labelledby="sec-autores">
                <h2 id="sec-autores" class="emp-seccion__titulo">Autores</h2>
                <div class="emp-marco-tabla">
                    <div class="table-responsive">
                        <table class="table emp-tabla align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Correo</th>
                                    <th scope="col">Institución</th>
                                    <th scope="col">Papel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($articulo->autores as $autor)
                                    <tr>
                                        <td>{{ $autor->persona?->nombre_completo }}</td>
                                        <td class="text-break">{{ $autor->persona?->correo?->comu_valor_comunicacion }}</td>
                                        <td>
                                            {{ $autor->auto_institucion }}
                                            @if ($autor->auto_grado_academico)
                                                <div class="small text-body-secondary">{{ $autor->auto_grado_academico }}</div>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">{{ $autor->pivot->auar_principal ? 'Autor principal' : 'Coautor' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        @if ($enRevision)
            <aside class="emp-detalle__lateral" aria-labelledby="titulo-dictamen">
                <div class="emp-accion">
                    <h2 id="titulo-dictamen" class="emp-accion__titulo">Dictamen</h2>
                    <p class="emp-accion__texto">El dictamen es definitivo y se avisa al autor por correo.</p>

                    <form method="POST" action="{{ route('admin.articulos.aprobar', $articulo) }}"
                          data-confirmar="¿Aprobar el artículo y generar su constancia? Esta acción no se puede deshacer."
                          data-confirmar-boton="Aprobar y generar constancia">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">Aprobar y generar constancia</button>
                        <p class="form-text mb-0">Se genera la constancia con folio único.</p>
                    </form>

                    <div class="emp-accion__separador"><span>o</span></div>

                    <form method="POST" action="{{ route('admin.articulos.rechazar', $articulo) }}"
                          data-confirmar="¿Rechazar el artículo? El rechazo es definitivo."
                          data-confirmar-boton="Rechazar artículo" data-destructivo>
                        @csrf
                        <div class="mb-3">
                            <label for="comentario" class="form-label">Motivo del rechazo</label>
                            <textarea id="comentario" name="comentario" rows="5" minlength="20" maxlength="350" required
                                      aria-describedby="comentario-ayuda @error('comentario') comentario-error @enderror"
                                      @class(['form-control', 'is-invalid' => $errors->has('comentario')])>{{ old('comentario') }}</textarea>
                            <div id="comentario-ayuda" class="form-text">Entre 20 y 350 caracteres. El autor lo recibirá por correo.</div>
                            @error('comentario')
                                <div id="comentario-error" class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-secondary w-100">Rechazar artículo</button>
                    </form>
                </div>
            </aside>
        @else
            <aside class="emp-detalle__lateral" aria-labelledby="titulo-dictamen">
                <div class="emp-accion">
                    <h2 id="titulo-dictamen" class="emp-accion__titulo">Dictamen</h2>
                    <dl class="emp-registro">
                        <div>
                            <dt>Resultado</dt>
                            <dd><x-insignia :estatus="$articulo->arti_status" /></dd>
                        </div>
                        <div>
                            <dt>Fecha</dt>
                            <dd class="emp-num">{{ $articulo->arti_fecha_modificacion->format('d/m/Y') }}</dd>
                        </div>
                        @if ($articulo->constancia)
                            <div>
                                <dt>Folio</dt>
                                <dd><strong>{{ $articulo->constancia->cons_folio }}</strong></dd>
                            </div>
                        @endif
                    </dl>
                    @if ($articulo->constancia)
                        <a href="{{ route('autor.articulos.constancia', $articulo) }}" class="btn btn-secondary w-100">
                            <x-icono nombre="descargar" :tamano="14" /> Descargar constancia
                        </a>
                        <a href="{{ route('constancias.verificar', $articulo->constancia->cons_folio) }}" class="emp-accion__enlace" target="_blank" rel="noopener">
                            Ver la verificación pública <x-icono nombre="externo" :tamano="12" />
                        </a>
                    @endif
                    @if ($articulo->arti_comentario_rechazo)
                        <p class="emp-accion__etiqueta">Motivo del rechazo</p>
                        <p class="emp-texto-libre mb-0">{{ $articulo->arti_comentario_rechazo }}</p>
                    @endif
                </div>
            </aside>
        @endif
    </div>
@endsection
