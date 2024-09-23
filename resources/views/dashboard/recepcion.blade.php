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
                            <th class="text-center bg-blue-200"></th>
                            <th class="text-center bg-blue-200">No.</th>
                            <th class="text-center bg-blue-200">CP</th>
                            <th class="text-center bg-blue-200">Entrega</th>
                            <th class="text-center bg-blue-200">AE</th>
                            <th class="text-center bg-blue-200">Dirección General</th>
                            <th class="text-center bg-blue-200">Total Entregas</th>
                            <th class="text-center bg-blue-200">Fecha Programada</th>
                            <th class="text-center bg-blue-200">Fecha de entrega</th>
                            <th class="text-center bg-blue-200">Expedientes entregados</th>
                            <th class="text-center bg-blue-200">Legajos entregados</th>
                            <th class="text-center bg-blue-200">Acuse</th>
                        </tr>
                    </x-slot>
                    
                    <x-slot name="body">
                        @foreach($entregasContadas as $index => $entregaGroup)
                            <tr onclick="toggleDetails({{ $index }})" style="cursor: pointer;">
                                <td class="text-center">
                                    <span id="toggle-icon-{{ $index }}" class="toggle-icon">+</span>
                                </td>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $entregaGroup->CP }}</td>
                                <td class="text-center">{{ $entregaGroup->entrega }}</td>
                                <td class="text-center">{{ $entregaGroup->AE }}</td>
                                <td class="text-center">{{ $entregaGroup->DG }}</td>
                                <td class="text-center">{{ $entregaGroup->total_entregas }}</td>
                                <td class="text-center">{{ $entregaGroup->fecha_entrega }}</td>
                                <td class="text-center">{{ $entregaGroup->responsable }}</td>
                                <td class="text-center">0</td>
                                <td class="text-center">0</td>
                                <td class="text-center">0</td>
                            </tr>
                            <tr id="details-{{ $index }}" style="display: none;">
                                <td colspan="12">
                                    @if($entregaGroup->entregas && count($entregaGroup->entregas) > 0)
                                        <div class="p-4 bg-gray-100">
                                            <strong>Expedientes programados:</strong>
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>CP</th>
                                                        <th>Entrega</th>
                                                        <th>Auditoria</th>
                                                        <th>Ente de Acción</th>
                                                        <th>Clave de Acción</th>
                                                        <th>Tipo de Acción</th>
                                                        <th>Legajos</th>
                                                        <th>Fecha Programada</th>
                                                        <th>Fecha de Entrega</th>
                                                        <th>¿Entregado?</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($entregaGroup->entregas as $entrega)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $entrega->CP }}</td>
                                                            <td>{{ $entrega->entrega }}</td>
                                                            <td>{{ $entrega->clave_de_accion }}</td>
                                                            <td>{{ $entrega->numero_legajos }}</td>
                                                            <td>{{ $entrega->fecha_entrega }}</td>
                                                            <td>0</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <ul class="list-disc pl-5">
                                                
                                            </ul>
                                        </div>
                                    @else
                                        <div class="p-4 bg-gray-100">
                                            <strong>No hay expedientes programados para esta entrega.</strong>
                                        </div>
                                    @endif
                                </td>
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
<!-- Add the JavaScript function -->
<script>
    function toggleDetails(index) {
        var row = document.getElementById('details-' + index);
        var icon = document.getElementById('toggle-icon-' + index);
        if (row.style.display === 'none' || row.style.display === '') {
            row.style.display = 'table-row';
            icon.textContent = '-'; // Update this if using SVG icons
        } else {
            row.style.display = 'none';
            icon.textContent = '+'; // Update this if using SVG icons
        }
    }
</script>
