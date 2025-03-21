<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Confirmación de Firma Contraparte
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-center">Firma de Acuse Completada</h3>
                <p class="mb-4 text-center">El acuse de entrega ha sido firmado por la contraparte.</p>
                <div class="mb-4">
                    <p><strong>Hash de Firma Contraparte:</strong> {{ $hash2 }}</p>
                </div>
                <div class="mb-4">
                    <p><strong>Visualizar PDF:</strong> <a href="{{ $pdfUrl }}" target="_blank" class="text-blue-500 underline">{{ $pdfUrl }}</a></p>
                </div>
                <div class="text-center">
                    <a href="{{ route('recepcion.index') }}" class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded">
                        Volver al Listado de Entregas
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
