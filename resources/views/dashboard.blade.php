@php
    // Definición de las tarjetas del dashboard
    $dashboardCards = [
        [
            'route' => route('dashboard.upload-excel.form'),
            'icon' => 'cloud-upload-outline',
            'text' => 'Cargar Acciones',
            'description' => 'Subir archivos Excel con acciones para procesar',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'primary',
        ],
        [
            'route' => route('dashboard.progress'),
            'icon' => 'analytics-outline',
            'text' => 'Proceso de Acciones',
            'description' => 'Seguimiento del proceso de acciones en curso',
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
            'text' => 'Distribución de Acciones',
            'description' => 'Asignar y distribuir acciones a los departamentos',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'warning',
        ],
        [
            'route' => route('dashboard.expedientes.entrega'),
            'icon' => 'calendar-number-outline',
            'text' => 'Programación de Entrega',
            'description' => 'Programar fechas de entrega de expedientes',
            'roles' => ['Director General', 'admin', 'Responsable de la programación por UAA'],
            'section' => 'Principal',
            'color' => 'primary',
        ],
        [
            'route' => route('recepcion.index'),
            'icon' => 'file-tray-full-outline',
            'text' => 'Recepción de Expedientes',
            'description' => 'Gestionar la recepción de expedientes',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'success',
        ],
        [
            'route' => route('programacion-historial.index'),
            'icon' => 'calendar-outline',
            'text' => 'Historial de Movimientos de Expedientes',
            'description' => 'Ver historial de programación de expedientes',
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
            'text' => 'Histórico de Expedientes',
            'description' => 'Consultar registros históricos de Expedientes',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'info',
        ],
        [
            'route' => route('dashboard.charts.index'),
            'icon' => 'pie-chart-outline',
            'text' => 'Listas de Verificación',
            'description' => 'Ver Informes y Estadísticas de las Listas de Verificación de Expedientes',
            'roles' => ['admin', 'AUDITOR ESPECIAL', 'Director General SEG', 'AECF', 'AED', 'AEGF', 'DGUAA'],
            'section' => 'Graficos',
            'color' => 'primary',
        ],
        [
            'route' => route('dashboard.charts.entregas'),
            'icon' => 'pie-chart-outline',
            'text' => 'Recepción y Entregas',
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
        /* Estilos mejorados para las tarjetas del dashboard */
.dashboard-card {
    display: flex;
    align-items: center;
    background-color: white;
    padding: 0.875rem;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    transition: var(--transition-normal);
    border: 1px solid var(--border-color);
    text-decoration: none;
    position: relative;
    overflow: hidden;
    font-size: 0.8125rem;
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
    transition: all 0.3s ease;
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
    transition: all 0.3s ease;
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
    transition: all 0.3s ease;
}

.dashboard-card:hover .card-icon-container {
    transform: scale(1.05);
    background: var(--primary-gradient);
}

.dashboard-icon {
    font-size: 1.25rem;
    transition: all 0.3s ease;
    color: var(--primary-color);
}

.dashboard-card:hover .dashboard-icon {
    color: white;
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
    transition: all 0.3s ease;
}

.dashboard-card:hover .card-title {
    color: var(--primary-color);
}

.card-description {
    font-size: 0.75rem;
    color: var(--text-muted);
    line-height: 1.2;
}

/* Esquemas de colores */
.dashboard-card.color-primary::after {
    background: linear-gradient(to bottom, var(--primary-light), var(--primary-dark));
}

.dashboard-card.color-success::after {
    background: linear-gradient(to bottom, var(--success-light), var(--success-dark));
}

.dashboard-card.color-warning::after {
    background: linear-gradient(to bottom, #f59e0b, #d97706);
}

.dashboard-card.color-danger::after {
    background: linear-gradient(to bottom, #ef4444, #b91c1c);
}

.dashboard-card.color-info::after {
    background: linear-gradient(to bottom, #38bdf8, #0369a1);
}

.dashboard-card.color-gray::after {
    background: linear-gradient(to bottom, #94a3b8, #475569);
}

.dashboard-card.color-admin::after {
    background: linear-gradient(to bottom, #a78bfa, #7c3aed);
}

/* Modificadores para secciones específicas */
.dashboard-card[href*="admin"] .card-icon-container,
.dashboard-card.color-admin .card-icon-container {
    background: rgba(168, 139, 250, 0.1);
}

.dashboard-card[href*="admin"]:hover .card-icon-container,
.dashboard-card.color-admin:hover .card-icon-container {
    background: linear-gradient(135deg, #a78bfa, #7c3aed);
}

.dashboard-card[href*="charts"] .card-icon-container,
.dashboard-card.color-info .card-icon-container {
    background: rgba(56, 189, 248, 0.1);
}

.dashboard-card[href*="charts"]:hover .card-icon-container,
.dashboard-card.color-info:hover .card-icon-container {
    background: linear-gradient(135deg, #38bdf8, #0369a1);
}

/* Estilo para la barra de búsqueda */
#dashboardSearch {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    border-radius: var(--radius-md);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    transition: var(--transition-normal);
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

/* Animación para las tarjetas */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.dashboard-card {
    animation: fadeIn 0.3s ease-out forwards;
}

.dashboard-card:nth-child(1) { animation-delay: 0.05s; }
.dashboard-card:nth-child(2) { animation-delay: 0.1s; }
.dashboard-card:nth-child(3) { animation-delay: 0.15s; }
.dashboard-card:nth-child(4) { animation-delay: 0.2s; }
.dashboard-card:nth-child(5) { animation-delay: 0.25s; }
.dashboard-card:nth-child(6) { animation-delay: 0.3s; }
.dashboard-card:nth-child(7) { animation-delay: 0.35s; }
.dashboard-card:nth-child(8) { animation-delay: 0.4s; }

/* Animación para tarjetas al hacer hover (sutil rebote) */
@keyframes subtleBounce {
    0%, 100% { transform: translateY(-2px); }
    50% { transform: translateY(-4px); }
}

.dashboard-card:hover {
    animation: subtleBounce 0.4s ease-in-out;
}

/* Estilo para las pestañas de navegación */
nav a {
    font-size: 0.75rem;
    padding: 0.325rem 0.625rem;
    border-radius: var(--radius-sm);
    transition: var(--transition-fast);
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
    transition: var(--transition-fast);
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
</x-app-layout>