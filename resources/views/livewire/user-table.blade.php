<div>
    <div class="flex justify-between mb-4">
        <x-ui.input wire:model="search" type="text" placeholder="Buscar Usuarios..." />

        <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Crear Usuario
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
            <x-ui.table.header>Roles</x-ui.table.header>
            <x-ui.table.action-header>Acciones</x-ui.table.action-header>
        </x-slot>

        <x-slot name="body">
            @foreach($users as $user)
                <x-ui.table.row>
                    <x-ui.table.column>{{ $user->name }}</x-ui.table.column>
                    <x-ui.table.column>{{ $user->email }}</x-ui.table.column>
                    <x-ui.table.column>
                        @foreach($user->roles as $role)
                            <x-ui.label color="green">{{ $role->name }}</x-ui.label>
                        @endforeach
                    </x-ui.table.column>
                    <x-ui.table.action-column>
                        <a href="{{ route('users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900">
                            Editar
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block ml-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                Eliminar
                            </button>
                        </form>
                    </x-ui.table.action-column>
                </x-ui.table.row>
            @endforeach
        </x-slot>
    </x-ui.table.index>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
