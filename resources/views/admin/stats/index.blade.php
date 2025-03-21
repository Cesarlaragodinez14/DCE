{{-- resources/views/admin/stats/index.blade.php --}}

<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Estadísticas Generales de Lista de Verificación de Expedientes') }}
            </h2>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Última actualización: {{ now()->format('d/m/Y H:i') }}</span>
                <button id="btnRefresh" class="bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition-colors">
                    <ion-icon name="refresh-outline" class="w-5 h-5 text-gray-600"></ion-icon>
                </button>
            </div>
        </div>
    </x-slot>

    @push('styles')
    <style>
        /* ================== ESTILOS PRINCIPALES ================== */
        .chart-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .chart-container:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .chart-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9fafb;
        }
        
        .chart-title {
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .chart-icon {
            color: #3b82f6;
            font-size: 1.25rem;
        }
        
        .chart-body {
            padding: 1.25rem;
        }
        
        .chart-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .chart-action-btn {
            padding: 0.25rem;
            border-radius: 0.375rem;
            color: #6b7280;
            transition: all 0.2s ease;
        }
        
        .chart-action-btn:hover {
            background-color: #f3f4f6;
            color: #3b82f6;
        }
        
        .filter-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }
        
        .filter-header {
            padding: 1rem 1.25rem;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .filter-body {
            padding: 1.25rem;
        }
        
        .filter-title {
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .filter-icon {
            color: #3b82f6;
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            padding: 0.75rem 1rem;
            background-color: #f9fafb;
            border-radius: 0.375rem;
        }
        
        .breadcrumb-link {
            color: #6b7280;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .breadcrumb-link:hover {
            color: #3b82f6;
        }
        
        .breadcrumb-separator {
            margin: 0 0.5rem;
            color: #d1d5db;
        }
        
        .breadcrumb-current {
            font-weight: 500;
            color: #374151;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem;
        }
        
        @media (min-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (min-width: 1280px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        /* Estilos para las tablas */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
        
        .stats-table th {
            background-color: #f9fafb;
            font-weight: 600;
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #e5e7eb;
            color: #374151;
        }
        
        .stats-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
        }
        
        .stats-table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* ================== NAVBAR FIJO INFERIOR ================== */
        .navbar {
            position: fixed; 
            bottom: 0; 
            left: 0; 
            right: 0;
            background-color: #ffffff;
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            height: 65px;
            display: flex; 
            align-items: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .navbar-container {
            max-width: 1200px; 
            margin: 0 auto; 
            width: 100%; 
            padding: 0 10px;
            display: flex; 
            justify-content: center;
        }
        
        .navbar-links {
            display: flex; 
            align-items: center; 
            width: 100%;
            overflow-x: auto; 
            scrollbar-width: none;
            justify-content: space-between;
            padding: 0.25rem;
        }
        
        .navbar-links::-webkit-scrollbar { 
            display: none; 
        }
        
        .navbar-link {
            display: flex; 
            flex-direction: column; 
            align-items: center;
            color: #6B7280; 
            text-decoration: none; 
            transition: all 0.3s ease;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            position: relative;
        }
        
        .navbar-icon {
            font-size: 1.5rem; 
            margin-bottom: 0.25rem; 
            color: inherit; 
            transition: color 0.3s ease;
        }
        
        .navbar-text {
            font-size: 0.75rem; 
            text-align: center; 
            white-space: nowrap;
            max-width: 70px;
            font-weight: 500;
        }
        
        .navbar-link:hover { 
            color: #3B82F6; 
            background-color: #f3f4f6;
        }
        
        .navbar-link.active {
            color: #3B82F6; 
            font-weight: bold;
            background-color: #eff6ff;
        }
        
        .navbar-link.active::after {
            content: '';
            position: absolute;
            bottom: -0.25rem;
            left: 50%;
            transform: translateX(-50%);
            width: 1.5rem;
            height: 0.25rem;
            background-color: #3B82F6;
            border-radius: 9999px;
        }
        
        .navbar-link.active .navbar-icon { 
            color: #3B82F6; 
        }

        @media (max-width: 1024px) {
            .navbar-links {
                justify-content: flex-start;
                padding-bottom: 0;
            }
            
            .navbar-link {
                flex-shrink: 0;
            }
        }

        @media (max-width: 768px) {
            .navbar { 
                height: 60px; 
            }
            
            .navbar-icon { 
                font-size: 1.25rem; 
            }
            
            .navbar-text { 
                font-size: 0.7rem; 
                max-width: 60px; 
            }
        }
        
        @media (max-width: 480px) {
            .navbar { 
                height: 55px; 
            }
            
            .navbar-icon { 
                font-size: 1.125rem; 
            }
            
            .navbar-text { 
                font-size: 0.65rem; 
                max-width: 50px; 
            }
            
            .navbar-link {
                padding: 0.5rem;
            }
        }
        /* ================== FIN NAVBAR ================== */
        
        /* Animaciones y efectos */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
        
        .animate-delay-100 { animation-delay: 0.1s; }
        .animate-delay-200 { animation-delay: 0.2s; }
        .animate-delay-300 { animation-delay: 0.3s; }
        
        /* Clases para los tooltips personalizados */
        .tooltip {
            position: relative;
            display: inline-block;
        }
        
        .tooltip .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #374151;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 0.5rem;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.75rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
        
        /* Personalización de los selectores Tom-Select */
        .ts-control {
            border-radius: 0.375rem !important;
            border-color: #d1d5db !important;
            box-shadow: none !important;
            padding: 0.5rem !important;
        }
        
        .ts-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 1px #3b82f6 !important;
        }
        
        .ts-dropdown {
            border-radius: 0.375rem !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            border-color: #e5e7eb !important;
        }
        
        .ts-dropdown .option.active {
            background-color: #eff6ff !important;
            color: #1e40af !important;
        }
        
        .ts-dropdown .option:hover {
            background-color: #f3f4f6 !important;
        }
        
        /* Estilos para botones */
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        .btn-secondary {
            background-color: #6b7280;
            color: white;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-secondary:hover {
            background-color: #4b5563;
        }

        /* Scroll personalizado */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c5c5c5;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Ocultar la barra de navegación inferior cuando se está en modo impresión */
        @media print {
            .navbar {
                display: none;
            }
            
            .chart-actions {
                display: none;
            }
            
            .py-10 {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }
            
            .breadcrumb {
                display: none;
            }
        }
    </style>
    @endpush

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-6 pb-20">
        <!-- Breadcrumbs Mejorados -->
        <div class="breadcrumb">
            <a href="/dashboard" class="breadcrumb-link">
                <ion-icon name="home-outline" class="inline-block mr-1"></ion-icon> Dashboard
            </a>
            <span class="breadcrumb-separator">
                <ion-icon name="chevron-forward-outline"></ion-icon>
            </span>
            <span class="breadcrumb-current">Graficos</span>
            <span class="breadcrumb-separator">
                <ion-icon name="chevron-forward-outline"></ion-icon>
            </span>
            <span class="breadcrumb-current">Lista de Verificación</span>
        </div>

        <!-- Formulario de Filtros Mejorado -->
        <div class="" style="background-color: #FFF">
            <div class="filter-header">
                <h3 class="filter-title">
                    <ion-icon name="options-outline" class="filter-icon"></ion-icon>
                    Filtros de Búsqueda
                </h3>
                <button id="toggle-filters" class="chart-action-btn">
                    <ion-icon name="chevron-down-outline"></ion-icon>
                </button>
            </div>
            
            <div id="filter-content" class="filter-body">
                <form method="GET" action="{{ route('dashboard.charts.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                        <!-- Filtro Entrega -->
                        <div>
                            <label for="entrega" class="block text-gray-700 text-sm font-medium mb-1">
                                <ion-icon name="archive-outline" class="inline-block mr-1 text-blue-500"></ion-icon>
                                Entrega:
                            </label>
                            <select name="entrega" id="entrega" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Todas</option>
                                @foreach($entregas as $entrega)
                                    <option value="{{ $entrega->id }}" {{ request('entrega') == $entrega->id ? 'selected' : '' }}>
                                        {{ $entrega->valor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro Cuenta Pública -->
                        <div>
                            <label for="cuenta_publica" class="block text-gray-700 text-sm font-medium mb-1">
                                <ion-icon name="document-outline" class="inline-block mr-1 text-blue-500"></ion-icon>
                                Cuenta Pública:
                            </label>
                            <select name="cuenta_publica" id="cuenta_publica" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Todas</option>
                                @foreach($cuentasPublicas as $cuenta)
                                    <option value="{{ $cuenta->id }}" {{ request('cuenta_publica') == $cuenta->id ? 'selected' : '' }}>
                                        {{ $cuenta->valor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        @role("admin|AUDITOR ESPECIAL")
                        <!-- Filtro UAA -->
                        <div>
                            <label for="uaa_id" class="block text-gray-700 text-sm font-medium mb-1">
                                <ion-icon name="school-outline" class="inline-block mr-1 text-blue-500"></ion-icon>
                                UAA:
                            </label>
                            <select name="uaa_id" id="uaa_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Todas</option>
                                @foreach($uaas as $uaa)
                                    <option value="{{ $uaa->id }}" {{ request('uaa_id') == $uaa->id ? 'selected' : '' }}>
                                        {{ $uaa->valor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro DGSEG EF -->
                        <div>
                            <label for="dg_id" class="block text-gray-700 text-sm font-medium mb-1">
                                <ion-icon name="people-outline" class="inline-block mr-1 text-blue-500"></ion-icon>
                                DGSEG EF:
                            </label>
                            <select name="dg_id" id="dg_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Todas</option>
                                @foreach($dgsegs as $dgseg)
                                    <option value="{{ $dgseg->id }}" {{ request('dg_id') == $dgseg->id ? 'selected' : '' }}>
                                        {{ $dgseg->valor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endrole
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="submit" class="btn-primary">
                            <ion-icon name="filter-outline"></ion-icon> Aplicar Filtros
                        </button>

                        <a href="{{ route('dashboard.charts.index') }}" class="btn-secondary">
                            <ion-icon name="refresh-outline"></ion-icon> Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contenedor principal para las gráficas -->
        <div class="space-y-8 pb-16">

            @include('admin.stats.charts._general_status', ['containerClass' => 'chart-container animate-fade-in animate-delay-200'])
            @include('admin.stats.charts._dgseg_ef', ['containerClass' => 'chart-container animate-fade-in animate-delay-200'])
            @include('admin.stats.charts._siglas_tipo_accion', ['containerClass' => 'chart-container animate-fade-in animate-delay-200'])
            @include('admin.stats.charts._siglas_audesp', ['containerClass' => 'chart-container animate-fade-in animate-delay-200'])
            @include('admin.stats.charts._status', ['containerClass' => 'chart-container animate-fade-in'])
            @include('admin.stats.charts._uaa_estatus', ['containerClass' => 'chart-container animate-fade-in animate-delay-100'])
            @include('admin.stats.charts._ente_fiscalizado', ['containerClass' => 'chart-container animate-fade-in animate-delay-200'])
            @include('admin.stats.charts._campos_modificados', ['containerClass' => 'chart-container animate-fade-in'])
            @include('admin.stats.charts._top_users', ['containerClass' => 'chart-container animate-fade-in animate-delay-100'])
            @include('admin.stats.charts._auditorias_changes', ['containerClass' => 'chart-container animate-fade-in animate-delay-200'])

        </div>

        <!-- Barra de Navegación Fija Mejorada -->
        <nav aria-label="Barra de navegación principal" class="navbar">
            <div class="navbar-container">
                <div class="navbar-links">
                    <a href="#delivery-status" class="navbar-link" aria-label="Ir a Cambios 30D">
                        <ion-icon name="archive-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Entregas</span>
                    </a> 
                    <a href="#estatus" class="navbar-link" aria-label="Ir a Expedientes por Estatus">
                        <ion-icon name="stats-chart-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Estatus</span>
                    </a>
                    <a href="#dgseg-ef" class="navbar-link" aria-label="Ir a DGSEG EF">
                        <ion-icon name="people-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">DGSEG</span>
                    </a>
                    <a href="#siglas-tipo-accion" class="navbar-link" aria-label="Ir a Siglas Tipo Acción">
                        <ion-icon name="text-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Tipo Acc.</span>
                    </a>
                    <a href="#siglas-audesp" class="navbar-link" aria-label="Ir a Siglas AudEsp">
                        <ion-icon name="clipboard-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Siglas AE</span>
                    </a>
                    <a href="#ae-uaa-status-multiple" class="navbar-link" aria-label="Ir a Expedientes por Estatus AE UAA">
                        <ion-icon name="grid-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">AE UAA</span>
                    </a>
                    <a href="#uaa-estatus" class="navbar-link" aria-label="Ir a UAA y Estatus">
                        <ion-icon name="school-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">UAA</span>
                    </a>
                    <a href="#ente-fiscalizado" class="navbar-link" aria-label="Ir a Expedientes por Ente Fiscalizado">
                        <ion-icon name="business-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Ente Fisc.</span>
                    </a>
                    <a href="#campos-modificados" class="navbar-link" aria-label="Ir a Observaciones">
                        <ion-icon name="build-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Obs.</span>
                    </a>
                    <a href="#dg-users-comparative" class="navbar-link" aria-label="Ir a Top Usuarios">
                        <ion-icon name="people-circle-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Exp-JD</span>
                    </a>
                    <a href="#auditorias-changes" class="navbar-link" aria-label="Ir a Cambios 30D">
                        <ion-icon name="time-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Cambios</span>
                    </a> 
                </div>
            </div>
        </nav>
    </div>

    {{-- Exponer los datos en JSON para JavaScript --}}
    @php
        // Suponiendo que tu controlador pasa la variable $dashboardData
        $jsonData = json_encode($dashboardData);
    @endphp

    <script>
        // Exponer globalmente
        window.dashboardData = {!! $jsonData !!};

        // 1. Función global para crear tablas
        window.createTable = function createTable(headers, rows) {
            const table = document.createElement('table');
            table.className = "stats-table";

            const thead = document.createElement('thead');
            const trHead = document.createElement('tr');
            headers.forEach(header => {
                const th = document.createElement('th');
                th.textContent = header;
                trHead.appendChild(th);
            });
            thead.appendChild(trHead);
            table.appendChild(thead);

            const tbody = document.createElement('tbody');
            rows.forEach(row => {
                const tr = document.createElement('tr');
                headers.forEach(header => {
                    const td = document.createElement('td');
                    td.textContent = row[header] ?? 'Sin Datos';
                    tr.appendChild(td);
                });
                tbody.appendChild(tr);
            });
            table.appendChild(tbody);

            return table;
        }

        // 2. Función global para obtener colores
        const chartColors = [
            '#3B82F6','#F59E0B','#10B981','#EF4444','#8B5CF6',
            '#EC4899','#6366F1','#14B8A6','#F97316','#06B6D4',
            '#84CC16','#A855F7','#DC2626','#0EA5E9','#22C55E'
        ];
        window.getColor = function getColor(index) {
            return chartColors[index % chartColors.length] || '#000000';
        }
        
        // 3. Funciones de utilidad
        window.toggleTable = function toggleTable(tableId) {
            const table = document.getElementById(tableId);
            if (table) {
                table.classList.toggle('hidden');
            }
        }
        
        window.exportToImage = function exportToImage(chartId) {
            const canvas = document.querySelector(`#${chartId} canvas`);
            if (!canvas) return;
            
            // Crear enlace temporal para descargar
            const link = document.createElement('a');
            link.download = `grafico-${chartId}-${new Date().toISOString().split('T')[0]}.png`;
            link.href = canvas.toDataURL('image/png', 1.0);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>

    @push('scripts')
        <!-- Chart.js y Ionicons -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Inicializar los selectores con Tom-Select
                const selects = ["#entrega", "#cuenta_publica", "#uaa_id", "#dg_id"];
                selects.forEach(selector => {
                    const element = document.querySelector(selector);
                    if (element) {
                        new TomSelect(element, {
                            create: false, 
                            sortField: "text",
                            plugins: ['clear_button'],
                            placeholder: "Seleccionar...",
                            allowEmptyOption: true
                        });
                    }
                });
                
                // Toggle para mostrar/ocultar filtros
                const toggleButton = document.getElementById('toggle-filters');
                const filterContent = document.getElementById('filter-content');
                
                if (toggleButton && filterContent) {
                    toggleButton.addEventListener('click', function() {
                        filterContent.classList.toggle('hidden');
                        
                        // Cambiar el ícono del botón
                        const icon = toggleButton.querySelector('ion-icon');
                        if (filterContent.classList.contains('hidden')) {
                            icon.setAttribute('name', 'chevron-down-outline');
                        } else {
                            icon.setAttribute('name', 'chevron-up-outline');
                        }
                    });
                }
                
                // Botón de refrescar
                const refreshButton = document.getElementById('btnRefresh');
                if (refreshButton) {
                    refreshButton.addEventListener('click', function() {
                        window.location.reload();
                    });
                }
            });
        </script>

        <!-- Manejo global de scroll suave y sección activa -->
        <script>
        "use strict";
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll suave
            document.querySelectorAll('nav a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        const offsetTop = target.offsetTop - 20;
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Resaltar sección activa
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.navbar-link');
            
            function highlightActiveSection() {
                const scrollPosition = window.scrollY + (window.innerHeight / 3);
                
                let currentSection = '';
                
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.offsetHeight;
                    
                    if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                        currentSection = section.getAttribute('id');
                    }
                });
                
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    const href = link.getAttribute('href').substring(1);
                    
                    if (href === currentSection) {
                        link.classList.add('active');
                    }
                });
            }
            
            window.addEventListener('scroll', highlightActiveSection);
            highlightActiveSection(); // Llamar la función al cargar para resaltar la sección inicial
            
            // Manejar el evento hashchange para cuando se hace clic en un enlace con #
            window.addEventListener('hashchange', function() {
                const hash = window.location.hash.substring(1);
                const section = document.getElementById(hash);
                
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth' });
                }
            });
            
            // Si hay un hash en la URL al cargar, navegar a esa sección
            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                const section = document.getElementById(hash);
                
                if (section) {
                    setTimeout(() => {
                        section.scrollIntoView({ behavior: 'smooth' });
                    }, 100);
                }
            }
        });
        </script>
    @endpush
</x-app-layout>