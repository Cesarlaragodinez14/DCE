<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Ya Generado</title>
</head>
<body>
    <h3>Esta clave de acción ya cuenta con un PDF firmado.</h3>
    <a href="{{ asset('storage/' . $filePath) }}" class="btn btn-primary">Descargar PDF</a>
</body>
</html>
