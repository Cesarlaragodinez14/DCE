<script>
    "use strict";

    let selectedExpedientes = [];
    let allCheckboxes = [];
    let toastTimeout = null;
    let selectedTipoMovimiento = null; // Variable para almacenar el tipo de movimiento seleccionado

    // Inicializar la página
    document.addEventListener('DOMContentLoaded', () => {
        // Inicializar checkboxes
        allCheckboxes = document.querySelectorAll('.received-checkbox');
        
        // Inicializar expedientes que ya están marcados al cargar la página
        allCheckboxes.forEach(chk => {
            if(chk.checked) {
                const expId = chk.getAttribute('data-expediente-id');
                if(!selectedExpedientes.includes(expId)) {
                    selectedExpedientes.push(expId);
                }
                
                // Si es el primer expediente marcado, establecer el tipo de movimiento
                if(selectedExpedientes.length === 1) {
                    const row = chk.closest('tr');
                    const tipoMovimiento = row.cells[10] ? row.cells[10].textContent.trim() : '';
                    selectedTipoMovimiento = tipoMovimiento;
                    disableIncompatibleCheckboxes(tipoMovimiento);
                }
            }
        });

        // Actualizar contador de seleccionados al inicio
        updateSelectedCount();
        
        // Establecer focus en el primer campo de filtro si no hay filtros aplicados
        if (!window.location.search.includes('entrega=') && 
            !window.location.search.includes('cuenta_publica=') && 
            !window.location.search.includes('estatus=') && 
            !window.location.search.includes('responsable=')) {
            document.getElementById('entrega').focus();
        }
        
        // Configurar el botón para mostrar/ocultar filtros
        const toggleFiltersBtn = document.getElementById('toggleFilters');
        const filtersContainer = document.getElementById('filtersContainer');
        const toggleText = toggleFiltersBtn.querySelector('span');
        
        toggleFiltersBtn.addEventListener('click', function() {
            if (filtersContainer.style.display === 'none') {
                filtersContainer.style.display = 'block';
                toggleText.textContent = 'Ocultar';
                toggleFiltersBtn.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />';
            } else {
                filtersContainer.style.display = 'none';
                toggleText.textContent = 'Mostrar';
                toggleFiltersBtn.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />';
            }
        });
        
        // Cerrar el error alert con el botón de cierre
        document.getElementById('closeErrorBtn').addEventListener('click', function() {
            hideError();
        });
        
        // Cerrar el success toast con el botón de cierre
        document.querySelector('#successToast button').addEventListener('click', function() {
            hideSuccess();
        });
    });

    // Actualizar contador de seleccionados
    function updateSelectedCount() {
        const countElement = document.getElementById('selectedCount');
        if (countElement) {
            countElement.textContent = selectedExpedientes.length;
        }
    }

    // Función para deshabilitar checkboxes que no tienen el mismo tipo de movimiento
    function disableIncompatibleCheckboxes(tipoMovimientoSeleccionado) {
        allCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const tipoMovimiento = row.cells[10] ? row.cells[10].textContent.trim() : '';
            
            if(tipoMovimiento !== tipoMovimientoSeleccionado && !checkbox.checked) {
                checkbox.disabled = true;
                // Agregar estilo visual para indicar que está deshabilitado
                row.style.opacity = '0.5';
                row.style.backgroundColor = '#f3f4f6';
            }
        });
    }

    // Función para habilitar todos los checkboxes
    function enableAllCheckboxes() {
        allCheckboxes.forEach(checkbox => {
            checkbox.disabled = false;
            const row = checkbox.closest('tr');
            // Remover estilos de deshabilitado
            row.style.opacity = '';
            row.style.backgroundColor = '';
        });
    }

    // Función auxiliar para extraer el destino del flujo de estatus
    function extraerDestinoDelEstatus(estatusTexto) {
        // Buscar texto entre paréntesis
        const match = estatusTexto.match(/\(([^)]+)\)/);
        if (!match) {
            return null;
        }
        
        const flujo = match[1];
        // Dividir por guión (puede ser - o –)
        const partes = flujo.split(/\s*[-–]\s*/);
        
        if (partes.length >= 2) {
            // Retornar el destino (segunda parte)
            return partes[1].trim();
        }
        
        return null;
    }

    // Función auxiliar para extraer el origen del flujo de estatus
    function extraerOrigenDelEstatus(estatusTexto) {
        // Buscar texto entre paréntesis
        const match = estatusTexto.match(/\(([^)]+)\)/);
        if (!match) {
            return null;
        }
        
        const flujo = match[1];
        // Dividir por guión (puede ser - o –)
        const partes = flujo.split(/\s*[-–]\s*/);
        
        if (partes.length >= 1) {
            // Retornar el origen (primera parte)
            return partes[0].trim();
        }
        
        return null;
    }

    // Función para enviar el formulario de filtros
    function submitFilterForm() {
        document.getElementById('filtrosForm').submit();
    }

    // 1) Modal de "Generar Acuse"
    function openAcuseModal() {
        const acuseListDiv = document.getElementById('acuseList');
        acuseListDiv.innerHTML = '';

        if (selectedExpedientes.length === 0) {
            showError('Primero selecciona una clave de acción');
            return;
        }

        const rows = Array.from(document.querySelectorAll('#recepcion-table tbody tr'));
        const selectedData = [];
        let estatusActual = null;
        let todosIgualEstatus = true;

        rows.forEach(row => {
            const chk = row.querySelector('.received-checkbox');
            if(chk && selectedExpedientes.includes(chk.getAttribute('data-expediente-id'))) {
                // El texto del estado ahora está en la celda 11 (índice basado en cero)
                const estatusExp = row.cells[11] ? row.cells[11].textContent.trim() : '';
                
                // Extraer solo el texto del estatus sin el span o badge
                const spanElement = row.cells[11]?.querySelector('span');
                const estatusLimpio = spanElement ? spanElement.textContent.trim() : estatusExp;
                
                // Verificar si todos los expedientes tienen el mismo estatus
                if(estatusActual === null) {
                    estatusActual = estatusLimpio;
                } else if(estatusActual !== estatusLimpio) {
                    todosIgualEstatus = false;
                }

                // Obtener los datos relevantes de las columnas de la tabla
                const clave = row.cells[9] ? row.cells[9].textContent.trim() : '';
                const responsable = row.cells[13] ? row.cells[13].textContent.trim() : '';
                const uaa = row.cells[4] ? row.cells[4].textContent.trim() : '';
                
                selectedData.push({ clave, responsable, uaa, estatusExp: estatusLimpio });
            }
        });

        // Validar que todos los expedientes tengan el mismo estatus
        if(!todosIgualEstatus) {
            showError('Todos los expedientes seleccionados deben tener el mismo estatus');
            return;
        }

        // Mostrar los expedientes seleccionados
        acuseListDiv.innerHTML = selectedData.map(item =>
            `<div class="py-3">
                <div class="flex justify-between">
                    <div class="font-medium text-primary-color">${item.clave}</div>
                    <div class="text-xs text-gray-500">${item.uaa}</div>
                </div>
                <div class="text-sm text-gray-600 mt-1">${item.responsable}</div>
                <div class="mt-2">
                    <span class="badge ${item.estatusExp.includes('Programado') ? 'bg-warning-light' : 'bg-primary-light'} text-white text-xs">
                        ${item.estatusExp}
                    </span>
                </div>
            </div>`
        ).join('');

        document.getElementById('countSelectedItems').textContent = selectedData.length;

        const estadoSelect = document.getElementById('estadoRecepcion');
        
        // Definir las reglas de flujo de trabajo
        const flujoTrabajo = {
            'Programado': ['Recibido en el DCE (UAA – DCE)'],
            'Sin Programar': ['Recibido en el DCE (UAA – DCE)'],
            'Recibido en el DCE (UAA – DCE)': ['Recibido por la DGSEG para revisión (DCE - DGSEG)'],
            'Recibido en el DCE (UAA – DCE) - Firmado': ['Recibido por la DGSEG para revisión (DCE - DGSEG)'],
            'Recibido por la DGSEG para revisión (DCE - DGSEG)': [
                'Recibido en el DCE para resguardo (DGSEG – DCE)',
                'Recibido en el DCE con corrección para la UAA (DGSEG – DCE)'
            ],
            'Recibido por la DGSEG para revisión (DCE - DGSEG) - Firmado': [
                'Recibido en el DCE para resguardo (DGSEG – DCE)',
                'Recibido en el DCE con corrección para la UAA (DGSEG – DCE)'
            ],
            'Recibido en el DCE con corrección para la UAA (DGSEG – DCE)': [
                'Recibido por la DGSEG para revisión (DCE - DGSEG)',
                'Recibido por la UAA para corrección (DCE - UAA)'
            ],
            'Recibido en el DCE con corrección para la UAA (DGSEG – DCE) - Firmado': [
                'Recibido por la DGSEG para revisión (DCE - DGSEG)',
                'Recibido por la UAA para corrección (DCE - UAA)'
            ],
            'Recibido por la UAA para corrección (DCE - UAA)': ['Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)'],
            'Recibido por la UAA para corrección (DCE - UAA) - Firmado': ['Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)'],
            'Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE)': ['Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)'],
            'Recibido en el DCE con las correcciones solicitadas por la DGSEG (UAA – DCE) - Firmado': ['Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)'],
            'Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG)': [
                'Recibido en el DCE para resguardo (DGSEG – DCE)',
                'Recibido en el DCE con corrección para la UAA (DGSEG – DCE)'
            ],
            'Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG) - Firmado': [
                'Recibido en el DCE para resguardo (DGSEG – DCE)',
                'Recibido en el DCE con corrección para la UAA (DGSEG – DCE)'
            ],
            // Estados RIASF - Devolución
            'Recibido por DCE para devolución a la UAA por cambio RIASF (DGSEG – DCE)': ['Recibido por la UAA por cambio RIASF (DCE - UAA)'],
            'Recibido por DCE para devolución a la UAA por cambio RIASF (DGSEG – DCE) - Firmado': ['Recibido por la UAA por cambio RIASF (DCE - UAA)'],
            // Estado final RIASF (no tiene movimientos siguientes)
            'Recibido por la UAA por cambio RIASF (DCE - UAA)': ['Recibido en el DCE (UAA – DCE)'],
            'Recibido por la UAA por cambio RIASF (DCE - UAA) - Firmado': ['Recibido en el DCE (UAA – DCE)']
        };

        // Obtener movimientos permitidos para el estatus actual
        const movimientosPermitidos = flujoTrabajo[estatusActual] || [];

        // Si no hay movimientos permitidos, mostrar error
        if(movimientosPermitidos.length === 0) {
            showError(`No hay movimientos permitidos para el estatus: ${estatusActual}`);
            closeAcuseModal();
            return;
        }

        // Ocultar todas las opciones primero
        Array.from(estadoSelect.options).forEach(opt => {
            opt.style.display = 'none';
        });

        // Mostrar solo la opción vacía y los movimientos permitidos
        estadoSelect.options[0].style.display = ''; // Opción "-- Seleccionar --"
        
        // Mostrar solo las opciones permitidas
        Array.from(estadoSelect.options).forEach(opt => {
            if(movimientosPermitidos.includes(opt.value)) {
                opt.style.display = '';
            }
        });

        // Si solo hay un movimiento permitido, seleccionarlo automáticamente
        if(movimientosPermitidos.length === 1) {
            estadoSelect.value = movimientosPermitidos[0];
        } else {
            estadoSelect.value = "";
        }

        // Mostrar el modal
        const modal = document.getElementById('acuseModal');
        modal.classList.remove('hidden');
        modal.classList.add('visible');
        
        // Enfocar el select de estado
        estadoSelect.focus();
    }

    function closeAcuseModal() {
        const modal = document.getElementById('acuseModal');
        modal.classList.remove('visible');
        modal.classList.add('hidden');
    }

    function confirmAcuse() {
        if(selectedExpedientes.length === 0) {
            showError('Primero selecciona una clave de acción');
            return;
        }
        
        const estadoSelect = document.getElementById('estadoRecepcion');
        const estadoSeleccionado = estadoSelect.value;
        
        if(!estadoSeleccionado) {
            showError('Debe seleccionar un estado de recepción para todos los expedientes.');
            estadoSelect.focus();
            return;
        }
        
        // Mostrar overlay de carga
        showLoading('Generando acuse de recepción...');
        
        document.getElementById('expedientesSeleccionadosInput').value = JSON.stringify(selectedExpedientes);
        document.getElementById('estadoRecepcionInput').value = estadoSeleccionado;
        document.getElementById('acuseForm').submit();
    }

    // 2) Modal de "Rastreo" (Timeline)
    async function openRastreoModal(expedienteId) {
        // Mostrar modal y su indicador de carga
        const rastreoModal = document.getElementById('rastreoModal');
        const timelineLoading = document.getElementById('timelineLoading');
        const timelineContainer = document.getElementById('timelineContainer');
        
        rastreoModal.classList.remove('hidden');
        rastreoModal.classList.add('visible');
        timelineLoading.classList.remove('hidden');
        timelineContainer.classList.add('hidden');
        
        try {
            const resp = await fetch("/recepcion/rastreo/" + expedienteId, {
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            if (!resp.ok) {
                showError('Error al obtener el Rastreo: ' + resp.statusText);
                closeRastreoModal();
                return;
            }

            const data = await resp.json();
            
            // Ocultar el loader y mostrar el contenedor
            timelineLoading.classList.add('hidden');
            timelineContainer.classList.remove('hidden');
            
            if (!data || !Array.isArray(data) || data.length === 0) {
                timelineContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-center font-medium text-base">No hay entregas previas en el historial</p>
                        <p class="text-center text-gray-400 text-sm mt-1">El expediente no tiene movimientos registrados</p>
                    </div>
                `;
            } else {
                // Construir el timeline con diseño moderno
                let html = '<div class="relative py-2">';
                
                // Línea de tiempo vertical
                html += '<div class="timeline-line"></div>';
                
                data.forEach((item, index) => {
                    const fecha = new Date(item.fecha);
                    const fechaFormateada = fecha.toLocaleDateString('es-MX', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    
                    let pdfButton = "";
                    
                    // Botón de descarga más moderno
                    if (item.pdf_path) {
                        const pdfUrl = `/storage/${item.pdf_path}`;
                        pdfButton = `
                            <a href="${pdfUrl}" target="_blank" 
                            class="btn btn-primary btn-sm mt-2 inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Descargar acuse
                            </a>`;
                    }

                    html += `
                        <div class="timeline-item animate-fade-in" style="animation-delay: ${index * 0.1}s">
                            <!-- Marcador del punto -->
                            <div class="timeline-marker"></div>
                            
                            <!-- Fecha -->
                            <div class="absolute left-[-7rem] top-0 w-24 text-right text-sm text-gray-500">${fechaFormateada}</div>
                            
                            <!-- Contenido del evento -->
                            <div class="timeline-content">
                                <div class="font-medium text-primary-color">${item.estado}</div>
                                
                                <div class="mt-3">
                                    ${pdfButton}
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                timelineContainer.innerHTML = html;
            }

        } catch (error) {
            console.error('Error en openRastreoModal:', error);
            showError('Ocurrió un error inesperado al obtener el rastreo.');
            closeRastreoModal();
        }
    }

    function closeRastreoModal() {
        const rastreoModal = document.getElementById('rastreoModal');
        rastreoModal.classList.remove('visible');
        rastreoModal.classList.add('hidden');
    }

    async function toggleEntregadoViaAjax(expedienteId, isChecked, checkbox) {
        // Obtener información del tipo de movimiento de la fila
        const row = checkbox.closest('tr');
        const tipoMovimiento = row.cells[10] ? row.cells[10].textContent.trim() : '';
        
        if(isChecked) {
            // Si es la primera selección, establecer el tipo de movimiento
            if(selectedExpedientes.length === 0) {
                selectedTipoMovimiento = tipoMovimiento;
                disableIncompatibleCheckboxes(tipoMovimiento);
                showSuccess(`Tipo de movimiento seleccionado: ${tipoMovimiento}`);
            }
            // Si ya hay selecciones, verificar que el tipo sea compatible
            else if(selectedTipoMovimiento && tipoMovimiento !== selectedTipoMovimiento) {
                checkbox.checked = false;
                showError(`Solo puedes seleccionar expedientes del tipo: ${selectedTipoMovimiento}`);
                return;
            }
            
            // Agregar a la lista de seleccionados
            if(!selectedExpedientes.includes(expedienteId.toString())) {
                selectedExpedientes.push(expedienteId.toString());
            }
        } else {
            // Remover de la lista de seleccionados
            selectedExpedientes = selectedExpedientes.filter(id => id !== expedienteId.toString());
            
            // Si no hay más expedientes seleccionados, limpiar restricciones
            if(selectedExpedientes.length === 0) {
                selectedTipoMovimiento = null;
                enableAllCheckboxes();
                showSuccess('Todas las claves de acción están disponibles nuevamente');
            }
        }
        
        // Actualizar contador de seleccionados
        updateSelectedCount();
        
        // Aplicar efecto visual al checkbox
        if (checkbox) {
            checkbox.disabled = true;
            checkbox.parentElement.classList.add('pulse');
        }
        
        // Actualizar la fila en la tabla
        if (checkbox) {
            if (row) {
                // Cambiar la clase de la fila
                if (isChecked) {
                    row.classList.add('row-success');
                } else {
                    row.classList.remove('row-success');
                }
                
                // Actualizar la celda de estatus (celda 11)
                const estatusCell = row.cells[11];
                if (estatusCell) {
                    if (isChecked) {
                        //estatusCell.innerHTML = `<span class="badge" style="background-color: var(--success-color)">Recibido</span>`;
                    } else {
                        //estatusCell.innerHTML = `<span class="badge" style="background-color: var(--warning-color)">Pendiente</span>`;
                    } 
                }
            }
        }
    
        // Remover efecto visual
        if (checkbox) {
            checkbox.disabled = false;
            checkbox.parentElement.classList.remove('pulse');
        }
    }
    
    // Funciones de utilidad para mostrar mensajes
    function showError(message) {
        // Limpiar cualquier toast anterior
        if (toastTimeout) {
            clearTimeout(toastTimeout);
            toastTimeout = null;
        }
        
        // Ocultar toast de éxito si está visible
        hideSuccess();
        
        // Mostrar mensaje de error
        const errorList = document.getElementById('errorList');
        errorList.innerHTML = `<li>${message}</li>`;
        
        const errorAlert = document.getElementById('errorAlert');
        errorAlert.classList.remove('hidden');
        errorAlert.classList.add('visible');
        errorAlert.classList.add('animate-fade-in');
        
        // Auto-ocultar después de 5 segundos
        toastTimeout = setTimeout(() => {
            hideError();
        }, 5000);
    }
    
    function hideError() {
        const errorAlert = document.getElementById('errorAlert');
        errorAlert.classList.remove('animate-fade-in');
        errorAlert.classList.add('animate-fade-out');
        
        // Después de la animación, ocultarlo completamente
        setTimeout(() => {
            errorAlert.classList.add('hidden');
            errorAlert.classList.remove('visible');
            errorAlert.classList.remove('animate-fade-out');
        }, 300);
    }
    
    function showSuccess(message) {
        // Limpiar cualquier toast anterior
        if (toastTimeout) {
            clearTimeout(toastTimeout);
            toastTimeout = null;
        }
        
        // Ocultar toast de error si está visible
        hideError();
        
        // Mostrar mensaje de éxito
        const successMessage = document.getElementById('successMessage');
        successMessage.textContent = message;
        
        const successToast = document.getElementById('successToast');
        successToast.classList.remove('hidden');
        successToast.classList.add('visible');
        successToast.classList.add('animate-fade-in');
        
        // Auto-ocultar después de 3 segundos
        toastTimeout = setTimeout(() => {
            hideSuccess();
        }, 3000);
    }
    
    function hideSuccess() {
        const successToast = document.getElementById('successToast');
        successToast.classList.remove('animate-fade-in');
        successToast.classList.add('animate-fade-out');
        
        // Después de la animación, ocultarlo completamente
        setTimeout(() => {
            successToast.classList.add('hidden');
            successToast.classList.remove('visible');
            successToast.classList.remove('animate-fade-out');
        }, 300);
    }
    
    function showLoading(message = 'Procesando...') {
        document.getElementById('loadingMessage').textContent = message;
        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.classList.remove('hidden');
        loadingOverlay.classList.add('visible');
    }
    
    function hideLoading() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.classList.remove('visible');
        loadingOverlay.classList.add('hidden');
    }
    
    // Función para exportar tabla a Excel
    function exportTableToExcel() {
        // Mostrar el indicador de carga
        showLoading('Generando archivo Excel...');
        
        try {
            // Obtener la tabla
            const table = document.getElementById('recepcion-table');
            
            // Crear un array para almacenar los datos de la tabla
            let data = [];
            
            // Obtener encabezados (excluyendo las últimas dos columnas)
            const headers = [];
            const headerCells = table.querySelectorAll('thead th');
            const columnCount = headerCells.length - 2; // Excluir las últimas dos columnas
            
            for (let i = 0; i < columnCount; i++) {
                headers.push(headerCells[i].textContent.trim());
            }
            
            data.push(headers);
            
            // Obtener filas de datos
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 0) {
                    const rowData = [];
                    
                    // Solo incluir las celdas hasta la antepenúltima columna
                    for (let i = 0; i < columnCount; i++) {
                        // Obtener el texto plano de la celda, sin elementos HTML
                        const cellText = cells[i].textContent.trim();
                        rowData.push(cellText);
                    }
                    
                    data.push(rowData);
                }
            });
            
            // Crear una hoja de cálculo
            const worksheet = XLSX.utils.aoa_to_sheet(data);
            
            // Crear un libro de trabajo
            const workbook = XLSX.utils.book_new();
            
            // Configurar las propiedades del libro para UTF-8
            workbook.Props = {
                Title: "Expedientes",
                Author: "SAES",
                CreatedDate: new Date()
            };
            
            // Agregar la hoja al libro
            XLSX.utils.book_append_sheet(workbook, worksheet, "Expedientes");
            
            // Generar el archivo Excel con codificación UTF-8
            const excelBuffer = XLSX.write(workbook, { 
                bookType: 'xlsx', 
                type: 'array',
                bookSST: false
            });
            
            // Convertir el buffer a Blob con tipo MIME adecuado
            const blob = new Blob([excelBuffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8'
            });
            
            // Crear URL para el blob
            const url = window.URL.createObjectURL(blob);
            
            // Obtener fecha actual para el nombre del archivo
            const date = new Date();
            const dateStr = date.toISOString().slice(0, 10);
            
            // Crear elemento de enlace y hacer clic en él
            const a = document.createElement('a');
            a.href = url;
            a.download = `Expedientes_${dateStr}.xlsx`;
            document.body.appendChild(a);
            a.click();
            
            // Limpiar
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            // Mostrar mensaje de éxito
            showSuccess('Archivo Excel generado correctamente');
        } catch (error) {
            console.error('Error al exportar a Excel:', error);
            showError('Ocurrió un error al generar el archivo Excel');
        } finally {
            // Ocultar el indicador de carga
            hideLoading();
        }
    }
</script>