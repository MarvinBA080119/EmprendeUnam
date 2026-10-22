@extends('layouts.autor')

@section('titulo', 'Enviar artículo')

@section('contenido')
    <x-encabezado titulo="Enviar artículo" :volver="route('autor.panel')" volver-texto="Mis artículos"
                  descripcion="El equipo editorial lo revisará y te avisará por correo el dictamen." />

    <div>
        <div>
            @php($props = [
                'accion' => route('autor.articulos.store'),
                'csrf' => csrf_token(),
                'temas' => $temas->map(fn ($t) => ['id' => $t->tema_id_tema, 'nombre' => $t->tema_nombre])->values(),
                'anterior' => [
                    'titulo' => old('titulo'),
                    'resumen' => old('resumen'),
                    'tema_id_tema' => old('tema_id_tema'),
                    'palabras' => old('palabras', []),
                    'tiene_coautores' => old('tiene_coautores', false),
                    'coautores' => old('coautores', []),
                ],
                'errores' => $errors->getMessages(),
                'pdfMaxKb' => (int) config('emprende.pdf_max_kb'),
                'maxCoautores' => \App\Http\Requests\Autor\EnviarArticuloRequest::MAX_COAUTORES,
                'maxPalabrasResumen' => \App\Http\Requests\Autor\EnviarArticuloRequest::MAX_PALABRAS_RESUMEN,
                'urlCancelar' => route('autor.panel'),
                'urlCartaCesion' => route('autor.carta-cesion'),
            ])
            <div id="formulario-articulo" data-props='@json($props)'>
                <noscript>
                    <div class="alert alert-warning">Para enviar un artículo necesitas activar JavaScript en tu navegador.</div>
                </noscript>
            </div>
        </div>
    </div>
@endsection
