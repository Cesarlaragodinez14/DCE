<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acuse de Recibo de Expedientes</title>

    <style>
        /* 1) Configurar página en horizontal y márgenes */
        @page {
            size: A4 landscape; /* Hoja A4 horizontal */
            margin: 1.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0; /* DomPDF respeta @page más que margin de body */
            /* Si deseas separar contenido de cabecera, puedes usar un margin-top mayor */
        }

        /* 2) Estilo del logo */
        .logo-asf {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 150px; /* Ajusta el ancho */
        }

        /* 3) Encabezado principal (opcional), ejemplo de posicionamiento */
        .principal {
            text-align: center;
            margin-top: 0;
            margin-bottom: 40px;
        }

        /* 4) Estilos de tabla */
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
            color: #FFF;
        }

        /* 5) Info en la parte superior (tipo de movimiento, fecha) */
        .header-info {
            margin-top: 40px; /* Si quieres dejar un espacio para el logo */
            margin-bottom: 10px;
            text-align: right;
        }
        .header-info p {
            margin: 0;
            line-height: 1.2;
        }

        /* 6) Secciones finales (firma) */
        .firma-section {
            margin-top: 20px;
        }

        /* 7) Pie */
        .footer {
            margin-top: 20px;
            font-size: 7px;
        }
    </style>
</head>
<body>

<!-- Logo ASF en la parte superior izquierda -->
<img src="{{ public_path('img/asf.png') }}" alt="Logo ASF" class="logo-asf">

<div class="principal">
    <!-- Encabezados "Auditoría Especial..." y "Departamento..." 
         según tu imagen, pero ya tienes algo similar en tu placeholders -->
    <h3>AUDITORÍA ESPECIAL DE SEGUIMIENTO, INFORMES E INVESTIGACIÓN</h3>
    <h4>DEPARTAMENTO DE CONTROL DE EXPEDIENTES</h4>
    <h2>ACUSE DE RECIBO DE EXPEDIENTES DE LAS ACCIONES O RECOMENDACIONES</h2>
</div>

<div class="header-info">
    <!-- (1) Tipo de movimiento -->
    <p><strong>TIPO DE MOVIMIENTO: </strong>{{ $placeholder1 }}</p>
    <!-- (2) Fecha -->
    <p><strong>FECHA: </strong>{{ $placeholder2 }}</p>
</div>

<!-- Tabla principal con "NÚMERO (3)", "CUENTA PÚBLICA (4)", etc. -->
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
            <td style="text-align: center">{{ $row['numTituloAuditoria'] }}</td>   
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
            <p style="text-align:left"><strong>NOMBRE:</strong> {{ $placeholder11 }}</p>
            <p style="text-align:left"><strong>CARGO:</strong> {{ $placeholder12 }}</p>
            <p style="text-align:left"><strong>AUDITORÍA ESPECIAL:</strong> {{ $placeholder13 }}</p>
            <p style="text-align:left"><strong>DIRECCIÓN GENERAL:</strong> {{ $placeholder14 }}</p>
        </td>
        <td style="width: 50%; vertical-align: top;">
            <p style="text-align: center"><strong>SERVIDOR(A) PÚBLICO(A) QUE RECIBE</strong></p>
            <p style="text-align:left"><strong>NOMBRE:</strong> {{ $placeholder15 }}</p>
            <p style="text-align:left"><strong>CARGO:</strong> {{ $placeholder16 }}</p>
            <p style="text-align:left"><strong>AUDITORÍA ESPECIAL:</strong> {{ $placeholder17 }}</p>
            <p style="text-align:left"><strong>DIRECCIÓN GENERAL:</strong> {{ $placeholder18 }}</p>
        </td>
    </tr>
</table>

<!-- Bloque QR + Hash (opcional) -->
@if(!empty($qrCodeDataUri))
    <table style="width:100%; margin-top:20px;">
        <tr>
            <!-- Columna del Código QR -->
            <td style="width:50%; text-align:center; vertical-align:top;">
                <img src="{{ $qrCodeDataUri }}" alt="Código QR" style="max-width:150px;">
            </td>
            <!-- Columna de la Información del Hash -->
            <td style="width:50%; vertical-align: top;">
                <p><strong>Hash:</strong> {{ $hash }}</p>
                <p><strong>Firmado Por:</strong> {{ $nombreUsuario ?? 'N/A' }}</p>
                <p><strong>Correo del Generador:</strong> {{ $userEmail }}</p>
                <p><strong>Dirección IP:</strong> {{ $ipAddress }}</p>
                <p><strong>Fecha/Hora de Generación:</strong> 
                   @if($generatedAt) {{ $generatedAt->format('d/m/Y H:i:s') }} @endif
                </p>
            </td>
        </tr>
    </table>
@endif

<!-- Pie -->
<p class="footer" style="text-align: center">
    El presente acuse no prejuzga sobre el contenido de la documentación que se integra en el expediente recibido ni de su debida integración.
</p>

</body>
</html>
