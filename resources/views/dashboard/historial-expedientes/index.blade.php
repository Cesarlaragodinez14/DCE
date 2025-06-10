<!-- resources/views/dashboard/historial-expedientes/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Histórico de la entrega - recepción de expedientes') }}
        </h2>
    </x-slot>

    <!-- CSS Variables -->
    @include('dashboard.historial-expedientes.assets.styles')

    <!-- Error Alert -->
    <div id="errorAlert" class="fixed top-4 right-4 z-50 hidden animate-fade-in max-w-md">
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-lg flex items-start">
            <div class="flex-shrink-0 mt-0.5">
                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3 flex-grow">
                <p class="text-sm font-medium">Se encontraron errores:</p>
                <ul id="errorList" class="mt-1 text-sm list-disc list-inside"></ul>
            </div>
            <button id="closeErrorBtn" type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex h-8 w-8">
                <span class="sr-only">Cerrar</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="successToast" class="fixed bottom-4 right-4 z-50 hidden animate-fade-in">
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-lg flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p id="successMessage" class="text-sm font-medium">Operación completada correctamente</p>
            </div>
            <button id="closeSuccessBtn" type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex h-8 w-8">
                <span class="sr-only">Cerrar</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <x-ui.breadcrumbs class="mb-6">
                <x-ui.breadcrumbs.link href="/dashboard" class="hover:text-primary-color transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </x-ui.breadcrumbs.link>
                <x-ui.breadcrumbs.separator />
                <x-ui.breadcrumbs.link active>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    {{ __('Histórico de la Entrega - Recepción de Expedientes') }}
                </x-ui.breadcrumbs.link>
            </x-ui.breadcrumbs>
            
            <!-- Filtros Card - Mejorado -->
            <div class="card mb-5">
                <div class="card-header">
                    <div class="filter-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtros de búsqueda
                    </div>
                    <button type="button" id="toggleFilters" class="btn btn-sm btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <span>Ocultar</span>
                    </button>
                </div>
                <div class="card-body" id="filtersContainer">
                    <form id="filtrosForm" action="{{ route('programacion-historial.index') }}" method="GET">
                        <!-- Fila de filtros -->
                        <div class="filter-row">
                            <!-- Clave de Acción -->
                            <div class="filter-col">
                                <label for="clave_accion" class="form-label">
                                    Clave de Acción
                                </label>
                                <input
                                    type="text"
                                    name="clave_accion"
                                    id="clave_accion"
                                    value="{{ request('clave_accion', $claveAccion) }}"
                                    class="form-input"
                                    placeholder="Ej. ACC-123"
                                >
                            </div>

                            <!-- Firmado por -->
                            <div class="filter-col">
                                <label for="generado_por" class="form-label">
                                    Firmado por:
                                </label>
                                <select 
                                    name="generado_por" 
                                    id="generado_por" 
                                    class="form-select"
                                    onchange="submitFilterForm()"
                                >
                                    <option value="">-- Seleccione Usuario --</option>
                                    @foreach($generados as $usr)
                                        <option value="{{ $usr->name }}"
                                            @if(request('generado_por', $generadoPor) == $usr->name) selected @endif>
                                            {{ $usr->name }} - ({{ $usr->total }}) Expedientes Firmados
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tipo de Movimiento -->
                            <div class="filter-col">
                                <label for="estado" class="form-label">
                                    Tipo de Movimiento:
                                </label>
                                <select 
                                    name="estado" 
                                    id="estado" 
                                    class="form-select"
                                    onchange="submitFilterForm()"
                                >
                                    <option value="">-- Seleccione Tipo de Movimiento --</option>
                                    @foreach($estados as $est)
                                        <option value="{{ $est }}"
                                            @if(request('estado', $estado) == $est) selected @endif>
                                            {{ $est }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Fecha de Recepción -->
                            <div class="filter-col">
                                <label for="fecha_recepcion" class="form-label">
                                    Fecha de Entrega - Recepción:
                                </label>
                                <select 
                                    name="fecha_recepcion" 
                                    id="fecha_recepcion" 
                                    class="form-select"
                                    onchange="submitFilterForm()"
                                >
                                    <option value="">-- Seleccione Fecha --</option>
                                    @foreach($fechasRecepcion as $f)
                                        @php
                                            $fechaForm = \Carbon\Carbon::parse($f)->format('Y-m-d');
                                        @endphp
                                        <option value="{{ $fechaForm }}"
                                            @if(request('fecha_recepcion', $fechaRecepcion) == $fechaForm) selected @endif>
                                            {{ $fechaForm }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="filter-actions">
                            <a href="{{ route('programacion-historial.index') }}" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Limpiar filtros
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABLA DE RESULTADOS -->
            <div class="card">
                <div class="card-header">
                    <div class="filter-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Histórico de la Entrega - Recepción de Expedientes
                    </div>
                    <div class="text-xs text-gray-500">
                        Total: <span class="font-semibold">{{ $movimientos->count() }}</span> registros
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                        @if($movimientos->count())
                            <table class="data-table" id="historial-table">
                                <thead>
                                    <tr>
                                        <th>Clave de Acción</th>
                                        <th>Firmado por</th>
                                        <th>Tipo de Movimiento</th>
                                        <th>Fecha de Movimiento</th>
                                        <th>PDF</th>
                                        <th>Fecha de Entrega - Recepción</th>
                                        <th>Estado Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movimientos as $mov)
                                        <tr>
                                            <!-- Clave de Acción -->
                                            <td class="font-semibold">
                                                <div class="flex items-center">
                                                    <span class="text-primary">{{ $mov->clave_accion ?? 'N/A' }}</span>
                                                </div>
                                            </td>

                                            <!-- Responsable -->
                                            <td>
                                                {{ $mov->responsable ?? 'N/A' }}
                                            </td>

                                            <!-- Estado Historial -->
                                            <td>
                                                <span class="badge {{ strpos($mov->hist_estado ?? '', 'Firmado') !== false ? 'badge-success' : (strpos($mov->hist_estado ?? '', 'Recibido') !== false ? 'badge-primary' : 'badge-secondary') }}">
                                                    {{ $mov->hist_estado ?? 'N/A' }}
                                                </span>
                                            </td>

                                            <!-- Fecha Estado -->
                                            <td>
                                                {{ $mov->fecha_estado ?? 'N/A' }}
                                            </td>

                                            <!-- PDF -->
                                            <td>
                                                @if(!empty($mov->hist_pdf))
                                                    <a href="{{ asset('storage/' . $mov->hist_pdf) }}"
                                                    target="_blank"
                                                    class="btn btn-sm btn-primary">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        Ver PDF
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">No disponible</span>
                                                @endif
                                            </td>

                                            <!-- Fecha de Recepción -->
                                            <td>
                                                @if(!empty($mov->fecha_real_entrega))
                                                    {{ \Carbon\Carbon::parse($mov->fecha_real_entrega)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-gray-400">---</span>
                                                @endif
                                            </td>

                                            <!-- Estado Actual -->
                                            <td>
                                                <span class="badge {{ strpos($mov->estado_actual ?? '', 'Firmado') !== false ? 'badge-success' : (strpos($mov->estado_actual ?? '', 'Recibido') !== false ? 'badge-primary' : 'badge-warning') }}">
                                                    {{ $mov->estado_actual ?? 'N/A' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <h3 class="empty-state-title">No se encontraron movimientos</h3>
                                <p class="empty-state-description">Intente con diferentes criterios de búsqueda o verifique que existan registros para el período seleccionado.</p>
                                
                                <a href="{{ route('programacion-historial.index') }}" class="btn btn-primary mt-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Limpiar filtros
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    @push('scripts')
        <!-- jQuery (si no está ya incluido en la aplicación) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- DataTables (opcional) -->
        <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
        
        <!-- Select2 (opcional) -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
        
        <!-- Script personalizado para esta página -->
        @include('dashboard.historial-expedientes.assets.scripts')
    @endpush
</x-app-layout>
