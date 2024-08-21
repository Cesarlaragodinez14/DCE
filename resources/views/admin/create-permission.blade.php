<!-- resources/views/admin/create-permission.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Permiso') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
        <!-- Mensaje de éxito -->
        @if(session('success'))
            <x-ui.toast type="success" class="mt-4">
                {{ session('success') }}
            </x-ui.toast>
        @endif

        <!-- Formulario de creación de permiso -->
        <div>
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf

                <!-- Nombre del Permiso -->
                <div class="mb-4">
                    <x-ui.label for="name">Nombre del Permiso</x-ui.label>
                    <x-ui.input type="text" name="name" id="name" class="w-full" required />
                </div>

                <!-- Roles -->
                <div class="mb-4">
                    <x-ui.label>Roles</x-ui.label>
                    @foreach($roles as $role)
                        <div class="flex items-center">
                            <x-ui.input.checkbox name="roles[]" value="{{ $role->id }}" />
                            <span class="ml-2">{{ $role->name }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Botón de Guardar -->
                <x-ui.button type="submit">
                    Crear Permiso
                </x-ui.button>
            </form>
        </div>
    </div>
</x-app-layout>
