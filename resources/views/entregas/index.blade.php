<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Listado de Entregas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <x-ui.table.index>
                    <x-slot name="head">
                        <x-ui.table.header>Expediente</x-ui.table.header>
                        <x-ui.table.header>Fecha de Entrega</x-ui.table.header>
                        <x-ui.table.header>Responsable</x-ui.table.header>
                        <x-ui.table.header>Confirmado Por</x-ui.table.header>
                        <x-ui.table.header>Acciones</x-ui.table.header>
                    </x-slot>

                    <x-slot name="body">
                        @foreach($entregas as $entrega)
                            <x-ui.table.row>
                                <x-ui.table.column>{{ $entrega->expediente->clave_accion }}</x-ui.table.column>
                                <x-ui.table.column>{{ $entrega->fecha_entrega }}</x-ui.table.column>
                                <x-ui.table.column>{{ $entrega->responsable }}</x-ui.table.column>
                                <x-ui.table.column>{{ $entrega->confirmadoPor->name }}</x-ui.table.column>
                                <x-ui.table.column>
                                    <a href="{{ route('entregas.show', $entrega->id) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                                    <a href="{{ route('entregas.edit', $entrega->id) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">Editar</a>
                                    <form action="{{ route('entregas.destroy', $entrega->id) }}" method="POST" class="inline-block ml-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </form>
                                </x-ui.table.column>
                            </x-ui.table.row>
                        @endforeach
                    </x-slot>
                </x-ui.table.index>
            </div>
        </div>
    </div>
</x-app-layout>
