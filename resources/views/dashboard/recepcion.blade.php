<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
            {{ __('Recepción de Expedientes') }}
        </h2>
    </x-slot>

    <!-- Alert Fijo para Errores con botón de cierre -->
    <div id="errorAlert" class="fixed bottom-0 left-0 right-0 bg-red-500 text-white p-4 rounded-t-lg shadow-lg hidden">
        <div class="flex justify-between items-center">
            <ul id="errorList" class="list-disc list-inside"></ul>
            <button id="closeErrorBtn" class="text-white font-bold text-xl leading-none">&times;</button>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                <form id="filtrosForm" method="GET" action="{{ route('recepcion.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                    <!-- Entrega -->
                    <div>
                        <label for="entrega" class="block text-sm font-medium text-gray-700">Entrega:</label>
                        <select name="entrega" id="entrega" class="mt-1 block w-full rounded-md border-gray-300" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach($entregas as $e)
                                <option value="{{ $e->id }}" {{ request('entrega') == $e->id ? 'selected' : '' }}>
                                    {{ $e->valor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Cuenta Pública -->
                    <div>
                        <label for="cuenta_publica" class="block text-sm font-medium text-gray-700">Cuenta Pública:</label>
                        <select name="cuenta_publica" id="cuenta_publica" class="mt-1 block w-full rounded-md border-gray-300" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach($cuentasPublicas as $cp)
                                <option value="{{ $cp->id }}" {{ request('cuenta_publica') == $cp->id ? 'selected' : '' }}>
                                    {{ $cp->valor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Estatus -->
                    <div>
                        <label for="estatus" class="block text-sm font-medium text-gray-700">Estatus:</label>
                        <select name="estatus" id="estatus" class="mt-1 block w-full rounded-md border-gray-300" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="Programado" {{ request('estatus')=='Programado' ? 'selected' : '' }}>Programado</option>
                            <option value="Recibido en el DCE (UAA – DCE)" {{ request('estatus')=='Recibido en el DCE (UAA – DCE)' ? 'selected' : '' }}>Recibido en el DCE (UAA – DCE)</option>
                            <option value="Recibido en el DCE (UAA – DCE) - Firmado" {{ request('estatus')=='Recibido en el DCE (UAA – DCE) - Firmado' ? 'selected' : '' }}>Recibido en el DCE (UAA – DCE) - Firmado</option>
                            <option value="Recibido por la DGSEG para revisión (DCE - DGSEG)" {{ request('estatus')=='Recibido por la DGSEG para revisión (DCE - DGSEG)' ? 'selected' : '' }}>Recibido por la DGSEG para revisión (DCE - DGSEG)</option>
                            <option value="Recibido por la DGSEG para revisión (DCE - DGSEG) - Firmado" {{ request('estatus')=='Recibido por la DGSEG para revisión (DCE - DGSEG) - Firmado' ? 'selected' : '' }}>Recibido por la DGSEG para revisión (DCE - DGSEG) - Firmado</option>
                            <option value="Recibido en el DCE para resguardo (DGSEG – DCE)" {{ request('estatus')=='Recibido en el DCE para resguardo (DGSEG – DCE)' ? 'selected' : '' }}>Recibido en el DCE para resguardo (DGSEG – DCE)</option>
                            <option value="Recibido en el DCE para resguardo (DGSEG – DCE) - Firmado" {{ request('estatus')=='Recibido en el DCE para resguardo (DGSEG – DCE) - Firmado' ? 'selected' : '' }}>Recibido en el DCE para resguardo (DGSEG – DCE) - Firmado</option>
                            <option value="Recibido en el DCE con corrección para la UAA (DGSEG – DCE)" {{ request('estatus')=='Recibido en el DCE con corrección para la UAA (DGSEG – DCE)' ? 'selected' : '' }}>Recibido en el DCE con corrección para la UAA (DGSEG – DCE)</option>
                            <option value="Recibido en el DCE con corrección para la UAA (DGSEG – DCE) - Firmado" {{ request('estatus')=='Recibido en el DCE con corrección para la UAA (DGSEG – DCE) - Firmado' ? 'selected' : '' }}>Recibido en el DCE con corrección para la UAA (DGSEG – DCE) - Firmado</option>
                            <option value="Recibido por la UAA para corrección (DCE - UAA)" {{ request('estatus')=='Recibido por la UAA para corrección (DCE - UAA)' ? 'selected' : '' }}>Recibido por la UAA para corrección (DCE - UAA)</option>
                            <option value="Recibido por la UAA para corrección (DCE - UAA) - Firmado" {{ request('estatus')=='Recibido por la UAA para corrección (DCE - UAA) - Firmado' ? 'selected' : '' }}>Recibido por la UAA para corrección (DCE - UAA) - Firmado</option>
                            <option value="Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)" {{ request('estatus')=='Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)' ? 'selected' : '' }}>
                                Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)
                            </option>
                            <option value="Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE) - Firmado" {{ request('estatus')=='Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE) - Firmado' ? 'selected' : '' }}>
                                Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE) - Firmado
                            </option>
                            <option value="Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)" {{ request('estatus')=='Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)' ? 'selected' : '' }}>Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)</option>
                            <option value="Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG) - Firmado" {{ request('estatus')=='Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG) - Firmado' ? 'selected' : '' }}>Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG) - Firmado</option>
                        </select>
                    </div>
                    <!-- Responsable -->
                    <div>
                        <label for="responsable" class="block text-sm font-medium text-gray-700">Responsable:</label>
                        <input type="text" name="responsable" id="responsable" 
                               class="mt-1 block w-full rounded-md border-gray-300" 
                               value="{{ request('responsable') }}" placeholder="Buscar responsable"
                               onchange="this.form.submit()">
                    </div>
                </form>
            </div>

            <!-- Tabla principal -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse" id="recepcion-table">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase text-center">#</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Cuenta Pública</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Entrega</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">AE</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">UAA</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Núm. Auditoría</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Título</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Clave de Acción</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Tipo de Acción</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Estatus Expediente</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Estatus Revisión</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Resp. Entrega UAA</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Resp. Entrega SEG</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Núm. Legajos</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Fecha de Entrega</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">¿Entregado?</th>
                                <!-- Cambiamos etiqueta a "Rastreo" -->
                                <th class="px-4 py-2 text-xs font-medium text-gray-600 uppercase">Rastreo</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-200">
                            @forelse($expedientes as $i => $exp)
                                <tr>
                                    <td class="px-4 py-2 text-center">{{ $i+1 }}</td>
                                    <td class="px-4 py-2">{{ $exp->cuenta_publica_valor ?? '' }}</td>
                                    <td class="px-4 py-2">{{ $exp->entrega_valor ?? '' }}</td>
                                    <td class="px-4 py-2">{{ $exp->ae_siglas ?? '' }}</td>
                                    <td class="px-4 py-2">{{ $exp->uaa_valor ?? '' }}</td>
                                    <td class="px-4 py-2">{{ $exp->numero_auditoria ?? '' }}</td>
                                    <td class="px-4 py-2">{{ $exp->titulo ?? '' }}</td>
                                    <td class="px-4 py-2">{{ $exp->clave_de_accion ?? '' }}</td>
                                    <td class="px-4 py-2">{{ $exp->tipo_accion_valor ?? '' }}</td>
                                    <td class="px-4 py-2">{{ $exp->estado ?? 'Pendiente' }}</td>
                                    <td class="px-4 py-2">{{ $exp->estatus_revision ?? 'Sin revisión' }}</td>
                                    <td class="px-4 py-2">{{ $exp->responsable_uaa ?? '' }}</td>
                                    <td class="px-4 py-2">{{ $exp->responsable_seg ?? '' }}</td>
                                    <td class="px-4 py-2 text-center">{{ $exp->numero_legajos ?? '' }}</td>
                                    <td class="px-4 py-2 text-center">
                                        @if(!empty($exp->fecha_real_entrega))
                                            {{ \Carbon\Carbon::parse($exp->fecha_real_entrega)->format('d/m/Y') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($exp->fecha_entrega)->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <!-- ¿Entregado? => checklist con AJAX -->
                                    <td class="px-4 py-2 text-center">
                                        <input type="checkbox" 
                                                class="form-checkbox h-5 w-5 text-green-500 received-checkbox" 
                                                data-expediente-id="{{ $exp->id }}"
                                                data-clave="{{ $exp->clave_de_accion }}"
                                                data-responsable="{{ $exp->responsable_uaa }}"
                                                onchange="toggleEntregadoViaAjax({{ $exp->id }}, this.checked)">
                                        
                                    </td>
                                    <!-- Rastreo (timeline) -->
                                    <td class="px-4 py-2 text-center">
                                        <button type="button" 
                                                class="bg-blue-500 hover:bg-blue-700 text-white text-xs px-2 py-1 rounded"
                                                onclick="openRastreoModal({{ $exp->id_entrega }})">
                                            Ver Rastreo
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="17" class="px-4 py-2 text-center text-gray-500">
                                        No hay expedientes que cumplan el criterio.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Botón "Generar acuse" -->
                <div class="mt-6 text-center">
                    <button type="button" 
                            class="bg-blue-600 hover:bg-blue-800 text-white font-semibold px-4 py-2 rounded"
                            onclick="openAcuseModal()">
                        Generar acuse
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación "Generar acuse" -->
    <div id="acuseModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded shadow-md max-w-lg w-full">
            <h3 class="text-lg font-bold mb-4 text-center">Expedientes Seleccionados</h3>

            <!-- Select de estados (uno solo para todos) -->
            <div class="mb-4 text-sm text-gray-700">
                <label for="estadoRecepcion" class="block text-sm font-medium text-gray-700 mb-1 text-left">
                    Selecciona un Estado de Recepción <span class="text-red-500">*</span>
                </label>
                <select id="estadoRecepcion" name="estado_recepcion" class="mt-1 block w-full rounded-md border-gray-300 text-gray-700">
                    <option value="">-- Seleccionar --</option>
                    <option value="Recibido en el DCE (UAA – DCE)">Recibido en el DCE (UAA – DCE)</option>
                    <option value="Recibido por la DGSEG para revisión (DCE - DGSEG)">Recibido por la DGSEG para revisión (DCE - DGSEG)</option>
                    <option value="Recibido en el DCE para resguardo (DGSEG – DCE)">Recibido en el DCE para resguardo (DGSEG – DCE)</option>
                    <option value="Recibido en el DCE con corrección para la UAA (DGSEG – DCE)">Recibido en el DCE con corrección para la UAA (DGSEG – DCE)</option>
                    <option value="Recibido por la UAA para corrección (DCE - UAA)">Recibido por la UAA para corrección (DCE - UAA)</option>
                    <option value="Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)">
                        Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)
                    </option>
                    <option value="Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)" {{ request('estatus')=='Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)' ? 'selected' : '' }}>Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)</option>
                </select>
            </div>

            <!-- Listado de Expedientes -->
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

    <!-- Modal de Rastreo (Timeline) -->
    <div id="rastreoModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded shadow-md max-w-xl w-full">
            <h3 class="text-lg font-bold mb-4 text-center">Rastreo de Entregas</h3>
            <div id="timelineContainer" class="mb-4 text-sm text-gray-700">
                <!-- Aquí se inyectará el timeline de forma dinámica -->
            </div>
            <div class="text-center mt-4">
                <button onclick="closeRastreoModal()" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Formulario oculto para confirmar acuse -->
    <form id="acuseForm" action="{{ route('recepcion.generarAcuse') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="expedientes_seleccionados" id="expedientesSeleccionadosInput">
        <input type="hidden" name="estado_recepcion" id="estadoRecepcionInput">
    </form>

    @push('scripts')
    <script>
        "use strict";

        let selectedExpedientes = [];

        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll('.received-checkbox');
            checkboxes.forEach(chk => {
                if(chk.checked && chk.disabled) {
                    selectedExpedientes.push(chk.getAttribute('data-expediente-id'));
                }
                chk.addEventListener('change', function() {
                    const expId = this.getAttribute('data-expediente-id');
                    if(this.checked) {
                        if(!selectedExpedientes.includes(expId)) {
                            selectedExpedientes.push(expId);
                        }
                    } else {
                        selectedExpedientes = selectedExpedientes.filter(id => id !== expId);
                    }
                });
            });
        });

        // 1) Modal de "Generar Acuse"
        function openAcuseModal() {
            const acuseListDiv = document.getElementById('acuseList');
            acuseListDiv.innerHTML = '';

            if (selectedExpedientes.length === 0) {
                acuseListDiv.innerHTML = '<p class="text-red-500">No hay expedientes seleccionados.</p>';
            } else {
                let hayProgramado = false;
                const rows = Array.from(document.querySelectorAll('#recepcion-table tbody tr'));
                const selectedData = [];

                rows.forEach(row => {
                    const chk = row.querySelector('.received-checkbox');
                    if(chk && selectedExpedientes.includes(chk.getAttribute('data-expediente-id'))) {
                        const estatusExp = row.children[9] ? row.children[9].innerText.trim() : '';
                        if(estatusExp === 'Programado') {
                            hayProgramado = true;
                        }

                        const clave = row.children[7] ? row.children[7].innerText.trim() : '';
                        const responsable = row.children[11] ? row.children[11].innerText.trim() : '';
                        selectedData.push({ clave, responsable, estatusExp });
                    }
                });

                acuseListDiv.innerHTML = selectedData.map(item =>
                    `<div class="border-b py-1 text-left">
                        <strong>Clave de Acción:</strong> ${item.clave}<br>
                        <strong>Responsable:</strong> ${item.responsable}
                    </div>`
                ).join('');

                const estadoSelect = document.getElementById('estadoRecepcion');
                // Restaurar todas las opciones
                const allOptions = Array.from(estadoSelect.options);
                allOptions.forEach(opt => opt.classList.remove('hidden'));

                // Si hay “Programado” => sólo “Recibido en el DCE (UAA – DCE)”
                if(hayProgramado) {
                    allOptions.forEach(opt => {
                        if(opt.value && opt.value !== 'Recibido en el DCE (UAA – DCE)') {
                            opt.classList.add('hidden');
                        }
                    });
                    estadoSelect.value = "Recibido en el DCE (UAA – DCE)";
                } else {
                    estadoSelect.value = ""; 
                }
            }

            document.getElementById('acuseModal').classList.remove('hidden');
            document.getElementById('acuseModal').classList.add('flex');
        }

        function closeAcuseModal() {
            document.getElementById('acuseModal').classList.add('hidden');
            document.getElementById('acuseModal').classList.remove('flex');
        }

        function confirmAcuse() {
            if(selectedExpedientes.length === 0) {
                alert('No hay expedientes seleccionados.');
                return;
            }
            const estadoSelect = document.getElementById('estadoRecepcion');
            const estadoSeleccionado = estadoSelect.value;
            if(!estadoSeleccionado) {
                alert('Debe seleccionar un estado de recepción para todos los expedientes.');
                return;
            }
            document.getElementById('expedientesSeleccionadosInput').value = JSON.stringify(selectedExpedientes);
            document.getElementById('estadoRecepcionInput').value = estadoSeleccionado;
            document.getElementById('acuseForm').submit();
        }

        // 2) Modal de "Rastreo" (Timeline)
