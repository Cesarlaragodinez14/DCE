@php
    // Obtener los roles del usuario actual
    $userRoles = auth()->user()->roles->pluck('name')->toArray();
    
    // Determinar si el usuario es admin (editor completo)
    $isAdmin = in_array('admin', $userRoles);
    
    // Determinar el tipo de acceso
    $isReadOnlyMode = !$isAdmin;
@endphp

<x-app-layout>
    <x-slot name="header" style="display: none;">
    
    </x-slot>

    <!-- CSS Variables -->
    @include('dashboard.recepcion_assets.styles')

    <!-- Error Alert -->
    <div id="errorAlert" class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 alert alert-error hidden">
        <div class="flex justify-between items-center w-full">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-error-dark" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            <ul id="errorList" class="list-disc list-inside"></ul>
            </div>
            <button id="closeErrorBtn" class="text-error-dark font-bold text-xl leading-none hover:text-error-dark/70 transition-colors ml-3">&times;</button>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="successToast" class="fixed bottom-4 right-4 z-50 alert alert-success hidden max-w-xs">
        <div class="flex justify-between items-center w-full">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-success-dark" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            <span id="successMessage"></span>
            </div>
            <button onclick="hideSuccess()" class="text-success-dark font-bold text-xl leading-none hover:text-success-dark/70 transition-colors ml-3">&times;</button>
        </div>
    </div>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Breadcrumbs -->
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.link href="/dashboard" class="hover:text-primary-color transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </x-ui.breadcrumbs.link>
            <x-ui.breadcrumbs.separator />
            <x-ui.breadcrumbs.link active>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Entrega - Recepción de Expedientes') }}
                @if($isReadOnlyMode)
                    <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Modo Lectura</span>
                @endif
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
                    <form id="filtrosForm" method="GET" action="{{ route('recepcion.index') }}">
                        <!-- Primera fila de filtros -->
                        <div class="filter-row">
                    <!-- Entrega -->
                            <div class="filter-col">
                        <label for="entrega" class="form-label">Entrega:</label>
                        <select name="entrega" id="entrega" class="form-select" onchange="submitFilterForm()">
                            <option value="">Todas</option>
                            @foreach($entregas as $e)
                                <option value="{{ $e->id }}" {{ request('entrega') == $e->id ? 'selected' : '' }}>
                                    {{ $e->valor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                            
                    <!-- Cuenta Pública -->
                    <div class="filter-col">
                        <label for="cuenta_publica" class="form-label">Cuenta Pública:</label>
                        <select name="cuenta_publica" id="cuenta_publica" class="form-select" onchange="submitFilterForm()">
                            <option value="">Todas</option>
                            @foreach($cuentasPublicas as $cp)
                                <option value="{{ $cp->id }}" {{ request('cuenta_publica') == $cp->id ? 'selected' : '' }}>
                                    {{ $cp->valor }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Cuenta Pública -->
                            <div class="filter-col">
                        <label for="tipo_accion" class="form-label">Tipo de Acción:</label>
                        <select name="tipo_accion" id="tipo_accion" class="form-select" onchange="submitFilterForm()">
                            <option value="">Todas</option>
                            @foreach($tipoDeAccion as $ta)
                                <option value="{{ $ta->id }}" {{ request('tipo_accion') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->valor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                
                    <!-- Ente Fiscalizado -->
                            <div class="filter-col">
                        <label for="ente_fiscalizado" class="form-label">Ente Fiscalizado:</label>
                        <select name="ente_fiscalizado" id="ente_fiscalizado" class="form-select" onchange="submitFilterForm()">
                            <option value="">Todos</option>
                            @foreach($enteFiscalizado as $ef)
                                <option value="{{ $ef->id }}" {{ request('ente_fiscalizado') == $ef->id ? 'selected' : '' }}>
                                    {{ $ef->valor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                
                            <!-- DG de Seguimiento -->
                            <div class="filter-col">
                                <label for="dgseg_ef" class="form-label">DG SEG:</label>
                                <select name="dgseg_ef" id="dgseg_ef" class="form-select" onchange="submitFilterForm()">
                                    <option value="">Todas</option>
                                    @foreach($dgSegEf as $de)
                                        <option value="{{ $de->id }}" {{ request('dgseg_ef') == $de->id ? 'selected' : '' }}>
                                            {{ $de->valor }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Segunda fila de filtros -->
                        <div class="filter-row">
                    <!-- Ente de la Acción -->
                            <div class="filter-col">
                        <label for="ente_de_la_accion" class="form-label">Ente de la Acción:</label>
                        <select name="ente_de_la_accion" id="ente_de_la_accion" class="form-select" onchange="submitFilterForm()">
                            <option value="">Todas</option>
                            @foreach($enteDeLaAccion as $ea)
                                <option value="{{ $ea->id }}" {{ request('ente_de_la_accion') == $ea->id ? 'selected' : '' }}>
                                    {{ $ea->valor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                
                    <!-- Tipo de Movimiento (Estatus) - Agrupado por categorías -->
                            <div class="filter-col" style="flex: 2">
                        <label for="estatus" class="form-label">Tipo de Movimiento:</label>
                        <select name="estatus" id="estatus" class="form-select" onchange="submitFilterForm()">
                            <option value="">Todos</option>
                            <optgroup label="Estado Inicial">
                                <option value="Programado" {{ request('estatus')=='Programado' ? 'selected' : '' }}>Programado</option>
                                <option value="Sin Programar" {{ request('estatus')=='Sin Programar' ? 'selected' : '' }}>Sin Programar</option>
                            </optgroup>
                            <optgroup label="UAA a DCE">
                                <option value="Recibido en el DCE PO superveniente (UAA – DCE)" {{ request('estatus')=='Recibido en el DCE PO superveniente (UAA – DCE)' ? 'selected' : '' }}>Recibido en el DCE PO superveniente (UAA – DCE)</option>
                                <option value="Recibido en el DCE PO superveniente (UAA – DCE) - Firmado" {{ request('estatus')=='Recibido en el DCE PO superveniente (UAA – DCE) - Firmado' ? 'selected' : '' }}>Recibido en el DCE PO superveniente (UAA – DCE) - Firmado</option>
                                <option value="Recibido en el DCE (UAA – DCE)" {{ request('estatus')=='Recibido en el DCE (UAA – DCE)' ? 'selected' : '' }}>Recibido en el DCE (UAA – DCE)</option>
                                <option value="Recibido en el DCE (UAA – DCE) - Firmado" {{ request('estatus')=='Recibido en el DCE (UAA – DCE) - Firmado' ? 'selected' : '' }}>Recibido en el DCE (UAA – DCE) - Firmado</option>
                                <option value="Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)" {{ request('estatus')=='Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)' ? 'selected' : '' }}>Recibido en el DCE con las correcciones (UAA – DCE)</option>
                                <option value="Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE) - Firmado" {{ request('estatus')=='Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE) - Firmado' ? 'selected' : '' }}>Recibido en el DCE con las correcciones (UAA – DCE) - Firmado</option>
                            </optgroup>
                            <optgroup label="DCE a DGSEG">
                                <option value="Recibido por la DGSEG para revisión (DCE - DGSEG)" {{ request('estatus')=='Recibido por la DGSEG para revisión (DCE - DGSEG)' ? 'selected' : '' }}>Recibido por la DGSEG para revisión</option>
                                <option value="Recibido por la DGSEG para revisión (DCE - DGSEG) - Firmado" {{ request('estatus')=='Recibido por la DGSEG para revisión (DCE - DGSEG) - Firmado' ? 'selected' : '' }}>Recibido por la DGSEG para revisión - Firmado</option>
                                <option value="Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)" {{ request('estatus')=='Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)' ? 'selected' : '' }}>Recibido por la DGSEG para revisión de correcciones</option>
                                <option value="Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG) - Firmado" {{ request('estatus')=='Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG) - Firmado' ? 'selected' : '' }}>Recibido por la DGSEG para revisión de correcciones - Firmado</option>
                            </optgroup>
                            <optgroup label="DGSEG a DCE">
                                <option value="Recibido en el DCE para resguardo (DGSEG – DCE)" {{ request('estatus')=='Recibido en el DCE para resguardo (DGSEG – DCE)' ? 'selected' : '' }}>Recibido en el DCE para resguardo</option>
                                <option value="Recibido en el DCE para resguardo (DGSEG – DCE) - Firmado" {{ request('estatus')=='Recibido en el DCE para resguardo (DGSEG – DCE) - Firmado' ? 'selected' : '' }}>Recibido en el DCE para resguardo - Firmado</option>
                                <option value="Recibido en el DCE con corrección para la UAA (DGSEG – DCE)" {{ request('estatus')=='Recibido en el DCE con corrección para la UAA (DGSEG – DCE)' ? 'selected' : '' }}>Recibido en el DCE con corrección para la UAA</option>
                                <option value="Recibido en el DCE con corrección para la UAA (DGSEG – DCE) - Firmado" {{ request('estatus')=='Recibido en el DCE con corrección para la UAA (DGSEG – DCE) - Firmado' ? 'selected' : '' }}>Recibido en el DCE con corrección para la UAA - Firmado</option>
                            </optgroup>
                            <optgroup label="DCE a UAA">
                                <option value="Recibido por la UAA para corrección (DCE - UAA)" {{ request('estatus')=='Recibido por la UAA para corrección (DCE - UAA)' ? 'selected' : '' }}>Recibido por la UAA para corrección</option>
                                <option value="Recibido por la UAA para corrección (DCE - UAA) - Firmado" {{ request('estatus')=='Recibido por la UAA para corrección (DCE - UAA) - Firmado' ? 'selected' : '' }}>Recibido por la UAA para corrección - Firmado</option>
                            </optgroup>
                            <optgroup label="RIASF - Devolución">
                                <option value="Recibido por DCE para devolución a la UAA por cambio RIASF (DGSEG – DCE)" {{ request('estatus')=='Recibido por DCE para devolución a la UAA por cambio RIASF (DGSEG – DCE)' ? 'selected' : '' }}>Recibido por DCE para devolución a la UAA por cambio RIASF (DGSEG – DCE)</option>
                                <option value="Recibido por DCE para devolución a la UAA por cambio RIASF (DGSEG – DCE) - Firmado" {{ request('estatus')=='Recibido por DCE para devolución a la UAA por cambio RIASF (DGSEG – DCE) - Firmado' ? 'selected' : '' }}>Recibido por DCE para devolución a la UAA por cambio RIASF (DGSEG – DCE) - Firmado</option>
                                <option value="Recibido por la UAA por cambio RIASF (DCE - UAA)" {{ request('estatus')=='Recibido por la UAA por cambio RIASF (DCE - UAA)' ? 'selected' : '' }}>Recibido por la UAA por cambio RIASF (DCE - UAA)</option>
                                <option value="Recibido por la UAA por cambio RIASF (DCE - UAA) - Firmado" {{ request('estatus')=='Recibido por la UAA por cambio RIASF (DCE - UAA) - Firmado' ? 'selected' : '' }}>Recibido por la UAA por cambio RIASF (DCE - UAA) - Firmado</option>
                            </optgroup>
                        </select>
                    </div>
                
                    <!-- Responsable con autocompletado -->
                            <div class="filter-col">
                        <label for="responsable" class="form-label">Responsable de la UAA:</label>
                        <div class="relative">
                            <input type="text" name="responsable" id="responsable" 
                                class="form-input pr-8" 
                                value="{{ request('responsable') }}" 
                                placeholder="Buscar responsable"
                                list="usersList"
                                onchange="submitFilterForm()">
                            <datalist id="usersList">
                                @foreach($users as $user)
                                    <option value="{{ $user }}">{{ $user }}</option>
                                @endforeach
                                <option value="Sin programar">Sin programar</option>
                            </datalist>
                            <button type="button" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                onclick="document.getElementById('responsable').value = ''; submitFilterForm()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                                </div>
                        </div>
                    </div>
                
                    <!-- Botones de acción -->
                        <div class="filter-actions">
                            <a href="{{ route('recepcion.index') }}" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            Limpiar
                        </a>
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Aplicar filtros
                            </button>
                    </div>
                </form>
                </div>
            </div>

            <!-- Expedientes para Recibir Hoy -->
            @if($isAdmin)
            <div class="card mb-5 sticky top-0 z-40 shadow-lg">
                <div class="card-header">
                    <div class="filter-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Expedientes
                    </div>
                </div>
                <div class="card-body flex items-center justify-between bg-white">
                    <div class="flex items-center">
                        <div class="text-sm text-primary-color font-medium mr-2">Expedientes seleccionados:</div>
                        <div id="selectedCount" class="badge badge-primary">0</div>
                        <div class="text-xs text-gray-500 ml-4">Selecciona expedientes para procesar</div>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" 
                                class="btn btn-secondary"
                                onclick="exportTableToExcel()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Exportar a Excel
                        </button>
                        <button type="button" 
                                class="btn btn-primary"
                                onclick="openAcuseModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Generar acuse
                        </button>
                    </div>
                </div>
            </div>
            @else
            <!-- Información para usuarios en modo lectura -->
            <div class="card mb-5">
                <div class="card-header">
                    <div class="filter-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Información
                    </div>
                </div>
                <div class="card-body bg-blue-50 border-l-4 border-blue-400">
                    <div class="flex items-center">
                        <div class="text-sm text-blue-800">
                            <p><strong>Modo de Lectura:</strong> Estás visualizando los expedientes en modo consulta. No puedes realizar modificaciones ni generar acuses.</p>
                            <p class="mt-1 text-xs">Total de expedientes: <span class="font-semibold">{{ count($expedientes) }}</span></p>
                        </div>
                        <div class="ml-auto">
                            <button type="button" 
                                    class="btn btn-secondary btn-sm"
                                    onclick="exportTableToExcel()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Exportar a Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
 
            <!-- Tabla principal -->
            <div class="card">
                <div class="card-header">
                    <div class="filter-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Listado de expedientes
                    </div>
                    <div class="text-xs text-gray-500">
                        Total: <span class="font-semibold">{{ count($expedientes) }}</span> expedientes
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                    <table class="data-table" id="recepcion-table">
                        <thead>
                            <tr>
                                    <th class="text-center w-10">#</th>
                                <th>Cuenta</th>
                                <th>Entrega</th>
                                <th>AE</th>
                                <th>UAA</th>
                                    <th>Ente Fiscalizado</th>
                                    <th>Ente de la Acción</th>
                                    <th>DG SEG</th>
                                <th>Núm.Aud</th>
                                <th>Clave Acción</th>
                                <th>Tipo</th>
                                <th>Tipo de Movimiento</th>
                                <th>Estatus</th>
                                @if($isAdmin)
                                <th>Checklist</th>
                                @endif
                                <th>Resp.UAA</th>
                                <th>Resp.SEG</th>
                                    <th class="text-center w-12">Leg</th>
                                <th>Fecha</th>
                                @if($isAdmin)
                                    <th class="text-center w-12">Ent</th>
                                @endif
                                    <th class="text-center w-16">Rastreo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expedientes as $i => $exp)
                                    <tr class="{{ !empty($exp->estado) && $exp->estado !== 'Programado' ? 'row-success' : '' }}">
                                        <td class="text-center font-semibold">{{ $i+1 }}</td>
                                    <td>{{ $exp->cuenta_publica_valor ?? '' }}</td>
                                    <td>{{ $exp->entrega_valor ?? '' }}</td>
                                    <td>{{ $exp->ae_siglas ?? '' }}</td>
                                        <td class="font-medium">{{ $exp->uaa_valor ?? '' }}</td>
                                    <td>{{ $exp->valor_ente_fiscalizado ?? '' }}</td>
                                    <td>{{ $exp->valor_ente_de_la_accion ?? '' }}</td>
                                    <td>{{ $exp->valor_dgseg_ef ?? '' }}</td>
                                    <td>{{ $exp->numero_auditoria ?? '' }}</td>
                                        <td class="text-primary font-semibold">{{ $exp->clave_de_accion ?? '' }}</td>
                                    <td>{{ $exp->tipo_accion_valor ?? '' }}</td>
                                    <td>
                                        @if(!empty($exp->estado) && $exp->estado !== 'Programado')
                                                <span class="badge text-white" style="background-color: var(--success-color); padding: 0.45rem 0.85rem;">{{ $exp->estado ?? 'Pendiente' }}</span>
                                        @else
                                                <span class="badge text-white" style="background-color: var(--warning-color); padding: 0.45rem 0.85rem;">{{ $exp->estado ?? 'Pendiente' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $exp->estatus_revision ?? 'Sin revisión' }}</td>
                                    @if($isAdmin)
                                    <td>
                                        <!-- Estado y Botones de Descarga - Versión Mejorada -->
                                        <div class="status-card">
                                            <!-- Badge de estado con diseño mejorado -->
                                            <div class="status-badge 
                                                {{ $exp->estatus_checklist == 'Aceptado' ? 'status-badge-success' : 
                                                ($exp->estatus_checklist == 'Devuelto' ? 'status-badge-warning' : 'status-badge-neutral') }}">
                                                <div class="status-icon">
                                                    @if($exp->estatus_checklist == 'Aceptado')
                                                        <svg class="status-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @elseif($exp->estatus_checklist == 'Devuelto')
                                                        <svg class="status-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    @else
                                                        <svg class="status-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <span class="status-text">{{ $exp->estatus_checklist }}</span>
                                            </div>
                                            
                                            <!-- Botones de Acción Mejorados -->
                                            <div class="action-buttons">
                                                @if ($exp->estatus_checklist == "Aceptado" && empty($exp->archivo_uua))
                                                    <a href="{{ route('auditorias.pdf', $exp->id) }}" class="action-button action-signature">
                                                        <div class="action-icon">
                                                            <svg class="action-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                        </div>
                                                        <span class="action-text">Con Firma de Seguimiento</span>
                                                    </a>
                                                @elseif($exp->estatus_checklist == "Aceptado" && !empty($exp->archivo_uua))
                                                    <a href="{{ route('auditorias.downloadUua', $exp->id) }}" class="action-button action-completed">
                                                        <div class="action-icon">
                                                            <svg class="action-svg" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                        </div>
                                                        <span class="action-text">Completado</span>
                                                    </a>
                                                @elseif ($exp->estatus_checklist == "Devuelto")
                                                    <a href="/auditorias/{{ $exp->id }}/pdf" class="action-button action-returned">
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
                                    @endif
                                    <td>{{ $exp->responsable_uaa ?? '' }}</td>
                                    <td>{{ $exp->responsable_seg ?? '' }}</td>
                                    <td class="text-center font-semibold">{{ $exp->numero_legajos ?? '' }}</td>
                                    <td>
                                            @if(!empty($exp->fecha_real_entrega))
                                            {{ \Carbon\Carbon::parse($exp->fecha_entrega)->format('d/m/Y') }}
                                            @else
                                                @if(!empty($exp->fecha_entrega))
                                                    {{ \Carbon\Carbon::parse($exp->fecha_entrega)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-warning">Sin programar</span>
                                                @endif
                                            @endif
                                    </td>
                                    @if($isAdmin)
                                    <td class="text-center">
                                        <label class="relative inline-flex items-center cursor-pointer tooltip">
                                            <input type="checkbox" 
                                                    class="custom-checkbox received-checkbox" 
                                                    data-expediente-id="{{ $exp->id }}"
                                                    data-clave="{{ $exp->clave_de_accion }}"
                                                    data-responsable="{{ $exp->responsable_uaa }}"
                                                    onchange="toggleEntregadoViaAjax({{ $exp->id }}, this.checked, this)">
                                            <span class="tooltip-text">Marcar como {{ !empty($exp->estado) && $exp->estado !== 'Programado' ? 'no entregado' : 'entregado' }}</span>
                                        </label>
                                    </td>
                                    @endif
                                    <td class="text-center">
                                        <button type="button" 
                                                    class="btn btn-primary btn-sm"
                                                onclick="openRastreoModal({{ $exp->id_entrega }})">
                                                    Ver
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                        <td colspan="{{ $isAdmin ? '19' : '17' }}" class="px-4 py-8 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                                <p class="text-center font-medium text-base">No hay expedientes que cumplan el criterio</p>
                                                <p class="text-center text-gray-400 text-sm mt-1">Selecciona al menos un filtro para ver resultados</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($isAdmin)
    <!-- Modal de confirmación "Generar acuse" -->
    <div id="acuseModal" class="fixed inset-0 modal-overlay hidden z-50 flex items-center justify-center">
        <div class="relative w-full max-w-lg animate-fade-in">
            <div class="modal-content bg-white p-6 rounded-lg shadow-xl">
                <div class="absolute top-3 right-3">
                    <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeAcuseModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <h3 class="text-xl font-bold mb-5 text-center flex items-center justify-center text-primary-color">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Generar Acuse de Recepción
                </h3>

                <!-- Select de estados -->
                <div class="mb-4">
                    <label for="estadoRecepcion" class="form-label flex items-center text-sm font-medium">
                        <span>Estado de Recepción</span>
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <select id="estadoRecepcion" name="estado_recepcion" class="form-select">
                        <option value="">-- Seleccionar --</option>
                        <option value="Recibido en el DCE PO superveniente (UAA – DCE)">Recibido en el DCE PO superveniente (UAA – DCE)</option>
                        <option value="Recibido en el DCE (UAA – DCE)">Recibido en el DCE (UAA – DCE)</option>
                        <option value="Recibido por la DGSEG para revisión (DCE - DGSEG)">Recibido por la DGSEG para revisión (DCE - DGSEG)</option>
                        <option value="Recibido en el DCE para resguardo (DGSEG – DCE)">Recibido en el DCE para resguardo (DGSEG – DCE)</option>
                        <option value="Recibido en el DCE con corrección para la UAA (DGSEG – DCE)">Recibido en el DCE con corrección para la UAA (DGSEG – DCE)</option>
                        <option value="Recibido por la UAA para corrección (DCE - UAA)">Recibido por la UAA para corrección (DCE - UAA)</option>
                        <option value="Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)">
                            Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)
                        </option>
                        <option value="Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)">Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)</option>

                        <optgroup label="RIASF - Devolución">
                            <option value="Recibido por DCE para devolución a la UAA por cambio RIASF (DGSEG – DCE)">Recibido por DCE para devolución a la UAA por cambio RIASF (DGSEG – DCE)</option>
                            <option value="Recibido por la UAA por cambio RIASF (DCE - UAA)">Recibido por la UAA por cambio RIASF (DCE - UAA)</option>
                        </optgroup>
                    </select>
                </div>

                <!-- Listado de Expedientes -->
                <div class="border border-gray-200 rounded-md mb-5">
                    <div class="bg-gray-50 p-3 border-b flex justify-between items-center">
                        <div class="flex items-center font-medium text-primary-color">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2M7 7h10" />
                            </svg>
                            Expedientes Seleccionados
                        </div>
                        <span class="badge badge-primary" id="countSelectedItems">0</span>
                    </div>
                    <div id="acuseList" class="max-h-60 overflow-y-auto p-3 divide-y divide-gray-100"></div>
                </div>

                <div class="flex justify-between mt-5">
                    <button onclick="closeAcuseModal()" class="btn btn-secondary flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancelar
                    </button>
                    <button onclick="confirmAcuse()" class="btn btn-success flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Confirmar Recepción
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario oculto para confirmar acuse -->
    <form id="acuseForm" action="{{ route('recepcion.generarAcuse') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="expedientes_seleccionados" id="expedientesSeleccionadosInput">
        <input type="hidden" name="estado_recepcion" id="estadoRecepcionInput">
    </form>
    @endif

    <!-- Modal de Rastreo (Timeline) -->
    <div id="rastreoModal" class="fixed inset-0 modal-overlay hidden z-50 flex items-center justify-center">
        <div class="relative w-full max-w-xl animate-fade-in">
            <div class="modal-content bg-white p-6 rounded-lg shadow-xl">
                <div class="absolute top-3 right-3">
                    <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeRastreoModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <h3 class="text-xl font-bold mb-5 text-center flex items-center justify-center text-primary-color">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Historial de Entregas
                </h3>
                
                <!-- Loading indicator -->
                <div id="timelineLoading" class="flex justify-center my-8">
                    <div class="spinner"></div>
                </div>
                
                <div id="timelineContainer" class="mb-4 text-sm text-gray-700 max-h-96 overflow-y-auto px-4 hidden timeline-wrapper">
                    <!-- El contenido se llena dinámicamente -->
                </div>
                
                <div class="text-center mt-5">
                    <button onclick="closeRastreoModal()" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 backdrop-filter backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white p-5 rounded-lg shadow-lg flex flex-col items-center animate-pulse">
            <div class="spinner mb-3"></div>
            <p class="text-gray-700 font-medium" id="loadingMessage">Procesando solicitud...</p>
        </div>
    </div>

    <!-- SheetJS (xlsx.js) para exportación a Excel -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    @push('scripts')
        @include('dashboard.recepcion_assets.scripts')
    @endpush
</x-app-layout>