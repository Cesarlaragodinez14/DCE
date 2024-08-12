<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Carga de Acciones') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-6 bg-white shadow-md rounded-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-6">Carga de Acciones Emitidas en la Entrega</h2>
    
        @if (session('success'))
            <div class="bg-green-500 text-white p-2 rounded mt-4">
                {{ session('success') }}
            </div>
        @endif
    
        <form action="{{ route('dashboard.upload-excel.upload') }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <div class="mb-4">
                <label for="archivo" class="block text-gray-700">Archivo Excel</label>
                <input type="file" id="archivo" name="archivo" class="w-full p-2 border rounded" accept=".xlsx, .xls">
                @error('archivo')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
    
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Cargar Acciones
            </button>
        </form>
    </div>
</x-app-layout>
