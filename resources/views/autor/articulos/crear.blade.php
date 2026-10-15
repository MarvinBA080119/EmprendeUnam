@extends('layouts.autor')

@section('titulo', 'Enviar artículo')

@section('contenido')
    <nav aria-label="Ruta de navegación">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('autor.panel') }}">Mis artículos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Enviar artículo</li>
        </ol>
    </nav>

    <h1 class="emp-titulo-seccion">Enviar artículo</h1>

    <div class="row">
        <div class="col-12 col-xl-10">
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
            ])
            <div id="formulario-articulo" data-props='@json($props)'>
                <noscript>
                    <div class="alert alert-warning">Para enviar un artículo necesitas activar JavaScript en tu navegador.</div>
                </noscript>
            </div>
        </div>
    </div>
@endsection
