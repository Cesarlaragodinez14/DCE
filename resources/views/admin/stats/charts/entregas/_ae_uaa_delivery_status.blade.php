{{-- resources/views/admin/stats/charts/_ae_delivery_status_multiple.blade.php --}}
<section id="ae-delivery-status-multiple" class="mb-8">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Estatus de la entrega de Expedientes de Acción por Auditoria Especial y DG.</h3>
        
        <!-- Controles de filtrado y visualización -->
        <div class="flex space-x-2">
            <select id="view-mode-selector" style="padding-right: 30px;" class="px-2 py-1 border rounded text-sm">
                <option value="chart">Vista de Gráfico</option>
                <option value="table">Vista de Tabla</option>
                <option value="both">Gráfico y Tabla</option>
            </select>
            
            <select id="chart-type-selector" style="padding-right: 30px;" class="px-2 py-1 border rounded text-sm">
                <option value="stacked">Barras Apiladas</option>
                <option value="grouped">Barras Agrupadas</option>
                <option value="percentage">Porcentajes</option>
            </select>
            
            <button id="export-data-btn" class="px-2 py-1 bg-blue-500 text-white rounded text-sm flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Exportar
            </button>
        </div>
    </div>
    
    <!-- Se eliminó el resumen general ya que existe en otra gráfica -->
    
    <!-- Contenedor principal donde se generarán múltiples gráficos (uno por AE) -->
    <div id="ae-delivery-charts-container" class="space-y-8"></div>
    
    <!-- Modal para visualizar detalles al hacer clic en una barra -->
    <div id="detail-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[80vh] overflow-auto p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 id="modal-title" class="text-lg font-semibold"></h3>
                <button id="close-modal" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="modal-content"></div>
            <div class="mt-4 flex justify-end">
                <button id="export-detail-btn" class="px-3 py-1 bg-blue-500 text-white rounded mr-2">Exportar Detalle</button>
                <button id="close-modal-btn" class="px-3 py-1 bg-gray-200 rounded">Cerrar</button>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
