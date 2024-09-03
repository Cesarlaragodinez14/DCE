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
                        <x-ui.table.header>Clave Acción</x-ui.table.header>
                        <x-ui.table.header>Tipo Acción</x-ui.table.header>
                        <x-ui.table.header>Número de Legajos</x-ui.table.header>
                    </x-slot>
                    <x-slot name="body">
                        @foreach($expedientes as $index => $expediente)
                            <x-ui.table.row>
                                <x-ui.table.column>{{ $index + 1 }}</x-ui.table.column>
                                <x-ui.table.column>{{ $expediente->clave_accion }}</x-ui.table.column>
                                <x-ui.table.column>{{ $expediente->tipo_accion }}</x-ui.table.column>
                                <x-ui.table.column>{{ $legajos[$index] }}</x-ui.table.column> <!-- Correctly indexed -->
                            </x-ui.table.row>
                        @endforeach
                    </x-slot>
                    
                </x-ui.table.index>

                <div class="mt-6 flex justify-end">
                    <!-- Existing Button to trigger the confirmation modal -->
                    <x-button type="button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="showModal()">
                        Confirmar Entrega
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Confirmation -->
    <div id="confirmationModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="bg-black opacity-50 absolute inset-0" onclick="closeModal()" style="background: #000; z-index:1000"></div>
        <div class="bg-white p-8 rounded-lg shadow-lg z-10 max-w-lg w-full" style="z-index:1000; padding: 30px">
            <h2 class="text-xl font-semibold mb-4">Confirmar Entrega</h2>
            <p class="mb-6">¿Estás seguro de que deseas confirmar esta entrega? Una vez confirmada, no podrás modificarla y se enviarán correos electrónicos de notificación a los involucrados.</p>
            <div class="flex justify-end">
               
                <form id="confirmEntregaForm" action="{{ route('expedientes.confirmar') }}" method="POST">
                    @csrf
                    <!-- Hidden fields to pass necessary data -->
                    <input type="hidden" name="fecha_entrega" value="{{ $fecha_entrega }}">
                    <input type="hidden" name="responsable" value="{{ $responsable }}">
                    @foreach($expedientes as $index => $expediente)
                        <input type="hidden" name="expedientes[]" value="{{ $expediente->id }}">
                        <input type="hidden" name="legajos[]" value="{{ $legajos[$index] }}">
                    @endforeach
                    <x-button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Confirmar
                    </x-button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showModal() {
            document.getElementById('confirmationModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('confirmationModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
