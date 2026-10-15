{{-- Plantilla base de los correos: encabezado #001328, botón #664300 con texto blanco. --}}
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#ffffff;font-family:Arial,Helvetica,sans-serif;color:#000000;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#ffffff;">
    <tr>
        <td align="center" style="padding:24px 12px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;border:1px solid #d9dce0;">
                <tr>
                    <td style="background-color:#001328;color:#ffffff;padding:20px 24px;">
                        <div style="font-family:Georgia,'Times New Roman',serif;font-size:22px;font-weight:bold;">Revista EmprendeUNAM</div>
                        <div style="font-size:12px;color:#ffffff;opacity:0.8;">UNAM · Facultad de Contaduría y Administración</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px;font-size:15px;line-height:1.6;color:#000000;">
                        @yield('contenido')
                    </td>
                </tr>
                <tr>
                    <td style="border-top:3px solid #664300;padding:16px 24px;font-size:12px;color:#000000;">
                        Este correo se envió automáticamente; por favor no lo respondas.
                        @yield('pie')
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
