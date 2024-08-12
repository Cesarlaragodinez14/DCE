<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container mx-auto">
                    <div class="flex space-x-4">
                        <!-- Otros enlaces del dashboard -->
                        <a href="{{ route('dashboard.upload-excel.form') }}" class="px-4 py-2 bg-blue-600 text-dark rounded hover:bg-blue-700">
                            Cargar Acciones.
                        </a>
                    </div>
                
                    <!-- Aquí podrías incluir otras secciones del dashboard -->
                
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
