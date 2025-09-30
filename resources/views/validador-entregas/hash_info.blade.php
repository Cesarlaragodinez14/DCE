<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validación de Acuse de Entrega</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fuente Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Estilos CSS -->
    <style>
        /* Estilos Globales */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 200px;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            font-weight: 700;
            color: #2c3e50;
        }
        .info {
            margin-top: 30px;
            text-align: left;
            display: inline-block;
            max-width: 600px;
            width: 100%;
        }
        .info p {
            font-size: 1.1em;
            margin-bottom: 15px;
            line-height: 1.6;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 8px;
        }
        .info p strong {
            font-weight: 500;
            color: #2c3e50;
        }
        .info p span {
            color: #7f8c8d;
            word-break: break-all;
            text-align: right;
            max-width: 65%;
        }
        .download-button {
            margin-top: 40px;
            text-align: center;
        }
        .download-button a {
            background-color: #3498db;
            color: #fff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-size: 1.2em;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .download-button a:hover {
            background-color: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        /* Diseño Responsivo */
        @media (max-width: 600px) {
            .info p {
                flex-direction: column;
                align-items: flex-start;
            }
            .info p span {
                text-align: left;
                max-width: 100%;
                margin-top: 5px;
            }
        }
        .wrapper{
            display:flex;justify-content:center;align-items:center;
        }
        .checkmark__circle {
            stroke-dasharray: 166;stroke-dashoffset: 166;stroke-width: 2;stroke-miterlimit: 10;stroke: #7ac142;fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        .checkmark {
            width: 56px;height: 56px;border-radius: 50%;display: block;stroke-width: 2;stroke: #fff;stroke-miterlimit: 10;
            margin: 10% auto;box-shadow: inset 0px 0px 0px #7ac142;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }
        .checkmark__check {
            transform-origin: 50% 50%;stroke-dasharray: 48;stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }
        @keyframes stroke {
            100% { stroke-dashoffset: 0; }
        }
        @keyframes scale {
            0%, 100% { transform: none; }
            50% { transform: scale3d(1.1, 1.1, 1); }
        }
        @keyframes fill {
            100% { box-shadow: inset 0px 0px 0px 30px #7ac142; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo de la Plataforma -->
        <div class="logo">
            <img src="http://saes.asf.gob.mx/img/logo-v.png" alt="Logo Plataforma">
        </div>
        <!-- Animación Check -->
        <div class="wrapper">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>

        <h1>Acuse Verificado</h1>

        <!-- Info de Validación -->
        <div class="info">
            <p><strong>Hash:</strong><span>{{ $hash }}</span></p>
            <p><strong>Correo del Generador:</strong><span>{{ $userEmail }}</span></p>
            <p><strong>Dirección IP:</strong><span>{{ $ipAddress }}</span></p>
            <p><strong>Fecha/Hora de Generación:</strong>
                <span>{{ \Carbon\Carbon::parse($generatedAt)->format('d/m/Y H:i:s') }}</span>
            </p>
        </div>

        <!-- Botón de Descarga -->
        <div class="download-button">
            <a href="{{ route('validador-entregas.download', ['hash' => $hash]) }}">
                Descargar PDF
            </a>
        </div>
    </div>
</body>
</html>
