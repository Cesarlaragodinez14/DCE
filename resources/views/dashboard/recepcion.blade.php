<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
            {{ __('Recepción de Expedientes -') }}
        </h2>
    </x-slot>

    <!-- CSS Variables -->
    <style>
        :root {
            --primary-color: #1e40af;
            --primary-light: #3b82f6;
            --primary-dark: #1e3a8a;
            --primary-gradient: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            --success-color: #10b981;
            --success-light: #34d399;
            --success-dark: #059669;
            --success-gradient: linear-gradient(135deg, var(--success-color), var(--success-light));
            --border-color: #e5e7eb;
            --text-color: #1f2937;
            --text-muted: #6b7280;
            --white: #ffffff;
            --bg-light: #f9fafb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --transition-normal: all 0.3s ease;
            --transition-fast: all 0.15s ease;
        }

        /* General Styles */
        body {
            color: var(--text-color);
            background-color: #f3f4f6;
        }

        .card {
            background-color: var(--white);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            transition: var(--transition-normal);
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
        }

        /* Primary Button */
        .btn-primary {
            background: var(--primary-gradient);
            color: var(--white);
            padding: 0.375rem 0.75rem;
            border-radius: var(--radius-sm);
            font-weight: 600;
            transition: var(--transition-fast);
            border: none;
            box-shadow: var(--shadow-sm);
            font-size: 0.875rem;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* Success Button */
        .btn-success {
            background: var(--success-gradient);
            color: var(--white);
            padding: 0.375rem 0.75rem;
            border-radius: var(--radius-sm);
            font-weight: 600;
            transition: var(--transition-fast);
            border: none;
            box-shadow: var(--shadow-sm);
            font-size: 0.875rem;
        }

        .btn-success:hover {
            background: var(--success-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* Secondary Button */
        .btn-secondary {
            background-color: #f3f4f6;
            color: var(--text-color);
            padding: 0.375rem 0.75rem;
            border-radius: var(--radius-sm);
            font-weight: 600;
            transition: var(--transition-fast);
            border: 1px solid var(--border-color);
            font-size: 0.875rem;
        }

        .btn-secondary:hover {
            background-color: #e5e7eb;
        }

        /* Danger Button */
        .btn-danger {
            background-color: #ef4444;
            color: var(--white);
            padding: 0.375rem 0.75rem;
            border-radius: var(--radius-sm);
            font-weight: 600;
            transition: var(--transition-fast);
            border: none;
            font-size: 0.875rem;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
        }

        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: var(--radius-md);
            overflow: hidden;
            font-size: 0.75rem;
        }

        .data-table th {
            background-color: var(--primary-color);
            color: var(--white);
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            padding: 0.5rem 0.5rem;
            text-align: left;
            position: sticky;
            top: 0;
            white-space: nowrap;
        }

        .data-table tr:nth-child(even) {
            background-color: rgba(243, 244, 246, 0.7);
        }

        .data-table tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }

        .data-table td {
            padding: 0.375rem 0.5rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            line-height: 1.2;
        }

        /* Custom Form Elements */
        .form-select, .form-input {
            width: 100%;
            padding: 0.375rem 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            background-color: var(--white);
            transition: var(--transition-fast);
            font-size: 0.875rem;
        }

        .form-select:focus, .form-input:focus {
            border-color: var(--primary-light);
            outline: none;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
        }

        /* Form Labels */
        .form-label {
            display: block;
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.25rem;
            font-size: 0.75rem;
        }

        /* Custom Checkboxes */
        .custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 1rem;
            height: 1rem;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-sm);
            position: relative;
            cursor: pointer;
            transition: var(--transition-fast);
            background-color: var(--white);
        }

        .custom-checkbox:checked {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .custom-checkbox:checked::after {
            content: '✓';
            color: white;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font-size: 0.625rem;
        }

        /* Modal styles */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
            transition: var(--transition-normal);
        }

        .modal-content {
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            max-width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        /* Timeline styles */
        .timeline-list {
            list-style: none;
            padding-left: 0;
            position: relative;
        }

        .timeline-list li {
            position: relative;
            padding-left: 1.5rem;
            padding-bottom: 1.5rem;
            border-left: 2px solid var(--primary-light);
            margin-left: 1rem;
        }

        .timeline-list li:last-child {
            border-left: 2px solid transparent;
        }

        .timeline-list li::before {
            content: "";
            position: absolute;
            left: -0.5rem;
            top: 0;
            width: 1rem;
            height: 1rem;
            background-color: var(--primary-light);
            border-radius: 50%;
        }

        /* Alerts */
        .alert {
            padding: 0.75rem;
            border-radius: var(--radius-md);
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #ef4444;
            color: #b91c1c;
        }

        .alert-success {
            background-color: #d1fae5;
            border: 1px solid #10b981;
            color: #047857;
        }

        /* Text color styles for status and action key */
        .text-primary {
            color: var(--primary-color);
            font-weight: 600;
        }

        .text-success {
            color: var(--success-color);
            font-weight: 600;
        }

        .text-warning {
            color: #f59e0b;
            font-weight: 600;
        }

        /* Animation for feedback */
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }

        .pulse {
            animation: pulse 1.5s infinite;
        }

        /* Tooltip styles */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltip-text {
            visibility: hidden;
            width: 120px;
            background-color: var(--text-color);
            color: var(--white);
            text-align: center;
            padding: 5px;
            border-radius: var(--radius-sm);
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.675rem;
        }

        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* Responsive table container */
        .table-container {
            overflow-x: auto;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
        }

        /* Loading spinner */
        .spinner {
            width: 1.5rem;
            height: 1.5rem;
            border: 2px solid rgba(59, 130, 246, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary-light);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr !important;
            }
            
            .hidden-mobile {
                display: none;
            }
            
            .data-table th, .data-table td {
                padding: 0.375rem;
                font-size: 0.7rem;
            }
            
            .card {
                padding: 0.75rem !important;
            }
        }

        /* Compact filter layout */
        .compact-filters .form-label {
            margin-bottom: 0.125rem;
        }
        
        .compact-filters .form-select,
        .compact-filters .form-input {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>

    <!-- Error Alert -->
    <div id="errorAlert" class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 alert alert-error hidden">
        <div class="flex justify-between items-center">
            <ul id="errorList" class="list-disc list-inside"></ul>
            <button id="closeErrorBtn" class="text-red-700 font-bold text-xl leading-none hover:text-red-900 transition-colors">&times;</button>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="successToast" class="fixed bottom-4 right-4 z-50 alert alert-success hidden max-w-xs">
        <div class="flex justify-between items-center">
            <span id="successMessage"></span>
            <button onclick="closeSuccessToast()" class="text-green-700 font-bold text-xl leading-none hover:text-green-900 transition-colors">&times;</button>
        </div>
    </div>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros Card - Más compacto -->
            <div class="card p-4 mb-4">
                <form id="filtrosForm" method="GET" action="{{ route('recepcion.index') }}" class="grid form-grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 compact-filters">
                    <!-- Entrega -->
                    <div>
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
                    <div>
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
                
                    <!-- Ente Fiscalizado -->
                    <div>
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
                
                    <!-- Ente de la Acción -->
                    <div>
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
                
                    <!-- DG de Seguimiento -->
                    <div>
                        <label for="dgseg_ef" class="form-label">DG de Seguimiento:</label>
                        <select name="dgseg_ef" id="dgseg_ef" class="form-select" onchange="submitFilterForm()">
                            <option value="">Todas</option>
                            @foreach($dgSegEf as $de)
                                <option value="{{ $de->id }}" {{ request('dgseg_ef') == $de->id ? 'selected' : '' }}>
                                    {{ $de->valor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                
                    <!-- Tipo de Movimiento (Estatus) - Agrupado por categorías -->
                    <div class="sm:col-span-2 md:col-span-2">
                        <label for="estatus" class="form-label">Tipo de Movimiento:</label>
                        <select name="estatus" id="estatus" class="form-select" onchange="submitFilterForm()">
                            <option value="">Todos</option>
                            <optgroup label="Estado Inicial">
                                <option value="Programado" {{ request('estatus')=='Programado' ? 'selected' : '' }}>Programado</option>
                                <option value="Sin Programar" {{ request('estatus')=='Sin Programar' ? 'selected' : '' }}>Sin Programar</option>
                            </optgroup>
                            <optgroup label="UAA a DCE">
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
                        </select>
                    </div>
                
                    <!-- Responsable con autocompletado -->
                    <div class="sm:col-span-2 md:col-span-2 lg:col-span-1">
                        <label for="responsable" class="form-label">Responsable:</label>
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
                
                    <!-- Botones de acción -->
                    <div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 flex justify-end space-x-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1 px-4 rounded transition-colors duration-200">
                            Filtrar
                        </button>
                        <a href="{{ route('recepcion.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-1 px-4 rounded transition-colors duration-200">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Expedientes para Recibir Hoy -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-4 border-l-4" style="border-color: var(--primary-color);">
                <div class="flex items-center justify-between">
                    <div>
                    </div>
                    <button type="button" 
                            class="btn-primary flex items-center text-xs"
                            onclick="openAcuseModal()">
                        Generar acuse
                    </button>
                </div>
            </div>

            <!-- Tabla principal -->
            <div class="card p-4">
                <div class="table-container mb-3">
                    <table class="data-table" id="recepcion-table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Cuenta</th>
                                <th>Entrega</th>
                                <th>AE</th>
                                <th>UAA</th>
                                <th>ente_fiscalizado</th>
                                <th>ente_de_la_accion</th>
                                <th>dgseg_ef</th>
                                <th>Núm.Aud</th>
                                <th>Clave Acción</th>
                                <th>Tipo</th>
                                <th>Tipo de Movimiento</th>
                                <th>Estatus</th>
                                <th>Resp.UAA</th>
                                <th>Resp.SEG</th>
                                <th class="text-center">Leg</th>
                                <th>Fecha</th>
                                <th class="text-center">Ent</th>
                                <th class="text-center">Rastreo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expedientes as $i => $exp)
                                <tr class="{{ !empty($exp->estado) && $exp->estado !== 'Programado' ? 'bg-green-50' : '' }}">
                                    <td class="text-center">{{ $i+1 }}</td>
                                    <td>{{ $exp->cuenta_publica_valor ?? '' }}</td>
                                    <td>{{ $exp->entrega_valor ?? '' }}</td>
                                    <td>{{ $exp->ae_siglas ?? '' }}</td>
                                    <td>{{ $exp->uaa_valor ?? '' }}</td>
                                    <td>{{ $exp->valor_ente_fiscalizado ?? '' }}</td>
                                    <td>{{ $exp->valor_ente_de_la_accion ?? '' }}</td>
                                    <td>{{ $exp->valor_dgseg_ef ?? '' }}</td>
                                    <td>{{ $exp->numero_auditoria ?? '' }}</td>
                                    <td class="text-primary">{{ $exp->clave_de_accion ?? '' }}</td>
                                    <td>{{ $exp->tipo_accion_valor ?? '' }}</td>
                                    <td>
                                        @if(!empty($exp->estado) && $exp->estado !== 'Programado')
                                            <span class="text-success">{{ $exp->estado ?? 'Pendiente' }}</span>
                                        @else
                                            <span class="text-warning">{{ $exp->estado ?? 'Pendiente' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $exp->estatus_revision ?? 'Sin revisión' }}</td>
                                    <td>{{ $exp->responsable_uaa ?? '' }}</td>
                                    <td>{{ $exp->responsable_seg ?? '' }}</td>
                                    <td class="text-center font-semibold">{{ $exp->numero_legajos ?? '' }}</td>
                                    <td>
                                        @if(!empty($exp->fecha_real_entrega) == "Sin programar")
                                            Sin programar
                                        @elseif(!empty($exp->fecha_real_entrega))
                                            {{ \Carbon\Carbon::parse($exp->fecha_real_entrega)->format('d/m/Y') }}
                                        @else
                                            @if(!empty($exp->fecha_real_entrega))
                                            {{ \Carbon\Carbon::parse($exp->fecha_entrega)->format('d/m/Y') }}
                                            @endif
                                        @endif
                                    </td>
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
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn-primary text-xs py-1 px-1 inline-flex items-center mx-auto"
                                                onclick="openRastreoModal({{ $exp->id_entrega }})">
                                                <small>Ver</small>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="17" class="px-4 py-2 text-center text-gray-500">
                                        <div class="py-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-base font-medium">No hay expedientes que cumplan el criterio.</p>
                                            <p class="text-xs text-gray-500 mt-1">Selecciona al menos un filtro para ver resultados.</p>
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

    <!-- Modal de confirmación "Generar acuse" -->
    <div id="acuseModal" class="fixed inset-0 modal-overlay hidden z-50">
        <div class="relative w-full max-w-lg mx-auto mt-20">
            <div class="modal-content bg-white p-6 rounded-lg shadow-xl">
                <div class="absolute top-3 right-3">
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeAcuseModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <h3 class="text-xl font-bold mb-4 text-center text-primary-color">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2 text-primary-light" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Generar Acuse de Recepción
                </h3>

                <!-- Select de estados -->
                <div class="mb-4">
                    <label for="estadoRecepcion" class="form-label flex items-center">
                        <span>Estado de Recepción</span>
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <select id="estadoRecepcion" name="estado_recepcion" class="form-select">
                        <option value="">-- Seleccionar --</option>
                        <option value="Recibido en el DCE (UAA – DCE)">Recibido en el DCE (UAA – DCE)</option>
                        <option value="Recibido por la DGSEG para revisión (DCE - DGSEG)">Recibido por la DGSEG para revisión (DCE - DGSEG)</option>
                        <option value="Recibido en el DCE para resguardo (DGSEG – DCE)">Recibido en el DCE para resguardo (DGSEG – DCE)</option>
                        <option value="Recibido en el DCE con corrección para la UAA (DGSEG – DCE)">Recibido en el DCE con corrección para la UAA (DGSEG – DCE)</option>
                        <option value="Recibido por la UAA para corrección (DCE - UAA)">Recibido por la UAA para corrección (DCE - UAA)</option>
                        <option value="Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)">
                            Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)
                        </option>
                        <option value="Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)">Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)</option>
                    </select>
                </div>

                <!-- Listado de Expedientes -->
                <div class="border border-gray-200 rounded-md mb-4">
                    <div class="bg-gray-50 p-2 border-b flex justify-between items-center">
                        <span class="font-medium text-primary-color">Expedientes Seleccionados</span>
                        <span class="badge badge-primary" id="countSelectedItems">0</span>
                    </div>
                    <div id="acuseList" class="max-h-52 overflow-y-auto p-2 divide-y divide-gray-100"></div>
                </div>

                <div class="flex justify-between mt-5">
                    <button onclick="closeAcuseModal()" class="btn-secondary">
                        Cancelar
                    </button>
                    <button onclick="confirmAcuse()" class="btn-success flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Confirmar Recepción
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Rastreo (Timeline) -->
    <div id="rastreoModal" class="fixed inset-0 modal-overlay hidden z-50">
        <div class="relative w-full max-w-xl mx-auto mt-20">
            <div class="modal-content bg-white p-6 rounded-lg shadow-xl">
                <div class="absolute top-3 right-3">
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeRastreoModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <h3 class="text-xl font-bold mb-4 text-center text-primary-color flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Historial de Entregas
                </h3>
                
                <!-- Loading indicator -->
                <div id="timelineLoading" class="flex justify-center my-8">
                    <div class="spinner"></div>
                </div>
                
                <div id="timelineContainer" class="mb-4 text-sm text-gray-700 divide-y divide-gray-100 max-h-96 overflow-y-auto px-2 hidden"></div>
                
                <div class="text-center mt-5">
                    <button onclick="closeRastreoModal()" class="btn-primary">
                        Cerrar
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

    <!-- Loading overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 backdrop-filter backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white p-5 rounded-lg shadow-lg flex flex-col items-center">
            <div class="spinner mb-3"></div>
            <p class="text-gray-700 font-medium" id="loadingMessage">Procesando solicitud...</p>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

