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

    <!-- Header Information (this will repeat on each page) -->
    <div class="header-info principal">
        <table>
            <tr>
                <td>
                    <img src="{{ public_path('img/asf.png') }}" alt="Logo ASF" style="position:absolute; left: 10px; top: 10px; max-width: 200px;">
                </td>
                <td style="text-align: right;">
                    <b>FORMATO 2</b><br> 
                    <b>Auditoría Especial de Seguimiento, Informes e Investigación</b><br>
                    <b>Departamento de Control de Expedientes</b><br><br>
                    @if ($formato == "01")
                        <b>Lista de verificación del Expediente de la Recomendación </b><br>
                    @elseif ($formato == "03")
                        <b>Lista de verificación del Expediente de la Solicitud de Aclaración</b><br>
                    @elseif ($formato == "06")
                        <b>Lista de verificación del Expediente de Pliego de Observaciones{{ $esSuperveniente ? ' Superveniente' : '' }}</b><br>
                    @elseif ($formato == "07")
                        <b>Lista de verificación del Expediente de la Recomendación al Desempeño</b><br>
                    @else
                        <b>Lista de verificación del Expediente de la Recomendación </b><br>
                    @endif
                    <small>({{ $auditoria->catEntrega->valor ?? '' }} C.P. {{ $auditoria->catCuentaPublica->valor ?? '' }})</small>
                </td>
            </tr>
        </table>
    </div>

    <!-- Main content (will adjust with header fixed) -->
    <div class="header-info" style="margin-top: -40px">
        <table>
            <tr>
                <td style="text-align: left; vertical-align: top">
                    <h2 style="text-align: center"><b><u>Área que entrega</u></b></h2>
                    <p><strong>Auditoría Especial: </strong>{{ $auditoria->catSiglasAuditoriaEspecial->descripcion ?? '' }}</p>
                    <p><strong>Dirección General de la UAA: </strong>{{ $auditoria->catUaa->nombre ?? '' }}</p>
                    <p><strong>Título de la Auditoría: </strong>{{ $auditoria->titulo }}</p>
                    <p><strong>Número de Auditoría: </strong>{{ $auditoria->catAuditoriaEspecial->valor }}</p>
                    <p><strong>Clave de la Acción: </strong>{{ $auditoria->catClaveAccion->valor ?? '' }}</p>
                    <p><strong>Nombre del Ente de la Acción o Recomendación: </strong>{{ $auditoria->catEnteDeLaAccion->valor ?? '' }}</p>
                </td>
                <td style="text-align: left; vertical-align: top">
                    <h2 style="text-align: center"><b><u>Área que recibe y revisa</u></b></h2>
                    <p><strong>Dirección General: </strong>{{ $auditoria->catDgsegEf->valor ?? '' }}</p>
                    <p><strong>Dirección de Área: </strong>{{ $auditoria->direccion_de_area ?? '' }}</p>
                    <p><strong>Subdirección: </strong>{{ $auditoria->sub_direccion_de_area ?? '' }}</p>
                    <p><strong>Fecha: </strong>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Checklist Table -->
    <table>
        <thead>
            <tr>
                <th style="text-align: center">N°</th>
                <th style="text-align: center; width: 500px;">Apartado / Subapartado</th>
                <th style="text-align: center">¿Aplica?</th>
                <th style="text-align: center">¿Obligatorio?</th>
                <th style="text-align: center">¿Se Integra?</th>
                <th style="text-align: center">Observaciones de Seguimiento</th>
                <th style="text-align: center">Comentarios UAA</th>
            </tr>
        </thead>
        <tbody>
            <!-- Render apartados and subapartados recursively -->
            @foreach ($apartados as $apartado)
                @include('partials.apartado_row_pdf', ['apartado' => $apartado, 'iteration' => $loop->iteration, 'parent' => null])
            @endforeach
        </tbody>
    </table>
    <div>
        <table>
            <tr>
                <td colspan="2" style="text-align:center">
                    <h2 style="text-align: center"><strong><u>Comentarios</u></strong></h2>
                    {{$auditoria->comentarios}}
                    <br>
                    <br>
                    <br>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    @if($auditoria->estatus_checklist == "Aceptado")
                        <h2 style="text-align: center"><strong><u>ACEPTA</u></strong></h2>
                    @elseif($auditoria->estatus_checklist == "Devuelto")
                        <h2 style="text-align: center"><strong><u>DEVUELVE</u></strong></h2>
                    @else
                        <h2 style="text-align: center"><strong><u>SIN ASIGNAR</u></strong></h2>
                    @endif
                </td>
            </tr>
            <tr>
                <td>
                    <h2 style="text-align: center"><strong><u>Servidor Público del área auditora que entrega el expediente</u></strong></h2>
                    <b>Nombre: {{$auditoria->auditor_nombre}}</b><br>
                    <b>Puesto: {{$auditoria->auditor_puesto}}</b><br>
                    <b>Clave de Acción: {{ $auditoria->catClaveAccion->valor}}</b>
                    <br>
                </td>
                <td>
                    <h2 style="text-align: center"><strong><u>Servidor Público de seguimiento que revisa, acepta o devuelve el expediente</u></strong></h2>
                    <b>Nombre: {{$auditoria->seguimiento_nombre}} </b><br>
                    <b>Puesto: {{$auditoria->seguimiento_puesto}} </b><br>
                    <b>Clave de Acción: {{ $auditoria->catClaveAccion->valor}}</b>
                    <br>
                </td>
            </tr>
        </table>
    </div>
    @if (auth()->user()->roles->pluck('name')[0] === 'Jefe de Departamento' || auth()->user()->roles->pluck('name')[0] === 'admin') 
     <!-- Incluir el parcial hash_info.blade.php para el hash de seguimiento -->
     @include('pdf.hash_info', [
        'qrCodeDataUri' => $qrCodeDataUri,
        'hash' => $hash,
        'currentUserName' => $user->name,
        'currentUserRole' => $auditoria->seguimiento_puesto, // Puedes ajustar según necesites
        'email' => $user->email,
        'ipAddress' => $ipAddress,
        'generatedAt' => \Carbon\Carbon::parse($generatedAt)->format('d/m/Y H:i:s'),
    ])
    @endif
</body>
</html>
