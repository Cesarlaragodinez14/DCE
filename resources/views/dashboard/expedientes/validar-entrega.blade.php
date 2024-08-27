<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Validar Entrega de Expedientes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detalles de la Entrega</h3>
                <p><strong>Fecha de Entrega:</strong> {{ $fecha_entrega }}</p>
                <p><strong>Encargado:</strong> {{ $responsable }}</p>

                <h3 class="text-lg font-medium text-gray-900 mt-6 mb-4">Expedientes a Entregar</h3>
                <x-ui.table.index>
                    <x-slot name="head">
                        <x-ui.table.header>Cons.</x-ui.table.header>
                        <x-ui.table.header>ID de Expediente</x-ui.table.header>
                    </x-slot>

                    <x-slot name="body">
                        @foreach($expedientesIds as $index => $expedienteId)
                            <x-ui.table.row>
                                <x-ui.table.column>{{ $index + 1 }}</x-ui.table.column>
                                <x-ui.table.column>{{ $expedienteId }}</x-ui.table.column>
                            </x-ui.table.row>
                        @endforeach
                    </x-slot>
                </x-ui.table.index>

                <div class="mt-6 flex justify-end">
                    <x-button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Confirmar Entrega
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
