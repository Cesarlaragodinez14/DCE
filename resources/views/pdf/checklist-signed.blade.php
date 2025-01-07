<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Auditoría</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #b4c6e7;
            text-align: left;
            padding: 4px;
        }
        th {
            background-color: #323f4f;
            color: #FFF
        }
        .header-info {
            margin-bottom: 20px;
        }
        .header-info p {
            margin: 0;
            line-height: 1.6;
        }
        .title {
            font-weight: bold;
        }
        .table-section-header {
            background-color: #323f4f;
            font-weight: bold;
            text-align: left;
        }
        .subapartado {
            padding-left: 20px;
        }
        .footer {
            margin-top: 20px;
            font-size: 7px;
        }
        .header-info table, .header-info tr, .header-info td{
            border: 0px solid #FFF;
        }
        /* Position the header */
        .principal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 100px;
            text-align: center;
        }
        /* Margin for content to avoid overlapping with the header */
        body {
            margin-top: 100px;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>

    <!-- Incluimos el contenido del PDF de seguimiento -->
    @include('pdf.checklist', [
        'auditoria' => $auditoria,
        'apartados' => $apartados,
        'checklist' => $checklist,
        'estatus_checklist' => $estatus_checklist,
        'formato' => $formato,
        'firmaPath' => null, // Ajusta según necesites
        'qrCodeDataUri' => $qrCodeDataUriTracking,
        'hash' => $trackingHash,
        'user' => $user,
        'ipAddress' => $ipAddress,
        'generatedAt' => $generatedAt,
    ])

    <!-- Incluir el parcial hash_info.blade.php para el hash de seguimiento -->
    @include('pdf.hash_info', [
        'qrCodeDataUri' => $qrCodeDataUriTracking,
        'hash' => $trackingHash,
        'currentUserName' => $trackingUserEmail,
        'currentUserRole' => 'Seguimiento', // Puedes ajustar según necesites
        'email' => $trackingUserEmail,
        'ipAddress' => $trackingIpAddress,
        'generatedAt' => $trackingGeneratedAt,
    ])

    <!-- Incluir el parcial hash_info_uaa.blade.php para el nuevo hash de UAA -->
    @include('pdf.hash_info_uaa', [
        'qrCodeDataUriUAA' => $qrCodeDataUriUAA,
        'hashSeguimiento' => $trackingHash,
        'hashUAA' => $uaaHash,
        'currentUserName' => $currentUserName,
        'currentUserRole' => $currentUserRole,
        'ipAddress' => $ipAddress,
        'generatedAt' => $generatedAt,
    ])

</body>
</html>
