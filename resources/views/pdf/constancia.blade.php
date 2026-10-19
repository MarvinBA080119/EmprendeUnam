{{--
    Constancia de aprobación: EJEMPLO PROVISIONAL (formato, texto y firma por definir).
    Encabezado #001328, acentos #664300. Tipografía DejaVu (incluida en dompdf).
--}}
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <title>Constancia {{ $folio }}</title>
    <style>
        @page { margin: 0; }
        body { margin: 0; font-family: "DejaVu Sans", sans-serif; color: #000000; font-size: 13px; }
        .encabezado { background: #001328; color: #ffffff; padding: 28px 56px 22px; }
        .encabezado .revista { font-family: "DejaVu Serif", serif; font-size: 28px; font-weight: bold; }
        .encabezado .facultad { font-size: 12px; margin-top: 4px; }
        .franja { height: 6px; background: #664300; }
        .cuerpo { padding: 70px 56px 0; }
        .titulo-doc { font-family: "DejaVu Serif", serif; color: #664300; font-size: 30px; letter-spacing: 4px;
                      text-transform: uppercase; text-align: center; margin: 0 0 36px; }
        .texto { font-size: 17px; line-height: 1.9; text-align: center; margin: 0 40px; }
        .articulo { font-family: "DejaVu Serif", serif; font-size: 17px; font-weight: bold; }
        table.pie { width: 100%; margin-top: 80px; border-collapse: collapse; }
        table.pie td { vertical-align: bottom; }
        .datos { font-size: 12px; line-height: 1.6; }
        .folio { color: #664300; font-weight: bold; font-size: 14px; }
        .firma { text-align: center; }
        .firma .linea { border-top: 1px solid #000000; width: 260px; margin: 0 auto 6px; }
        .firma .leyenda { font-size: 11px; }
        .qr { text-align: right; font-size: 9px; }
        .qr img { width: 110px; height: 110px; }
        .nota { position: absolute; bottom: 18px; left: 56px; right: 56px; font-size: 9px; color: #000000;
                border-top: 1px solid #664300; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="encabezado">
        <div class="revista">Revista EmprendeUNAM</div>
        <div class="facultad">Universidad Nacional Autónoma de México · Facultad de Contaduría y Administración</div>
    </div>
    <div class="franja"></div>

    <div class="cuerpo">
        <p class="titulo-doc">Constancia</p>

        <p class="texto">
            Se hace constar que el artículo<br>
            <span class="articulo">«{{ $titulo }}»</span><br>
            de <strong>{{ $autor }}</strong>@if (count($coautores)) (coautores: {{ implode(', ', $coautores) }})@endif,
            fue aprobado para su publicación en la Revista EmprendeUNAM.
        </p>

        <table class="pie">
            <tr>
                <td class="datos" style="width: 34%;">
                    Ciudad de México, {{ $fecha }}<br>
                    Folio: <span class="folio">{{ $folio }}</span>
                </td>
                <td class="firma" style="width: 38%;">
                    <div class="linea"></div>
                    <div class="leyenda">Firma o sello institucional (por definir)</div>
                </td>
                <td class="qr" style="width: 28%;">
                    <img src="{{ $qr }}" alt="Código QR de verificación"><br>
                    Verifica esta constancia
                </td>
            </tr>
        </table>
    </div>

    <div class="nota">Verificación en línea: {{ $urlVerificacion }}</div>
</body>
</html>
