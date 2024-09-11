<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Recepción de expedientes / UAA - DCE') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
                
                <!-- Filtros -->
                <div class="flex items-center space-x-4 mb-4">
                    <!-- Selector de AE -->
                    <x-ui.input.select label="AE:" name="ae" onchange="this.form.submit()">
                        <option value="">Seleccionar AE</option>
                        @foreach($auditoriasEspeciales as $ae)
                            <option value="{{ $ae->id }}" {{ request('ae') == $ae->id ? 'selected' : '' }}>
                                {{ $ae->valor }}
                            </option>
                        @endforeach
                    </x-ui.input.select>

                    <!-- Selector de DG -->
                    <x-ui.input.select label="DG:" name="dg" onchange="this.form.submit()">
                        <option value="">Seleccionar DG</option>
                        @foreach($direccionesGenerales as $dg)
                            <option value="{{ $dg->id }}" {{ request('dg') == $dg->id ? 'selected' : '' }}>
                                {{ $dg->valor }}
                            </option>
                        @endforeach
                    </x-ui.input.select>
                </div>

                <!-- Tabla de conteo por CP, Entrega, AE, DG, Fecha Programada y Responsable -->
                <x-ui.table>
                    <x-slot name="head">
                        <tr>
                            <th class="text-center bg-blue-200">CP</th>
                            <th class="text-center bg-blue-200">Entrega</th>
                            <th class="text-center bg-blue-200">AE</th>
                            <th class="text-center bg-blue-200">Dirección General</th>
                            <th class="text-center bg-blue-200">Fecha Programada</th>
                            <th class="text-center bg-blue-200">Responsable</th>
                            <th class="text-center bg-blue-200">Total Entregas</th>
                        </tr>
                    </x-slot>

                    <x-slot name="body">
                        @foreach($entregasContadas as $entrega)
                            <tr>
                                <td class="text-center">{{ $entrega->CP }}</td>
                                <td class="text-center">{{ $entrega->entrega }}</td>
                                <td class="text-center">{{ $entrega->AE }}</td>
                                <td class="text-center">{{ $entrega->DG }}</td>
                                <td class="text-center">{{ $entrega->fecha_entrega }}</td>
                                <td class="text-center">{{ $entrega->responsable }}</td>
                                <td class="text-center">{{ $entrega->total_entregas }}</td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>

                <!-- Mensaje de días hábiles restantes -->
                <div class="mt-4 text-sm text-blue-500">
                    Restan {{ $dias_habiles }} días hábiles
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
