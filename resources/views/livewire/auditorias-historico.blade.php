<div>
    <!-- Listado de Auditorías -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h3 class="text-2xl font-semibold text-gray-800 mb-6">Consulta de observaciones a los expedientes de acción</h3>

        <!-- Barra de Búsqueda -->
        <div class="mb-6">
            <input
                type="text"
                wire:model.debounce.300ms="search"
                placeholder="“Buscar por clave de acción..."
                class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
            >
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <!-- Encabezados de las columnas -->
                        <th class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            ID
                        </th>
                        @foreach($auditoriaFields as $field => $fieldName)
                            <th class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                {{ $fieldName }}
                            </th>
                        @endforeach
                        <th class="px-6 py-3 border-b-2 border-gray-300 bg-gray-200 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="text-sm font-normal text-gray-700">
                    @forelse ($auditorias as $auditoria)
                        <tr class="hover:bg-gray-100 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 border-b border-gray-200">{{ $auditoria->id }}</td>
                            @foreach($auditoriaFields as $field => $fieldName)
                                <td class="px-6 py-4 border-b border-gray-200">
                                    {{ is_bool($auditoria->$field) ? ($auditoria->$field ? 'Sí' : 'No') : $auditoria->$field }}
                                </td>
                            @endforeach
                            <td class="px-6 py-4 border-b border-gray-200">
                                <button
                                    wire:click="loadHistorial({{ $auditoria->id }})"
                                    class="text-indigo-600 hover:text-indigo-900 font-medium"
                                >
                                    Ver Historial
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($auditoriaFields) + 2 }}" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron auditorías.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $auditorias->links() }}
        </div>
    </div>

    <!-- Modal para Mostrar Historial -->
    @if($selectedAuditoriaId)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <!-- Fondo Oscuro -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <!-- Trick para centrar el modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Contenido del Modal -->
                <div class="inline-block align-middle bg-white rounded-lg text-left overflow-y-auto shadow-xl transform transition-all sm:my-8 sm:max-w-5xl sm:w-full duration-300 ease-in-out" style="max-height: 98vh;">
                    <div class="relative bg-white px-6 pt-6 pb-4 sm:p-6 sm:pb-4">
                        <!-- Botón de Cierre en la Esquina Superior Derecha -->
                        <div class="absolute top-3 right-3">
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Cerrar</span>
                                <!-- Icono X (SVG) -->
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mt-3 w-full">
                            <h3 class="text-2xl font-semibold text-gray-800 mb-6" id="modal-title">
                                Historial del expediente de acción no, {{ $selectedAuditoriaId }}
                            </h3>

                            <!-- Indicador de Carga -->
                            @if($isLoading)
                                <div class="flex justify-center items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span class="text-gray-600 text-lg">Cargando...</span>
                                </div>
                            @else
                                <!-- Historial de Cambios en Auditoría -->
                                <div class="mb-8">
                                    <h4 class="text-xl font-semibold text-gray-800 mb-4">“Cambios en la acción</h4>
                                    @if(count($dataForAuditoria) > 0)
                                        <x-historial-table :historiales="$dataForAuditoria" />
                                    @else
                                        <p class="text-md text-gray-600">Sin cambios registrados en auditoría.</p>
                                    @endif
                                </div>

                                <!-- Historial de Cambios en Checklist Apartados -->
                                <div>
                                    <h4 class="text-xl font-semibold text-gray-800 mb-4">Cambios en Checklist Apartados</h4>
                                    @if(count($dataForChecklistApartados) > 0)
                                        <x-historial-table :historiales="$dataForChecklistApartados" />
                                    @else
                                        <p class="text-md text-gray-600">Sin cambios registrados en checklist apartados.</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Botón de Cierre en la Parte Inferior del Modal -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="closeModal" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-lg font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-lg">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
