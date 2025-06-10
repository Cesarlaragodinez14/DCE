@php
    // Definición de las tarjetas del dashboard
    $dashboardCards = [
        [
            'route' => route('dashboard.upload-excel.form'),
            'icon' => 'cloud-upload-outline',
            'text' => 'Carga de información de los Expedientes de Acción',
            'description' => 'Subir archivos Excel con acciones para procesar',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'primary',
        ],
        [
            'route' => route('dashboard.progress'),
            'icon' => 'analytics-outline',
            'text' => 'Proceso de Carga de información de los Expedientes de Acción',
            'description' => 'Seguimiento al proceso de carga de información',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'info',
        ],
        [ 
            'route' => route('dashboard.oficio-uaa'),
            'icon' => 'mail-outline',
            'text' => 'Envío de Oficio a las UAA',
            'description' => 'Gestionar envío de oficios a Unidades de Auditoría',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'success',
        ],
        [
            'route' => route('dashboard.distribucion'),
            'icon' => 'swap-horizontal-outline',
            'text' => 'Distribución de Expedientes de Acción',
            'description' => 'Asignar y distribuir acciones a los departamentos',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'warning',
        ],
        [
            'route' => route('dashboard.expedientes.entrega'),
            'icon' => 'calendar-number-outline',
            'text' => 'Programación de Entrega de Expedientes de Acción por la UAA',
            'description' => 'Programar Fechas y Responsables de Entrega de Expedientes',
            'roles' => ['Director General', 'admin', 'Responsable de la programación por UAA'],
            'section' => 'Principal',
            'color' => 'primary',
        ],
        [
            'route' => route('recepcion.index'),
            'icon' => 'file-tray-full-outline',
            'text' => 'Entrega - Recepción de Expedientes',
            'description' => 'Gestionar la entrega - recepción de los expedientes',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'success',
        ],
        [
            'route' => route('programacion-historial.index'),
            'icon' => 'calendar-outline',
            'text' => 'Histórico de la entrega - recepción de expedientes',
            'description' => 'Ver historico de la entrega - recepción de los expedientes',
            'roles' => ['admin', 'Jefe de Departamento', 'Responsable de la programacion por UAA', 'Auditor habilitado UAA', 'Director General'],
            'section' => 'Principal',
            'color' => 'info',
        ],
        [
            'route' => url('/dashboard/all-auditorias'),
            'icon' => 'checkbox-outline',
            'text' => 'Revisión de Expedientes',
            'description' => 'Revisar y validar expedientes en proceso',
            'roles' => ['Auditor habilitado', 'Director General', 'Jefe de Departamento', 'admin', 'Auditor habilitado', 'Auditor habilitado UAA'],
            'section' => 'Principal',
            'color' => 'primary',
        ],
        [
            'route' => route('dashboard.pdf-histories.index'),
            'icon' => 'search-outline',
            'text' => 'Histórico de Listas de Verificación',
            'description' => 'Consultar historiales de listas de verificación',
            'roles' => ['Jefe de Departamento', 'admin', 'Auditor habilitado', 'Auditor habilitado UAA', 'Director General'],
            'section' => 'Principal',
            'color' => 'warning',
        ],
        [
            'route' => route('auditorias.show'),
            'icon' => 'hourglass-outline',
            'text' => 'Histórico de Observaciones en la Revisión de los Expediente de Acción',
            'description' => 'Consultar observaciones derivadas en la revisión de los Expediente de acción',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'info',
        ],
        [
            'route' => route('dashboard.charts.index'),
            'icon' => 'pie-chart-outline',
            'text' => 'Revisión de Expedientes',
            'description' => 'Gráficos y estadisticas',
            'roles' => ['admin', 'AUDITOR ESPECIAL', 'Director General SEG', 'AECF', 'AED', 'AEGF', 'DGUAA'],
            'section' => 'Graficos',
            'color' => 'primary',
        ],
        [
            'route' => route('dashboard.charts.entregas'),
            'icon' => 'pie-chart-outline',
            'text' => 'Entrega-recepción de expedientes de acción',
            'description' => 'Ver Informes y Estadísticas de Entrega y Repeción de Expedientes',
            'roles' => ['admin', 'AUDITOR ESPECIAL', 'Director General SEG', 'AECF', 'AED', 'AEGF', 'DGUAA'],
            'section' => 'Graficos',
            'color' => 'success',
        ],

        // Sección Administración (solo para 'admin')
        [
            'route' => route('admin.roles.create'),
            'icon' => 'people-outline',
            'text' => 'Crear Nuevo Rol',
            'description' => 'Definir nuevos roles en el sistema',
            'roles' => ['admin'],
            'section' => 'Administración',
            'color' => 'admin',
        ],
        [
            'route' => route('admin.permissions.create'),
            'icon' => 'key-outline',
            'text' => 'Crear Nuevo Permiso',
            'description' => 'Configurar nuevos permisos para roles',
            'roles' => ['admin'],
            'section' => 'Administración',
            'color' => 'admin',
        ],
        [
            'route' => route('admin.roles-permissions'),
            'icon' => 'settings-outline',
            'text' => 'Gestión de Permisos',
            'description' => 'Administrar permisos de usuarios',
            'roles' => ['admin'],
            'section' => 'Administración',
            'color' => 'admin',
        ],
        [
            'route' => route('users.index'),
            'icon' => 'person-outline',
            'text' => 'Gestión de Usuarios',
            'description' => 'Administrar usuarios del sistema',
            'roles' => ['admin'],
            'section' => 'Administración',
            'color' => 'admin',
        ],
    ];

    // Función actualizada para renderizar una tarjeta
    function renderDashboardCard($card) {
        $colorClass = 'color-' . ($card['color'] ?? 'gray');
        
        return '
            <a href="' . e($card['route']) . '" class="dashboard-card ' . $colorClass . '">
                <div class="card-icon-container">
                    <ion-icon name="' . e($card['icon']) . '" class="dashboard-icon"></ion-icon>
                </div>
                <div class="card-content">
                    <h3 class="card-title">' . e($card['text']) . '</h3>
                    <p class="card-description">' . e($card['description'] ?? '') . '</p>
                </div>
            </a>
        ';
    }

    // Obtener los roles del usuario actual
    $userRoles = auth()->user()->roles->pluck('name')->toArray();
    
    // Agrupar las tarjetas por sección y filtrar por roles
    $sections = [];
    $availableSections = [];
    
    foreach ($dashboardCards as $card) {
        $section = $card['section'] ?? 'Principal';
        
        // Verificar si el usuario tiene acceso a esta tarjeta
        $hasAccess = !empty(array_intersect($card['roles'], $userRoles));
        
        if ($hasAccess) {
            $sections[$section][] = $card;
            
            // Registrar esta sección como disponible
            if (!in_array($section, $availableSections)) {
                $availableSections[] = $section;
            }
        }
    }
    
    // Si no hay secciones disponibles, mostrar un mensaje de error
    if (empty($availableSections)) {
        // Esto no debería ocurrir normalmente, pero por si acaso
        $noAccessMessage = 'No tienes acceso a ninguna funcionalidad del sistema. Contacta al administrador.';
    }
    
    // Determinar la sección activa
    $requestedSection = request()->query('section');
    
    // Si la sección solicitada existe y el usuario tiene acceso a ella
    if ($requestedSection && in_array($requestedSection, $availableSections)) {
        $activeSection = $requestedSection;
    } 
    // Si no, usar la primera sección disponible
    else if (!empty($availableSections)) {
        $activeSection = $availableSections[0];
    } 
    // Si no hay secciones disponibles (caso extremo)
    else {
        $activeSection = null;
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex items-center">
                <span class="text-xs text-gray-600 mr-2">Bienvenido, {{ auth()->user()->name }}</span>
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </div>
    </x-slot>

    <style>
        /* Variables CSS para consistencia */
        :root {
            --primary-color: #3b82f6;
            --primary-light: #60a5fa;
            --primary-dark: #2563eb;
            --success-color: #10b981;
            --success-light: #34d399;
            --success-dark: #059669;
            --warning-color: #f59e0b;
            --warning-dark: #d97706;
            --danger-color: #ef4444;
            --danger-dark: #b91c1c;
            --info-color: #38bdf8;
            --info-dark: #0369a1;
            --admin-color: #a78bfa;
            --admin-dark: #7c3aed;
            
            --text-color: #1f2937;
            --text-muted: #6b7280;
            --bg-light: #f3f4f6;
            --border-color: #e5e7eb;
            
            --radius-sm: 0.25rem;
            --radius-md: 0.375rem;
            --radius-lg: 0.5rem;
            
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            
            --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-normal: 200ms cubic-bezier(0.4, 0, 0.2, 1);
            
            --primary-gradient: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
        }

        /* Estilos base para las tarjetas del dashboard */
        .dashboard-card {
            display: flex;
            align-items: center;
            background-color: white;
            padding: 0.875rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            text-decoration: none;
            position: relative;
            overflow: hidden;
            font-size: 0.8125rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            will-change: transform, box-shadow;
        }

        /* Overlay de gradiente sutil */
        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.7) 0%, rgba(255,255,255,0) 100%);
            z-index: 0;
            transition: opacity 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .dashboard-card:hover::before {
            opacity: 0.8;
        }

        /* Indicador de borde con gradiente */
        .dashboard-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-gradient);
            transition: width 0.2s ease;
        }

        .dashboard-card:hover::after {
            width: 6px;
        }

        /* Contenedor del icono */
        .card-icon-container {
            flex-shrink: 0;
            margin-right: 0.75rem;
            position: relative;
            z-index: 1;
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-md);
            background: var(--bg-light);
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .dashboard-card:hover .card-icon-container {
            transform: scale(1.05);
        }

        .dashboard-icon {
            font-size: 1.25rem;
            color: var(--primary-color);
            transition: color 0.2s ease;
        }

        /* Contenido de la tarjeta */
        .card-content {
            flex-grow: 1;
            position: relative;
            z-index: 1;
        }

        .card-title {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.125rem;
            color: var(--text-color);
            transition: color 0.2s ease;
        }

        .dashboard-card:hover .card-title {
            color: var(--primary-color);
        }

        .card-description {
            font-size: 0.75rem;
            color: var(--text-muted);
            line-height: 1.2;
        }

        /* Esquemas de colores para los bordes */
        .dashboard-card.color-primary::after {
            background: linear-gradient(to bottom, var(--primary-light), var(--primary-dark));
        }

        .dashboard-card.color-success::after {
            background: linear-gradient(to bottom, var(--success-light), var(--success-dark));
        }

        .dashboard-card.color-warning::after {
            background: linear-gradient(to bottom, var(--warning-color), var(--warning-dark));
        }

        .dashboard-card.color-danger::after {
            background: linear-gradient(to bottom, var(--danger-color), var(--danger-dark));
        }

        .dashboard-card.color-info::after {
            background: linear-gradient(to bottom, var(--info-color), var(--info-dark));
        }

        .dashboard-card.color-gray::after {
            background: linear-gradient(to bottom, #94a3b8, #475569);
        }

        .dashboard-card.color-admin::after {
            background: linear-gradient(to bottom, var(--admin-color), var(--admin-dark));
        }

        /* Efectos de colores en iconos para secciones específicas */
        .dashboard-card.color-primary .card-icon-container {
            background: rgba(59, 130, 246, 0.1);
        }
        .dashboard-card.color-primary:hover .card-icon-container {
            background: var(--primary-gradient);
        }
        .dashboard-card.color-primary:hover .dashboard-icon {
            color: white;
        }

        .dashboard-card.color-success .card-icon-container {
            background: rgba(16, 185, 129, 0.1);
        }
        .dashboard-card.color-success:hover .card-icon-container {
            background: linear-gradient(135deg, var(--success-light), var(--success-dark));
        }
        .dashboard-card.color-success:hover .dashboard-icon {
            color: white;
        }

        .dashboard-card.color-warning .card-icon-container {
            background: rgba(245, 158, 11, 0.1);
        }
        .dashboard-card.color-warning:hover .card-icon-container {
            background: linear-gradient(135deg, var(--warning-color), var(--warning-dark));
        }
        .dashboard-card.color-warning:hover .dashboard-icon {
            color: white;
        }

        .dashboard-card.color-info .card-icon-container {
            background: rgba(56, 189, 248, 0.1);
        }
        .dashboard-card.color-info:hover .card-icon-container {
            background: linear-gradient(135deg, var(--info-color), var(--info-dark));
        }
        .dashboard-card.color-info:hover .dashboard-icon {
            color: white;
        }

        .dashboard-card.color-admin .card-icon-container {
            background: rgba(168, 139, 250, 0.1);
        }
        .dashboard-card.color-admin:hover .card-icon-container {
            background: linear-gradient(135deg, var(--admin-color), var(--admin-dark));
        }
        .dashboard-card.color-admin:hover .dashboard-icon {
            color: white;
        }

        /* Estilo para la barra de búsqueda */
        #dashboardSearch {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }

        #dashboardSearch:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
        }

        /* Adaptaciones al diseño de pantalla */
        @media (max-width: 1280px) {
            .grid-cols-4 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 1024px) {
            .grid-cols-4, .grid-cols-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .grid-cols-4, .grid-cols-3, .grid-cols-2 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
            
            .dashboard-card {
                padding: 0.75rem;
            }
            
            .card-icon-container {
                width: 2rem;
                height: 2rem;
            }
            
            .dashboard-icon {
                font-size: 1rem;
            }
        }

        /* Animación para las tarjetas - usando una solo al cargar */
        @keyframes fadeInUp {
            from { 
                opacity: 0; 
                transform: translateY(10px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .dashboard-card {
            opacity: 0;
            animation: fadeInUp 0.3s ease-out forwards;
        }

        /* Establecer delays progresivos */
        .dashboard-card:nth-child(1) { animation-delay: 0.05s; }
        .dashboard-card:nth-child(2) { animation-delay: 0.1s; }
        .dashboard-card:nth-child(3) { animation-delay: 0.15s; }
        .dashboard-card:nth-child(4) { animation-delay: 0.2s; }
        .dashboard-card:nth-child(5) { animation-delay: 0.25s; }
        .dashboard-card:nth-child(6) { animation-delay: 0.3s; }
        .dashboard-card:nth-child(7) { animation-delay: 0.35s; }
        .dashboard-card:nth-child(8) { animation-delay: 0.4s; }

        /* Estilo para las pestañas de navegación */
        nav a {
            font-size: 0.75rem;
            padding: 0.325rem 0.625rem;
            border-radius: var(--radius-sm);
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        nav a.bg-blue-100 {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Alerta de sin acceso */
        .bg-yellow-50 {
            padding: 0.75rem;
            font-size: 0.75rem;
            border-radius: var(--radius-md);
        }

        /* Mensaje de no resultados */
        #noSearchResults {
            padding: 1.5rem;
            border-radius: var(--radius-md);
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        #noSearchResults.show {
            opacity: 1;
            transform: translateY(0);
        }

        #noSearchResults ion-icon {
            font-size: 2rem;
            color: var(--text-muted);
        }

        #noSearchResults p {
            font-size: 0.75rem;
            margin-top: 0.5rem;
        }

        #clearSearch {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
            background-color: rgba(59, 130, 246, 0.1);
        }

        #clearSearch:hover {
            background-color: rgba(59, 130, 246, 0.2);
        }
    </style>

    <div class="py-4">
        <div class="mx-auto" style="max-width: 98%">
            @if(empty($availableSections))
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <ion-icon name="alert-circle" class="h-4 w-4 text-yellow-400"></ion-icon>
                        </div>
                        <div class="ml-2">
                            <p class="text-xs text-yellow-700">
                                {{ $noAccessMessage }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Tabs de navegación por secciones -->
                @if(count($availableSections) > 1)
                    <div class="mb-4 border-b border-gray-200">
                        <nav class="flex space-x-3 overflow-x-auto pb-1" aria-label="Secciones">
                            @foreach($availableSections as $sectionName)
                                <a href="?section={{ $sectionName }}" 
                                   class="px-3 py-1 text-xs font-medium rounded-md {{ $activeSection === $sectionName ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">
                                    {{ $sectionName }}
                                </a>
                            @endforeach
                        </nav>
                    </div>
                @endif

                <!-- Barra de búsqueda -->
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <ion-icon name="search-outline" class="w-4 h-4 text-gray-400"></ion-icon>
                        </div>
                        <input style="padding-left: 30px;" id="dashboardSearch" type="text" class="block w-full text-xs border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Buscar acciones o herramientas...">
                    </div>
                </div>

                <!-- Mostrar tarjetas de la sección activa -->
                @if($activeSection && !empty($sections[$activeSection]))
                    <div id="dashboardCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($sections[$activeSection] as $card)
                            {!! renderDashboardCard($card) !!}
                        @endforeach
                    </div>
                    
                    <!-- Mensaje de búsqueda sin resultados (inicialmente oculto) -->
                    <div id="noSearchResults" class="bg-white rounded-lg shadow-sm p-4 text-center hidden">
                        <ion-icon name="search" class="w-8 h-8 mx-auto text-gray-400"></ion-icon>
                        <p class="mt-2 text-xs text-gray-600">No se encontraron herramientas que coincidan con tu búsqueda.</p>
                        <button id="clearSearch" class="mt-2 text-blue-500 hover:text-blue-700 text-xs font-medium">
                            Limpiar búsqueda
                        </button>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                        <ion-icon name="information-circle-outline" class="w-8 h-8 mx-auto text-blue-400"></ion-icon>
                        <p class="mt-2 text-xs text-gray-600">No hay opciones disponibles en esta sección para tu perfil.</p>
                        @if(count($availableSections) > 0)
                            <p class="mt-1 text-xs text-gray-500">Puedes navegar a otra sección utilizando las pestañas superiores.</p>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('dashboardSearch');
            const cards = document.querySelectorAll('.dashboard-card');
            const cardsContainer = document.getElementById('dashboardCards');
            const noResultsEl = document.getElementById('noSearchResults');
            const clearSearchBtn = document.getElementById('clearSearch');
            
            if (searchInput && cards.length > 0) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let hasResults = false;
                    
                    cards.forEach(card => {
                        const title = card.querySelector('.card-title').textContent.toLowerCase();
                        const description = card.querySelector('.card-description').textContent.toLowerCase();
                        
                        if (title.includes(searchTerm) || description.includes(searchTerm)) {
                            card.style.display = '';
                            hasResults = true;
                        } else {
                            card.style.display = 'none';
                        }
                    });
                    
                    // Mostrar u ocultar mensaje de no resultados
                    if (hasResults) {
                        noResultsEl.classList.remove('show');
                        setTimeout(() => { noResultsEl.style.display = 'none'; }, 300);
                    } else {
                        noResultsEl.style.display = 'block';
                        setTimeout(() => { noResultsEl.classList.add('show'); }, 10);
                    }
                });
                
                // Limpiar búsqueda
                if (clearSearchBtn) {
                    clearSearchBtn.addEventListener('click', function() {
                        searchInput.value = '';
                        searchInput.dispatchEvent(new Event('input'));
                        searchInput.focus();
                    });
                }
            }
        });
    </script>
</x-app-layout>