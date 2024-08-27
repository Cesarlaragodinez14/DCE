<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Validación de Entregas') }}
        </h2>
    </x-slot>

    <!-- Alert Fijo para Errores -->
    <div id="errorAlert" style="background: #000" class="fixed bottom-0 left-0 right-0 bg-red-500 text-white p-4 rounded-t-lg shadow-lg {{ $errors->any() ? '' : 'hidden' }}">
        <ul id="errorList" class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <form id="validacionForm" action="{{ route('expedientes.validar') }}" method="POST">
                    @csrf

                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ __('Auditoría: ') }} {{ $uaaName }}
                                </h3>
                            </div>
                            <div class="flex items-center space-x-4">
                                <input type="date" name="fecha_entrega" class="form-input rounded-md" value="{{ old('fecha_entrega') }}" required>
                                <select name="responsable" class="form-select rounded-md" required>
                                    <option value="">Seleccione un responsable</option>
                                    <option value="Juan Pérez" {{ old('responsable') == 'Juan Pérez' ? 'selected' : '' }}>Juan Pérez</option>
                                    <!-- Añadir más opciones -->
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-ui.table.index>
                                <x-slot name="head">
                                    <x-ui.table.header>Cons.</x-ui.table.header>
                                    <x-ui.table.header>CP</x-ui.table.header>
                                    <x-ui.table.header>Entrega</x-ui.table.header>
                                    <x-ui.table.header>Auditoría</x-ui.table.header>
                                    <x-ui.table.header>Ente acción</x-ui.table.header>
                                    <x-ui.table.header>Clave acción</x-ui.table.header>
                                    <x-ui.table.header>Tipo acción</x-ui.table.header>
                                    <x-ui.table.header>Número de legajos</x-ui.table.header>
                                    <x-ui.table.header>Seleccionar</x-ui.table.header>
                                </x-slot>

                                <x-slot name="body">
                                    @foreach($expedientes as $index => $expediente)
                                        <x-ui.table.row>
                                            <x-ui.table.column>{{ $index + 1 }}</x-ui.table.column>
                                            <x-ui.table.column>{{ $expediente->CP }}</x-ui.table.column>
                                            <x-ui.table.column>{{ $expediente->entrega }}</x-ui.table.column>
                                            <x-ui.table.column>{{ $expediente->auditoria_especial }}</x-ui.table.column>
                                            <x-ui.table.column>{{ $expediente->ente_accion }}</x-ui.table.column>
                                            <x-ui.table.column>{{ $expediente->clave_accion }}</x-ui.table.column>
                                            <x-ui.table.column>{{ $expediente->tipo_accion }}</x-ui.table.column>
                                            <x-ui.table.column>
                                                <input type="number" min="1" class="form-input rounded-md legajos-input" name="legajos[{{ $index }}]" value="{{ old('legajos.'.$index) }}" {{ in_array($expediente->id, old('expedientes', [])) ? '' : 'disabled' }}>
                                            </x-ui.table.column>
                                            <x-ui.table.column>
                                                <input type="checkbox" class="form-checkbox expediente-checkbox" name="expedientes[]" value="{{ $expediente->id }}" {{ in_array($expediente->id, old('expedientes', [])) ? 'checked' : '' }}>
                                            </x-ui.table.column>
                                        </x-ui.table.row>
                                    @endforeach
                                </x-slot>
                            </x-ui.table.index>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <x-button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" id="validarButton">
                                Validar Entrega
                            </x-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.expediente-checkbox');
            const validarButton = document.getElementById('validarButton');
            const errorAlert = document.getElementById('errorAlert');
            const errorList = document.getElementById('errorList');

            checkboxes.forEach((checkbox, index) => {
                checkbox.addEventListener('change', function () {
                    const legajosInput = document.querySelector(`input[name="legajos[${index}]"]`);
                    if (checkbox.checked) {
                        legajosInput.disabled = false;
                        legajosInput.required = true;
                    } else {
                        legajosInput.disabled = true;
                        legajosInput.required = false;
                    }
                });
            });

            validarButton.addEventListener('click', function () {
                let errors = [];

                checkboxes.forEach((checkbox, index) => {
                    const legajosInput = document.querySelector(`input[name="legajos[${index}]"]`);
                    if (checkbox.checked && (!legajosInput.value || legajosInput.value <= 0)) {
                        errors.push(`Debe ingresar un número válido de legajos para el expediente ${index + 1}.`);
                    }
                });

                if (!document.querySelector('input[name="fecha_entrega"]').value) {
                    errors.push('Debe seleccionar una fecha de entrega.');
                }

                if (!document.querySelector('select[name="responsable"]').value) {
                    errors.push('Debe seleccionar un responsable.');
                }

                if (errors.length > 0) {
                    errorList.innerHTML = errors.map(error => `<li>${error}</li>`).join('');
                    errorAlert.classList.remove('hidden');
                    setTimeout(() => {
                        errorAlert.classList.add('hidden');
                    }, 5000); // Esconder el alert después de 5 segundos
                } else {
                    document.getElementById('validacionForm').submit();
                }
            });
        });
    </script>
</x-app-layout>
