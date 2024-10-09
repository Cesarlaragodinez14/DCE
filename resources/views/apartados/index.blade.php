<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-center text-gray-800">{{ __('Información de la Auditoría') }}</h2>
        <!-- Grid de Información -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <!-- Área que entrega -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Área que entrega</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><strong>Auditoría Especial:</strong> {{ $auditoria->catSiglasAuditoriaEspecial->descripcion ?? '' }}</li>
                    <li><strong>Dirección General de la UAA:</strong> {{ $auditoria->catUaa->nombre ?? '' }}</li>
                    <li><strong>Título de la Auditoría:</strong> {{ $auditoria->titulo }}</li>
                    <li><strong>Número de Auditoría:</strong> {{ $auditoria->catAuditoriaEspecial->valor ?? '' }}</li>
                    <li><strong>Clave de la Acción:</strong> {{ $auditoria->catClaveAccion->valor ?? '' }}</li>
                    <li><strong>Nombre del Ente de la Acción o Recomendación:</strong> {{ $auditoria->catEnteDeLaAccion->valor ?? '' }}</li>
                </ul>
            </div>
            <!-- Área que recibe y revisa -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Área que recibe y revisa</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><strong>Dirección General:</strong> {{ $auditoria->catDgsegEf->valor ?? '' }}</li>
                    <li><strong>Dirección de Área:</strong> {{ $auditoria->direccion_de_area ?? '' }}</li>
                    <li><strong>Subdirección:</strong> {{ $auditoria->sub_direccion_de_area ?? '' }}</li>
                    <li><strong>Fecha:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</li>
                </ul>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-lg p-6">
                <form action="{{ route('apartados.checklist.store') }}" method="POST" id="checklist-form">
                    @csrf
                    <input type="hidden" name="auditoria_id" value="{{ $auditoria->id }}">

                    <!-- Estatus del Checklist -->
                    <div class="mb-8">
                        <label for="estatus_checklist" class="block text-lg font-medium text-gray-700">Estatus del Checklist</label>
                        <!-- Custom Select Component -->
                        <div class="relative mt-2">
                            <select name="estatus_checklist" id="estatus_checklist" class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded-md leading-tight focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Selecciona</option>
                                <option value="1" {{ old('estatus_checklist', $auditoria->estatus_checklist) == '1' ? 'selected' : '' }}>ACEPTA</option>
                                <option value="0" {{ old('estatus_checklist', $auditoria->estatus_checklist) == '0' ? 'selected' : '' }}>DEVUELVE</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path d="M5.516 7.548L9.951 12l4.435-4.452-1.024-1.024L9.951 9.952 6.541 6.524z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del Servidor Público -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Datos del Servidor Público</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Área auditora -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-inner">
                                <h4 class="text-lg font-medium text-gray-800 mb-4">Área auditora que entrega el expediente</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label for="auditor_nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                                        <input type="text" name="auditor_nombre" id="auditor_nombre" value="{{ old('auditor_nombre', $auditoria->auditor_nombre ?? '') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label for="auditor_puesto" class="block text-sm font-medium text-gray-700">Puesto</label>
                                        <input type="text" name="auditor_puesto" id="auditor_puesto" value="{{ old('auditor_puesto', $auditoria->auditor_puesto ?? '') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                            <!-- Seguimiento -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-inner">
                                <h4 class="text-lg font-medium text-gray-800 mb-4">Seguimiento que revisa, acepta o devuelve el expediente</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label for="seguimiento_nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                                        <input type="text" name="seguimiento_nombre" id="seguimiento_nombre" value="{{ old('seguimiento_nombre', $auditoria->seguimiento_nombre ?? '') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label for="seguimiento_puesto" class="block text-sm font-medium text-gray-700">Puesto</label>
                                        <input type="text" name="seguimiento_puesto" id="seguimiento_puesto" value="{{ old('seguimiento_puesto', $auditoria->seguimiento_puesto ?? '') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comentarios -->
                    <div class="mb-8">
                        <label for="comentarios" class="block text-lg font-medium text-gray-700">Comentarios</label>
                        <textarea name="comentarios" id="comentarios" rows="4" class="mt-2 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">{{ old('comentarios', $auditoria->comentarios ?? '') }}</textarea>
                    </div>

                    <!-- Tabla de Checklist -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Checklist</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border-b px-4 py-2 text-left text-gray-600">N°</th>
                                        <th class="border-b px-4 py-2 text-left text-gray-600">Apartado / Subapartado</th>
                                        <th class="border-b px-4 py-2 text-center text-gray-600">¿Obligatorio?</th>
                                        <th class="border-b px-4 py-2 text-center text-gray-600">¿Se Integra?</th>
                                        <th class="border-b px-4 py-2 text-left text-gray-600">Observaciones</th>
                                        <th class="border-b px-4 py-2 text-left text-gray-600">Comentarios UAA</th>
                                    </tr>
                                </thead>
                                <tbody id="checklist-body">
                                    <!-- Filas de Apartados -->
                                    @foreach ($apartados as $apartado)
                                        @include('partials.apartado_row', [
                                            'apartado' => $apartado,
                                            'parentIteration' => $loop->iteration,
                                            'is_subrow' => false,
                                            'auditoria' => $auditoria,
                                            'checklist' => $checklist,
                                        ])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex items-center justify-between">
                        <button type="submit" id="guardar-checklist" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Guardar Checklist
                        </button>
                        <a href="{{ route('auditorias.pdf', $auditoria->id) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Descargar PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div id="confirmation-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <!-- Fondo -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Contenido del Modal -->
            <div class="inline-block overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                        Confirmación
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Una vez guardado, no podrá realizar ninguna modificación. ¿Desea continuar?
                        </p>
                    </div>
                </div>
                <div class="px-6 py-3 sm:flex sm:flex-row-reverse">
                    <button id="confirm-save" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Guardar
                    </button>
                    <button id="cancel-save" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Toggle subapartados
            const toggleButtons = document.querySelectorAll('.toggle-subapartado');

            toggleButtons.forEach(button => {
                button.addEventListener('click', () => {
                    let parentId = button.dataset.parentId;
                    let subRows = document.querySelectorAll(`.parent-${parentId}`);
                    subRows.forEach(row => {
                        row.classList.toggle('hidden');
                    });
                    button.querySelector('.toggle-icon').classList.toggle('rotate-90');
                });
            });

            // Smooth horizontal scroll on mouse wheel
            const tableContainer = document.querySelector('.overflow-x-auto');
            tableContainer.addEventListener('wheel', (evt) => {
                if (evt.deltaY !== 0) {
                    evt.preventDefault();
                    tableContainer.scrollLeft += evt.deltaY;
                }
            });

            // Modal confirmation on form submit
            const form = document.getElementById('checklist-form');
            const estatusSelect = document.getElementById('estatus_checklist');
            const guardarButton = document.getElementById('guardar-checklist');
            const modal = document.getElementById('confirmation-modal');
            const confirmSaveButton = document.getElementById('confirm-save');
            const cancelSaveButton = document.getElementById('cancel-save');

            guardarButton.addEventListener('click', (e) => {
                if (estatusSelect.value === '1') {
                    e.preventDefault();
                    // Show modal
                    modal.classList.remove('hidden');
                }
            });

            confirmSaveButton.addEventListener('click', () => {
                // Hide modal and submit form
                modal.classList.add('hidden');
                form.submit();
            });

            cancelSaveButton.addEventListener('click', () => {
                // Hide modal
                modal.classList.add('hidden');
            });
        });
    </script>
</x-app-layout>
