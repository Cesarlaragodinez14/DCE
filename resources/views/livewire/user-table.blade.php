<div>
    <div class="mb-4 flex justify-between">
        <!-- Campo de búsqueda -->
        <x-ui.input wire:model="search" type="text" placeholder="Buscar Usuarios..." />

        <!-- Botón para ejecutar la búsqueda -->
        <a href="#" wire:click="sortBy('name')">
            <x-ui.button wire:click="search" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-4">
                Buscar o Filtrar
            </x-ui.button>
        </a>

        <!-- Botón para agregar un nuevo usuario -->
        <a href="{{ route('users.create') }}">
            <x-ui.button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-4">
                Agregar Usuario
            </x-ui.button>
        </a>
    </div>

    <x-ui.table.index>
        <x-slot name="head">
            <x-ui.table.header wire:click="sortBy('name')" class="cursor-pointer">
                Nombre @if($sortField == 'name') @if($sortDirection == 'asc') ↑ @else ↓ @endif @endif
            </x-ui.table.header>
            <x-ui.table.header wire:click="sortBy('email')" class="cursor-pointer">
                Email @if($sortField == 'email') @if($sortDirection == 'asc') ↑ @else ↓ @endif @endif
            </x-ui.table.header>
            <x-ui.table.header wire:click="sortBy('uaa')" class="cursor-pointer">
                UAA @if($sortField == 'uaa') @if($sortDirection == 'asc') ↑ @else ↓ @endif @endif
            </x-ui.table.header>
            <x-ui.table.header wire:click="sortBy('roles')" class="cursor-pointer">
                Roles @if($sortField == 'roles') @if($sortDirection == 'asc') ↑ @else ↓ @endif @endif
            </x-ui.table.header>
            <x-ui.table.action-header>Acciones</x-ui.table.action-header>
        </x-slot>

        <x-slot name="body">
            @foreach($users as $user)
                <x-ui.table.row>
                    <x-ui.table.column>{{ $user->name }}</x-ui.table.column>
                    <x-ui.table.column>{{ $user->email }}</x-ui.table.column>
                    <x-ui.table.column>
                        {{ $user->uaa->valor ?? 'Sin Asignar' }}
                    </x-ui.table.column>
                    <x-ui.table.column>
                        @foreach($user->roles as $role)
                            <x-ui.label color="green">{{ $role->name }}</x-ui.label>
                        @endforeach
                    </x-ui.table.column>
                    <x-ui.table.action-column>
                        <x-ui.action wire:navigate href="{{ route('users.edit', $user->id) }}">
                            Editar
                        </x-ui.action>
                        <x-ui.action.danger class="ml-4" onclick="openDeleteModal({{ $user->id }})">
                            Eliminar
                        </x-ui.action.danger>
                    </x-ui.table.action-column>
                </x-ui.table.row>
            @endforeach
        </x-slot>
    </x-ui.table.index>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

     <!-- Modal de confirmación de eliminación -->
     <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Confirmar eliminación</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.</p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button id="confirmDeleteBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Eliminar
                    </button>
                    <button onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let userIdToDelete = null;
    
        // Función para abrir el modal y asignar el ID del usuario
        function openDeleteModal(userId) {
            userIdToDelete = userId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
    
        // Función para cerrar el modal
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            userIdToDelete = null;
        }
    
        // Función para confirmar la eliminación
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (userIdToDelete) {
                // Aquí puedes usar Livewire para realizar la eliminación
                @this.call('deleteUser', userIdToDelete);
                closeDeleteModal();
            }
        });
    </script>
    
</div>
