<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Carga de Acciones - Progreso') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-6 bg-white shadow-md rounded-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-6">Progreso de Importaciones</h2>
    
        @if (session('success'))
            <div class="bg-green-500 text-white p-2 rounded mt-4">
                {{ session('success') }}
            </div>
        @endif
    
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="px-6 py-2">Archivo</th>
                    <th class="px-6 py-2">Progreso</th>
                    <th class="px-6 py-2">Estado</th>
                    <th class="px-6 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($imports as $import)
                <tr>
                    <td class="px-6 py-2">{{ $import->file_path }}</td>
                    <td class="px-6 py-2">{{ $import->processed_rows }} / {{ $import->total_rows }}</td>
                    <td class="px-6 py-2">{{ ucfirst($import->status) }}</td>
                    <td class="px-6 py-2">
                        @if ($import->status === 'completed')
                        <a href="{{ route('dashboard.show-imported-data', $import->id) }}" class="text-blue-500">Ver datos</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
