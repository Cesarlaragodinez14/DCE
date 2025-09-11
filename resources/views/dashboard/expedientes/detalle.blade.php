<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
            {{ __('Programación de entrega de expedientes de acción por la UAA') }}
            <div class="ml-auto flex items-center space-x-4">
                <x-ui.filter-cp-en
                    :entregas="$entregas"
                    :cuentasPublicas="$cuentasPublicas"
                    route="dashboard.expedientes.entrega"
                    defaultEntregaLabel="Seleccionar Entrega"
                    defaultCuentaPublicaLabel="Seleccionar Cuenta Pública"
                />
            </div>
        </h2>
    </x-slot>

    <!-- Alert de errores con botón de cierre -->
    <div id="errorAlert" class="fixed bottom-0 left-0 right-0 bg-red-500 text-white p-4 rounded-t-lg shadow-lg hidden">
        <div class="flex justify-between items-center">
            <ul id="errorList" class="list-disc list-inside"></ul>
            <button id="closeErrorBtn" class="text-white font-bold text-xl">&times;</button>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($expedientes->isEmpty())
                <div class="text-center p-6 bg-red-100 text-red-600">
                    <p>No hay información disponible para la selección actual.</p>
                </div>
            @else
                <div class="bg-white shadow-lg rounded-lg">
                    <form id="validacionForm" action="{{ route('expedientes.validar') }}" method="POST">
                        @csrf
                        <div class="p-6 border-b border-gray-200">
                            <!-- Encabezado: Fecha y Responsable -->
                            <div class="flex flex-col sm:flex-row items-center justify-between space-y-2 sm:space-y-0">
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ __('Auditoría: ') }} {{ $uaaName }}
                                </h3>
                                <div class="flex items-center space-x-4">
                                    <input type="date" name="fecha_entrega" class="form-input rounded-md" 
                                           value="{{ old('fecha_entrega', \Carbon\Carbon::now()->format('Y-m-d')) }}" 
                                           required>
                                    <input type="text" name="responsable" class="form-input rounded-md" 
                                           placeholder="Nombre del responsable" 
                                           value="{{ old('responsable') }}" 
                                           list="userNameList" 
                                           required>
                                    <datalist id="userNameList">
                                        @foreach($users as $user)
                                            <option value="{{ $user }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">El responsable deberá acudir con identificación oficial para la entrega.</p>
                        </div>

                        <!-- Tabla de Expedientes -->
                        <div class="mt-6 overflow-x-auto">
                            <x-ui.table.index>
                                <x-slot name="head">
                                    <x-ui.table.header>#</x-ui.table.header>
                                    <x-ui.table.header>CP</x-ui.table.header>
                                    <x-ui.table.header>Entrega</x-ui.table.header>
                                    <x-ui.table.header>Núm. Auditoría</x-ui.table.header>
                                    <x-ui.table.header>Ente acción</x-ui.table.header>
                                    <x-ui.table.header>Clave acción</x-ui.table.header>
                                    <x-ui.table.header>Tipo acción</x-ui.table.header>
                                    <x-ui.table.header>Estatus de Expediente</x-ui.table.header>
                                    <x-ui.table.header>Responsable de Entrega</x-ui.table.header>
                                    <x-ui.table.header>Número de legajos</x-ui.table.header>
                                    <x-ui.table.header>Seleccionar</x-ui.table.header>
                                </x-slot>
                            
                                <x-slot name="body">
                                    @forelse($expedientes as $index => $expediente)
                                        @php
                                            // Usamos el ID del expediente para identificar de forma única la fila
                                            $uniqueKey = $expediente->id; 
                                            
                                            // Variables provenientes de la leftJoin (pueden ser NULL si no hay programación)
                                            $estadoEntrega    = $expediente->estado_entrega; // 'Programado', 'Entregado', o NULL
                                            $fechaProg        = $expediente->entrega_programada ? \Carbon\Carbon::parse($expediente->entrega_programada) : null;
                                            $fechaReal        = $expediente->entrega_realizada ? \Carbon\Carbon::parse($expediente->entrega_realizada) : null;
                                            $legajosRecibidos = $expediente->numero_legajos;
                                            $respEntrega      = $expediente->responsable_entrega ?? 'No asignado';
                                            $fechaHoy         = \Carbon\Carbon::now();
                            
                                            // Lógica para determinar el estado a mostrar y si se deben habilitar controles
                                            if (is_null($estadoEntrega)) {
                                                // Sin programación: se muestra "Sin Programación" y se permite capturar legajos (input inicialmente disabled)
                                                $statusExp = 'Sin Programación';
                                                $checkboxDisabled = false;
                                                $legajosMostrar = '';  // Valor vacío por defecto
                                            } else {
                                                // Si ya está programado o entregado, se muestra la información y no se permite la edición
                                                if (str_contains($estadoEntrega, "Recibido") && $fechaReal) {
                                                    $statusExp = 'Entregado el ' . $fechaReal->format('d/m/Y');
                                                    $legajosMostrar = $legajosRecibidos;
                                                    $checkboxDisabled = true;
                                                } else if (str_contains($estadoEntrega, "Recibido") && $fechaReal == null) {
                                                    $statusExp = 'Entregado el ' . $fechaProg->format('d/m/Y');
                                                    $legajosMostrar = $legajosRecibidos;
                                                    $checkboxDisabled = true;
                                                } else {
                                                    $statusExp = 'Fecha programada: '.$fechaProg->format('d/m/Y');
                                                    $legajosMostrar = $legajosRecibidos;
                                                    $checkboxDisabled = true;
                                                }
                                            }
                                        @endphp
                            
                                        <x-ui.table.row>
                                            <x-ui.table.column>{{ $index + 1 }}</x-ui.table.column>
                                            <x-ui.table.column>{{ $expediente->CP }}</x-ui.table.column>
                                            <x-ui.table.column>{{ $expediente->entrega }}</x-ui.table.column>
                                            <x-ui.table.column>{{ $expediente->numero_auditoria }}</x-ui.table.column>
                                            <x-ui.table.column>{{ $expediente->ente_accion }}</x-ui.table.column>
                                            <x-ui.table.column>{{ $expediente->clave_accion }}</x-ui.table.column>
                                            <x-ui.table.column>{{ $expediente->tipo_accion }}</x-ui.table.column>
                            
                                            <!-- Estatus de Expediente -->
                                            <x-ui.table.column>{{ $statusExp }}</x-ui.table.column>
                            
                                            <!-- Responsable de Entrega -->
                                            <x-ui.table.column>{{ $respEntrega }}</x-ui.table.column>
                            
                                            <!-- Número de legajos -->
                                            <x-ui.table.column>
                                                @if($statusExp !== 'Sin Programación')
                                                    <input type="number" min="1" 
                                                           class="form-input rounded-md" 
                                                           name="legajos[{{ $uniqueKey }}]"
                                                           value="{{ $legajosMostrar }}"
                                                           disabled>
                                                @else
                                                    <!-- En la condición "Sin Programación", el input se muestra inicialmente disabled -->
                                                    <input type="number" min="1"
                                                           class="form-input rounded-md legajos-input"
                                                           name="legajos[{{ $uniqueKey }}]"
                                                           value="{{ old("legajos.$uniqueKey") }}"
                                                           disabled>
                                                @endif
                                            </x-ui.table.column>
                            
                                            <!-- Checkbox de selección -->
                                            <x-ui.table.column>
                                                @if(str_contains($statusExp, 'Entregado'))
                                                <span class="text-green-600 font-semibold">{{$statusExp}}</span>
                                                @elseif($statusExp !== 'Sin Programación')
                                                    <span class="text-green-600 font-semibold">Programado</span>
                                                @else
                                                    <input type="checkbox"
                                                           class="form-checkbox expediente-checkbox"
                                                           name="expedientes[]"
                                                           value="{{ $expediente->id }}"
                                                           data-index="{{ $uniqueKey }}"
                                                           {{ $checkboxDisabled ? 'disabled' : '' }}>
                                                @endif
                                            </x-ui.table.column>
                                        </x-ui.table.row>
                                    @empty
                                        <tr>
                                            <td colspan="17" class="px-4 py-2 text-center text-gray-500">
                                                No hay expedientes que cumplan el criterio.
                                            </td>
                                        </tr>
                                    @endforelse
                                </x-slot>
                            </x-ui.table.index>
                        </div>

                        <!-- Botón para enviar -->
                        <div class="mt-6 flex justify-center">
                            <x-button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" id="validarButton">
                                Validar Entrega
                            </x-button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Modal de Acuse -->
    <div id="acuseModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded shadow-md max-w-lg w-full">
            <h3 class="text-lg font-bold mb-4 text-center">Expedientes Seleccionados</h3>
            
            <!-- Select de estado único para todos -->
            <div class="mb-4 text-sm text-gray-700">
                <label for="estadoRecepcion" class="block text-sm font-medium text-gray-700 mb-1 text-left">
                    Selecciona un Estado de Recepción <span class="text-red-500">*</span>
                </label>
                <select id="estadoRecepcion" name="estado_recepcion" class="mt-1 block w-full rounded-md border-gray-300 text-gray-700">
                    <option value="">-- Seleccionar --</option>
                    <option value="Recibido en el DCE PO superveniente (UAA – DCE)">Recibido en el DCE PO superveniente (UAA – DCE)</option>
                    <option value="Recibido en el DCE (UAA – DCE)">Recibido en el DCE (UAA – DCE)</option>
                    <option value="Recibido por la DGSEG para revisión (DCE - DGSEG)">Recibido por la DGSEG para revisión (DCE - DGSEG)</option>
                    <option value="Recibido en el DCE para resguardo (DGSEG – DCE)">Recibido en el DCE para resguardo (DGSEG – DCE)</option>
                    <option value="Recibido en el DCE con corrección para la UAA (DGSEG – DCE)">Recibido en el DCE con corrección para la UAA (DGSEG – DCE)</option>
                    <option value="Recibido por la UAA para corrección">Recibido por la UAA para corrección</option>
                    <option value="Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)">
                        Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)
                    </option>
                </select>
            </div>
            
            <!-- Listado de expedientes seleccionados -->
            <div id="acuseList" style="max-height: 50vh; overflow-y: auto;" class="mb-4 text-sm text-gray-700 border-t pt-2"></div>
            
            <div class="text-center mt-4">
                <button onclick="confirmAcuse()" class="bg-green-500 text-white px-4 py-2 rounded mr-2">
                    Confirmar
                </button>
                <button onclick="closeAcuseModal()" class="bg-red-500 text-white px-4 py-2 rounded">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- Formulario oculto para generar acuse -->
    <form id="acuseForm" action="{{ route('recepcion.generarAcuse') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="expedientes_seleccionados" id="expedientesSeleccionadosInput">
        <input type="hidden" name="estado_recepcion" id="estadoRecepcionInput">
    </form>

    @push('scripts')
    <script>
        "use strict";
        // Usaremos un objeto para almacenar las selecciones: key=index, value=expedienteId
        let selectedExpedientes = {};

        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll('.expediente-checkbox');
            checkboxes.forEach(chk => {
                chk.addEventListener('change', function() {
                    const index = this.getAttribute('data-index');
                    const legajosInput = document.querySelector(`input[name="legajos[${index}]"]`);
                    if (this.checked) {
                        // Al seleccionar el checkbox, habilitamos el input de legajos
                        if (legajosInput) {
                            legajosInput.disabled = false;
                            legajosInput.required = true;
                        }
                    } else {
                        // Si se deselecciona, se vuelve a deshabilitar el input
                        if (legajosInput) {
                            legajosInput.disabled = true;
                            legajosInput.required = false;
                        }
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const validarButton = document.getElementById('validarButton');
            const errorAlert = document.getElementById('errorAlert');
            const errorList = document.getElementById('errorList');
            const closeErrorBtn = document.getElementById('closeErrorBtn');

            if (closeErrorBtn) {
                closeErrorBtn.addEventListener('click', function() {
                    errorAlert.classList.add('hidden');
                });
            }

            validarButton.addEventListener('click', function () {
                let errors = [];
                document.querySelectorAll('.expediente-checkbox').forEach(chk => {
                    const index = chk.getAttribute('data-index');
                    if (!chk.disabled && chk.checked) {
                        const legajosInput = document.querySelector(`input[name="legajos[${index}]"]`);
                        if (!legajosInput.value || parseInt(legajosInput.value) <= 0) {
                            errors.push(`Debe ingresar un número válido de legajos para el expediente de la fila ${parseInt(index) + 1}.`);
                        }
                    }
                });

                const fechaEntrega = document.querySelector('input[name="fecha_entrega"]').value;
                if (!fechaEntrega) {
                    errors.push('Debe seleccionar una fecha de entrega.');
                }

                const responsable = document.querySelector('input[name="responsable"]').value;
                if (!responsable) {
                    errors.push('Debe ingresar el nombre del responsable.');
                }

                if (errors.length > 0) {
                    errorList.innerHTML = errors.map(e => `<li>${e}</li>`).join('');
                    errorAlert.classList.remove('hidden');
                    setTimeout(() => {
                        errorAlert.classList.add('hidden');
                    }, 5000);
                } else {
                    document.getElementById('validacionForm').submit();
                }
            });
        });

        // Modal de acuse
        function openAcuseModal() {
            const acuseListDiv = document.getElementById('acuseList');
            acuseListDiv.innerHTML = '';

            const indices = Object.keys(selectedExpedientes);
            if (indices.length === 0) {
                acuseListDiv.innerHTML = '<p class="text-red-500">No hay expedientes seleccionados.</p>';
            } else {
                const rows = Array.from(document.querySelectorAll('#recepcion-table tbody tr'));
                let selectedData = [];
                rows.forEach(row => {
                    const chk = row.querySelector('.expediente-checkbox');
                    if (chk && indices.includes(chk.getAttribute('data-index'))) {
                        // Asegurarse de que se extraigan las columnas correctas:
                        // 8ª columna (índice 7): Clave de Acción
                        // 12ª columna (índice 11): Responsable de Entrega UAA
                        const clave = row.children[7] ? row.children[7].innerText.trim() : '';
                        const responsable = row.children[11] ? row.children[11].innerText.trim() : '';
                        selectedData.push({ clave, responsable });
                    }
                });
                acuseListDiv.innerHTML = selectedData.map(item =>
                    `<div class="border-b py-1 text-left">
                        <strong>Clave de Acción:</strong> ${item.clave}<br>
                        <strong>Responsable:</strong> ${item.responsable}
                    </div>`
                ).join('');
            }

            document.getElementById('acuseModal').classList.remove('hidden');
            document.getElementById('acuseModal').classList.add('flex');
        }

        function closeAcuseModal() {
            document.getElementById('acuseModal').classList.add('hidden');
            document.getElementById('acuseModal').classList.remove('flex');
        }

        function confirmAcuse() {
            if (Object.keys(selectedExpedientes).length === 0) {
                alert('No hay expedientes seleccionados.');
                return;
            }
            const estadoSelect = document.getElementById('estadoRecepcion');
            const estadoSeleccionado = estadoSelect.value;
            if (!estadoSeleccionado) {
                alert('Debe seleccionar un estado de recepción para todos los expedientes.');
                return;
            }
            document.getElementById('expedientesSeleccionadosInput').value = JSON.stringify(selectedExpedientes);
            document.getElementById('estadoRecepcionInput').value = estadoSeleccionado;
            document.getElementById('acuseForm').submit();
        }

        async function toggleEntregadoViaAjax(expedienteId, isChecked) {
            try {
                const resp = await fetch("{{ route('recepcion.ajaxToggleEntregado') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ expediente_id: expedienteId, entregado: isChecked })
                });
                const data = await resp.json();
                if (!resp.ok) {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error en toggleEntregadoViaAjax:', error);
                alert('Ocurrió un error inesperado.');
            }
        }
    </script>
    @endpush

    <style>
        /* Tabla moderna centrada */
        #recepcion-table {
            border-collapse: collapse;
            width: 100%;
        }
        #recepcion-table th, #recepcion-table td {
            padding: 0.75rem;
            text-align: center;
            vertical-align: middle;
        }
        #recepcion-table th {
            background-color: #f9fafb;
            color: #4b5563;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        #recepcion-table tbody tr:hover {
            background-color: #f3f4f6;
        }
        /* Modal */
        #acuseModal {
            z-index: 9999;
        }
    </style>
</x-app-layout>
