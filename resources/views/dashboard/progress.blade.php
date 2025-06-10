<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Proceso de Carga de Información de los Expedientes de Acción') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
        <!-- Breadcrumbs -->
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.link href="/dashboard">Dashboard</x-ui.breadcrumbs.link>
            <x-ui.breadcrumbs.separator />
            <x-ui.breadcrumbs.link active>{{ __('Proceso de Carga de Información de los Expedientes de Acción') }}</x-ui.breadcrumbs.link>
        </x-ui.breadcrumbs>

        <!-- Progreso de Importaciones -->
        <div class="bg-white shadow-md rounded-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Progreso de la Carga de Información</h2>
        
            @if (session('success'))
                <div class="bg-green-500 text-white p-4 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif
        
            <x-ui.table>
                <x-slot name="head">
                    <x-ui.table.header>Archivo</x-ui.table.header>
                    <x-ui.table.header>Progreso</x-ui.table.header>
                    <x-ui.table.header>Estado</x-ui.table.header>
                    <x-ui.table.header>Acciones</x-ui.table.header>
                </x-slot>
        
                <x-slot name="body">
                    @foreach($imports as $import)
                        <x-ui.table.row wire:loading.class.delay="opacity-75">
                            <x-ui.table.column>{{ $import->file_path }}</x-ui.table.column>
                            <x-ui.table.column>{{ $import->processed_rows }} / {{ $import->total_rows }}</x-ui.table.column>
                            <x-ui.table.column>{{ ucfirst($import->status) }}</x-ui.table.column>
                            <x-ui.table.column>
                                @if ($import->status === 'completed')
                                    <a href="{{ route('dashboard.show-imported-data', $import->id) }}" class="text-blue-500 hover:underline">Ver datos</a>
                                @endif
                            </x-ui.table.column>
                        </x-ui.table.row>
                    @endforeach
                </x-slot>
            </x-ui.table>
        </div>
    </div>
</x-app-layout>
