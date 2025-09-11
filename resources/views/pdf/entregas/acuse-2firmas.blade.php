<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acuse de Recibo - Segunda Firma</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
        }
        .logo-asf {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 140px;
        }
        .principal {
            margin-left: 200px;
            text-align: right;
            margin-top: 0;
        }
        .header-info {
            margin-top: 10px;
            margin-bottom: 10px;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        th, td {
            border: 1px solid #b4c6e7;
            padding: 4px;
            text-align: center;
        }
        th {
            background-color: #323f4f;
            color: #FFF;
        }
        .firma-section {
            margin-top: 20px;
        }
        .footer {
            margin-top: 20px;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Logo en la parte superior -->
<img src="{{ public_path('img/asf.png') }}" alt="Logo ASF" class="logo-asf">

<div class="principal">
    <h3>AUDITORÍA ESPECIAL DE SEGUIMIENTO, INFORMES E INVESTIGACIÓN</h3>
    <h4>DEPARTAMENTO DE CONTROL DE EXPEDIENTES</h4>
</div>
<div style="text-align: center">
    <h2>ACUSE DE RECIBO DE EXPEDIENTE DE LAS ACCIONES O RECOMENDACIONES</h2>
</div>

<!-- Tipo de movimiento y fecha (2da firma) -->
<div class="header-info">
    <p><strong>TIPO DE MOVIMIENTO:</strong> {{ $estado }}</p>
    @if(!empty($fechaSegunda))
        <p><strong>FECHA (2da Firma):</strong> 
            {{ \Carbon\Carbon::parse($fechaSegunda)->format('d/m/Y H:i:s') }}
        </p>
    @endif
</div>

<!-- Tabla principal con los datos de la(s) auditoría(s) -->
<table style="text-align:center">
    <thead>
        <tr>
            <th style="text-align:center; width: 8%">NÚMERO</th>
            <th style="text-align:center; width: 14%">CUENTA PÚBLICA / EJERCICIO FISCAL</th>
            <th style="text-align:center; width: 14%">PERIODO DE ENTREGA DEL INFORME</th>
            <th style="text-align:center; width: 18%">NÚM. Y TÍTULO DE LA AUDITORÍA</th>
            <th style="text-align:center; width: 14%">ENTIDAD RESPONSABLE</th>
            <th style="text-align:center; width: 14%">CLAVE DE LA ACCIÓN / RECOMENDACIÓN</th>
            <th style="text-align:center; width: 12%">TIPO DE ACCIÓN / RECOMENDACIÓN</th>
            <th style="text-align:center; width: 6%">LEGAJO(S)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expedienteRows as $row)
        <tr>
            <td style="text-align: center">{{ $loop->iteration }}</td>    
            <td style="text-align: center">{{ $row['cuentaPublica'] }}</td>        
            <td style="text-align: center">{{ $row['periodoEntrega'] }}</td>       
            <td style="text-align: center">{{ $row['numero_auditoria'] ." - " . $row['numTituloAuditoria'] }}</td>   
            <td style="text-align: center">{{ $row['entidadResponsable'] }}</td>   
            <td style="text-align: center">{{ $row['claveAccion'] }}</td>          
            <td style="text-align: center">{{ $row['tipoAccion'] }}</td>           
            <td style="text-align: center">{{ $row['legajos'] }}</td>              
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Sección de firmas (quien entrega, quien recibe) en tabla -->
<table style="width: 100%; margin-top: 20px;">
    <tr>
        <td style="width: 50%; vertical-align: top;">
            <p style="text-align: center"><strong>SERVIDOR(A) PÚBLICO(A) QUE ENTREGA EL EXPEDIENTE</strong></p>
            <p style="text-align:left"><strong>NOMBRE:</strong> {{ $respEntregaName }}</p>
            <p style="text-align:left"><strong>CARGO:</strong> {{ $respEntregaCargo }}</p>
            <p style="text-align:left"><strong>AUDITORÍA ESPECIAL:</strong> {{ $respEntregaAe }}</p>
            <p style="text-align:left"><strong>DIRECCIÓN GENERAL:</strong> {{ $respEntregaDg }}</p>
        </td>
        <td style="width: 50%; vertical-align: top;">
            <p style="text-align: center"><strong>SERVIDOR(A) PÚBLICO(A) QUE RECIBE</strong></p>
            <p style="text-align:left"><strong>NOMBRE:</strong> {{ $respRecibeName }}</p>
            <p style="text-align:left"><strong>CARGO:</strong> {{ $respRecibeCargo }}</p>
            <p style="text-align:left"><strong>AUDITORÍA ESPECIAL:</strong> {{ $respRecibeAe }}</p>
            <p style="text-align:left"><strong>DIRECCIÓN GENERAL:</strong> {{ $respRecibeDg }}</p>
        </td>
    </tr>
</table>

<table style="width:100%; margin-top:20px;">
    <tr>
        @if($qrCodeDataUri1)
            <!-- Columna del Código QR -->
            <td style="width:15%; text-align:center; vertical-align:top;">
                <img src="{{ $qrCodeDataUri1 }}" alt="Código QR" style="max-width:150px;">
            </td>
            <!-- Columna de la Información del Hash -->
            <td style="width:35%; vertical-align: top; text-align:left">
                <p><strong>Hash:</strong> {{ $hashPrimera }}</p>
                <p><strong>Firmado Por:</strong> {{ $nombrePrimera ?? 'N/A' }}</p>
                <p><strong>Correo del Generador:</strong> {{ $emailPrimera }}</p>
                <p><strong>Dirección IP:</strong> {{ $ipPrimera }}</p>
                <p><strong>Fecha/Hora de Generación:</strong> 
                @if($fechaPrimera) {{ \Carbon\Carbon::parse($fechaPrimera)->format('d/m/Y H:i:s') }} @endif
                </p>
            </td>
        @endif
    </tr>
    <tr>
        @if(!empty($qrCodeDataUri2))
            <!-- Columna del Código QR -->
            <td style="width:15%; text-align:center; vertical-align:top;">
                <img src="{{ $qrCodeDataUri2 }}" alt="Código QR" style="max-width:150px;">
            </td>
            <!-- Columna de la Información del Hash -->
            <td style="width:35%; vertical-align: top; text-align:left">
                <p><strong>Hash:</strong> {{ $hashSegunda }}</p>
                <p><strong>Firmado Por:</strong> {{ $nombreSegunda ?? 'N/A' }}</p>
                <p><strong>Correo del Generador:</strong> {{ $emailSegunda }}</p>
                <p><strong>Dirección IP:</strong> {{ $ipSegunda }}</p>
                <p><strong>Fecha/Hora de Generación:</strong> 
                @if($fechaSegunda) {{ \Carbon\Carbon::parse($fechaSegunda)->format('d/m/Y H:i:s') }} @endif
                </p>
            </td>
        @endif
    </tr>
</table>
<p class="footer">
    El presente acuse no prejuzga sobre el contenido de la documentación 
    que se integra en el expediente recibido ni de su debida integración.
    <P style="width: 100%; text-align: right; font-size: 8px; color: #c6c6c6">
        NÚMERO DE FORMATO: 4SE2AC70-02
    </P>
</p>

</body>
</html>
