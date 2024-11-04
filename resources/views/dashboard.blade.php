@php
    // Definición de las tarjetas del dashboard
    $dashboardCards = [

        [
            'route' => route('dashboard.upload-excel.form'),
            'icon' => 'cloud-upload-outline',
            'text' => 'Cargar Acciones',
            'roles' => ['admin'],
            'section' => 'Principal',
        ],
        [
            'route' => route('dashboard.progress'),
            'icon' => 'analytics-outline',
            'text' => 'Proceso de Acciones',
            'roles' => ['admin'],
            'section' => 'Principal',
        ],
        [
            'route' => route('dashboard.oficio-uaa'),
            'icon' => 'mail-outline',
            'text' => 'Envío de Oficio a las UAA',
            'roles' => ['admin'],
            'section' => 'Principal',
        ],
        // Sección Principal
        [
            'route' => route('dashboard.distribucion'),
            'icon' => 'swap-horizontal-outline',
            'text' => 'Distribución de Acciones',
            'roles' => ['Auditor habilitado', 'admin'],
            'section' => 'Principal',
        ],
        [
            'route' => route('dashboard.expedientes.entrega'),
            'icon' => 'calendar-number-outline',
            'text' => 'Programación de Entrega de Expedientes',
            'roles' => ['Director General', 'admin'],
            'section' => 'Principal',
        ],
        [
            'route' => route('dashboard.expedientes.recepcion'),
            'icon' => 'file-tray-full-outline',
            'text' => 'Recepción de Expedientes',
            'roles' => ['Auditor habilitado', 'admin'],
            'section' => 'Principal',
        ],
        [
            'route' => url('/dashboard/all-auditorias'),
            'icon' => 'checkbox-outline',
            'text' => 'Revisión de expediente',
            'roles' => ['Auditor habilitado', 'Director General', 'Jefe de Departamento', 'admin'],
            'section' => 'Principal',
        ],

        // Sección Administración (solo para 'admin')
        [
            'route' => route('admin.roles.create'),
            'icon' => 'people-outline',
            'text' => 'Crear Nuevo Rol',
            'roles' => ['admin'],
            'section' => 'Administración',
        ],
        [
            'route' => route('admin.permissions.create'),
            'icon' => 'key-outline',
            'text' => 'Crear Nuevo Permiso',
            'roles' => ['admin'],
            'section' => 'Administración',
        ],
        [
            'route' => route('admin.roles-permissions'),
            'icon' => 'settings-outline',
            'text' => 'Gestión de Permisos de Usuario',
            'roles' => ['admin'],
            'section' => 'Administración',
        ],
        [
            'route' => route('users.index'),
            'icon' => 'person-outline',
            'text' => 'Gestión de Usuarios',
            'roles' => ['admin'],
            'section' => 'Administración',
        ],
    ];

    // Función para renderizar una tarjeta
    function renderDashboardCard($card) {
        return '
            <a href="' . e($card['route']) . '" class="dashboard-card">
                <ion-icon name="' . e($card['icon']) . '" class="dashboard-icon"></ion-icon>
                <span class="dashboard-text">' . e($card['text']) . '</span>
            </a>
        ';
    }

    // Agrupar las tarjetas por sección
    $sections = [];
    foreach ($dashboardCards as $card) {
        $section = $card['section'] ?? 'Principal';
        unset($card['section']);
        $sections[$section][] = $card;
    }

    // Obtener los roles del usuario actual
    $userRoles = auth()->user()->roles->pluck('name')->toArray();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto" style="max-width: 90%">

            @foreach($sections as $sectionName => $cards)
                {{-- Filtrar las tarjetas que el usuario puede ver --}}
                @php
                    $visibleCards = collect($cards)->filter(function($card) use ($userRoles) {
                        return !empty(array_intersect($card['roles'], $userRoles));
                    });
                @endphp

                @if($visibleCards->isNotEmpty())
                    {{-- Mostrar el título de la sección si no es 'Principal' --}}
                    @if($sectionName !== 'Principal')
                        <div class="mt-8">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">{{ $sectionName }}</h3>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($visibleCards as $card)
                            {!! renderDashboardCard($card) !!}
                        @endforeach
                    </div>
                @endif
            @endforeach

        </div>
    </div>

    <!-- CSS Personalizado -->
    <style>
        .dashboard-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .dashboard-icon {
            font-size: 36px;
            color: #2563EB;
        }
        .dashboard-text {
            margin-top: 12px;
            font-size: 1.125rem;
            font-weight: 600;
            color: #4A5568;
            text-align: center;
        }
    </style>
</x-app-layout>
