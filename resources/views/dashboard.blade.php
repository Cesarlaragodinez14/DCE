<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto" style="max-width: 90%">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                
                <!-- Tarjetas Principales -->
                <a href="{{ route('dashboard.upload-excel.form') }}" class="dashboard-card">
                    <ion-icon name="cloud-upload-outline" class="dashboard-icon"></ion-icon>
                    <span class="dashboard-text">Cargar Acciones</span>
                </a>

                <a href="{{ route('dashboard.progress') }}" class="dashboard-card">
                    <ion-icon name="analytics-outline" class="dashboard-icon"></ion-icon>
                    <span class="dashboard-text">Proceso de Acciones</span>
                </a>

                <a href="{{ route('dashboard.distribucion') }}" class="dashboard-card">
                    <ion-icon name="swap-horizontal-outline" class="dashboard-icon"></ion-icon>
                    <span class="dashboard-text">Distribución de Acciones</span>
                </a>

                <a href="{{ route('dashboard.oficio-uaa') }}" class="dashboard-card">
                    <ion-icon name="mail-outline" class="dashboard-icon"></ion-icon>
                    <span class="dashboard-text">Envio de Oficio a las UAA</span>
                </a>

                <a href="{{ route('dashboard.expedientes.entrega') }}" class="dashboard-card">
                    <ion-icon name="calendar-number-outline" class="dashboard-icon"></ion-icon>
                    <span class="dashboard-text">Programación de Entrega de Expedientes</span>
                </a>
                <a href="{{ route('dashboard.expedientes.recepcion') }}" class="dashboard-card">
                    <ion-icon name="file-tray-full-outline" class="dashboard-icon"></ion-icon>
                    <span class="dashboard-text">Recepción de Expedientes</span>
                </a>
                <a href="{{ url('/dashboard/all-auditorias') }}" class="dashboard-card">
                    <ion-icon name="checkbox-outline" class="dashboard-icon"></ion-icon>
                    <span class="dashboard-text">Checklist</span>
                </a>
            </div>

            <!-- Sección de Administración -->
            @role('admin')
            <div class="mt-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Administración</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    
                    <a href="{{ route('admin.roles.create') }}" class="dashboard-card">
                        <ion-icon name="people-outline" class="dashboard-icon"></ion-icon>
                        <span class="dashboard-text">Crear Nuevo Rol</span>
                    </a>

                    <a href="{{ route('admin.permissions.create') }}" class="dashboard-card">
                        <ion-icon name="key-outline" class="dashboard-icon"></ion-icon>
                        <span class="dashboard-text">Crear Nuevo Permiso</span>
                    </a>

                    <a href="{{ route('admin.roles-permissions') }}" class="dashboard-card">
                        <ion-icon name="settings-outline" class="dashboard-icon"></ion-icon>
                        <span class="dashboard-text">Gestión de Permisos de Usuario</span>
                    </a>

                    <a href="{{ route('users.index') }}" class="dashboard-card">
                        <ion-icon name="person-outline" class="dashboard-icon"></ion-icon>
                        <span class="dashboard-text">Gestión de Usuarios</span>
                    </a>
                </div>
            </div>
            @endrole

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
        }
    </style>
</x-app-layout>