.timeline-wrapper .timeline-item {
    animation: fadeIn 0.5s ease-out;
    animation-fill-mode: both;
}

.timeline-wrapper .timeline-item:nth-child(1) { animation-delay: 0.1s; }
.timeline-wrapper .timeline-item:nth-child(2) { animation-delay: 0.2s; }
.timeline-wrapper .timeline-item:nth-child(3) { animation-delay: 0.3s; }
.timeline-wrapper .timeline-item:nth-child(4) { animation-delay: 0.4s; }
.timeline-wrapper .timeline-item:nth-child(5) { animation-delay: 0.5s; }
    </style>
    @push('scripts')
    <script>
        "use strict";

        let selectedExpedientes = [];
        let allCheckboxes = [];

        // Inicializar la página
        document.addEventListener('DOMContentLoaded', () => {
            // Inicializar checkboxes
            allCheckboxes = document.querySelectorAll('.received-checkbox');
            
            allCheckboxes.forEach(chk => {
                if(chk.checked) {
                    const expId = chk.getAttribute('data-expediente-id');
                    if(!selectedExpedientes.includes(expId)) {
                        selectedExpedientes.push(expId);
                    }
                }
                
                chk.addEventListener('change', function() {
                    const expId = this.getAttribute('data-expediente-id');
                    if(this.checked) {
                        if(!selectedExpedientes.includes(expId)) {
                            selectedExpedientes.push(expId);
                        }
                    } else {
                        selectedExpedientes = selectedExpedientes.filter(id => id !== expId);
                    }
                });
            });

            // Inicializar tooltips si es necesario
            
            // Establecer focus en el primer campo de filtro si no hay filtros aplicados
            if (!window.location.search.includes('entrega=') && 
                !window.location.search.includes('cuenta_publica=') && 
                !window.location.search.includes('estatus=') && 
                !window.location.search.includes('responsable=')) {
                document.getElementById('entrega').focus();
            }
            
            // Cerrar el error alert después de 5 segundos si está visible
            const errorAlert = document.getElementById('errorAlert');
            if (!errorAlert.classList.contains('hidden')) {
                setTimeout(() => {
                    errorAlert.classList.add('hidden');
                }, 5000);
            }
        });


        // Función para enviar el formulario de filtros
        function submitFilterForm() {
            document.getElementById('filtrosForm').submit();
        }

        // 1) Modal de "Generar Acuse"