async function openRastreoModal(expedienteId) {
    try {
        const resp = await fetch("/recepcion/rastreo/" + expedienteId, {
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });

        if (!resp.ok) {
            alert('Error al obtener el Rastreo: ' + resp.statusText);
            return;
        }

        const data = await resp.json();
        const timelineContainer = document.getElementById('timelineContainer');
        
        if (!data || !Array.isArray(data) || data.length === 0) {
            timelineContainer.innerHTML = '<p class="text-red-500">No hay entregas previas en el historial.</p>';
        } else {
            let html = '<ul class="timeline-list">';
            data.forEach(item => {
                let pdfButton = "";
                
                // Si existe pdf_path, generar botón de descarga
                if (item.pdf_path) {
                    const pdfUrl = `/storage/${item.pdf_path}`; // Ajusta si es necesario
                    pdfButton = `<a href="${pdfUrl}" target="_blank" 
                                  class="bg-blue-500 text-white px-4 py-2 rounded">
                                  Descargar Acuse
                                </a>`;
                }

                html += `<li class="mb-2">
                            <div class="font-semibold">${item.estado}</div>
                            <div class="text-xs text-gray-700">
                                <b>Fecha de recepción:</b> ${item.fecha} <br>
                            </div>
                            <div class="text-xs text-gray-600">
                                ${item.observaciones ?? ''}
                            </div>
                            <div style="margin-top:10px">
                            ${pdfButton} <!-- Botón de descarga si existe PDF -->
                            </div>
                        </li>`;
            });
            html += '</ul>';
            timelineContainer.innerHTML = html;
        }

        // Abrir modal
        document.getElementById('rastreoModal').classList.remove('hidden');
        document.getElementById('rastreoModal').classList.add('flex');

    } catch (error) {
        console.error('Error en openRastreoModal:', error);
        alert('Ocurrió un error inesperado al obtener el rastreo.');
    }
}

        function closeRastreoModal() {
            document.getElementById('rastreoModal').classList.add('hidden');
            document.getElementById('rastreoModal').classList.remove('flex');
        }

        async function toggleEntregadoViaAjax(expedienteId, isChecked) {
            try {
                const resp = await fetch("{{ route('recepcion.ajaxToggleEntregado') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        expediente_id: expedienteId,
                        entregado: isChecked   // boolean
                    })
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
        #timelineContainer{
            max-height: 50vh;
            overflow: auto;
        }
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
        #acuseModal, #rastreoModal {
            z-index: 9999;
        }

        .timeline-list {
            list-style: none;
            padding-left: 0;
        }
        .timeline-list li::before {
            content: "• ";
            color: #444;
        }
    </style>
</x-app-layout>
