<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Carga de información de los Expedientes de Acción') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
        <!-- Breadcrumbs -->
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.link href="/dashboard">Dashboard</x-ui.breadcrumbs.link> 
            <x-ui.breadcrumbs.separator />
            <x-ui.breadcrumbs.link active>{{ __('Carga de Acciones') }}</x-ui.breadcrumbs.link>
        </x-ui.breadcrumbs>

        <!-- Formulario de Carga -->
        <div class="container mx-auto p-6 bg-white shadow-md rounded-md">
            @if (session('success'))
                <div class="bg-green-500 text-white p-2 rounded mt-4">
                    {{ session('success') }}
                </div>
            @endif
        
            <form style="text-align: center" action="{{ route('dashboard.upload-excel.upload') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label for="archivo" class="block text-gray-700">Anexar archivo con la relación de acciones emitidas (XLS, XLSX):</label>
                    <input type="file" id="archivo" name="archivo" class="w-full p-2 border rounded" accept=".xlsx, .xls">
                    @error('archivo')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
        
                <button style="
                background: #2563eb;
                color: #FFF!important;
            " type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Carga de información de los Expedientes de Acción
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