function openAcuseModal() {
    const acuseListDiv = document.getElementById('acuseList');
    acuseListDiv.innerHTML = '';

    if (selectedExpedientes.length === 0) {
        showError('Primero selecciona una clave de acción');
        return;
    }

    let hayProgramado = false;
    let haySinProgramar = false;
    const rows = Array.from(document.querySelectorAll('#recepcion-table tbody tr'));
    const selectedData = [];

    rows.forEach(row => {
        const chk = row.querySelector('.received-checkbox');
        if(chk && selectedExpedientes.includes(chk.getAttribute('data-expediente-id'))) {
            // El texto del estado ahora está en la celda 9 (índice 8 basado en cero)
            // Pero necesitamos verificar si es realmente "Sin programar"
            const estatusExp = row.cells[11] ? row.cells[11].textContent.trim() : '';
            
            if(estatusExp === 'Programado') { 
                hayProgramado = true;
            }
            
            if(estatusExp === 'Sin programar') {
                haySinProgramar = true;
            }

            // Obtener los datos relevantes de las columnas de la tabla
            // Ajusta estos índices según la estructura actual de tu tabla
            const clave = row.cells[9] ? row.cells[9].textContent.trim() : '';
            const responsable = row.cells[13] ? row.cells[13].textContent.trim() : '';
            const uaa = row.cells[4] ? row.cells[4].textContent.trim() : '';
            
            selectedData.push({ clave, responsable, uaa, estatusExp });
        }
    });

    acuseListDiv.innerHTML = selectedData.map(item =>
        `<div class="py-2">
            <div class="flex justify-between">
                <div class="font-medium text-primary-color">${item.clave}</div>
                <div class="text-xs text-gray-500">${item.uaa}</div>
            </div>
            <div class="text-sm text-gray-600">${item.responsable}</div>
            <div class="text-xs ${item.estatusExp === 'Sin programar' ? 'text-orange-500' : 'text-blue-500'} mt-1">
                Estado: ${item.estatusExp}
            </div>
        </div>`
    ).join('');

    document.getElementById('countSelectedItems').textContent = selectedData.length;

    const estadoSelect = document.getElementById('estadoRecepcion');
    // Restaurar todas las opciones
    Array.from(estadoSelect.options).forEach(opt => {
        opt.style.display = '';
    });

    // Lógica para determinar qué opciones mostrar en el select de estado
    if(haySinProgramar) {
        // Si hay expedientes "Sin programar", solo se permite "Recibido en el DCE (UAA – DCE)"
        Array.from(estadoSelect.options).forEach(opt => {
            if(opt.value && opt.value !== 'Recibido en el DCE (UAA – DCE)') {
                opt.style.display = 'none';
            }
        });
        estadoSelect.value = "Recibido en el DCE (UAA – DCE)";
    } 
    else if(hayProgramado) {
        // Si hay expedientes "Programado", solo se permite "Recibido en el DCE (UAA – DCE)"
        Array.from(estadoSelect.options).forEach(opt => {
            if(opt.value && opt.value !== 'Recibido en el DCE (UAA – DCE)') {
                opt.style.display = 'none';
            }
        });
        estadoSelect.value = "Recibido en el DCE (UAA – DCE)";
    } 
    else {
        // Para otros casos, permitir cualquier estado
        estadoSelect.value = ""; 
    }

    // Mostrar el modal
    document.getElementById('acuseModal').classList.remove('hidden');
    document.getElementById('acuseModal').classList.add('flex');
    
    // Enfocar el select de estado
    document.getElementById('estadoRecepcion').focus();
}

