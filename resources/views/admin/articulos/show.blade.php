@extends('layouts.admin')

@section('titulo', $articulo->arti_titulo)

@section('contenido')
    <nav aria-label="Ruta de navegación">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.articulos.index') }}">Artículos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
        </ol>
    </nav>

    <h1 class="emp-titulo-seccion">{{ $articulo->arti_titulo }}</h1>

    @error('articulo')
        <div class="alert alert-danger" role="alert">{{ $message }}</div>
    @enderror

    <div class="row g-4">
        <div class="col-12 col-xl-7">
            <div class="card emp-tarjeta mb-4">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Estatus</dt>
                        <dd class="col-sm-8"><x-insignia :estatus="$articulo->arti_status" /></dd>
                        <dt class="col-sm-4">Tema</dt>
                        <dd class="col-sm-8">{{ $articulo->tema->tema_nombre }}</dd>
                        <dt class="col-sm-4">Enviado</dt>
                        <dd class="col-sm-8">{{ $articulo->arti_fecha_registro->format('d/m/Y') }}</dd>
                        <dt class="col-sm-4">Última modificación</dt>
                        <dd class="col-sm-8">{{ $articulo->arti_fecha_modificacion->format('d/m/Y') }}</dd>
                        <dt class="col-sm-4">Palabras clave</dt>
                        <dd class="col-sm-8">{{ $articulo->palabras->pluck('pala_descripcion')->join(' · ') }}</dd>
                        @if ($articulo->constancia)
                            <dt class="col-sm-4">Constancia</dt>
                            <dd class="col-sm-8">
                                Folio {{ $articulo->constancia->cons_folio }}
                                ({{ $articulo->constancia->cons_fecha_emision->format('d/m/Y') }}) ·
                                <a href="{{ route('autor.articulos.constancia', $articulo) }}">Descargar</a>
                            </dd>
                        @endif
                        @if ($articulo->arti_comentario_rechazo)
                            <dt class="col-sm-4">Motivo del rechazo</dt>
                            <dd class="col-sm-8" style="white-space: pre-line">{{ $articulo->arti_comentario_rechazo }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <h2 class="h4">Autores</h2>
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
                                <td>{{ $autor->pivot->auar_principal ? 'Autor principal' : 'Coautor' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <a href="{{ route('admin.articulos.pdf', $articulo) }}" class="btn btn-outline-primary">Descargar PDF del artículo</a>
        </div>

        @if ($articulo->arti_status === \App\Enums\EstatusArticulo::EN_REVISION)
            <div class="col-12 col-xl-5">
                <div class="card emp-tarjeta mb-4">
                    <div class="card-body">
                        <h2 class="h5">Aprobar</h2>
                        <p>Se generará la constancia con folio único y se avisará al autor. El dictamen es definitivo.</p>
                        <form method="POST" action="{{ route('admin.articulos.aprobar', $articulo) }}"
                              onsubmit="return confirm('¿Aprobar el artículo y generar su constancia? Esta acción no se puede deshacer.');">
                            @csrf
                            <button type="submit" class="btn btn-secondary">Aprobar y generar constancia</button>
                        </form>
                    </div>
                </div>

                <div class="card emp-tarjeta">
                    <div class="card-body">
                        <h2 class="h5">Rechazar</h2>
                        <form method="POST" action="{{ route('admin.articulos.rechazar', $articulo) }}"
                              onsubmit="return confirm('¿Rechazar el artículo? El rechazo es definitivo.');">
                            @csrf
                            <div class="mb-3">
                                <label for="comentario" class="form-label">Motivo del rechazo <span aria-hidden="true">*</span></label>
                                <textarea id="comentario" name="comentario" rows="5" minlength="20" maxlength="350" required
                                          aria-describedby="comentario-ayuda @error('comentario') comentario-error @enderror"
                                          @class(['form-control', 'is-invalid' => $errors->has('comentario')])>{{ old('comentario') }}</textarea>
                                <div id="comentario-ayuda" class="form-text">Entre 20 y 350 caracteres. El autor lo recibirá por correo.</div>
                                @error('comentario')
                                    <div id="comentario-error" class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-dark">Rechazar artículo</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
