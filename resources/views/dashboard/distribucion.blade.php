<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Distribución de Auditorías') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.link href="/dashboard">Dashboard</x-ui.breadcrumbs.link>
            <x-ui.breadcrumbs.separator />
            <x-ui.breadcrumbs.link active>{{ __('Distribución de Auditorías') }}</x-ui.breadcrumbs.link>
        </x-ui.breadcrumbs>

        <x-ui.container.table>
            <x-ui.table>
                <x-slot name="head">
                    <tr>
                        <th rowspan="2" class="text-center bg-blue-200">Auditoría Especial</th>
                        <th rowspan="2" class="text-center bg-blue-200">UAA</th>
                        <th rowspan="2" class="text-center bg-blue-200">Total de Auditorías</th>
                        <th colspan="2" class="text-center bg-gray-300">Auditorías Programadas</th>
                        <th colspan="{{ $numAcciones }}" class="text-center bg-gray-300">Tipo de Acción</th>
                        <th rowspan="2" class="text-center bg-blue-200">Total</th>
                    </tr>
                    <tr>
                        <th class="text-center bg-blue-200">Con Acciones</th>
                        <th class="text-center bg-blue-200">Sin Acciones</th>
                        @foreach($acciones as $accion)
                            <th class="text-center bg-blue-200">{{ $accion }}</th>
                        @endforeach
                    </tr>
                </x-slot>

                <x-slot name="body">
                    @php
                        $currentEspecial = null;
                        $especialName = '';
                        $subtotal = [
                            'total' => 0,
                            'con_acciones' => 0,
                            'sin_acciones' => 0,
                            'acciones' => array_fill_keys($acciones, 0),
                        ];
                        $grandTotal = [
                            'total' => 0,
                            'con_acciones' => 0,
                            'sin_acciones' => 0,
                            'acciones' => array_fill_keys($acciones, 0),
                        ];
                    @endphp

                    @foreach($reporte as $fila)
                        @if($currentEspecial !== $fila->{"Auditoria Especial"})
                            @if($currentEspecial !== null)
                                <!-- Subtotal -->
                                <tr style="background: #c6c6c6;" class="bg-gray-300 font-bold">
                                    <td colspan="2" class="text-right">Subtotal</td>
                                    <td>{{ $subtotal['total'] }}</td>
                                    <td>{{ $subtotal['con_acciones'] }}</td>
                                    <td>{{ $subtotal['sin_acciones'] }}</td>
                                    @foreach($acciones as $accion)
                                        <td>{{ $subtotal['acciones'][$accion] }}</td>
                                    @endforeach
                                    <td>{{ array_sum($subtotal['acciones']) }}</td>
                                </tr>
                                @php
                                    $subtotal = [
                                        'total' => 0,
                                        'con_acciones' => 0,
                                        'sin_acciones' => 0,
                                        'acciones' => array_fill_keys($acciones, 0),
                                    ];
                                @endphp
                            @endif

                            @php
                                $currentEspecial = $fila->{"Auditoria Especial"};
                                $especialName = $currentEspecial == 1 ? 'Cumplimiento Financiero' :
                                                ($currentEspecial == 2 ? 'Gasto Federalizado' :
                                                'Desempeño');
                            @endphp

                            @php
                                $currentEspecial = $fila->{"Auditoria Especial"};
                                $especialName = $currentEspecial == 1 ? 'Cumplimiento Financiero' :
                                                ($currentEspecial == 2 ? 'Gasto Federalizado' :
                                                'Desempeño');
                                $rowspan = $reporte->where('Auditoria Especial', $currentEspecial)->count();
                            @endphp

                            <tr>
                                <td rowspan="{{ $rowspan+1 }}" class="text-center font-bold bg-blue-200">
                                    {{ $especialName }}
                                </td>
                            </tr>

                        @endif

                        <!-- Datos -->
                        <tr>
                            <td>{{ $fila->UAA }}</td>
                            <td style="background: #c6c6c6;">{{ $fila->{"TOTAL AUDITORÍAS"} }}</td>
                            <td>{{ $fila->{"AUDITORÍAS CON ACCIONES"} }}</td>
                            <td>{{ $fila->{"AUDITORÍAS SIN ACCIONES"} }}</td>
                            @foreach($acciones as $accion)
                                <td>{{ $fila->$accion }}</td>
                                @php
                                    $subtotal['acciones'][$accion] += $fila->$accion;
                                    $grandTotal['acciones'][$accion] += $fila->$accion;
                                @endphp
                            @endforeach
                            @php
                                $totalFila = 0;

                                // Sumar dinámicamente las acciones específicas
                                foreach ($acciones as $accion) {
                                    $totalFila += (int)$fila->$accion;
                                }
                            @endphp

                            <td style="background: #c6c6c6;">{{ $totalFila }}</td>
                        </tr>

                        @php
                            $subtotal['total'] += $fila->{"TOTAL AUDITORÍAS"};
                            $subtotal['con_acciones'] += $fila->{"AUDITORÍAS CON ACCIONES"};
                            $subtotal['sin_acciones'] += $fila->{"AUDITORÍAS SIN ACCIONES"};
                            $grandTotal['total'] += $fila->{"TOTAL AUDITORÍAS"};
                            $grandTotal['con_acciones'] += $fila->{"AUDITORÍAS CON ACCIONES"};
                            $grandTotal['sin_acciones'] += $fila->{"AUDITORÍAS SIN ACCIONES"};
                        @endphp
                    @endforeach

                    <!-- Subtotal Final -->
                    <tr style="background: #c6c6c6;" class="bg-gray-300 font-bold">
                        <td colspan="2" class="text-right">Subtotal</td>
                        <td>{{ $subtotal['total'] }}</td>
                        <td>{{ $subtotal['con_acciones'] }}</td>
                        <td>{{ $subtotal['sin_acciones'] }}</td>
                        @foreach($acciones as $accion)
                            <td>{{ $subtotal['acciones'][$accion] }}</td>
                        @endforeach
                        @php
                            $totalSubtotal = 0;
                            foreach ($subtotal['acciones'] as $valor) {
                                $totalSubtotal += (int)$valor; // Aseguramos que el valor sea tratado como entero
                            }
                        @endphp

                        <td>{{ $totalSubtotal }}</td>

                    </tr>

                    <!-- Total -->
                    <tr class="bg-gray-500 text-white font-bold">
                        <td colspan="2" class="text-right">Total</td>
                        <td>{{ $grandTotal['total'] }}</td>
                        <td>{{ $grandTotal['con_acciones'] }}</td>
                        <td>{{ $grandTotal['sin_acciones'] }}</td>
                        @foreach($acciones as $accion)
                            <td>{{ $grandTotal['acciones'][$accion] }}</td>
                        @endforeach
                        @php
                            // Calcular el total sumando los valores acumulados en grandTotal
                            $totalGeneral = $grandTotal['con_acciones'] + $grandTotal['sin_acciones'];

                            // Sumar todas las acciones al total general
                            foreach ($acciones as $accion) {
                                $totalGeneral += (int)$grandTotal['acciones'][$accion];
                            }
                        @endphp
                        <td>{{ $totalGeneral/2 }}</td>
                    </tr>

                </x-slot>
            </x-ui.table>
        </x-ui.container.table>

        <x-ui.container.table>
            <x-ui.table>
                <x-slot name="head">
                    <tr>
                        <th class="text-center bg-blue-200">Dirección General de Seguimiento</th>
                        @foreach($acciones as $accion)
                            <th class="text-center bg-blue-200">{{ $accion }}</th>
                        @endforeach
                        <th class="text-center bg-blue-200">Total general</th>
                    </tr>
                </x-slot>

                <x-slot name="body">
                    @foreach($reporteSegundaTabla as $fila)
                        <tr>
                            <td>{{ $fila->{"Direccion General de Seguimiento"} }}</td>
                            @foreach($acciones as $accion)
                                <td>{{ $fila->$accion }}</td>
                            @endforeach
                            <td>{{ $fila->{"Total general"} }}</td>
                        </tr>
                    @endforeach
                </x-slot>
            </x-ui.table>
        </x-ui.container.table>
    </div>
</x-app-layout>
