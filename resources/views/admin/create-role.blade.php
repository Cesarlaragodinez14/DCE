<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Rol') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
        <!-- Mensaje de éxito -->
        @if(session('success'))
            <x-ui.toast type="success" class="mt-4">
                {{ session('success') }}
            </x-ui.toast>
        @endif

        <!-- Formulario de creación de rol -->
        <div>
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <!-- Nombre del Rol -->
                <div class="mb-4">
                    <x-ui.label for="name">Nombre del Rol</x-ui.label>
                    <x-ui.input type="text" name="name" id="name" class="w-full" required />
                </div>

                <!-- Permisos -->
                <div class="mb-4">
                    <x-ui.label>Permisos</x-ui.label>
                    @foreach($permissions as $permission)
                        <div class="flex items-center">
                            <x-ui.input.checkbox name="permissions[]" value="{{ $permission->name }}" />
                            <span class="ml-2">{{ $permission->name }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Botón de Guardar -->
                <x-ui.button type="submit">
                    Crear Rol
                </x-ui.button>
            </form>
        </div>
    </div>
</x-app-layout>
