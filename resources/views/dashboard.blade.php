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
            'color' => 'gray',
        ],
        [
            'route' => route('dashboard.progress'),
            'icon' => 'analytics-outline',
            'text' => 'Proceso de Acciones',
            'description' => 'Seguimiento del proceso de acciones en curso',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'gray',
        ],
        [
            'route' => route('dashboard.oficio-uaa'),
            'icon' => 'mail-outline',
            'text' => 'Envío de Oficio a las UAA',
            'description' => 'Gestionar envío de oficios a Unidades de Auditoría',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'gray',
        ],
        [
            'route' => route('dashboard.distribucion'),
            'icon' => 'swap-horizontal-outline',
            'text' => 'Distribución de Acciones',
            'description' => 'Asignar y distribuir acciones a los departamentos',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'gray',
        ],
        [
            'route' => route('dashboard.expedientes.entrega'),
            'icon' => 'calendar-number-outline',
            'text' => 'Programación de Entrega',
            'description' => 'Programar fechas de entrega de expedientes',
            'roles' => ['Director General', 'admin', 'Responsable de la programación por UAA'],
            'section' => 'Principal',
            'color' => 'gray',
        ],
        [
            'route' => route('recepcion.index'),
            'icon' => 'file-tray-full-outline',
            'text' => 'Recepción de Expedientes',
            'description' => 'Gestionar la recepción de expedientes',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'gray',
        ],
        [
            'route' => route('programacion-historial.index'),
            'icon' => 'calendar-outline',
            'text' => 'Historial de Movimientos de Expedientes',
            'description' => 'Ver historial de programación de expedientes',
            'roles' => ['admin', 'Jefe de Departamento', 'Responsable de la programacion por UAA', 'Auditor habilitado UAA', 'Director General'],
            'section' => 'Principal',
            'color' => 'gray',
        ],
        [
            'route' => url('/dashboard/all-auditorias'),
            'icon' => 'checkbox-outline',
            'text' => 'Revisión de Expedientes',
            'description' => 'Revisar y validar expedientes en proceso',
            'roles' => ['Auditor habilitado', 'Director General', 'Jefe de Departamento', 'admin', 'Auditor habilitado', 'Auditor habilitado UAA'],
            'section' => 'Principal',
            'color' => 'gray',
        ],
        [
            'route' => route('dashboard.pdf-histories.index'),
            'icon' => 'search-outline',
            'text' => 'Histórico de Listas de Verificación',
            'description' => 'Consultar historiales de listas de verificación',
            'roles' => ['Jefe de Departamento', 'admin', 'Auditor habilitado', 'Auditor habilitado UAA', 'Director General'],
            'section' => 'Principal',
            'color' => 'gray',
        ],
        [
            'route' => route('auditorias.show'),
            'icon' => 'hourglass-outline',
            'text' => 'Histórico de Expedientes',
            'description' => 'Consultar registros históricos de Expedientes',
            'roles' => ['admin'],
            'section' => 'Principal',
            'color' => 'gray',
        ],
        [
            'route' => route('dashboard.charts.index'),
            'icon' => 'pie-chart-outline',
            'text' => 'Listas de Verificación',
            'description' => 'Ver Informes y Estadísticas de las Listas de Verificación de Expedientes',
            'roles' => ['admin', 'AUDITOR ESPECIAL', 'Director General SEG', 'AECF', 'AED', 'AEGF', 'DGUAA'],
            'section' => 'Graficos',
            'color' => 'gray',
        ],
        [
            'route' => route('dashboard.charts.entregas'),
            'icon' => 'pie-chart-outline',
            'text' => 'Recepción y Entregas',
            'description' => 'Ver Informes y Estadísticas de Entrega y Repeción de Expedientes',
            'roles' => ['admin', 'AUDITOR ESPECIAL', 'Director General SEG', 'AECF', 'AED', 'AEGF', 'DGUAA'],
            'section' => 'Graficos',
            'color' => 'gray',
        ],

        // Sección Administración (solo para 'admin')
        [
            'route' => route('admin.roles.create'),
            'icon' => 'people-outline',
            'text' => 'Crear Nuevo Rol',
            'description' => 'Definir nuevos roles en el sistema',
            'roles' => ['admin'],
            'section' => 'Administración',
            'color' => 'gray',
        ],
        [
            'route' => route('admin.permissions.create'),
            'icon' => 'key-outline',
            'text' => 'Crear Nuevo Permiso',
            'description' => 'Configurar nuevos permisos para roles',
            'roles' => ['admin'],
            'section' => 'Administración',
            'color' => 'gray',
        ],
        [
            'route' => route('admin.roles-permissions'),
            'icon' => 'settings-outline',
            'text' => 'Gestión de Permisos',
            'description' => 'Administrar permisos de usuarios',
            'roles' => ['admin'],
            'section' => 'Administración',
            'color' => 'gray',
        ],
        [
            'route' => route('users.index'),
            'icon' => 'person-outline',
            'text' => 'Gestión de Usuarios',
            'description' => 'Administrar usuarios del sistema',
            'roles' => ['admin'],
            'section' => 'Administración',
            'color' => 'gray',
        ],
    ];

    // Definición de los colores de las tarjetas
    $colorClasses = [
        'gray' => [
            'bg' => 'bg-gray-50',
            'icon' => 'text-gray-600',
            'text' => 'text-gray-800',
            'border' => 'border-gray-200',
            'hover' => 'hover:bg-gray-100',
        ],
    ];

    // Función para renderizar una tarjeta
    function renderDashboardCard($card, $colorClasses) {
        $colorClass = $colorClasses[$card['color'] ?? 'blue'];
        
        return '
            <a href="' . e($card['route']) . '" class="dashboard-card ' . $colorClass['bg'] . ' ' . $colorClass['border'] . ' ' . $colorClass['hover'] . '">
                <div class="card-icon-container">
                    <ion-icon name="' . e($card['icon']) . '" class="dashboard-icon ' . $colorClass['icon'] . '"></ion-icon>
                </div>
                <div class="card-content">
                    <h3 class="card-title ' . $colorClass['text'] . '">' . e($card['text']) . '</h3>
                    <p class="card-description">' . e($card['description'] ?? '') . '</p>
                </div>
            </a>
        ';
    }

    // Agrupar las tarjetas por sección
    $sections = [];
    foreach ($dashboardCards as $card) {
        $section = $card['section'] ?? 'Principal';
        $sections[$section][] = $card;
    }

    // Obtener los roles del usuario actual
    $userRoles = auth()->user()->roles->pluck('name')->toArray();
    
    // Obtener la sección activa (si hay una en la URL)
    $activeSection = request()->query('section', 'Principal');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex items-center">
                <span class="text-sm text-gray-600 mr-2">Bienvenido, {{ auth()->user()->name }}</span>
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto" style="max-width: 95%">
            <!-- Tabs de navegación por secciones -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="flex space-x-4 overflow-x-auto pb-1" aria-label="Secciones">
                    @foreach(array_keys($sections) as $sectionName)
                        @php
                            $sectionHasCards = collect($sections[$sectionName])->filter(function($card) use ($userRoles) {
                                return !empty(array_intersect($card['roles'], $userRoles));
                            })->isNotEmpty();
                        @endphp
                        
                        @if($sectionHasCards)
                            <a href="?section={{ $sectionName }}" 
                               class="px-3 py-2 text-sm font-medium rounded-md {{ $activeSection === $sectionName ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">
                                {{ $sectionName }}
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>

            <!-- Barra de búsqueda -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <ion-icon name="search-outline" class="w-5 h-5 text-gray-400"></ion-icon>
                    </div>
                    <input style="padding-left: 40px;" id="dashboardSearch" type="text" class="block w-full p-2 pl-10 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Buscar acciones o herramientas...">
                </div>
            </div>

            <!-- Mostrar tarjetas de la sección activa -->
            @php
                $visibleCards = collect($sections[$activeSection] ?? [])->filter(function($card) use ($userRoles) {
                    return !empty(array_intersect($card['roles'], $userRoles));
                });
            @endphp

            @if($visibleCards->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($visibleCards as $card)
                        {!! renderDashboardCard($card, $colorClasses) !!}
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <ion-icon name="alert-circle-outline" class="w-12 h-12 mx-auto text-gray-400"></ion-icon>
                    <p class="mt-2 text-gray-600">No hay opciones disponibles en esta sección para tu perfil.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- CSS Personalizado -->
    <style>
        .dashboard-card {
            display: flex;
            align-items: center;
            background-color: white;
            padding: 1.25rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-icon-container {
            flex-shrink: 0;
            margin-right: 1rem;
        }
        
        .dashboard-icon {
            font-size: 2rem;
        }
        
        .card-content {
            flex-grow: 1;
        }
        
        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .card-description {
            font-size: 0.875rem;
            color: #6B7280;
            line-height: 1.25;
        }
        
        /* Estilo para la barra de búsqueda */
        #dashboardSearch:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
        }
        
        /* Animación para las tarjetas */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .dashboard-card {
            animation: fadeIn 0.3s ease-out forwards;
        }
    </style>

    <!-- Script para búsqueda en tiempo real -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('dashboardSearch');
            const cards = document.querySelectorAll('.dashboard-card');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                cards.forEach(card => {
                    const title = card.querySelector('.card-title').textContent.toLowerCase();
                    const description = card.querySelector('.card-description').textContent.toLowerCase();
                    
                    if (title.includes(searchTerm) || description.includes(searchTerm)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</x-app-layout>