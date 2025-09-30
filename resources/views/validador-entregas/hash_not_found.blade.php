<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>No Encontrado - Validación de Entrega</title>
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
            padding: 60px 20px;
            text-align: center;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 200px;
        }
        .error-icon {
            font-size: 120px;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            font-weight: 700;
            color: #2c3e50;
        }
        p {
            font-size: 1.2em;
            color: #7f8c8d;
            margin-bottom: 30px;
        }
        .back-button {
            margin-top: 20px;
            text-align: center;
        }
        .back-button a {
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
        .back-button a:hover {
            background-color: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        /* Animación del Icono de Error */
        .error-icon {
            animation: shake 0.5s;
            animation-iteration-count: 1;
        }
        @keyframes shake {
            0% { transform: translate(1px, 1px) rotate(0deg); }
            10% { transform: translate(-1px, -2px) rotate(-5deg); }
            20% { transform: translate(-3px, 0px) rotate(5deg); }
            30% { transform: translate(3px, 2px) rotate(0deg); }
            40% { transform: translate(1px, -1px) rotate(5deg); }
            50% { transform: translate(-1px, 2px) rotate(-5deg); }
            60% { transform: translate(-3px, 1px) rotate(0deg); }
            70% { transform: translate(3px, 1px) rotate(-5deg); }
            80% { transform: translate(-1px, -1px) rotate(5deg); }
            90% { transform: translate(1px, 2px) rotate(0deg); }
            100% { transform: translate(1px, -2px) rotate(-5deg); }
        }
        /* Diseño Responsivo */
        @media (max-width: 600px) {
            .container {
                padding: 40px 20px;
            }
            h1 {
                font-size: 2em;
            }
            .error-icon {
                font-size: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo de la Plataforma -->
        <div class="logo">
            <img src="http://saes.asf.gob.mx/img/logo-v.png" alt="Logo Plataforma">
        </div>
        <!-- Icono de Error -->
        <div class="error-icon">&#9888;</div>
        <h1>Error</h1>
        <p>El hash proporcionado no es válido o no se encontró.</p>
        <!-- Botón para Volver al Inicio -->
        <div class="back-button">
            <a href="{{ url('/') }}">Volver al inicio</a>
        </div>
    </div>
</body>
</html>
