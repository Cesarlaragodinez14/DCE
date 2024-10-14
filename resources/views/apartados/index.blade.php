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
                @if($auditoria->estatus_checklist === 'Aceptado')
                <div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg p-6 text-center">
                    <!-- Título Principal -->
                    <h3 class="text-2xl font-bold text-green-600 mb-4">
                        Se ha aceptado este expediente, en espera de firmas
                    </h3>
                
                    <!-- Mensajes de Sesión -->
                    @if (session()->has('message'))
                        <div class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg">
                            {{ session('message') }}
                        </div>
                    @endif
                
                    <!-- Enlace para Descargar PDF -->
                    <a href="/auditorias/{{ $auditoria->id }}/pdf" class="text-blue-500 hover:text-blue-700 underline">
                        <h4 class="text-lg">Descargar PDF para su firma</h4>
                    </a>
                
                    <!-- Stepper de Progreso -->
                    <div class="mt-6">
                        <div class="flex justify-between items-center">
                            <!-- Paso 1: Descargar Archivo Inicial -->
                            <div class="flex flex-col items-center">
                                <div class="relative">
                                    <div class="w-8 h-8 flex items-center justify-center rounded-full 
                                        @if($auditoria->archivo_seguimiento || $auditoria->archivo_uua)
                                            bg-green-500 text-white
                                        @else
                                            bg-gray-300 text-gray-700
                                        @endif
                                    ">
                                        @if($auditoria->archivo_seguimiento || $auditoria->archivo_uua)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <span>1</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="mt-2 text-sm font-medium">Descargar</span>
                            </div>
                
                            <!-- Línea de Conexión -->
                            <div class="flex-1 h-1 bg-gray-300"></div>
                
                            <!-- Paso 2: Subir Seguimiento con Firma -->
                            <div class="flex flex-col items-center">
                                <div class="relative">
                                    <div class="w-8 h-8 flex items-center justify-center rounded-full 
                                        @if($auditoria->archivo_seguimiento)
                                            bg-green-500 text-white
                                        @else
                                            bg-gray-300 text-gray-700
                                        @endif
                                    ">
                                        @if($auditoria->archivo_seguimiento)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <span>2</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="mt-2 text-sm font-medium">Seguimiento</span>
                            </div>
                
                            <!-- Línea de Conexión -->
                            <div class="flex-1 h-1 bg-gray-300"></div>
                
                            <!-- Paso 3: Subir Firma de la UAA -->
                            <div class="flex flex-col items-center">
                                <div class="relative">
                                    <div class="w-8 h-8 flex items-center justify-center rounded-full 
                                        @if($auditoria->archivo_uua)
                                            bg-green-500 text-white
                                        @else
                                            bg-gray-300 text-gray-700
                                        @endif
                                    ">
                                        @if($auditoria->archivo_uua)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <span>3</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="mt-2 text-sm font-medium">Firma UAA</span>
                            </div>
                        </div>
                
                        <!-- Descripciones de los Pasos -->
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between">
                                <!-- Descripción Paso 1 -->
                                <div class="text-center">
                                    <p class="text-sm font-semibold">Paso 1: Descargar Archivo Inicial</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-semibold">Paso 2: Subir Seguimiento con Firma</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-semibold">Paso 3: Subir Firma de la UAA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- Formulario de Carga de Seguimiento con Firma -->
                    <div class="max-w-md mx-auto bg-white shadow-md rounded-lg p-6 mt-8">
                        <h4 class="text-xl font-semibold text-gray-700 mb-4">Subir Seguimiento con Firma</h4>
                        <form id="uploadSeguimientoForm" class="space-y-4">
                            @csrf
                            <!-- Campo de archivo -->
                            <div>
                                <label for="seguimiento_archivo" class="block text-sm font-medium text-gray-700">Selecciona el archivo de Seguimiento</label>
                                <input type="file" name="seguimiento_archivo" id="seguimiento_archivo" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">Archivos permitidos: PDF, DOC, DOCX, PNG, JPG, JPEG. Tamaño máximo: 2MB.</p>
                                <span id="seguimientoError" class="text-red-500 text-sm hidden">Por favor, selecciona un archivo válido.</span>
                            </div>
                
                            <!-- Botón de envío -->
                            <div>
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Subir Seguimiento
                                </button>
                            </div>
                        </form>
                
                        <!-- Mensajes de Éxito o Error -->
                        <div id="seguimientoMessage" class="mt-4 hidden text-center"></div>
                    </div>
                
                    <!-- Formulario de Carga de Firma de la UAA -->
                    <div class="max-w-md mx-auto bg-white shadow-md rounded-lg p-6 mt-8">
                        <h4 class="text-xl font-semibold text-gray-700 mb-4">Subir Firma de la UAA</h4>
                        <form id="uploadUuaForm" class="space-y-4">
                            @csrf
                            <!-- Campo de archivo -->
                            <div>
                                <label for="uua_archivo" class="block text-sm font-medium text-gray-700">Selecciona el archivo firmado por la UAA</label>
                                <input type="file" name="uua_archivo" id="uua_archivo" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">Archivos permitidos: PDF, DOC, DOCX, PNG, JPG, JPEG. Tamaño máximo: 2MB.</p>
                                <span id="uuaError" class="text-red-500 text-sm hidden">Por favor, selecciona un archivo válido.</span>
                            </div>
                
                            <!-- Botón de envío -->
                            <div>
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Subir Firma de la UAA
                                </button>
                            </div>
                        </form>
                
                        <!-- Mensajes de Éxito o Error -->
                        <div id="uuaMessage" class="mt-4 hidden text-center"></div>
                    </div>
                
                    <!-- Scripts -->
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            // Formulario de Seguimiento
                            const seguimientoForm = document.getElementById('uploadSeguimientoForm');
                            const seguimientoInput = document.getElementById('seguimiento_archivo');
                            const seguimientoError = document.getElementById('seguimientoError');
                            const seguimientoMessage = document.getElementById('seguimientoMessage');
                
                            // Formulario de UUA
                            const uuaForm = document.getElementById('uploadUuaForm');
                            const uuaInput = document.getElementById('uua_archivo');
                            const uuaError = document.getElementById('uuaError');
                            const uuaMessage = document.getElementById('uuaMessage');
                
                            // Función para actualizar el stepper
                            function updateStepper(paso) {
                                // Paso 1: Descargar (si ya descargó, no cambia)
                                // Paso 2: Seguimiento
                                const step2 = document.querySelectorAll('.flex.flex-col.items-center')[1].querySelector('div.relative div');
                                if (paso >= 2) {
                                    step2.classList.remove('bg-gray-300', 'text-gray-700');
                                    step2.classList.add('bg-green-500', 'text-white');
                                    step2.innerHTML = `
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    `;
                                }
                
                                // Paso 3: Firma UAA
                                const step3 = document.querySelectorAll('.flex.flex-col.items-center')[2].querySelector('div.relative div');
                                if (paso >= 3) {
                                    step3.classList.remove('bg-gray-300', 'text-gray-700');
                                    step3.classList.add('bg-green-500', 'text-white');
                                    step3.innerHTML = `
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    `;
                                }
                            }
                
                            // Manejador de envío del formulario de Seguimiento
                            seguimientoForm.addEventListener('submit', function (e) {
                                e.preventDefault();
                
                                // Limpiar mensajes anteriores
                                seguimientoError.classList.add('hidden');
                                seguimientoMessage.classList.add('hidden');
                                seguimientoMessage.innerHTML = '';
                
                                // Validación del archivo
                                const archivo = seguimientoInput.files[0];
                                if (!archivo) {
                                    seguimientoError.textContent = 'Por favor, selecciona un archivo.';
                                    seguimientoError.classList.remove('hidden');
                                    return;
                                }
                
                                const allowedTypes = [
                                    'application/pdf',
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'image/png',
                                    'image/jpeg',
                                    'image/jpg'
                                ];
                                if (!allowedTypes.includes(archivo.type)) {
                                    seguimientoError.textContent = 'Tipo de archivo no permitido.';
                                    seguimientoError.classList.remove('hidden');
                                    return;
                                }
                
                                const maxSize = 2 * 1024 * 1024; // 2MB
                                if (archivo.size > maxSize) {
                                    seguimientoError.textContent = 'El archivo excede el tamaño máximo de 2MB.';
                                    seguimientoError.classList.remove('hidden');
                                    return;
                                }
                
                                // Preparar los datos para enviar
                                const formData = new FormData(seguimientoForm);
                
                                // Enviar la solicitud AJAX
                                fetch('{{ route('apartados.storeSeguimiento') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        seguimientoMessage.classList.remove('hidden', 'text-red-500');
                                        seguimientoMessage.classList.add('text-green-500');
                                        seguimientoMessage.textContent = data.message || 'Seguimiento cargado exitosamente.';
                
                                        // Actualizar el stepper
                                        updateStepper(2);
                
                                        // Deshabilitar el formulario de Seguimiento
                                        seguimientoForm.querySelector('button[type="submit"]').disabled = true;
                
                                        // Activar el formulario de UUA
                                        uuaForm.querySelector('button[type="submit"]').disabled = false;
                                    } else {
                                        seguimientoMessage.classList.remove('hidden', 'text-green-500');
                                        seguimientoMessage.classList.add('text-red-500');
                                        seguimientoMessage.textContent = data.message || 'Hubo un error al cargar el Seguimiento.';
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    seguimientoMessage.classList.remove('hidden', 'text-green-500');
                                    seguimientoMessage.classList.add('text-red-500');
                                    seguimientoMessage.textContent = 'Hubo un error al cargar el Seguimiento.';
                                });
                            });
                
                            // Manejador de envío del formulario de UUA
                            uuaForm.addEventListener('submit', function (e) {
                                e.preventDefault();
                
                                // Limpiar mensajes anteriores
                                uuaError.classList.add('hidden');
                                uuaMessage.classList.add('hidden');
                                uuaMessage.innerHTML = '';
                
                                // Validación del archivo
                                const archivo = uuaInput.files[0];
                                if (!archivo) {
                                    uuaError.textContent = 'Por favor, selecciona un archivo.';
                                    uuaError.classList.remove('hidden');
                                    return;
                                }
                
                                const allowedTypes = [
                                    'application/pdf',
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'image/png',
                                    'image/jpeg',
                                    'image/jpg'
                                ];
                                if (!allowedTypes.includes(archivo.type)) {
                                    uuaError.textContent = 'Tipo de archivo no permitido.';
                                    uuaError.classList.remove('hidden');
                                    return;
                                }
                
                                const maxSize = 2 * 1024 * 1024; // 2MB
                                if (archivo.size > maxSize) {
                                    uuaError.textContent = 'El archivo excede el tamaño máximo de 2MB.';
                                    uuaError.classList.remove('hidden');
                                    return;
                                }
                
                                // Preparar los datos para enviar
                                const formData = new FormData(uuaForm);
                
                                // Enviar la solicitud AJAX
                                fetch('{{ route('apartados.storeUua') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        uuaMessage.classList.remove('hidden', 'text-red-500');
                                        uuaMessage.classList.add('text-green-500');
                                        uuaMessage.textContent = data.message || 'Firma de la UAA cargada exitosamente.';
                
                                        // Actualizar el stepper
                                        updateStepper(3);
                
                                        // Deshabilitar el formulario de UUA
                                        uuaForm.querySelector('button[type="submit"]').disabled = true;
                                    } else {
                                        uuaMessage.classList.remove('hidden', 'text-green-500');
                                        uuaMessage.classList.add('text-red-500');
                                        uuaMessage.textContent = data.message || 'Hubo un error al cargar la Firma de la UAA.';
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    uuaMessage.classList.remove('hidden', 'text-green-500');
                                    uuaMessage.classList.add('text-red-500');
                                    uuaMessage.textContent = 'Hubo un error al cargar la Firma de la UAA.';
                                });
                            });
                        });
                    </script>
                </div>
                
                @else
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
