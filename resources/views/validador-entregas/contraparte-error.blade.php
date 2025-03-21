<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Error</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error: </strong>
                <span class="block sm:inline">{{ $error }}</span>
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('recepcion.index') }}" class="text-blue-600 hover:underline">Volver al listado de entregas</a>
            </div>
        </div>
    </div>
</x-app-layout>
