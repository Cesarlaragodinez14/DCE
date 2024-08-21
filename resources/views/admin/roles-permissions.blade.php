<!-- resources/views/admin/roles-permissions.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Roles y Permisos') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
        <!-- Mensaje de éxito -->
        @if(session('success'))
                {{ session('success') }}
        @endif

        <!-- Tabla de Gestión de Roles y Permisos -->
        <x-ui.container.table>
            <x-ui.table>
                <x-slot name="head">
                    <x-ui.table.header>Usuario</x-ui.table.header>
                    <x-ui.table.header>Roles</x-ui.table.header>
                    <x-ui.table.header>Permisos</x-ui.table.header>
                    <x-ui.table.header>Acciones</x-ui.table.header>
                </x-slot>

                <x-slot name="body">
                    @foreach($users as $user)
                        <x-ui.table.row>
                            <!-- Columna de Usuario -->
                            <x-ui.table.column>
                                {{ $user->name }}
                            </x-ui.table.column>

                            <!-- Columna de Roles -->
                            <x-ui.table.column>
                                <form action="{{ route('admin.roles-permissions.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <select multiple name="roles[]" class="w-full">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ $user->roles->contains($role->name) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                            </x-ui.table.column>

                            <!-- Columna de Permisos -->
                            <x-ui.table.column>
                                <select multiple name="permissions[]" class="w-full">
                                    @foreach($permissions as $permission)
                                        <option value="{{ $permission->name }}" {{ $user->permissions->contains($permission->name) ? 'selected' : '' }}>
                                            {{ $permission->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </x-ui.table.column>

                            <!-- Columna de Acciones -->
                            <x-ui.table.column>
                                <x-ui.button type="submit">
                                    Actualizar
                                </x-ui.button>
                            </x-ui.table.column>
                                </form>
                        </x-ui.table.row>
                    @endforeach
                </x-slot>
            </x-ui.table>
        </x-ui.container.table>
    </div>
</x-app-layout>