function closeAcuseModal() {
    document.getElementById('acuseModal').classList.add('hidden');
    document.getElementById('acuseModal').classList.remove('flex');
}

function confirmAcuse() {
    if(selectedExpedientes.length === 0) {
        showError('Primero selecciona una clave de acción');
        return;
    }
    
    const estadoSelect = document.getElementById('estadoRecepcion');
    const estadoSeleccionado = estadoSelect.value;
    
    if(!estadoSeleccionado) {
        showError('Debe seleccionar un estado de recepción para todos los expedientes.');
        estadoSelect.focus();
        return;
    }
    
    // Validación adicional para expedientes Sin programar
    const rows = Array.from(document.querySelectorAll('#recepcion-table tbody tr'));
    let haySinProgramar = false;
    
    rows.forEach(row => {
        const chk = row.querySelector('.received-checkbox');
        if(chk && selectedExpedientes.includes(chk.getAttribute('data-expediente-id'))) {
            const estatusExp = row.cells[11] ? row.cells[11].textContent.trim() : '';
            if(estatusExp === 'Sin programar' && estadoSeleccionado !== 'Recibido en el DCE (UAA – DCE)') {
                haySinProgramar = true;
            }
        }
    });
    
    if(haySinProgramar) {
        showError('Los expedientes sin programar solo pueden recibirse como "Recibido en el DCE (UAA – DCE)".');
        return;
    }
    
    // Mostrar overlay de carga
    showLoading('Generando acuse de recepción...');
    
    document.getElementById('expedientesSeleccionadosInput').value = JSON.stringify(selectedExpedientes);
    document.getElementById('estadoRecepcionInput').value = estadoSeleccionado;
    document.getElementById('acuseForm').submit();
}

