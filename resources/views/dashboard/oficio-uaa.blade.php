<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Envío de oficio a las UAA para la entrega de los expedientes de acción') }}
            <!-- Selectores para filtros -->
            <div class="ml-auto flex items-center space-x-4">
                <!-- Selector de Entrega -->
                <form id="filterForm" method="GET" action="{{ route('dashboard.oficio-uaa') }}" class="flex space-x-2 items-center">
                    <!-- Selector de Entrega -->
                    <select name="entrega" onchange="this.form.submit()" class="form-select rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @foreach($entregas as $entrega)
                            <option value="{{ $entrega->id }}" {{ request('entrega') == $entrega->id ? 'selected' : '' }}>
                                {{ $entrega->valor }}
                            </option>
                        @endforeach
                    </select>
        
                    <!-- Selector de Cuenta Pública -->
                    <select name="cuenta_publica" onchange="this.form.submit()" class="form-select rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @foreach($cuentasPublicas as $cuenta)
                            <option value="{{ $cuenta->id }}" {{ request('cuenta_publica') == $cuenta->id ? 'selected' : '' }}>
                                {{ $cuenta->valor }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.link href="/dashboard">Dashboard</x-ui.breadcrumbs.link>
            <x-ui.breadcrumbs.separator />
            <x-ui.breadcrumbs.link active>{{ __('Envío de oficio a las UAA para la entrega de los expedientes de acción') }}</x-ui.breadcrumbs.link>
        </x-ui.breadcrumbs>

        @if($reporte->isEmpty() && empty($reporteSegundaTabla))
            <div class="text-center p-6 bg-red-100 text-red-600">
                <p>No hay información disponible para la selección actual.</p>
            </div>
        @else

            <x-ui.container.table>
                <x-ui.table>
                    <x-slot name="head">
                        <tr class="bg-gray-700 text-white">
                            <th rowspan="2" class="text-center">Auditoría Especial / Dirección General</th>
                            <th rowspan="2" class="text-center">UAA</th>
                            <th colspan="{{ $numAcciones }}" class="text-center">Tipo de Acción</th>
                            <th rowspan="2" class="text-center">Total a entregar en los 20 días</th>
                            <th rowspan="2" class="text-center">Mínimo para entregar por día (5%)</th>
                        </tr>
                        <tr class="bg-gray-300">
                            @foreach($acciones as $accion)
                                <th class="text-center">{{ $accion }}</th>
                            @endforeach
                        </tr>
                    </x-slot>

                    <x-slot name="body">
                        @php
                            $currentEspecial = null;
                            $subtotal = [
                                'acciones' => array_fill_keys($acciones, 0),
                                'total_dias' => 0,
                                'min_dia' => 0
                            ];
                            $grandTotal = [
                                'acciones' => array_fill_keys($acciones, 0),
                                'total_dias' => 0,
                                'min_dia' => 0
                            ];
                        @endphp

                        @foreach($reporte as $fila)
                            @if($currentEspecial !== $fila->{"Auditoria Especial"})
                                @if($currentEspecial !== null)
                                    <!-- Subtotal -->
                                    <tr class="bg-gray-200 font-bold">
                                        <td colspan="2" class="text-right">Subtotal</td>
                                        @foreach($acciones as $accion)
                                            <td class="text-center">{{ $subtotal['acciones'][$accion] }}</td>
                                        @endforeach
                                        <td class="text-center">{{ $subtotal['total_dias'] }}</td>
                                        <td class="text-center">{{ $subtotal['min_dia'] }}</td>
                                    </tr>
                                    @php
                                        $subtotal = [
                                            'acciones' => array_fill_keys($acciones, 0),
                                            'total_dias' => 0,
                                            'min_dia' => 0
                                        ];
                                    @endphp
                                @endif

                                @php
                                    $currentEspecial = $fila->{"Auditoria Especial"};
                                    $especialName = $currentEspecial == 1 ? 'Auditoría Especial de Cumplimiento Financiero' :
                                                    ($currentEspecial == 2 ? 'Auditoría Especial del Gasto Federalizado' :
                                                    'Auditoría Especial de Desempeño');
                                    $rowspan = $reporte->where('Auditoria Especial', $currentEspecial)->count();
                                @endphp

                                <tr>
                                    <td rowspan="{{ $rowspan + 1 }}" class="text-center font-bold bg-blue-200">
                                        {{ $especialName }}
                                    </td>
                                </tr>

                            @endif

                            @php
                                $totalEntregar = 0;
                                foreach ($acciones as $accion) {
                                    $totalEntregar += (int)$fila->$accion;
                                }
                                $minimoDia = round($totalEntregar * 0.05);
                            @endphp

                            <!-- Datos -->
                            <tr>
                                <td>{{ $fila->UAA }}</td>
                                @foreach($acciones as $accion)
                                    <td class="text-center">{{ $fila->$accion }}</td>
                                    @php
                                        $subtotal['acciones'][$accion] += $fila->$accion;
                                        $grandTotal['acciones'][$accion] += $fila->$accion;
                                    @endphp
                                @endforeach
                                <td class="text-center">{{ $totalEntregar }}</td>
                                <td class="text-center">{{ $minimoDia }}</td>
                            </tr>

                            @php
                                $subtotal['total_dias'] += $totalEntregar;
                                $subtotal['min_dia'] += $minimoDia;
                                $grandTotal['total_dias'] += $totalEntregar;
                                $grandTotal['min_dia'] += $minimoDia;
                            @endphp
                        @endforeach

                        <!-- Subtotal Final -->
                        <tr class="bg-gray-200 font-bold">
                            <td colspan="2" class="text-right">Subtotal</td>
                            @foreach($acciones as $accion)
                                <td class="text-center">{{ $subtotal['acciones'][$accion] }}</td>
                            @endforeach
                            <td class="text-center">{{ $subtotal['total_dias'] }}</td>
                            <td class="text-center">{{ round($subtotal['min_dia'], 2) }}</td>
                        </tr>

                        <!-- Total -->
                        <tr class="bg-gray-500 text-white font-bold">
                            <td colspan="2" class="text-right">Total</td>
                            @foreach($acciones as $accion)
                                <td class="text-center">{{ $grandTotal['acciones'][$accion] }}</td>
                            @endforeach
                            <td class="text-center">{{ $grandTotal['total_dias'] }}</td>
                            <td class="text-center">{{ round($grandTotal['min_dia'], 2) }}</td>
                        </tr>
                    </x-slot>
                </x-ui.table>
            </x-ui.container.table>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
                <x-ui.container.table>
                    <x-ui.table>
                        <x-slot name="head">
                            <tr class="bg-gray-700 text-white">
                                <th colspan="5" class="text-center">Oficio para la entrega de los expedientes de acción</th>
                            </tr>
                            <tr class="bg-blue-100 text-white">
                                <th class="text-center">No. de oficio</th>
                                <th class="text-center">Fecha de envío</th>
                                <th class="text-center">Oficio</th>
                                <th class="text-center">Fecha del acuse</th>
                                <th class="text-center">Acuse</th>
                            </tr>
                        </x-slot>
        
                        <x-slot name="body">
                            <tr>
                                <td class="text-center p-4 flex space-x-2">
                                    <!-- Campo para las letras -->
                                    <input 
                                        type="text" 
                                        name="prefix" 
                                        id="prefix" 
                                        class="w-1/3 p-2 border border-gray-300 rounded" 
                                        placeholder="AESI" 
                                        maxlength="4"
                                        oninput="this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '');"
                                        required
                                        style="width: 57px;"
                                    >
                                    
                                    <!-- Separador fijo "/" -->
                                    <span class="self-center">/</span>
                                    
                                    <!-- Campo para los cuatro dígitos -->
                                    <input 
                                        type="text" 
                                        name="codigo" 
                                        id="codigo" 
                                        class="w-1/3 p-2 border border-gray-300 rounded" 
                                        placeholder="0000" 
                                        maxlength="4"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                        required
                                        style="width: 61px;"
                                    >
                                    
                                    <!-- Separador fijo "/" -->
                                    <span class="self-center">/</span>
                                    
                                    <!-- Selector de año -->
                                    <select 
                                        style="width: 80px;"
                                        name="year" 
                                        id="year" 
                                        class="w-1/3 p-2 border border-gray-300 rounded"
                                        required
                                    >
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                        <!-- Agrega más opciones según sea necesario -->
                                    </select>
                                </td>
                                
                        
                                <!-- Fecha de envío -->
                                <td class="text-center p-4">
                                    <input 
                                        type="date" 
                                        name="fecha_envio" 
                                        class="w-full p-2 border border-gray-300 rounded" 
                                    >
                                </td>
                        
                                <!-- Cargar oficio (Archivo PDF) -->
                                <td class="text-center p-4">
                                    <input 
                                        type="file" 
                                        name="archivo_oficio" 
                                        accept="application/pdf" 
                                        class="w-full p-2 border border-gray-300 rounded"
                                    >
                                </td>
                        
                                <!-- Fecha del acuse -->
                                <td class="text-center p-4">
                                    <input 
                                        type="date" 
                                        name="fecha_acuse" 
                                        class="w-full p-2 border border-gray-300 rounded" 
                                    >
                                </td>
                        
                                <!-- Cargar Acuse (Archivo PDF) -->
                                <td class="text-center p-4">
                                    <input 
                                        type="file" 
                                        name="archivo_acuse" 
                                        accept="application/pdf" 
                                        class="w-full p-2 border border-gray-300 rounded"
                                    >
                                </td>
                            </tr>
                        </x-slot>
                        
                    </x-ui.table>
                </x-ui.container.table>
            </div>
        @endif
    </div>
</x-app-layout>
