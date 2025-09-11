<div>
    <!-- Listado de Auditorías -->
    <div class="bg-white shadow-lg rounded-xl p-8 mb-8">
        <div class="flex items-center mb-8">
            <div class="flex-shrink-0">
                <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <ion-icon name="document-text-outline" class="text-2xl text-white"></ion-icon>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-3xl font-bold text-gray-900">Consultar Historial de Modificaciones</h3>
                <p class="text-lg text-gray-600 mt-1">Revisa las observaciones derivadas en la revisión de los expedientes de acción</p>
            </div>
        </div>

        <!-- Barra de Búsqueda Mejorada -->
        <div class="mb-8">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <ion-icon name="search-outline" class="text-xl text-gray-400"></ion-icon>
                </div>
                <input
                    type="text"
                    wire:model.debounce.300ms="search"
                    placeholder="Buscar por clave de acción, responsable, comentarios..."
                    class="block w-full pl-10 pr-12 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out text-lg"
                >
                @if($search)
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button wire:click="$set('search', '')" class="text-gray-400 hover:text-gray-600">
                            <ion-icon name="close-outline" class="text-xl"></ion-icon>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto shadow-sm rounded-lg border border-gray-200">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <!-- Encabezados de las columnas -->
                        <th class="px-6 py-4 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <ion-icon name="pricetag-outline" class="text-sm text-indigo-500 mr-2"></ion-icon>
                                ID
                            </div>
                        </th>
                        @foreach($auditoriaFields as $field => $fieldName)
                            <th class="px-6 py-4 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                {{ $fieldName }}
                            </th>
                        @endforeach
                        <th class="px-6 py-4 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <ion-icon name="time-outline" class="text-sm text-emerald-500 mr-2"></ion-icon>
                                Historial de modificaciones
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="text-sm font-normal text-gray-700 divide-y divide-gray-200">
                    @forelse ($auditorias as $auditoria)
                        <tr class="hover:bg-blue-50 transition duration-200 ease-in-out">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $auditoria->id }}
                                </span>
                            </td>
                            @foreach($auditoriaFields as $field => $fieldName)
                                <td class="px-6 py-4">
                                    @if(is_bool($auditoria->$field))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $auditoria->$field ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $auditoria->$field ? 'Sí' : 'No' }}
                                        </span>
                                    @else
                                        <span class="text-gray-900">{{ $auditoria->$field ?: 'N/A' }}</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-6 py-4">
                                <button
                                    wire:click="loadHistorial({{ $auditoria->id }})"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-105"
                                >
                                    <ion-icon name="time-outline" class="text-base mr-2"></ion-icon>
                                    Ver Historial
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($auditoriaFields) + 2 }}" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <ion-icon name="document-text-outline" class="text-6xl text-gray-400 mb-4"></ion-icon>
                                    <p class="text-xl font-medium text-gray-500 mb-2">No se encontraron auditorías</p>
                                    <p class="text-gray-400">Intenta ajustar los criterios de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-8">
            {{ $auditorias->links() }}
        </div>
    </div>

    <!-- Modal para Mostrar Historial -->
    @if($selectedAuditoriaId)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <!-- Fondo Oscuro -->
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

                <!-- Trick para centrar el modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Contenido del Modal -->
                <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-7xl sm:w-full duration-300 ease-in-out" style="max-height: 95vh;">
                    <div class="relative bg-white px-8 pt-8 pb-6">
                        <!-- Botón de Cierre en la Esquina Superior Derecha -->
                        <div class="absolute top-6 right-6 z-10">
                            <button wire:click="closeModal" class="bg-gray-100 hover:bg-gray-200 rounded-full p-2 transition-colors duration-200">
                                <span class="sr-only">Cerrar</span>
                                <ion-icon name="close-outline" class="text-2xl text-gray-600"></ion-icon>
                            </button>
                        </div>

                        <!-- Header del Modal -->
                        <div class="mb-8">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                        <ion-icon name="time-outline" class="text-2xl text-white"></ion-icon>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-3xl font-bold text-gray-900" id="modal-title">
                                        Historial de Modificaciones
                                    </h3>
                                    <p class="text-lg text-gray-600 mt-1">
                                        Lista de verificación No. {{ $selectedAuditoriaId }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido Scrolleable -->
                        <div class="overflow-y-auto" style="max-height: 70vh;">
                            <!-- Indicador de Carga -->
                            @if($isLoading)
                                <div class="flex justify-center items-center py-16">
                                    <div class="flex flex-col items-center">
                                        <div class="animate-spin h-12 w-12 text-indigo-600 mb-4">
                                            <ion-icon name="reload-outline" class="text-5xl"></ion-icon>
                                        </div>
                                        <span class="text-gray-600 text-xl font-medium">Cargando historial...</span>
                                        <span class="text-gray-400 text-sm mt-2">Por favor espera un momento</span>
                                    </div>
                                </div>
                            @else
                                <!-- Historial de Cambios en Auditoría -->
                                <div class="mb-12">
                                    <div class="flex items-center mb-6">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                                                <ion-icon name="document-text-outline" class="text-xl text-white"></ion-icon>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-2xl font-bold text-gray-900">Cambios en la Acción</h4>
                                            <p class="text-gray-600">Modificaciones realizadas en los datos principales de la auditoría</p>
                                        </div>
                                    </div>
                                    @if(count($dataForAuditoria) > 0)
                                        <x-historial-table :historiales="$dataForAuditoria" />
                                    @else
                                        <div class="bg-gray-50 rounded-xl p-8 text-center">
                                            <ion-icon name="document-text-outline" class="text-6xl text-gray-400 mx-auto mb-4"></ion-icon>
                                            <p class="text-lg font-medium text-gray-500 mb-2">Sin cambios registrados</p>
                                            <p class="text-gray-400">No se han realizado modificaciones en la auditoría</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Historial de Cambios en Checklist Apartados -->
                                <div>
                                    <div class="flex items-center mb-6">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                                <ion-icon name="folder-outline" class="text-xl text-white"></ion-icon>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-2xl font-bold text-gray-900">Cambios en Checklist por Apartados</h4>
                                            <p class="text-gray-600">Modificaciones realizadas en los diferentes apartados del checklist</p>
                                        </div>
                                    </div>
                                    
                                    @if(count($apartadosWithChanges) > 0)
                                        <div class="space-y-8">
                                            @foreach($apartadosWithChanges as $apartadoId => $apartadoData)
                                                <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 border border-gray-200">
                                                    <!-- Header del Apartado -->
                                                    <div class="flex items-center mb-4">
                                                        <div class="flex-shrink-0">
                                                            <div class="h-8 w-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                                <ion-icon name="document-outline" class="text-base text-white"></ion-icon>
                                                            </div>
                                                        </div>
                                                        <div class="ml-3">
                                                            <h5 class="text-lg font-bold text-gray-900">{{ $apartadoData['nombre'] }}</h5>
                                                            <p class="text-sm text-gray-600">
                                                                {{ count($apartadoData['cambios']) }} 
                                                                {{ count($apartadoData['cambios']) === 1 ? 'modificación registrada' : 'modificaciones registradas' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Tabla de cambios para este apartado -->
                                                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                                        <div class="overflow-x-auto">
                                                            <table class="min-w-full bg-white">
                                                                <thead>
                                                                    <tr class="bg-gradient-to-r from-gray-100 to-gray-200">
                                                                        <th class="px-4 py-3 border-b border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                                            <div class="flex items-center">
                                                                                <ion-icon name="calendar-outline" class="text-xs text-blue-500 mr-2"></ion-icon>
                                                                                Fecha
                                                                            </div>
                                                                        </th>
                                                                        <th class="px-4 py-3 border-b border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                                            <div class="flex items-center">
                                                                                <ion-icon name="person-outline" class="text-xs text-green-500 mr-2"></ion-icon>
                                                                                Usuario
                                                                            </div>
                                                                        </th>
                                                                        <th class="px-4 py-3 border-b border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                                            <div class="flex items-center">
                                                                                <ion-icon name="document-outline" class="text-xs text-amber-500 mr-2"></ion-icon>
                                                                                Campo
                                                                            </div>
                                                                        </th>
                                                                        <th class="px-4 py-3 border-b border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                                            <div class="flex items-center">
                                                                                <ion-icon name="time-outline" class="text-xs text-red-500 mr-2"></ion-icon>
                                                                                Antes
                                                                            </div>
                                                                        </th>
                                                                        <th class="px-4 py-3 border-b border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                                            <div class="flex items-center">
                                                                                <ion-icon name="checkmark-circle-outline" class="text-xs text-emerald-500 mr-2"></ion-icon>
                                                                                Después
                                                                            </div>
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-gray-200">
                                                                    @foreach($apartadoData['cambios'] as $cambio)
                                                                        <tr class="hover:bg-blue-50 transition duration-200 ease-in-out">
                                                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                                    {{ $cambio['date'] }}
                                                                                </span>
                                                                            </td>
                                                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                                                <div class="flex items-center">
                                                                                    <div class="flex-shrink-0 h-6 w-6">
                                                                                        <div class="h-6 w-6 rounded-full bg-gradient-to-r from-green-400 to-blue-500 flex items-center justify-center">
                                                                                            <span class="text-xs font-medium text-white">
                                                                                                {{ strtoupper(substr($cambio['user'], 0, 1)) }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="ml-2">
                                                                                        <p class="text-xs font-medium text-gray-900">{{ $cambio['user'] }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-amber-100 text-amber-800">
                                                                                    {{ $cambio['field'] }}
                                                                                </span>
                                                                            </td>
                                                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                                                <div class="max-w-xs">
                                                                                    @if(strlen($cambio['before']) > 40)
                                                                                        <div class="relative group">
                                                                                            <div class="comentario-observacionmb-2 shadow-lg max-w-sm">
                                                                                                {{ $cambio['before'] }}
                                                                                                <div class="absolute top-full left-4 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @else
                                                                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800">
                                                                                            {{ $cambio['before'] }}
                                                                                        </span>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                                                <div class="max-w-xs">
                                                                                    @if(strlen($cambio['after']) > 40)
                                                                                        <div class="relative group">
                                                                                            <div class="comentario-observacionmb-2 shadow-lg max-w-sm">
                                                                                                {{ $cambio['after'] }}
                                                                                                <div class="absolute top-full left-4 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @else
                                                                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-100 text-emerald-800">
                                                                                            {{ $cambio['after'] }}
                                                                                        </span>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="bg-gray-50 rounded-xl p-8 text-center">
                                            <ion-icon name="folder-outline" class="text-6xl text-gray-400 mx-auto mb-4"></ion-icon>
                                            <p class="text-lg font-medium text-gray-500 mb-2">Sin cambios registrados</p>
                                            <p class="text-gray-400">No se han realizado modificaciones en los apartados del checklist</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Botón de Cierre en la Parte Inferior del Modal -->
                    <div class="bg-gray-50 px-8 py-4 sm:flex sm:flex-row-reverse border-t border-gray-200">
                        <button wire:click="closeModal" type="button" class="w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-lg font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto transition duration-150 ease-in-out">
                            <ion-icon name="close-outline" class="text-xl mr-2"></ion-icon>
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
