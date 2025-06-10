<div class="py-10 sm:px-6 lg:px-8 space-y-4">
    <!-- Incluir los estilos -->
    @include('livewire.dashboard.pdf-histories.assets.styles')

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

    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.link href="/dashboard">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </div>
        </x-ui.breadcrumbs.link>
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link active>
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Histórico de listas de verificación
            </div>
        </x-ui.breadcrumbs.link>
    </x-ui.breadcrumbs> 

    @if(session()->has('success'))
        <div class="p-4 mb-4 text-sm bg-green-50 border-l-4 border-green-500 rounded-lg shadow-sm transform transition duration-300 ease-in-out animate-fade-in-down" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium text-green-700">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 mb-4 text-sm bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm transform transition duration-300 ease-in-out animate-fade-in-down" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium text-red-700">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="filter-action-container">
        <div class="filter-action-header">
            <div class="filter-action-title">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>Filtros y Búsqueda</span>
            </div>
            <div class="filter-counter">
                <span>{{ $pdfHistories->total() }} registros</span>
            </div>
        </div>
        
        <div class="filter-action-content">
            <div class="filter-action-body">
                <!-- Controles de búsqueda y filtros -->
                <div class="filter-controls">
                    <div class="search-control">
                        <div class="search-icon-container">
                            <svg class="search-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input
                            wire:model.debounce.300ms="search"
                            type="text"
                            class="search-input"
                            placeholder="Buscar por Clave de Acción, Cuenta Pública o Entrega..."
                        />
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="action-controls">
                    <a href="{{ route('dashboard.all-auditorias.index') }}" class="action-button action-export">
                        <div class="action-button-content">
                            <svg class="action-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span class="action-text">Volver a los Expedientes de Acción</span>
                        </div>
                        <div class="action-shine"></div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Histórico de listas de verificación -->
    <div class="premium-table-container">
        <div class="premium-table-wrapper">
            <table class="premium-table">
                <thead class="premium-table-head">
                    <tr>
                        <th class="premium-th sortable" wire:click="sortBy('clave_de_accion')">
                            <div class="th-content">
                                <span>Clave de Acción</span>
                                @if($sortField === 'clave_de_accion')
                                    <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                    
                        <th class="premium-th sortable" wire:click="sortBy('cat_entregas.valor')">
                            <div class="th-content">
                                <span>Entrega</span>
                                @if($sortField === 'cat_entregas.valor')
                                    <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                    
                        <th class="premium-th sortable" wire:click="sortBy('auditorias.estatus_checklist')">
                            <div class="th-content">
                                <span>Estatus de la revisión</span>
                                @if($sortField === 'auditorias.estatus_checklist')
                                    <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                    
                        <th class="premium-th sortable" wire:click="sortBy('cat_uaa.valor')">
                            <div class="th-content">
                                <span>UAA</span>
                                @if($sortField === 'cat_uaa.valor')
                                    <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                    
                        <th class="premium-th sortable" wire:click="sortBy('cat_dgseg_ef.valor')">
                            <div class="th-content">
                                <span>DG SEG</span>
                                @if($sortField === 'cat_dgseg_ef.valor')
                                    <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                    
                        <th class="premium-th sortable" wire:click="sortBy('auditorias.titulo')">
                            <div class="th-content">
                                <span>Titulo de la Auditoría</span>
                                @if($sortField === 'auditorias.titulo')
                                    <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>

                        <th class="premium-th sortable" wire:click="sortBy('generated_by')">
                            <div class="th-content">
                                <span>Lista de verificación generada por</span>
                                @if($sortField === 'generated_by')
                                    <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('created_at')">
                            <div class="th-content">
                                <span>Fecha de Generación</span>
                                @if($sortField === 'created_at')
                                    <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                    
                        <th class="premium-th sortable" wire:click="sortBy('pdf_path')">
                            <div class="th-content">
                                <span>PDF</span>
                                @if($sortField === 'pdf_path')
                                    <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                    </tr>
                </thead>
                
                <tbody class="premium-table-body">
                    @forelse ($pdfHistories as $history)
                        <tr class="premium-row" wire:loading.class.delay="opacity-50">
                            <!-- Clave de Acción -->
                            <td class="premium-td highlight-cell">
                                <div class="cell-content">
                                    {{ $history->clave_de_accion }}
                                </div>
                            </td>
                
                            <!-- Entrega -->
                            <td class="premium-td">
                                <div class="cell-content">
                                    {{ $history->auditoria->catEntrega->valor ?? 'N/A' }}
                                </div>
                            </td>
                
                            <td class="premium-td">
                                @php 
                                    $statusToPrint = "Desconocido"; // Valor por defecto en caso de que no coincida
                                    $statusClass = "status-badge-neutral";
                                    
                                    if (preg_match('/\/[\dA-Za-z\-]+-(Conformidad-UAA|Devuelto|Aceptado)-\d{14}\.pdf$/', $history->pdf_path, $matches)) {
                                        $statusToPrint = str_replace('-', ' ', $matches[1]); // Extrae el estado y reemplaza guiones
                                        
                                        if ($statusToPrint == 'Aceptado') {
                                            $statusClass = "status-badge-success";
                                        } else if ($statusToPrint == 'Devuelto') {
                                            $statusClass = "status-badge-warning";
                                        }
                                    }
                                @endphp
                                <div class="status-badge {{ $statusClass }}">
                                    <div class="status-icon">
                                        @if($statusToPrint == 'Aceptado')
                                            <svg class="status-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($statusToPrint == 'Devuelto')
                                            <svg class="status-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        @else
                                            <svg class="status-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="status-text">{{ $statusToPrint }}</span>
                                </div>
                            </td>
                
                            <!-- Siglas DG UAA -->
                            <td class="premium-td">
                                <div class="cell-content">
                                    <span class="primary-text">{{ $history->auditoria->catUaa->valor ?? 'N/A' }}</span>
                                    <span class="secondary-text">{{ $history->auditoria->catUaa->nombre ?? '' }}</span>
                                </div>
                            </td>
                
                            <!-- DG DGSEGxEF -->
                            <td class="premium-td">
                                <div class="cell-content">
                                    {{ $history->auditoria->catDgsegEf->valor ?? 'N/A' }}
                                </div>
                            </td>
                
                            <!-- Título -->
                            <td class="premium-td">
                                <div class="cell-content truncate-text" title="{{ $history->auditoria->titulo ?? 'N/A' }}">
                                    {{ $history->auditoria->titulo ?? 'N/A' }}
                                </div>
                            </td>

                            <td class="premium-td">
                                <div class="cell-content">
                                    {{ $history->user->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="premium-td">
                                <div class="cell-content">
                                    {{ $history->created_at->format('Y-m-d H:i:s') }}
                                </div>
                            </td>
                
                            <!-- PDF -->
                            <td class="premium-td">
                                <a href="{{ Storage::disk('public')->url($history->pdf_path) }}" target="_blank" class="action-button action-export" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">
                                    <div class="action-button-content">
                                        <svg class="action-icon" style="width: 0.875rem; height: 0.875rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span class="action-text">Ver PDF</span>
                                    </div>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="premium-td">
                                <div class="empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="empty-state-title">No se encontraron PDFs generados.</h3>
                                    <p class="empty-state-description">Intenta con diferentes términos de búsqueda o verifica que existan registros en el sistema.</p>
                                    
                                    <button onclick="window.location.href='{{ route('dashboard.all-auditorias.index') }}'" class="empty-state-button">
                                        <svg class="empty-state-button-icon" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                        <span>Volver a los Expedientes</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="premium-pagination">
            {{ $pdfHistories->links() }}
        </div>
    </div>

    <!-- Incluir los scripts -->
    @include('livewire.dashboard.pdf-histories.assets.scripts')
</div>
