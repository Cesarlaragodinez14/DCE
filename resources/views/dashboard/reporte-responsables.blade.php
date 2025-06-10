<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reporte de Responsables por DGSEG EF') }}
            <!-- Selectores para filtros -->
            <div class="ml-auto flex items-center space-x-4">
                <x-ui.filter-cp-en
                    :entregas="$entregas"
                    :cuentasPublicas="$cuentasPublicas"
                    route="tarjeta-auditor-esp.index"
                    defaultEntregaLabel="Seleccionar Entrega"
                    defaultCuentaPublicaLabel="Seleccionar Cuenta Pública"
                />
            </div>
        </h2>
    </x-slot>

    <style>
        .bg-blue-200, .bg-gray-300 {
            --tw-bg-opacity: 1;
            background-color: #243c64!important;
            color: #FFF!important;
        }
        .table-header {
            background-color: #243c64!important;
            color: #FFF!important;
            font-weight: bold;
            text-align: center;
            padding: 12px 8px;
            border: 1px solid #ccc;
        }
        .table-cell {
            text-align: center;
            padding: 8px;
            border: 1px solid #ccc;
        }
        .table-cell-left {
            text-align: left;
            padding: 8px;
            border: 1px solid #ccc;
        }
    </style>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.link href="/dashboard">Dashboard</x-ui.breadcrumbs.link>
            <x-ui.breadcrumbs.separator />
            <x-ui.breadcrumbs.link active>{{ __('Reporte de Responsables por DGSEG EF') }}</x-ui.breadcrumbs.link>
        </x-ui.breadcrumbs>

        @if($reporte->isEmpty() || (empty(request('cuenta_publica')) || empty(request('entrega'))))
            <div class="text-center p-6 bg-red-100 text-red-600">
                <p>No hay información disponible para la selección actual.</p>
                <p>Por favor selecciona una Entrega y una Cuenta Pública para generar el reporte.</p>
            </div>
        @else

        @php
            $totales = [
                'a_recibir' => 0,
                'entregados' => 0,
                'pendientes_entregar' => 0,
                'aceptados' => 0,
                'devueltos' => 0,
                'en_revision' => 0,
                'sin_revisar' => 0,
            ];
        @endphp

        <x-ui.container.table>
            <x-ui.table>
                <x-slot name="head">
                    <tr>
                        <th class="table-header">Responsables</th>
                        <th class="table-header">A Recibir</th>
                        <th class="table-header">Entregados</th>
                        <th class="table-header">Pendientes de Entregar</th>
                        <th class="table-header">Aceptados</th>
                        <th class="table-header">Devueltos</th>
                        <th class="table-header">En Revisión</th>
                        <th class="table-header">Sin Revisar</th>
                        <th class="table-header">% de Avance</th>
                    </tr>
                </x-slot>

                <x-slot name="body">
                    @foreach($reporte as $fila)
                        <tr>
                            <td class="table-cell-left">{{ $fila->responsable }}</td>
                            <td class="table-cell">{{ $fila->a_recibir }}</td>
                            <td class="table-cell">{{ $fila->entregados }}</td>
                            <td class="table-cell">{{ $fila->pendientes_entregar }}</td>
                            <td class="table-cell">{{ $fila->aceptados }}</td>
                            <td class="table-cell">{{ $fila->devueltos }}</td>
                            <td class="table-cell">{{ $fila->en_revision }}</td>
                            <td class="table-cell">{{ $fila->sin_revisar }}</td>
                            <td class="table-cell">{{ $fila->porcentaje_avance }}%</td>
                        </tr>

                        @php
                            $totales['a_recibir'] += $fila->a_recibir;
                            $totales['entregados'] += $fila->entregados;
                            $totales['pendientes_entregar'] += $fila->pendientes_entregar;
                            $totales['aceptados'] += $fila->aceptados;
                            $totales['devueltos'] += $fila->devueltos;
                            $totales['en_revision'] += $fila->en_revision;
                            $totales['sin_revisar'] += $fila->sin_revisar;
                        @endphp
                    @endforeach

                    @if($reporte->isNotEmpty())
                        @php
                            $porcentajeTotal = $totales['a_recibir'] > 0 ? round(($totales['aceptados'] / $totales['a_recibir']) * 100, 2) : 0;
                        @endphp
                        <tr class="bg-gray-500 text-white font-bold">
                            <td class="table-cell-left">TOTAL</td>
                            <td class="table-cell">{{ $totales['a_recibir'] }}</td>
                            <td class="table-cell">{{ $totales['entregados'] }}</td>
                            <td class="table-cell">{{ $totales['pendientes_entregar'] }}</td>
                            <td class="table-cell">{{ $totales['aceptados'] }}</td>
                            <td class="table-cell">{{ $totales['devueltos'] }}</td>
                            <td class="table-cell">{{ $totales['en_revision'] }}</td>
                            <td class="table-cell">{{ $totales['sin_revisar'] }}</td>
                            <td class="table-cell">{{ $porcentajeTotal }}%</td>
                        </tr>
                    @endif
                </x-slot>
            </x-ui.table>
        </x-ui.container.table>

        @php
            // Obtener los valores para los títulos dinámicos
            $entregaTexto = '';
            if (request('entrega')) {
                $entregaSeleccionada = $entregas->where('id', request('entrega'))->first();
                if ($entregaSeleccionada) {
                    $entregaTexto = $entregaSeleccionada->valor;
                }
            }
            
            $cuentaPublicaTexto = '';
            if (request('cuenta_publica')) {
                $cuentaPublicaSeleccionada = $cuentasPublicas->where('id', request('cuenta_publica'))->first();
                if ($cuentaPublicaSeleccionada) {
                    $cuentaPublicaTexto = $cuentaPublicaSeleccionada->valor;
                }
            }
            
            // Formatear fecha de hoy
            $fechaHoy = \Carbon\Carbon::now()->locale('es')->isoFormat('D [de] MMMM');
        @endphp

        <!-- Nueva tabla de Resumen General -->
        <div class="mt-8">
            <!-- Título principal centrado -->
            <h3 class="text-center text-lg font-bold mb-2">
                Avance de la {{ $entregaTexto }} entrega de expedientes de acción de la CP {{ $cuentaPublicaTexto }}
            </h3>
            
            <!-- Subtítulo con fecha -->
            <p class="text-center text-sm text-gray-600 mb-4">
                (Corte {{ $fechaHoy }} del presente año)
            </p>
            
            <!-- Subtítulo Resumen General -->
            <h4 class="text-center text-md font-semibold mb-4">Resumen General</h4>
            
            <!-- Tabla de resumen -->
            <div class="flex justify-center">
                <div class="w-1/2">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="table-header text-left">Concepto</th>
                                <th class="table-header">Totales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="table-cell-left font-medium">Total de Expedientes a Recibir</td>
                                <td class="table-cell font-bold">{{ $totales['a_recibir'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td class="table-cell-left font-medium">Expedientes Aceptados</td>
                                <td class="table-cell">{{ $totales['aceptados'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td class="table-cell-left font-medium">Expedientes Devueltos</td>
                                <td class="table-cell">{{ $totales['devueltos'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td class="table-cell-left font-medium">En Revisión</td>
                                <td class="table-cell">{{ $totales['en_revision'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td class="table-cell-left font-medium">Sin Revisar</td>
                                <td class="table-cell">{{ $totales['sin_revisar'] ?? 0 }}</td>
                            </tr>
                            <tr class="bg-gray-500 text-white font-bold">
                                <td class="table-cell-left">TOTAL</td>
                                <td class="table-cell">{{ ($totales['aceptados'] ?? 0) + ($totales['devueltos'] ?? 0) + ($totales['en_revision'] ?? 0) + ($totales['sin_revisar'] ?? 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if(request('entrega') == 18 && request('cuenta_publica') == 1)
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">
                        804 expedientes fueron devueltos a las UAA por cambions en el RIASF
                    </p>
                </div>
            @endif
        </div>

        <!-- Nueva tabla de Expedientes devueltos a la UAA -->
        <div class="mt-8">
            <h3 class="text-center text-lg font-bold mb-4">Expedientes devueltos a la UAA</h3>
            
            <x-ui.container.table>
                <x-ui.table>
                    <x-slot name="head">
                        <tr>
                            <th class="table-header">Responsables</th>
                            <th class="table-header">R</th>
                            <th class="table-header">PO</th>
                            <th class="table-header">SA</th>
                            <th class="table-header">Total General</th>
                        </tr>
                    </x-slot>

                    <x-slot name="body">
                        @php
                            $totalesDevueltos = [
                                'r' => 0,
                                'po' => 0,
                                'sa' => 0,
                                'total_general' => 0,
                            ];
                        @endphp

                        @foreach($reporteDevueltos as $fila)
                            <tr>
                                <td class="table-cell-left">{{ $fila->responsable }}</td>
                                <td class="table-cell">{{ $fila->r }}</td>
                                <td class="table-cell">{{ $fila->po }}</td>
                                <td class="table-cell">{{ $fila->sa }}</td>
                                <td class="table-cell">{{ $fila->total_general }}</td>
                            </tr>

                            @php
                                $totalesDevueltos['r'] += $fila->r;
                                $totalesDevueltos['po'] += $fila->po;
                                $totalesDevueltos['sa'] += $fila->sa;
                                $totalesDevueltos['total_general'] += $fila->total_general;
                            @endphp
                        @endforeach

                        @if($reporteDevueltos->isNotEmpty())
                            <tr class="bg-gray-500 text-white font-bold">
                                <td class="table-cell-left">TOTAL</td>
                                <td class="table-cell">{{ $totalesDevueltos['r'] }}</td>
                                <td class="table-cell">{{ $totalesDevueltos['po'] }}</td>
                                <td class="table-cell">{{ $totalesDevueltos['sa'] }}</td>
                                <td class="table-cell">{{ $totalesDevueltos['total_general'] }}</td>
                            </tr>
                        @endif
                    </x-slot>
                </x-ui.table>
            </x-ui.container.table>
        </div>

        @if(request('entrega') == 18 && request('cuenta_publica') == 1)
            <div class="mt-4 p-4 bg-blue-100 border border-blue-300 rounded-lg">
                <p class="text-blue-700 text-sm">
                    <strong>Nota:</strong> Este reporte aplica las exclusiones del RIASF (Reglamento Interno de la Auditoría Superior de la Federación):
                </p>
            </div>
        @endif

        @endif
    </div>
</x-app-layout> 