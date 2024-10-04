<!-- resources/views/dashboard/recepcion.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Recepción de Expedientes / UAA - DCE') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensaje de días hábiles restantes -->
            <div class="mb-4 text-sm text-blue-500 text-right">
                Restan {{ $dias_habiles }} días hábiles
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
                <!-- Filtros -->
                <form method="GET" action="{{ route('dashboard.expedientes.recepcion') }}" class="mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Selector de AE -->
                        <div>
                            <label for="ae" class="block text-sm font-medium text-gray-700">AE:</label>
                            <select id="ae" name="ae" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" onchange="this.form.submit()">
                                <option value="">Seleccionar AE</option>
                                @foreach($auditoriasEspeciales as $ae)
                                    <option value="{{ $ae->id }}" {{ request('ae') == $ae->id ? 'selected' : '' }}>
                                        {{ $ae->valor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Selector de DG -->
                        <div>
                            <label for="dg" class="block text-sm font-medium text-gray-700">DG:</label>
                            <select id="dg" name="dg" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" onchange="this.form.submit()">
                                <option value="">Seleccionar DG</option>
                                @foreach($direccionesGenerales as $dg)
                                    <option value="{{ $dg->id }}" {{ request('dg') == $dg->id ? 'selected' : '' }}>
                                        {{ $dg->valor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                <!-- Tabla principal -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="entregas-table">
                        <thead class="">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider"></th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">No.</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">CP</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Entrega</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">AE</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Dirección General</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Total Entregas</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Fecha Programada</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Responsable</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Expedientes Entregados</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Legajos Entregados</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Acuse</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($entregasContadas as $index => $entregaGroup)
                                <tr class="hover:bg-gray-50" onclick="toggleDetails({{ $index }})" style="cursor: pointer;">
                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                        <svg id="toggle-icon-{{ $index }}" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 transition-transform duration-200 transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path id="icon-path-{{ $index }}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $entregaGroup->CP }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $entregaGroup->entrega }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $entregaGroup->AE }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $entregaGroup->DG }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">{{ $entregaGroup->total_entregas }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($entregaGroup->fecha_entrega)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $entregaGroup->responsable }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">0</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">0</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">0</td>
                                </tr>
                                <tr id="details-{{ $index }}" class="details-row hidden">
                                    <td colspan="12">
                                        @if($entregaGroup->entregas && count($entregaGroup->entregas) > 0)
                                            <div class="bg-gray-50 rounded-lg">
                                                <h3 class="text-lg font-semibold mb-4 text-gray-800" style="padding: 20px">Expedientes Programados</h3>
                                                <div class="overflow-x-auto" style="text-align: center;">
                                                    <table class="min-w-full divide-y divide-gray-200" 
                                                        style="
                                                            margin: 0 auto;
                                                            width: 100%;
                                                        ">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">No.</th>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Clave de Acción</th>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Título</th>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Legajos</th>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Fecha de Entrega</th>
                                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">¿Entregado?</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            @foreach($entregaGroup->entregas as $key => $entrega)
                                                                <tr>
                                                                    <td class="px-4 py-2 whitespace-nowrap">{{ $key + 1 }}</td>
                                                                    <td class="px-4 py-2 whitespace-nowrap">{{ $entrega->clave_de_accion }}</td>
                                                                    <td class="px-4 py-2 whitespace-nowrap">{{ $entrega->titulo }}</td>
                                                                    <td class="px-4 py-2 whitespace-nowrap text-center">{{ $entrega->numero_legajos }}</td>
                                                                    <td class="px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($entrega->fecha_entrega)->format('d/m/Y') }}</td>
                                                                    <td class="px-4 py-2 whitespace-nowrap text-center">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @else
                                            <div class="p-4 bg-yellow-50 rounded-lg">
                                                <p class="text-yellow-700"><strong>No hay expedientes programados para esta entrega.</strong></p>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación (si es necesario) -->
                {{-- @if($entregasContadas->hasPages())
                    <div class="mt-4">
                        {{ $entregasContadas->links() }}
                    </div>
                @endif --}}
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Scripts -->
<script>
    function toggleDetails(index) {
        var row = document.getElementById('details-' + index);
        var icon = document.getElementById('icon-path-' + index);
        if (row.classList.contains('hidden')) {
            row.classList.remove('hidden');
            // Rotar el icono
            icon.setAttribute('d', 'M20 12H4');
        } else {
            row.classList.add('hidden');
            // Volver al icono original
            icon.setAttribute('d', 'M12 4v16m8-8H4');
        }
    }
</script>

<!-- Estilos adicionales -->
<style>
    /* Estilos para la transición de las filas */
    .details-row {
        transition: all 0.3s ease-in-out;
    }

    /* Estilos para el icono */
    .toggle-icon {
        transition: transform 0.3s ease-in-out;
    }

    /* Estilos para hover en las filas */
    tr:hover {
        background-color: #c6c6c6;
    }

    thead{
        background: currentColor;
    }
</style>