// Función de ayuda para verificar si la cadena contiene "Sin programar" sin distinguir mayúsculas/minúsculas
function esSinProgramar(texto) {
    return texto.toLowerCase().includes('sin programar');
}

        // 2) Modal de "Rastreo" (Timeline)
        async function openRastreoModal(expedienteId) {
            // Mostrar modal y su indicador de carga
            const rastreoModal = document.getElementById('rastreoModal');
            const timelineLoading = document.getElementById('timelineLoading');
            const timelineContainer = document.getElementById('timelineContainer');
            
            rastreoModal.classList.remove('hidden');
            rastreoModal.classList.add('flex');
            timelineLoading.classList.remove('hidden');
            timelineContainer.classList.add('hidden');
            
            try {
                const resp = await fetch("/recepcion/rastreo/" + expedienteId, {
                    method: 'GET',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });

                if (!resp.ok) {
                    showError('Error al obtener el Rastreo: ' + resp.statusText);
                    closeRastreoModal();
                    return;
                }

                const data = await resp.json();
                
                // Ocultar el loader y mostrar el contenedor
                timelineLoading.classList.add('hidden');
                timelineContainer.classList.remove('hidden');
                
                if (!data || !Array.isArray(data) || data.length === 0) {
                    timelineContainer.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-6 text-gray-500">
                            <p class="text-center font-medium">No hay entregas previas en el historial</p>
                        </div>
                    `;
                } else {
                    // Construir el timeline con diseño más limpio
                    let html = '<div class="relative pt-1 pb-2">';
                    
                    // Línea de tiempo vertical
                    html += '<div class="absolute left-2 top-0 bottom-0 w-0.5 bg-gray-200"></div>';
                    
                    data.forEach((item, index) => {
                        const isLast = index === data.length - 1;
                        const fecha = new Date(item.fecha);
                        const fechaFormateada = fecha.toLocaleDateString('es-MX', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        });
                        
                        let pdfButton = "";
                        
                        // Botón de descarga más sutil
                        if (item.pdf_path) {
                            const pdfUrl = `/storage/${item.pdf_path}`;
                            pdfButton = `
                                <a href="${pdfUrl}" target="_blank" 
                                class="text-xs text-primary-color hover:underline ml-auto">
                                    Descargar acuse
                                </a>`;
                        }

                        html += `
                            <div class="timeline-item relative mb-5 ml-6 ${isLast ? '' : ''}">
                                <!-- Círculo indicador -->
                                <div style="margin-left: 12px;" class="absolute left-[-14px] top-1.5 w-4 h-4 rounded-full border-2 border-white bg-primary-light"></div>
                                
                                <!-- Fecha al lado del punto -->
                                <div style="margin: 20px 15px;" class="absolute left-[-6.5rem] top-1 w-24 text-right text-xs text-gray-500">${fechaFormateada}</div>
                                
                                <!-- Contenido del evento -->
                                <div class="bg-white rounded border-l-2 border-primary-light pl-3 py-1.5">
                                    <div style="margin: 20px;" class="font-medium text-sm text-primary-color">${item.estado}</div>
                                    
                                    <div class="flex items-center justify-end mt-1.5">
                                        ${pdfButton}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    timelineContainer.innerHTML = html;
                }

            } catch (error) {
                console.error('Error en openRastreoModal:', error);
                showError('Ocurrió un error inesperado al obtener el rastreo.');
                closeRastreoModal();
            }
        }

        function closeRastreoModal() {
            document.getElementById('rastreoModal').classList.add('hidden');
            document.getElementById('rastreoModal').classList.remove('flex');
        }

        async function toggleEntregadoViaAjax(expedienteId, isChecked, checkbox) {
                // Aplicar efecto visual al checkbox
                if (checkbox) {
                    checkbox.disabled = true;
                    checkbox.parentElement.classList.add('pulse');
                }
                
                    // Mostrar mensaje de éxito
                    showSuccess(isChecked ? 'Expediente seleccionado' : 'Expediente no seleccionado');
                    
                    // Actualizar la fila en la tabla
                    if (checkbox) {
                        const row = checkbox.closest('tr');
                        if (row) {
                            // Cambiar la clase de la fila
                            if (isChecked) {
                                row.classList.add('bg-green-50');
                            } else {
                                row.classList.remove('bg-green-50');
                            }
                            
                            // Actualizar la celda de estatus
                            const estatusCell = row.cells[11];
                            if (estatusCell) {
                                if (isChecked) {
                                    estatusCell.innerHTML = `<span class="text-success">Recibido</span>`;
                                } else {
                                    estatusCell.innerHTML = `<span class="text-warning">Pendiente</span>`;
                                }
                            }
                        }
                    }
                
                // Remover efecto visual
                if (checkbox) {
                    checkbox.disabled = false;
                    checkbox.parentElement.classList.remove('pulse');
                }
        }
        
        // Funciones de utilidad para mostrar mensajes
        function showError(message) {
            const errorList = document.getElementById('errorList');
            errorList.innerHTML = `<li>${message}</li>`;
            
            const errorAlert = document.getElementById('errorAlert');
            errorAlert.classList.remove('hidden');
            
            setTimeout(() => {
                errorAlert.classList.add('hidden');
            }, 5000);
            
            document.getElementById('closeErrorBtn').addEventListener('click', function() {
                errorAlert.classList.add('hidden');
            });
        }
        
        function showSuccess(message) {
            const successMessage = document.getElementById('successMessage');
            successMessage.textContent = message;
            
            const successToast = document.getElementById('successToast');
            successToast.classList.remove('hidden');
            
            setTimeout(() => {
                successToast.classList.add('hidden');
            }, 3000);
        }
        
        function closeSuccessToast() {
            document.getElementById('successToast').classList.add('hidden');
        }
        
        function showLoading(message = 'Procesando...') {
            document.getElementById('loadingMessage').textContent = message;
            document.getElementById('loadingOverlay').classList.remove('hidden');
        }
        
        function hideLoading() {
            document.getElementById('loadingOverlay').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>