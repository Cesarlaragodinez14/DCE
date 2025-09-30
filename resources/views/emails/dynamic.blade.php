<!-- resources/views/emails/dynamic.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'Notificación' }}</title>
    <style>
        /* Estilos para dispositivos móviles */
        @media only screen and (max-width: 600px) {
            .container {
                padding: 20px !important;
            }
            .button {
                width: 100% !important;
            }
        }

        /* Estilos generales */
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        table{
            padding: 20px;
            text-align: center;
        }
        .container {
            background-color: #ffffff;
            padding: 40px; /* Aumentamos el padding para más espacio interno */
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            font-family: Arial, sans-serif;
            box-sizing: border-box;
        }
        .header {
            padding-bottom: 40px; /* Mayor espacio debajo del logo */
            text-align: center;
            max-width: 200px;
        }
        .header img {
            width: 150px;
            height: auto;
            border: 0;
        }
        .title {
            font-size: 26px; /* Aumentamos el tamaño de fuente para mayor prominencia */
            font-weight: bold;
            color: #333333;
            margin-bottom: 20px; /* Mayor margen inferior */
        }
        .divider {
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 30px; /* Mayor margen inferior */
        }
        .content {
            font-size: 16px;
            line-height: 1.6; /* Mejor legibilidad */
            color: #555555;
            margin-bottom: 30px; /* Mayor margen inferior */
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: #ffffff;
            padding: 15px 30px; /* Aumentamos el padding para un botón más grande */
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #45a049;
        }
        .footer {
            font-size: 12px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px; /* Mayor padding superior */
            text-align: center;
        }
    </style>
</head>
<body>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" bgcolor="#f4f4f4" style="padding: 40px 0;"> <!-- Aumentamos el padding superior e inferior -->
                <!-- Container Principal -->
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="container">
                    <!-- Header con Logo -->
                    <tr>
                        <td class="header" style="text-align: center">
                            <img style="max-width: 250px;" src="http://saes.asf.gob.mx/img/logo-v.png" alt="{{ config('app.name') }}" />
                        </td>
                    </tr>
                    <!-- Título del Correo -->
                    <tr>
                        <td class="title">
                            {{ $subject ?? 'Notificación' }}
                        </td>
                    </tr>
                    <!-- Línea Divisoria -->
                    <tr>
                        <td class="divider">
                        </td>
                    </tr>
                    <!-- Contenido Principal -->
                    <tr>
                        <td class="content">
                            {!! $content !!}
                        </td>
                    </tr>
                    <!-- Botón de Acción -->
                    @isset($data['action']['url'])
                        <tr>
                            <td align="center">
                                <a href="{{ $data['action']['url'] }}" target="_blank" class="button">
                                    {{ $data['action']['text'] }}
                                </a>
                            </td>
                        </tr>
                    @endisset
                    <!-- Espacio Extra Opcional -->
                    <tr>
                        <td style="padding-top: 20px;">
                        </td>
                    </tr>
                    <!-- Pie de Página -->
                    @isset($data['footer'])
                        <tr>
                            <td class="footer">
                                {{ $data['footer'] }}
                            </td>
                        </tr>
                    @endisset
                </table>
                <!-- Fin del Container Principal -->
            </td>
        </tr>
    </table>
</body>
</html>
