<div class="mx-auto sm:px-6 lg:px-8">
    @include('livewire.dashboard.all-auditorias.auditorias_assets.styles')
    
    <!-- Notificaciones: Toast de Error y Éxito -->
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
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex h-8 w-8">
                <span class="sr-only">Cerrar</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Header Section -->
    <div class="mb-6">
        <x-ui.breadcrumbs class="mb-4">
            <x-ui.breadcrumbs.link href="/dashboard" class="text-gray-600 hover:text-indigo-600 transition-colors">
                Dashboard
            </x-ui.breadcrumbs.link>
            <x-ui.breadcrumbs.separator />
            <x-ui.breadcrumbs.link active class="font-medium">
                {{ __('crud.allAuditorias.collectionTitle') }}
            </x-ui.breadcrumbs.link>
        </x-ui.breadcrumbs>

        <!-- Notification Messages -->
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
    </div>
    <!-- Barra de filtros y acciones premium con CSS personalizado -->
    <div class="filter-action-container">
        <div class="filter-action-header">
            <div class="filter-action-title">
                <ion-icon name="options-outline" class="filter-icon"></ion-icon>
                <span>Filtros y Acciones</span>
            </div>
            <div class="filter-counter">
                <span>{{ $allAuditorias->total() }} registros</span>
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
                            placeholder="Buscar en: {{ __('crud.allAuditorias.collectionTitle') }}..."
                        />
                    </div>

                    <div class="filter-selectors">
                        <x-ui.filter-cp-en
                            :entregas="$entrega"
                            :cuentasPublicas="$cuentaPublica"
                            route="dashboard.all-auditorias.index"
                            defaultEntregaLabel="Seleccionar Entrega"
                            defaultCuentaPublicaLabel="Seleccionar Cuenta Pública"
                        />
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="action-controls">
                    @role('admin')
                        <button wire:click="exportExcel" class="action-button action-export" wire:loading.class="loading" wire:loading.attr="disabled">
                            <div class="action-button-content">
                                <svg class="action-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span class="action-text" wire:loading.remove wire:target="exportExcel">Exportar</span>
                                <span class="action-text" wire:loading wire:target="exportExcel">
                                    <svg class="loading-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Exportando...
                                </span>
                            </div>
                            <div class="action-shine"></div>
                        </button>
                    @endrole
                </div>
            </div>
        </div>
    </div>
    {{-- Delete Modal --}}
    <x-ui.modal.confirm wire:model="confirmingDeletion">
        <x-slot name="title"> 
            <div class="flex items-center text-red-600">
                <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                {{ __('Borrar') }} 
            </div>
        </x-slot>

        <x-slot name="content"> 
            <p class="text-gray-700">{{ __('¿Deseas confirmar esta acción?') }}</p>
            <p class="text-sm text-gray-500 mt-2">Esta acción no se puede deshacer.</p>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-3">
                <x-ui.button
                    wire:click="$toggle('confirmingDeletion')"
                    wire:loading.attr="disabled"
                    class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                >
                    {{ __('Cancelar') }}
                </x-ui.button>

                <x-ui.button.danger
                    wire:click="delete({{ $deletingAuditorias }})"
                    wire:loading.attr="disabled"
                    class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
                >
                    <span wire:loading.remove wire:target="delete">{{ __('Borrar') }}</span>
                    <span wire:loading wire:target="delete" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Borrando...
                    </span>
                </x-ui.button.danger>
            </div>
        </x-slot>
    </x-ui.modal.confirm>
    {{-- Tabla Premium con Efectos Avanzados --}}
    <div class="premium-table-container">
        <div class="premium-table-wrapper">
            <table class="premium-table">
                <thead class="premium-table-head">
                    <tr>
                        <th class="premium-th action-column">
                            <div class="th-content">
                                <span>{{ __('Editar') }}</span>
                            </div>
                        </th>
                        <th class="premium-th status-column">
                            <div class="th-content">
                                <span>{{ __('Estatus de la Revisión') }}</span>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('clave_de_accion')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.clave_de_accion.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th">
                            <div class="th-content">
                                <span>Tipo de Acción</span>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('cuenta_publica')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.cuenta_publica.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('entrega')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.entrega.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('siglas_auditoria_especial')">
                            <div class="th-content">
                                <span>AE</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('uaa')">
                            <div class="th-content">
                                <span>UAA</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('titulo')">
                            <div class="th-content">
                                <span>Titulo de la Auditoría</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('numero_de_auditoria')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.numero_de_auditoria.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('ente_de_la_accion')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.ente_de_la_accion.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('dgseg_ef')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.dgseg_ef.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('sub_direccion_de_area')">
                            <div class="th-content">
                                <span>{{ __('crud.allAuditorias.inputs.sub_direccion_de_area.label') }}</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('nombre_sub_director_de_area')">
                            <div class="th-content">
                                <span>Nombre SD SEG</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th sortable" wire:click="sortBy('jefe_de_departamento')">
                            <div class="th-content">
                                <span>JD SEG</span>
                                <svg class="sort-icon" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </th>
                        <th class="premium-th admin-column">
                            <div class="th-content">
                                <span>Admin</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="premium-table-body">
                    @forelse ($allAuditorias as $auditorias)
                    <tr class="premium-row" wire:loading.class.delay="opacity-50">
                        <!-- Acciones Rápidas -->
                        <td class="premium-td">
                            <div class="action-buttons-container">
                                <a href="{{ route('auditorias.apartados', $auditorias->id) }}" 
                                class="quick-action-button edit-action"
                                title="Editar Apartados">
                                    <svg viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                
                                @if(Auth::user()->id === 1 || Auth::user()->id === 2)
                                <button onclick="openResetModal({{ $auditorias->id }}, '{{ addslashes($auditorias->clave_de_accion) }}')" 
                                        class="quick-action-button reset-action"
                                        title="Resetear Firmas">
                                    <svg viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Estado y Botones de Descarga - Versión Mejorada -->
                        <td class="premium-td">
                            <div class="status-card">
                                <!-- Badge de estado con diseño mejorado -->
                                <div class="status-badge 
                                    {{ $auditorias->estatus_checklist == 'Aceptado' ? 'status-badge-success' : 
                                    ($auditorias->estatus_checklist == 'Devuelto' ? 'status-badge-warning' : 'status-badge-neutral') }}">
                                    <div class="status-icon">
                                        @if($auditorias->estatus_checklist == 'Aceptado')
                                            <svg class="status-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($auditorias->estatus_checklist == 'Devuelto')
                                            <svg class="status-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        @else
                                            <svg class="status-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="status-text">{{ $auditorias->estatus_checklist }}</span>
                                </div>
                                
                                <!-- Botones de Acción Mejorados -->
                                <div class="action-buttons">
                                    @if ($auditorias->estatus_checklist == "Aceptado" && empty($auditorias->archivo_uua))
                                        <a href="{{ route('auditorias.pdf', $auditorias->id) }}" class="action-button action-signature">
                                            <div class="action-icon">
                                                <svg class="action-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <span class="action-text">Con Firma de Seguimiento</span>
                                        </a>
                                    @elseif($auditorias->estatus_checklist == "Aceptado" && !empty($auditorias->archivo_uua))
                                        <a href="{{ route('auditorias.downloadUua', $auditorias->id) }}" class="action-button action-completed">
                                            <div class="action-icon">
                                                <svg class="action-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </div>
                                            <span class="action-text">Completado</span>
                                        </a>
                                    @elseif ($auditorias->estatus_checklist == "Devuelto")
                                        <a href="/auditorias/{{ $auditorias->id }}/pdf" class="action-button action-returned">
                                            <div class="action-icon">
                                                <svg class="action-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </div>
                                            <span class="action-text">Devuelto</span>
                                        </a>
                                    @else
                                        <div class="action-button action-disabled">
                                            <div class="action-icon">
                                                <svg class="action-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </div>
                                            <span class="action-text">Sin PDF generado</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <!-- Datos de la auditoría con estilos mejorados -->
                        <td class="premium-td highlight-cell">
                            <div class="cell-content">
                                {{ $auditorias->clave_de_accion }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catSiglasTipoAccion->valor }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catCuentaPublica->valor }}
                            </div>
                        </td>
                        
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catEntrega->valor }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catSiglasAuditoriaEspecial->valor }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                <span class="primary-text">{{ $auditorias->catUaa->valor }}</span>
                                <span class="secondary-text">{{ $auditorias->catUaa->nombre }}</span>
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content truncate-text" title="{{ $auditorias->titulo }}">
                                {{ $auditorias->titulo }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catAuditoriaEspecial->valor }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catEnteDeLaAccion->valor }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->catDgsegEf->valor }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->sub_direccion_de_area }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->nombre_sub_director_de_area }}
                            </div>
                        </td>
                        
                        <td class="premium-td">
                            <div class="cell-content">
                                {{ $auditorias->jefe_de_departamento }}
                            </div>
                        </td>

                        <!-- Botones de Admin -->
                        <td class="premium-td">
                            @role('admin')
                            <div class="admin-actions">
                                @if(Auth::user()->id === 1 || Auth::user()->id === 2 || Auth::user()->id === 3)
                                    @can('update', $auditorias)
                                    <a wire:navigate href="{{ route('dashboard.all-auditorias.edit', $auditorias) }}" 
                                    class="admin-button edit-button">
                                        <svg class="admin-button-icon" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        <span class="admin-button-text">Editar</span>
                                    </a>
                                    @endcan 
                                    
                                    @can('delete', $auditorias)
                                    <button wire:click="confirmDeletion({{ $auditorias->id }})" 
                                            class="admin-button delete-button">
                                        <svg class="admin-button-icon" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="admin-button-text">Borrar</span>
                                    </button>
                                    @endcan
                                @endif
                            </div>
                            @endrole
                        </td>
                    </tr>
                    @empty
                    <!-- Estado Vacío Mejorado -->
                    <tr>
                        <td colspan="16" class="premium-td-empty">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="empty-state-title">No se encontró: {{ __('crud.allAuditorias.collectionTitle') }}</h3>
                                <p class="empty-state-description">Intenta con diferentes términos de búsqueda o quita los filtros aplicados para ver más resultados.</p>
                                
                                <button onclick="resetFilters()" class="empty-state-button">
                                    <svg class="empty-state-button-icon" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Restablecer filtros</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación Premium -->
        <div class="premium-pagination">
            {{ $allAuditorias->links() }}
        </div>
    </div>
    <!-- Reset Modal mejorado para mejor UX -->
    <div
        id="resetModal"
        class="fixed inset-0 z-50 hidden overflow-y-auto modal-overlay"
        aria-labelledby="resetModalTitle"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="bg-white rounded-xl overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-50 border border-gray-200">
                <div class="bg-red-50 px-4 py-3 border-b border-red-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-full p-2">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg leading-6 font-medium text-red-800" id="resetModalTitle">
                            Confirmar Reseteo de Clave de Acción
                        </h3>
                    </div>
                </div>
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <p class="text-gray-700 mb-4">
                                Estás a punto de reiniciar las firmas de la clave de acción:
                            </p>
                            <div class="bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-3 mb-4">
                                <p class="text-indigo-800 font-medium text-lg text-center" id="modalClaveAccion"></p>
                            </div>
                            
                            <div class="mt-3 p-4 bg-yellow-50 border border-yellow-100 rounded-lg flex items-start">
                                <svg class="h-6 w-6 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <p class="text-sm text-yellow-800 font-medium">Advertencia:</p>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        Esta acción es irreversible y eliminará todas las firmas existentes para este expediente. Los usuarios tendrán que volver a firmar todos los documentos.
                                    </p>
                                </div>
                            </div>
                            
                            <form id="resetForm" method="POST" action="" class="mt-5">
                                @csrf
                                @method('POST')
                                <div class="mt-4">
                                    <label for="confirmation_text" class="block text-sm font-medium text-gray-700 mb-2">
                                        Para confirmar, escribe exactamente:
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            name="confirmation_text"
                                            id="confirmation_text"
                                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 transition duration-150 pl-4 pr-4 py-3"
                                            placeholder='Deseo reiniciar esta clave de acción'
                                            required
                                        />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none opacity-0 confirmation-check text-green-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 pl-1">Frase requerida: <span class="font-mono bg-gray-100 px-1 py-0.5 rounded text-gray-700">"Deseo reiniciar esta clave de acción"</span></p>
                                </div>
                                <input type="hidden" name="auditoria_id" id="auditoria_id" value="">
                                <input type="hidden" name="clave_accion" id="clave_accion" value="">
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button
                        type="button"
                        id="confirmResetBtn"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                        onclick="submitReset()"
                        disabled
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Confirmar Reseteo
                    </button>
                    <button
                        type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-150"
                        onclick="closeResetModal()"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('livewire.dashboard.all-auditorias.auditorias_assets.scripts')
    
    <script>
        // Inicializar manejadores de eventos para los toasts
        document.addEventListener('DOMContentLoaded', function() {
            // Cerrar el toast de error con el botón de cierre
            const closeErrorBtn = document.getElementById('closeErrorBtn');
            if (closeErrorBtn) {
                closeErrorBtn.addEventListener('click', function() {
                    hideError();
                });
            }
            
            // Cerrar el toast de éxito con el botón de cierre
            const closeSuccessBtn = document.querySelector('#successToast button');
            if (closeSuccessBtn) {
                closeSuccessBtn.addEventListener('click', function() {
                    hideSuccess();
                });
            }
        });
    </script>
</div>