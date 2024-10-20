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
    @push('styles')
        <style>
            /* Resaltar filas inválidas */
            tr.border-red-500 {
                border-left: 4px solid #dc2626; /* Rojo */
            }

            tr.bg-red-50 {
                background-color: #fef2f2; /* Rojo claro */
            }

            /* Estilos para el modal */
            #confirmation-modal {
                z-index: 1000; /* Asegura que el modal esté por encima de otros elementos */
            }

            #confirmation-modal.hidden {
                display: none;
            }

            #confirmation-modal .bg-white {
                animation: fadeIn 0.3s ease-in-out;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: scale(0.95); }
                to { opacity: 1; transform: scale(1); }
            }
            /* Estilos para el contenedor de mensajes de error */
            #validation-error {
                top: 0%; /* Equivale a mt-4 */
                background-color: #fee2e2; /* Equivale a bg-red-100 */
                border: 1px solid #f87171; /* Equivale a border y border-red-400 */
                color: #000; /* Equivale a text-red-700 */
                padding: 0.75rem 1rem; /* Equivale a py-3 px-4 */
                border-radius: 0.375rem; /* Equivale a rounded */
                position: fixed; /* Equivale a relative */
            }

            /* Mostrar el contenedor cuando no está oculto */
            #validation-error.visible {
                display: block;
            }

            /* Estilos para el texto fuerte "Error:" */
            #validation-error strong {
                font-weight: bold; /* Equivale a font-bold */
            }

            /* Estilos para el texto del mensaje */
            #validation-error-text {
                display: block; /* Equivale a block */
                /* Puedes ajustar según tus necesidades */
            }

            /* Estilos para el botón de cerrar */
            #validation-error .close-btn {
                position: absolute; /* Equivale a absolute */
                top: 0;
                bottom: 0;
                right: 0;
                padding: 0.75rem 1rem; /* Equivale a py-3 px-4 */
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #991b1b; /* Rojo oscuro para el icono */
            }

            /* Efecto de transición para el modal */
            #confirmation-modal .bg-white {
                animation: fadeIn 0.3s ease-in-out;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: scale(0.95); }
                to { opacity: 1; transform: scale(1); }
            }

            /* Rotar el icono de cerrar al pasar el ratón */
            #validation-error .close-btn:hover svg {
                transform: rotate(90deg);
                transition: transform 0.2s;
            }

        </style>
    @endpush

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-lg p-6">
                @if($auditoria->estatus_checklist === 'Aceptado')
                    <div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg p-6 text-center relative">
                        
                        <!-- Mensajes de Sesión -->
                        @if (session()->has('message'))
                            <div class="absolute top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg animate__animated animate__fadeInDown">
                                {{ session('message') }}
                            </div>
                        @endif
                    
                        @if($auditoria->archivo_uua)
                    
                        <!-- Verificar si ya se ha subido el archivo de la UAA -->
                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-gray-700">Se ha terminado el proceso de verificación para esta clave de acción</h3>
                                <p class="text-lg font-medium text-gray-700">Archivo de la UAA ya subido:</p>
                                <a href="{{ route('auditorias.downloadUua', $auditoria->id) }}" class="mt-2 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-300">
                                    <ion-icon name="download-outline" class="mr-2"></ion-icon> Descargar Firma de la UAA
                                </a>
                            </div>
                        @else
                            <h3><b>Se ha completado el proceso de revisión para este expediente:</b></h3>
                            <!-- Incluir el Stepper -->
                            @include('apartados.partials.stepper', ['auditoria' => $auditoria])
                            <!-- Incluir el Formulario de Carga de la UAA -->
                            @include('apartados.partials.upload_uua_form', ['auditoria' => $auditoria])
                        @endif
                
                    </div>
                @else
                    <!-- Otro contenido para casos donde el estatus_checklist no es 'Aceptado' -->
                    <div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg p-6 mt-8 text-center">
                        <p class="text-gray-700">La auditoría aún no ha sido aceptada para subir la firma de la UAA.</p>
                    </div>
                @if($auditoria->estatus_checklist === 'Devuelto')
                    <!-- Enlace para Descargar PDF -->
                    <a href="/auditorias/{{ $auditoria->id }}/pdf" class="text-blue-500 hover:text-blue-700 underline">
                        <h4 class="text-lg">Descargar PDF para su firma</h4>
                    </a>
                @endif
                <form action="{{ route('apartados.checklist.store') }}" method="POST" id="checklist-form">
                    @csrf
                    <input type="hidden" name="auditoria_id" value="{{ $auditoria->id }}">

                    <!-- Estatus del Checklist -->
                    <div class="mb-8">
                        <label for="estatus_checklist" class="block text-lg font-medium text-gray-700">Estatus del Checklist</label>
                        <!-- Custom Select Component -->
                        <div class="relative mt-2">
                            <select name="estatus_checklist" id="estatus_checklist" class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded-md leading-tight focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="En Proceso" {{ old('estatus_checklist', $auditoria->estatus_checklist) == 'En Proceso' ? 'selected' : '' }}>EN PROCESO</option>
                                <option value="Aceptado" {{ old('estatus_checklist', $auditoria->estatus_checklist) == 'Aceptado' ? 'selected' : '' }}>ACEPTA</option>
                                <option value="Devuelto" {{ old('estatus_checklist', $auditoria->estatus_checklist) == 'Devuelto' ? 'selected' : '' }}>DEVUELVE</option>
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
                                        <input type="text" name="auditor_nombre" id="auditor_nombre" value="{{ old('auditor_nombre', trim($auditoria->auditor_nombre ?? $auditoria->jefe_de_departamento) ?: $auditoria->jefe_de_departamento) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label for="auditor_puesto" class="block text-sm font-medium text-gray-700">Puesto</label>
                                        <input type="text" name="auditor_puesto" id="auditor_puesto" value="{{ old('auditor_puesto', trim($auditoria->auditor_puesto ?? $auditoria->jd) ?: $auditoria->jd) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Seguimiento -->
                            <div class="bg-gray-50 p-6 rounded-lg shadow-inner">
                                <h4 class="text-lg font-medium text-gray-800 mb-4">Seguimiento que revisa, acepta o devuelve el expediente</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label for="seguimiento_nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                                        <input type="text" name="seguimiento_nombre" id="seguimiento_nombre" value="{{ old('seguimiento_nombre', trim($auditoria->seguimiento_nombre ?? auth()->user()->name) ?: auth()->user()->name) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label for="seguimiento_puesto" class="block text-sm font-medium text-gray-700">Puesto</label>
                                        <input type="text" name="seguimiento_puesto" id="seguimiento_puesto" value="{{ old('seguimiento_puesto', trim($auditoria->seguimiento_puesto ?? auth()->user()->puesto) ?: auth()->user()->puesto) }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
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
                    </div>
                </form>
                @endif
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
                        ¿Está seguro que desea aceptar el expediente?
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Una vez aceptado se le hara del conocimiento a la UAA y deberá de proceder a la descarga y firma de la lista de verificación
                            <br>
                            En caso que no acepte, deberá de mantener el estatus de "En proceso".
                            <br>
                            Una vez aceptado, no podrá modificar la lista de verificación.
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

    @push('scripts')
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
                if (tableContainer) {
                    tableContainer.addEventListener('wheel', (evt) => {
                        if (evt.deltaY !== 0) {
                            evt.preventDefault();
                            tableContainer.scrollLeft += evt.deltaY;
                        }
                    });
                }

                // Modal confirmation on form submit
                const form = document.getElementById('checklist-form');
                const estatusSelect = document.getElementById('estatus_checklist');
                const guardarButton = document.getElementById('guardar-checklist');
                const modal = document.getElementById('confirmation-modal');
                const confirmSaveButton = document.getElementById('confirm-save');
                const cancelSaveButton = document.getElementById('cancel-save');
                const closeAlertButton = document.getElementById('close-error-alert');
                const validationError = document.getElementById('validation-error');
                const validationErrorText = document.getElementById('validation-error-text');

                closeAlertButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    validationError.classList.add('hidden');
                });

                guardarButton.addEventListener('click', (e) => {
                    if (estatusSelect.value === 'Aceptado') {
                        e.preventDefault();

                        // Realizar la validación antes de mostrar el modal
                        const mandatoryRows = document.querySelectorAll('tr.mandatory');
                        let isValid = true;
                        let invalidApartados = [];

                        mandatoryRows.forEach(row => {
                            const seIntegraCheckbox = row.querySelector('input[name*="se_integra"]');
                            const comentariosUaaTextarea = row.querySelector('textarea[name*="comentarios_uaa"]');

                            const seIntegraChecked = seIntegraCheckbox && seIntegraCheckbox.checked;
                            const comentariosUaaFilled = comentariosUaaTextarea && comentariosUaaTextarea.value.trim() !== '';

                            if (!seIntegraChecked && !comentariosUaaFilled) {
                                isValid = false;

                                // Obtener el nombre del apartado desde el atributo de datos
                                const nombreApartado = row.dataset.nombreApartado || 'Apartado sin nombre';
                                invalidApartados.push(nombreApartado);

                                // Resaltar la fila
                                row.classList.add('border-red-500', 'bg-red-50');
                            } else {
                                // Remover resaltado si ya no es inválido
                                row.classList.remove('border-red-500', 'bg-red-50');
                            }
                        });

                        if (isValid) {
                            // Mostrar modal si todo es válido
                            modal.classList.remove('hidden');
                            validationError.classList.add('hidden');
                        } else {
                            // Mostrar mensaje de error indicando qué apartados faltan
                            validationErrorText.innerHTML = `No puedes marcar como "Aceptado" porque los siguientes apartados obligatorios no están completos:<br> <b>${invalidApartados.join('<br>')}</b>`;
                            validationError.classList.remove('hidden');
                        }
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
    @endpush
</x-app-layout>
