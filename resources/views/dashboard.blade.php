<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="container mx-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <a href="{{ route('dashboard.upload-excel.form') }}" class="flex flex-col bg-white shadow-xl sm:rounded-lg items-center justify-center p-6 bg-white text-black rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <div class="p-4 bg-gray-100 rounded-full">
                                <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <span class="mt-4 font-semibold">Cargar Acciones</span>
                        </a>
                        
                        <a href="{{ route('dashboard.progress') }}" class="flex flex-col bg-white shadow-xl sm:rounded-lg items-center justify-center p-6 bg-white text-black rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <div class="p-4 bg-gray-100 rounded-full">
                                <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18"></path>
                                </svg>
                            </div>
                            <span class="mt-4 font-semibold">Proceso de acciones</span>
                        </a>
                        
                        <a href="{{ route('dashboard.distribucion') }}" class="flex flex-col bg-white shadow-xl sm:rounded-lg items-center justify-center p-6 bg-white text-black rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <div class="p-4 bg-gray-100 rounded-full">
                                <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m2 0a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                                </svg>
                            </div>
                            <span class="mt-4 font-semibold">Distribución de acciones</span>
                        </a>

                        <a href="{{ route('dashboard.oficio-uaa') }}" class="flex flex-col bg-white shadow-xl sm:rounded-lg items-center justify-center p-6 bg-white text-black rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <div class="p-4 bg-gray-100 rounded-full">
                                <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m2 0a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                                </svg>
                            </div>
                            <span class="mt-4 font-semibold">Envio de Oficio a las UAA</span>
                        </a>
                        <a href="{{ route('dashboard.expedientes.entrega') }}" class="flex flex-col bg-white shadow-xl sm:rounded-lg items-center justify-center p-6 bg-white text-black rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <div class="p-4 bg-gray-100 rounded-full">
                                <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <span class="mt-4 font-semibold">Programación de entrega de expedientes</span>
                        </a>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
