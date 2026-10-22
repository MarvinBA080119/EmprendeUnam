{{--
    Carta de agradecimiento al autor de un artículo aprobado (formato de la
    dirección editorial). Menciona el número y el bimestre cuando el artículo
    ya salió en un número visible. Tamaño carta, DejaVu
    (incluida en dompdf). Las imágenes llegan como data URI.
--}}
@php
    $varios = count($autores) > 1;
@endphp
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <title>Carta de agradecimiento {{ $folio }}</title>
    <style>
        @page { margin: 48px 64px 40px; }
        body { font-family: "DejaVu Sans", sans-serif; color: #000000; font-size: 12.5px; line-height: 1.6; }
        table.encabezado { width: 100%; border-collapse: collapse; }
        table.encabezado td { vertical-align: middle; }
        .logo { width: 78px; }
        .escudo { width: 72px; }
        .institucion { text-align: center; color: #001328; }
        .institucion .facultad { font-size: 15px; font-weight: bold; }
        .institucion .revista { font-size: 12px; font-style: italic; }
        .asunto { text-align: right; margin: 34px 0 26px; font-weight: bold; }
        .destinatario { font-weight: bold; text-transform: uppercase; margin: 0; }
        .presente { letter-spacing: 3px; margin: 0 0 22px; }
        .cuerpo p { text-align: justify; margin: 0 0 14px; }
        .articulo { font-weight: bold; }
        .revista-nombre { font-style: italic; }
        .cierre { text-align: center; margin-top: 30px; }
        .cierre p { margin: 0; }
        .lema { font-style: italic; }
        .espacio-firma { height: 56px; }
        .firmante { font-weight: bold; }
        .pie { position: fixed; bottom: -8px; left: 0; right: 0; }
        .pie table { width: 100%; border-collapse: collapse; }
        .pie td { vertical-align: bottom; font-size: 8.5px; color: #333333; }
        .marca { width: 120px; }
    </style>
</head>
<body>
    <table class="encabezado">
        <tr>
            <td style="width: 90px;"><img class="logo" src="{{ $logoFca }}" alt=""></td>
            <td class="institucion">
                <div class="facultad">Facultad de Contaduría y Administración</div>
                <div class="revista">Revista Emprendedores al servicio de la pequeña y mediana empresa</div>
            </td>
            <td style="width: 90px; text-align: right;"><img class="escudo" src="{{ $escudoUnam }}" alt=""></td>
        </tr>
    </table>

    <p class="asunto">Asunto: Carta de agradecimiento</p>

    @foreach ($autores as $autor)
        <p class="destinatario">{{ $autor }}</p>
    @endforeach
    <p class="presente">P r e s e n t e</p>

    <div class="cuerpo">
        <p>
            Por este medio {{ $varios ? 'les' : 'le' }} agradezco su valiosa contribución a nuestras publicaciones con el
            artículo <span class="articulo">{{ $titulo }}</span>
            @if ($numero)
                que se incorporó en el número {{ $numero }} correspondiente al bimestre {{ $periodo }} en la revista
            @else
                que fue aprobado para su publicación en la revista
            @endif
            <span class="revista-nombre">Emprendedores al servicio de la pequeña y mediana empresa</span>.
        </p>
        <p>
            {{ $varios ? 'Les' : 'Le' }} felicito y reitero la invitación a seguir colaborando en beneficio de los
            emprendedores y de nuestra comunidad.
        </p>
        <p>Quedo a sus órdenes y {{ $varios ? 'les' : 'le' }} envío un cordial saludo.</p>
    </div>

    <div class="cierre">
        <p>Atentamente</p>
        <p class="lema">“Por Mi Raza Hablará El Espíritu”</p>
        <p>Ciudad Universitaria, Cd. Mx., a {{ $fecha }}</p>
        <div class="espacio-firma"></div>
        <p>{{ $firma['cargo'] }}</p>
        <p class="firmante">{{ $firma['nombre'] }}</p>
    </div>

    <div class="pie">
        <table>
            <tr>
                <td>Folio {{ $folio }} · Verifica esta carta en {{ $urlVerificacion }}</td>
                <td style="text-align: right;"><img class="marca" src="{{ $fca2026 }}" alt=""></td>
            </tr>
        </table>
    </div>
</body>
</html>