"use strict";
document.addEventListener('DOMContentLoaded', function() {
    // Configuración de colores accesibles (siguiendo recomendaciones para daltonismo)
    const COLORS = {
        delivered: '#38A169', // Verde más oscuro
        in_process: '#F6AD55', // Naranja más suave
        unscheduled: '#E53E3E', // Rojo más visible
        total: '#2B6CB0'       // Azul para totales
    };
    
    // Referencias a elementos del DOM
    const container = document.getElementById('ae-delivery-charts-container');
    const viewModeSelector = document.getElementById('view-mode-selector');
    const chartTypeSelector = document.getElementById('chart-type-selector');
    const exportDataBtn = document.getElementById('export-data-btn');
    const detailModal = document.getElementById('detail-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const closeModalX = document.getElementById('close-modal');
    
    // Datos y variables globales
    let chartInstances = [];
    let currentViewMode = 'both';
    let currentChartType = 'stacked';
    const rawData = window.dashboardData?.deliveryStatusByAeUaa;
    
    if (!rawData || !container) return;
    
    // Inicializar los eventos
    initEvents();
    
    // Procesar y mostrar los datos
    processAndDisplayData(rawData);
    
    /**
     * Inicializar eventos
     */
    function initEvents() {
        // Cambio de modo de visualización
        viewModeSelector.addEventListener('change', function() {
            currentViewMode = this.value;
            updateAllCharts();
        });
        
        // Cambio de tipo de gráfico
        chartTypeSelector.addEventListener('change', function() {
            currentChartType = this.value;
            updateAllCharts();
        });
        
        // Botones de exportación
        exportDataBtn.addEventListener('click', exportAllData);
        
        // Cerrar modal
        closeModalBtn.addEventListener('click', () => detailModal.classList.add('hidden'));
        closeModalX.addEventListener('click', () => detailModal.classList.add('hidden'));
        
        // Cerrar modal al hacer clic fuera
        detailModal.addEventListener('click', function(e) {
            if (e.target === this) {
                detailModal.classList.add('hidden');
            }
        });
    }
    
    /**
     * Procesa los datos y muestra los gráficos
     */
    function processAndDisplayData(data) {
        // Agrupar datos por Auditoría Especial (AE)
        const aeGroups = {};
        let grandTotal = {
            delivered: 0,
            in_process: 0,
            unscheduled: 0,
            total: 0
        };
        
        data.forEach(item => {
            const ae = item.ae_valor || 'Sin Datos';
            if (!aeGroups[ae]) {
                aeGroups[ae] = [];
            }
            aeGroups[ae].push(item);
            
            // Acumular para el total general
            grandTotal.delivered += parseInt(item.delivered) || 0;
            grandTotal.in_process += parseInt(item.in_process) || 0;
            grandTotal.unscheduled += parseInt(item.unscheduled) || 0;
        });
        
        grandTotal.total = grandTotal.delivered + grandTotal.in_process + grandTotal.unscheduled;
        
        // Se ha eliminado la llamada al resumen general
        
        // Para cada AE, generar un gráfico y tabla
        let aeIndex = 0;
        Object.keys(aeGroups).sort().forEach(aeSigla => {
            createAeSection(aeSigla, aeGroups[aeSigla], aeIndex);
            aeIndex++;
        });
    }
    
    /**
     * Función de resumen eliminada ya que esa información se muestra en otra gráfica
     */
    
    /**
     * Crea una sección para una Auditoría Especial
     */
    function createAeSection(aeSigla, groupItems, aeIndex) {
        // Crear un objeto de agrupación por UAA para la AE actual
        const grouping = {};
        groupItems.forEach(item => {
            const uaa = item.uaa_valor || 'Sin Datos';
            if (!grouping[uaa]) {
                grouping[uaa] = {
                    delivered: 0,
                    in_process: 0,
                    unscheduled: 0,
                    total: 0
                };
            }
            grouping[uaa].delivered += parseInt(item.delivered) || 0;
            grouping[uaa].in_process += parseInt(item.in_process) || 0;
            grouping[uaa].unscheduled += parseInt(item.unscheduled) || 0;
            grouping[uaa].total = grouping[uaa].delivered + grouping[uaa].in_process + grouping[uaa].unscheduled;
        });

        // Ordenar las UAA por total descendente
        const sortedUaa = Object.keys(grouping).sort((a, b) => grouping[b].total - grouping[a].total);
        
        // Calcular el total general para esta AE
        const totalAE = sortedUaa.reduce((sum, uaa) => sum + grouping[uaa].total, 0);
        const deliveredAE = sortedUaa.reduce((sum, uaa) => sum + grouping[uaa].delivered, 0);
        const inProcessAE = sortedUaa.reduce((sum, uaa) => sum + grouping[uaa].in_process, 0);
        const unscheduledAE = sortedUaa.reduce((sum, uaa) => sum + grouping[uaa].unscheduled, 0);

        // Crear un contenedor para este AE
        const aeDiv = document.createElement('div');
        aeDiv.className = "border p-4 rounded-lg bg-white shadow";
        aeDiv.dataset.ae = aeSigla;
        
        // Crear encabezado con acciones
        const headerDiv = document.createElement('div');
        headerDiv.className = "flex justify-between items-center mb-4";
        
        // Título de la sección para la AE con progreso visual
        const titleDiv = document.createElement('div');
        titleDiv.className = "flex-grow";
        
        const h4 = document.createElement('h4');
        h4.className = "text-md font-semibold";
        h4.textContent = `Auditoría Especial: ${aeSigla} (Total: ${totalAE})`;
        titleDiv.appendChild(h4);
        
        // Añadir barra de progreso
        const progressBar = document.createElement('div');
        progressBar.className = "w-full bg-gray-200 rounded-full h-2.5 mt-2";
        progressBar.innerHTML = `
            <div class="bg-green-500 h-2.5 rounded-full" style="width: ${(deliveredAE/totalAE*100).toFixed(1)}%"></div>
        `;
        const progressText = document.createElement('div');
        progressText.className = "flex justify-between text-xs text-gray-500 mt-1";
        progressText.innerHTML = `
            <span>Aceptados: ${deliveredAE} (${(deliveredAE/totalAE*100).toFixed(1)}%)</span>
            <span>En proceso de aceptación : ${inProcessAE} (${(inProcessAE/totalAE*100).toFixed(1)}%)</span>
            <span>No Entregados: ${unscheduledAE} (${(unscheduledAE/totalAE*100).toFixed(1)}%)</span>
        `;
        titleDiv.appendChild(progressBar);
        titleDiv.appendChild(progressText);
        
        // Botones de acción
        const actionsDiv = document.createElement('div');
        actionsDiv.className = "flex space-x-2";
        actionsDiv.innerHTML = `
            <button class="toggle-view-btn px-2 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
            <button class="export-ae-btn px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600" data-ae="${aeSigla}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            </button>
        `;
        
        headerDiv.appendChild(titleDiv);
        headerDiv.appendChild(actionsDiv);
        aeDiv.appendChild(headerDiv);
        
        // Contenedor para gráfico y tabla
        const contentDiv = document.createElement('div');
        contentDiv.className = "chart-table-container";
        
        // Crear contenedor para el gráfico
        const chartDiv = document.createElement('div');
        chartDiv.className = "chart-container mb-4";
        const canvas = document.createElement('canvas');
        canvas.id = `ae-delivery-chart-${aeIndex}`;
        // Modificación para la creación del canvas en la función createAeSection
// Reemplazar la línea:
        canvas.height = Math.max(100, 100 + sortedUaa.length * 10);

        // Con esta nueva implementación que asigna más espacio por UAA:
        canvas.height = Math.max(250, 100 + sortedUaa.length * 30);

        // También es importante asegurar que el contenedor del canvas tenga una altura adecuada
        // Añadir estas propiedades de estilo al div chartDiv:
        chartDiv.style.height = `${Math.max(300, 150 + sortedUaa.length * 30)}px`;
        chartDiv.style.minHeight = '400px';

        chartDiv.appendChild(canvas);
        
        // Crear tabla de datos
        const tableDiv = document.createElement('div');
        tableDiv.className = "table-container overflow-auto";
        const table = document.createElement('table');
        table.className = "min-w-full divide-y divide-gray-200";
        
        // Encabezados de tabla
        const thead = document.createElement('thead');
        thead.className = "bg-gray-50";
        thead.innerHTML = `
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UAA</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aceptados</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">En proceso de aceptación</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Entregados</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
            </tr>
        `;
        
        // Cuerpo de la tabla
        const tbody = document.createElement('tbody');
        tbody.className = "bg-white divide-y divide-gray-200";
        
        // Agregar filas a la tabla
        sortedUaa.forEach((uaa, idx) => {
            const row = document.createElement('tr');
            row.className = idx % 2 === 0 ? "bg-white" : "bg-gray-50";
            
            const item = grouping[uaa];
            const deliveredPerc = (item.delivered / item.total * 100).toFixed(1);
            const inProcessPerc = (item.in_process / item.total * 100).toFixed(1);
            const unscheduledPerc = (item.unscheduled / item.total * 100).toFixed(1);
            
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${uaa}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.delivered} (${deliveredPerc}%)</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.in_process} (${inProcessPerc}%)</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.unscheduled} (${unscheduledPerc}%)</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.total}</td>
            `;
            
            tbody.appendChild(row);
        });
        
        table.appendChild(thead);
        table.appendChild(tbody);
        tableDiv.appendChild(table);
        
        // Agregar gráfico y tabla al contenedor
        contentDiv.appendChild(chartDiv);
        contentDiv.appendChild(tableDiv);
        aeDiv.appendChild(contentDiv);
        
        // Agregar esta sección al contenedor principal
        container.appendChild(aeDiv);
        
        // Crear el gráfico
        createChart(canvas.id, aeSigla, sortedUaa, grouping);
        
        // Configurar eventos para esta sección
        setupAESectionEvents(aeDiv, aeSigla, grouping, sortedUaa);
    }
    
    /**
     * Crea un gráfico para una AE
     */
    function createChart(canvasId, aeSigla, labels, data) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        
        // Preparar datos para el gráfico
        const chartData = prepareChartData(labels, data, currentChartType);
        
        // Configuración del gráfico
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y', // Barras horizontales para mejor visualización con muchas UAAs
            scales: {
                x: { 
                    stacked: currentChartType !== 'grouped',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (currentChartType === 'percentage') {
                                return value + '%';
                            }
                            return value;
                        }
                    }
                },
                y: { 
                    stacked: true 
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const label = context.dataset.label;
                            const uaa = labels[context.dataIndex];
                            const total = data[uaa].total;
                            
                            if (currentChartType === 'percentage') {
                                return `${label}: ${value.toFixed(1)}%`;
                            }
                            
                            const perc = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                            return `${label}: ${value} (${perc})`;
                        }
                    }
                },
                legend: {
                    position: 'top',
                },
                title: {
                    display: false,
                }
            },
            onClick: function(e, elements) {
                if (elements && elements.length > 0) {
                    const index = elements[0].index;
                    const uaa = labels[index];
                    showDetailModal(aeSigla, uaa, data[uaa]);
                }
            }
        };
        
        // Crear el gráfico
        const chart = new Chart(ctx, {
            type: currentChartType === 'grouped' ? 'bar' : 'bar',
            data: chartData,
            options: options
        });
        
        // Guardar la instancia para actualizaciones posteriores
        chartInstances.push({
            id: canvasId,
            chart: chart,
            aeSigla: aeSigla,
            labels: labels,
            data: data
        });
        
        return chart;
    }
    
    /**
     * Prepara datos para los diferentes tipos de gráficos
     */
    function prepareChartData(labels, data, chartType) {
        const datasetDelivered = labels.map(uaa => data[uaa].delivered);
        const datasetInProcess = labels.map(uaa => data[uaa].in_process);
        const datasetUnscheduled = labels.map(uaa => data[uaa].unscheduled);
        
        // Si es gráfico de porcentaje, convertir valores a porcentajes
        if (chartType === 'percentage') {
            const newDatasetDelivered = [];
            const newDatasetInProcess = [];
            const newDatasetUnscheduled = [];
            
            for (let i = 0; i < labels.length; i++) {
                const uaa = labels[i];
                const total = data[uaa].total;
                
                if (total > 0) {
                    newDatasetDelivered.push((data[uaa].delivered / total) * 100);
                    newDatasetInProcess.push((data[uaa].in_process / total) * 100);
                    newDatasetUnscheduled.push((data[uaa].unscheduled / total) * 100);
                } else {
                    newDatasetDelivered.push(0);
                    newDatasetInProcess.push(0);
                    newDatasetUnscheduled.push(0);
                }
            }
            
            return {
                labels: labels,
                datasets: [
                    {
                        label: 'Aceptados',
                        data: newDatasetDelivered,
                        backgroundColor: COLORS.delivered,
                        borderColor: COLORS.delivered,
                        borderWidth: 1
                    },
                    {
                        label: 'En proceso de aceptación',
                        data: newDatasetInProcess,
                        backgroundColor: COLORS.in_process,
                        borderColor: COLORS.in_process,
                        borderWidth: 1
                    },
                    {
                        label: 'No Entregados',
                        data: newDatasetUnscheduled,
                        backgroundColor: COLORS.unscheduled,
                        borderColor: COLORS.unscheduled,
                        borderWidth: 1
                    }
                ]
            };
        }
        
        // Datos para gráficos normales (apilados o agrupados)
        return {
            labels: labels,
            datasets: [
                {
                    label: 'Aceptados',
                    data: datasetDelivered,
                    backgroundColor: COLORS.delivered,
                    borderColor: COLORS.delivered,
                    borderWidth: 1
                },
                {
                    label: 'En proceso de aceptación',
                    data: datasetInProcess,
                    backgroundColor: COLORS.in_process,
                    borderColor: COLORS.in_process,
                    borderWidth: 1
                },
                {
                    label: 'No Entregados',
                    data: datasetUnscheduled,
                    backgroundColor: COLORS.unscheduled,
                    borderColor: COLORS.unscheduled,
                    borderWidth: 1
                }
            ]
        };
    }
    
    /**
     * Configura eventos para una sección de AE
     */
    function setupAESectionEvents(aeDiv, aeSigla, data, labels) {
        // Botón para alternar vista
        const toggleBtn = aeDiv.querySelector('.toggle-view-btn');
        toggleBtn.addEventListener('click', () => {
            const chartContainer = aeDiv.querySelector('.chart-container');
            const tableContainer = aeDiv.querySelector('.table-container');
            
            if (chartContainer.style.display === 'none') {
                // Mostrar ambos
                chartContainer.style.display = 'block';
                tableContainer.style.display = 'block';
            } else if (tableContainer.style.display === 'none') {
                // Mostrar sólo tabla
                chartContainer.style.display = 'none';
                tableContainer.style.display = 'block';
            } else {
                // Mostrar sólo gráfico
                chartContainer.style.display = 'block';
                tableContainer.style.display = 'none';
            }
        });
        
        // Botón para exportar datos de esta AE
        const exportBtn = aeDiv.querySelector('.export-ae-btn');
        exportBtn.addEventListener('click', () => {
            exportAEData(aeSigla, data, labels);
        });
    }
    
    /**
     * Actualiza todos los gráficos según las configuraciones
     */
    function updateAllCharts() {
        chartInstances.forEach(instance => {
            // Actualizar visibilidad según el modo de vista
            const aeDiv = document.querySelector(`[data-ae="${instance.aeSigla}"]`);
            const chartContainer = aeDiv.querySelector('.chart-container');
            const tableContainer = aeDiv.querySelector('.table-container');
            
            if (currentViewMode === 'chart') {
                chartContainer.style.display = 'block';
                tableContainer.style.display = 'none';
            } else if (currentViewMode === 'table') {
                chartContainer.style.display = 'none';
                tableContainer.style.display = 'block';
            } else {
                chartContainer.style.display = 'block';
                tableContainer.style.display = 'block';
            }
            
            // Actualizar datos del gráfico según el tipo
            const newData = prepareChartData(instance.labels, instance.data, currentChartType);
            instance.chart.data = newData;
            
            // Actualizar opciones del gráfico
            instance.chart.options.scales.x.stacked = currentChartType !== 'grouped';
            
            if (currentChartType === 'percentage') {
                instance.chart.options.scales.x.ticks.callback = function(value) {
                    return value + '%';
                };
            } else {
                instance.chart.options.scales.x.ticks.callback = function(value) {
                    return value;
                };
            }
            
            // Actualizar el gráfico
            instance.chart.update();
        });
    }
    
    /**
     * Muestra un modal con detalles al hacer clic en una barra
     */
    function showDetailModal(ae, uaa, data) {
        const modalTitle = document.getElementById('modal-title');
        const modalContent = document.getElementById('modal-content');
        
        modalTitle.textContent = `Detalle de Expedientes: ${ae} - ${uaa}`;
        
        modalContent.innerHTML = `
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="border rounded p-3 text-center">
                    <h5 class="text-sm text-gray-500">Aceptados</h5>
                    <p class="text-xl font-semibold" style="color: ${COLORS.delivered}">${data.delivered}</p>
                    <p class="text-sm">${(data.delivered/data.total*100).toFixed(1)}%</p>
                </div>
                <div class="border rounded p-3 text-center">
                    <h5 class="text-sm text-gray-500">En proceso de aceptación</h5>
                    <p class="text-xl font-semibold" style="color: ${COLORS.in_process}">${data.in_process}</p>
                    <p class="text-sm">${(data.in_process/data.total*100).toFixed(1)}%</p>
                </div>
                <div class="border rounded p-3 text-center">
                    <h5 class="text-sm text-gray-500">No Entregados</h5>
                    <p class="text-xl font-semibold" style="color: ${COLORS.unscheduled}">${data.unscheduled}</p>
                    <p class="text-sm">${(data.unscheduled/data.total*100).toFixed(1)}%</p>
                </div>
            </div>
            
            <div class="mt-4">
                <h5 class="text-sm font-medium mb-2">Acciones</h5>
                <button class="view-detail-btn px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm mr-2">
                    Ver Lista de Expedientes
                </button>
                <button class="download-report-btn px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm">
                    Descargar Reporte
                </button>
            </div>
        `;
        
        // Configurar eventos del modal
        const viewDetailBtn = modalContent.querySelector('.view-detail-btn');
        viewDetailBtn.addEventListener('click', () => {
            // Aquí se podría implementar la acción para ver la lista de expedientes
            alert(`Se mostraría la lista de expedientes para ${ae} - ${uaa}`);
        });
        
        const downloadReportBtn = modalContent.querySelector('.download-report-btn');
        downloadReportBtn.addEventListener('click', () => {
            // Aquí se podría implementar la descarga del reporte
            alert(`Se descargaría el reporte para ${ae} - ${uaa}`);
        });
        
        // Mostrar el modal
        detailModal.classList.remove('hidden');
    }
    
    /**
     * Exporta los datos de todas las AEs
     */
    function exportAllData() {
        let csv = 'Auditoría Especial,UAA,Aceptados,En proceso de aceptación,No Entregados,Total\n';
        
        chartInstances.forEach(instance => {
            instance.labels.forEach(uaa => {
                const item = instance.data[uaa];
                csv += `${instance.aeSigla},${uaa},${item.delivered},${item.in_process},${item.unscheduled},${item.total}\n`;
            });
        });
        
        downloadCSV(csv, 'expedientes_por_ae_uaa.csv');
    }
    
    /**
     * Exporta los datos de una AE específica
     */
    function exportAEData(aeSigla, data, labels) {
        let csv = 'UAA,Aceptados,En proceso de aceptación,No Entregados,Total\n';
        
        labels.forEach(uaa => {
            const item = data[uaa];
            csv += `${uaa},${item.delivered},${item.in_process},${item.unscheduled},${item.total}\n`;
        });
        
        downloadCSV(csv, `expedientes_${aeSigla}.csv`);
    }
    
    /**
     * Descarga un archivo CSV
     */
    function downloadCSV(csv, filename) {
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    /**
     * Función auxiliar para obtener colores
     */
    function getColor(index) {
        const colors = [COLORS.delivered, COLORS.in_process, COLORS.unscheduled];
        return colors[index] || '#777777';
    }
});
</script>
@endpush