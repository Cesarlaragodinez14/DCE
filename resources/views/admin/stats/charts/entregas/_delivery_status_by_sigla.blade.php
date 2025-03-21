{{-- resources/views/admin/stats/charts/entregas/_delivery_status_by_sigla.blade.php --}}
<section id="delivery-status-by-sigla" class="mb-8 bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Entregas de Expedientes por Siglas de Auditoría Especial</h3>
        <div class="flex space-x-2">
            <button id="view-chart-btn" class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm font-medium active">Gráfica</button>
            <button id="view-table-btn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md text-sm font-medium">Tabla</button>
            <button id="download-csv-btn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md text-sm font-medium flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                CSV
            </button>
        </div>
    </div>
    
    <div class="bg-gray-50 p-4 rounded-md mb-4 flex flex-wrap items-center text-sm">
        <div id="legend-delivered" class="flex items-center mr-6 mb-2 cursor-pointer hover:bg-gray-100 p-1 rounded transition-colors" data-index="0" data-active="true">
            <div class="w-4 h-4 rounded mr-2" style="background-color: #4ade80;"></div>
            <span>Aceptados</span>
        </div>
        <div id="legend-in-process" class="flex items-center mr-6 mb-2 cursor-pointer hover:bg-gray-100 p-1 rounded transition-colors" data-index="1" data-active="true">
            <div class="w-4 h-4 rounded mr-2" style="background-color: #facc15;"></div>
            <span>En Proceso de Aceptación</span>
        </div>
        <div id="legend-unscheduled" class="flex items-center mr-6 mb-2 cursor-pointer hover:bg-gray-100 p-1 rounded transition-colors" data-index="2" data-active="true">
            <div class="w-4 h-4 rounded mr-2" style="background-color: #f87171;"></div>
            <span>No Aceptados</span>
        </div>
        <div class="ml-auto text-xs text-gray-500 italic">* Haga clic en las leyendas para filtrar</div>
    </div>
    
    <div id="chart-container" class="relative" style="height: 400px;">
        <canvas id="deliveryStatusBySiglaChart"></canvas>
        <div id="chart-loader" class="hidden absolute inset-0 flex items-center justify-center bg-white bg-opacity-70">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
        </div>
    </div>
    
    <div id="table-container" class="hidden mt-6">
        <div id="table-delivery-status-by-sigla" class="overflow-x-auto"></div>
    </div>
    
    <div class="mt-4 text-sm text-gray-600 flex justify-between items-center">
        <span>Última actualización: {{ date('d/m/Y H:i') }}</span>
        <div id="chart-summary" class="flex space-x-4">
            <!-- Se llenará dinámicamente -->
        </div>
    </div>
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos DOM
    const chartContainer = document.getElementById('chart-container');
    const tableContainer = document.getElementById('table-container');
    const viewChartBtn = document.getElementById('view-chart-btn');
    const viewTableBtn = document.getElementById('view-table-btn');
    const downloadCsvBtn = document.getElementById('download-csv-btn');
    const chartLoader = document.getElementById('chart-loader');
    const chartSummary = document.getElementById('chart-summary');
    
    // Cambio entre vista de gráfica y tabla
    viewChartBtn.addEventListener('click', function() {
        chartContainer.classList.remove('hidden');
        tableContainer.classList.add('hidden');
        viewChartBtn.classList.add('bg-blue-600', 'text-white');
        viewChartBtn.classList.remove('bg-gray-200', 'text-gray-700');
        viewTableBtn.classList.add('bg-gray-200', 'text-gray-700');
        viewTableBtn.classList.remove('bg-blue-600', 'text-white');
    });
    
    viewTableBtn.addEventListener('click', function() {
        chartContainer.classList.add('hidden');
        tableContainer.classList.remove('hidden');
        viewChartBtn.classList.add('bg-gray-200', 'text-gray-700');
        viewChartBtn.classList.remove('bg-blue-600', 'text-white');
        viewTableBtn.classList.add('bg-blue-600', 'text-white');
        viewTableBtn.classList.remove('bg-gray-200', 'text-gray-700');
    });
    
    // Colores para los estados (semánticos)
    const statusColors = {
        delivered: '#4ade80',     // Verde para Aceptados
        in_process: '#facc15',    // Amarillo para en proceso
        unscheduled: '#f87171'    // Rojo para no Aceptados
    };
    
    // 1) Recuperar los datos
    const rawData = window.dashboardData?.deliveryStatusBySigla;
    if (!rawData) {
        chartContainer.innerHTML = '<div class="text-center py-10 text-gray-500">No se encontraron datos para mostrar</div>';
        return;
    }
    
    // Mostrar loader
    chartLoader.classList.remove('hidden');
    
    // 2) Procesar y agrupar los datos
    const grouping = {};
    const tableRows = [];
    let grandTotal = {
        delivered: 0,
        in_process: 0,
        unscheduled: 0,
        total: 0
    };
    
    rawData.forEach(item => {
        const sigla = item.sigla_name || 'Sin Asignar';
        const delivered = parseInt(item.delivered) || 0;
        const in_process = parseInt(item.in_process) || 0;
        const unscheduled = parseInt(item.unscheduled) || 0;
        const totalSigla = delivered + in_process + unscheduled;
        
        // Acumular para el resumen global
        grandTotal.delivered += delivered;
        grandTotal.in_process += in_process;
        grandTotal.unscheduled += unscheduled;
        grandTotal.total += totalSigla;

        // Crear filas para la tabla con formato mejorado
        tableRows.push({
            'Sigla': `<strong>${sigla}</strong>`,
            'Estado': '<strong>Total</strong>',
            'Cantidad': `<strong>${totalSigla}</strong>`,
            'Porcentaje': '<strong>100%</strong>'
        });
        
        if (totalSigla > 0) {
            const deliveredPct = ((delivered / totalSigla) * 100).toFixed(1);
            const inProcessPct = ((in_process / totalSigla) * 100).toFixed(1);
            const unscheduledPct = ((unscheduled / totalSigla) * 100).toFixed(1);
            
            tableRows.push({
                'Sigla': '',
                'Estado': '<span class="flex items-center"><span class="w-3 h-3 rounded-full mr-2" style="background-color: #4ade80;"></span>Aceptados</span>',
                'Cantidad': delivered,
                'Porcentaje': `<div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-green-400 h-2.5 rounded-full" style="width: ${deliveredPct}%"></div>
                </div><span class="text-xs">${deliveredPct}%</span>`
            });
            
            tableRows.push({
                'Sigla': '',
                'Estado': '<span class="flex items-center"><span class="w-3 h-3 rounded-full mr-2" style="background-color: #facc15;"></span>En Proceso</span>',
                'Cantidad': in_process,
                'Porcentaje': `<div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-yellow-400 h-2.5 rounded-full" style="width: ${inProcessPct}%"></div>
                </div><span class="text-xs">${inProcessPct}%</span>`
            });
            
            tableRows.push({
                'Sigla': '',
                'Estado': '<span class="flex items-center"><span class="w-3 h-3 rounded-full mr-2" style="background-color: #f87171;"></span>No Aceptados</span>',
                'Cantidad': unscheduled,
                'Porcentaje': `<div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-red-400 h-2.5 rounded-full" style="width: ${unscheduledPct}%"></div>
                </div><span class="text-xs">${unscheduledPct}%</span>`
            });
        }
        
        // Agregar línea separadora excepto para el último elemento
        if (rawData.indexOf(item) < rawData.length - 1) {
            tableRows.push({
                'Sigla': '<div class="border-b border-gray-200 my-2"></div>',
                'Estado': '',
                'Cantidad': '',
                'Porcentaje': ''
            });
        }

        // Agrupar para la gráfica
        if (!grouping[sigla]) {
            grouping[sigla] = {
                delivered: 0,
                in_process: 0,
                unscheduled: 0,
                total: 0
            };
        }
        grouping[sigla].delivered += delivered;
        grouping[sigla].in_process += in_process;
        grouping[sigla].unscheduled += unscheduled;
        grouping[sigla].total += totalSigla;
    });
    
    // Mostrar resumen global
    const totalPct = grandTotal.total > 0 ? 100 : 0;
    const deliveredPct = grandTotal.total > 0 ? ((grandTotal.delivered / grandTotal.total) * 100).toFixed(1) : 0;
    const inProcessPct = grandTotal.total > 0 ? ((grandTotal.in_process / grandTotal.total) * 100).toFixed(1) : 0;
    const unscheduledPct = grandTotal.total > 0 ? ((grandTotal.unscheduled / grandTotal.total) * 100).toFixed(1) : 0;
    
    chartSummary.innerHTML = `
        <div>
            <span class="font-medium">Total:</span>
            <span class="font-bold">${grandTotal.total}</span>
        </div>
        <div>
            <span class="text-green-600 font-medium">Aceptados:</span>
            <span class="font-bold">${grandTotal.delivered}</span>
            <span class="text-xs text-gray-500">(${deliveredPct}%)</span>
        </div>
        <div>
            <span class="text-yellow-600 font-medium">En Proceso:</span>
            <span class="font-bold">${grandTotal.in_process}</span>
            <span class="text-xs text-gray-500">(${inProcessPct}%)</span>
        </div>
        <div>
            <span class="text-red-600 font-medium">No Aceptados:</span>
            <span class="font-bold">${grandTotal.unscheduled}</span>
            <span class="text-xs text-gray-500">(${unscheduledPct}%)</span>
        </div>
    `;
    
    // 3) Crear la tabla manualmente para permitir HTML
    const tableContainer2 = document.getElementById('table-delivery-status-by-sigla');
    if (tableContainer2) {
        // Crear tabla manualmente para permitir contenido HTML
        const table = document.createElement('table');
        table.className = 'min-w-full divide-y divide-gray-200';
        
        // Crear encabezado
        const thead = document.createElement('thead');
        thead.className = 'bg-gray-50';
        const headerRow = document.createElement('tr');
        
        ['Sigla', 'Estado', 'Cantidad', 'Porcentaje'].forEach(headerText => {
            const th = document.createElement('th');
            th.className = 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider';
            th.textContent = headerText;
            headerRow.appendChild(th);
        });
        
        thead.appendChild(headerRow);
        table.appendChild(thead);
        
        // Crear cuerpo de la tabla
        const tbody = document.createElement('tbody');
        tbody.className = 'bg-white divide-y divide-gray-200';
        
        // Procesar filas sin HTML para la versión sencilla
        const simpleTableRows = [];
        rawData.forEach(item => {
            const sigla = item.sigla_name || 'Sin Asignar';
            const delivered = parseInt(item.delivered) || 0;
            const in_process = parseInt(item.in_process) || 0;
            const unscheduled = parseInt(item.unscheduled) || 0;
            const totalSigla = delivered + in_process + unscheduled;
            
            // Fila de total
            const totalRow = document.createElement('tr');
            
            const siglaCell = document.createElement('td');
            siglaCell.className = 'px-6 py-4 whitespace-nowrap text-sm font-bold';
            siglaCell.textContent = sigla;
            
            const estadoTotalCell = document.createElement('td');
            estadoTotalCell.className = 'px-6 py-4 whitespace-nowrap text-sm font-bold';
            estadoTotalCell.textContent = 'Total';
            
            const cantidadTotalCell = document.createElement('td');
            cantidadTotalCell.className = 'px-6 py-4 whitespace-nowrap text-sm font-bold';
            cantidadTotalCell.textContent = totalSigla;
            
            const porcentajeTotalCell = document.createElement('td');
            porcentajeTotalCell.className = 'px-6 py-4 whitespace-nowrap text-sm font-bold';
            porcentajeTotalCell.textContent = '100%';
            
            totalRow.appendChild(siglaCell);
            totalRow.appendChild(estadoTotalCell);
            totalRow.appendChild(cantidadTotalCell);
            totalRow.appendChild(porcentajeTotalCell);
            tbody.appendChild(totalRow);
            
            if (totalSigla > 0) {
                // Fila Aceptados
                const AceptadosRow = document.createElement('tr');
                
                const siglaVaciaCell1 = document.createElement('td');
                siglaVaciaCell1.className = 'px-6 py-4 whitespace-nowrap text-sm';
                
                const estadoAceptadosCell = document.createElement('td');
                estadoAceptadosCell.className = 'px-6 py-4 whitespace-nowrap text-sm';
                
                // Crear elementos para el estado con el indicador de color
                const estadoWrapper = document.createElement('div');
                estadoWrapper.className = 'flex items-center';
                
                const colorIndicator = document.createElement('span');
                colorIndicator.className = 'w-3 h-3 rounded-full mr-2';
                colorIndicator.style.backgroundColor = '#4ade80';
                
                const estadoText = document.createTextNode('Aceptados');
                
                estadoWrapper.appendChild(colorIndicator);
                estadoWrapper.appendChild(estadoText);
                estadoAceptadosCell.appendChild(estadoWrapper);
                
                const cantidadAceptadosCell = document.createElement('td');
                cantidadAceptadosCell.className = 'px-6 py-4 whitespace-nowrap text-sm';
                cantidadAceptadosCell.textContent = delivered;
                
                const porcentajeAceptadosCell = document.createElement('td');
                porcentajeAceptadosCell.className = 'px-6 py-4 whitespace-nowrap text-sm';
                
                const deliveredPct = ((delivered / totalSigla) * 100).toFixed(1);
                
                // Crear barra de progreso
                const progressContainer = document.createElement('div');
                progressContainer.className = 'w-full bg-gray-200 rounded-full h-2.5';
                
                const progressBar = document.createElement('div');
                progressBar.className = 'bg-green-400 h-2.5 rounded-full';
                progressBar.style.width = `${deliveredPct}%`;
                
                const percentText = document.createElement('span');
                percentText.className = 'text-xs';
                percentText.textContent = `${deliveredPct}%`;
                
                progressContainer.appendChild(progressBar);
                porcentajeAceptadosCell.appendChild(progressContainer);
                porcentajeAceptadosCell.appendChild(percentText);
                
                AceptadosRow.appendChild(siglaVaciaCell1);
                AceptadosRow.appendChild(estadoAceptadosCell);
                AceptadosRow.appendChild(cantidadAceptadosCell);
                AceptadosRow.appendChild(porcentajeAceptadosCell);
                tbody.appendChild(AceptadosRow);
                
                // Fila En Proceso
                const procesoRow = document.createElement('tr');
                
                const siglaVaciaCell2 = document.createElement('td');
                siglaVaciaCell2.className = 'px-6 py-4 whitespace-nowrap text-sm';
                
                const estadoProcesoCell = document.createElement('td');
                estadoProcesoCell.className = 'px-6 py-4 whitespace-nowrap text-sm';
                
                const procesoWrapper = document.createElement('div');
                procesoWrapper.className = 'flex items-center';
                
                const procesoIndicator = document.createElement('span');
                procesoIndicator.className = 'w-3 h-3 rounded-full mr-2';
                procesoIndicator.style.backgroundColor = '#facc15';
                
                const procesoText = document.createTextNode('En Proceso');
                
                procesoWrapper.appendChild(procesoIndicator);
                procesoWrapper.appendChild(procesoText);
                estadoProcesoCell.appendChild(procesoWrapper);
                
                const cantidadProcesoCell = document.createElement('td');
                cantidadProcesoCell.className = 'px-6 py-4 whitespace-nowrap text-sm';
                cantidadProcesoCell.textContent = in_process;
                
                const porcentajeProcesoCell = document.createElement('td');
                porcentajeProcesoCell.className = 'px-6 py-4 whitespace-nowrap text-sm';
                
                const inProcessPct = ((in_process / totalSigla) * 100).toFixed(1);
                
                const procesoProgressContainer = document.createElement('div');
                procesoProgressContainer.className = 'w-full bg-gray-200 rounded-full h-2.5';
                
                const procesoProgressBar = document.createElement('div');
                procesoProgressBar.className = 'bg-yellow-400 h-2.5 rounded-full';
                procesoProgressBar.style.width = `${inProcessPct}%`;
                
                const procesoPercentText = document.createElement('span');
                procesoPercentText.className = 'text-xs';
                procesoPercentText.textContent = `${inProcessPct}%`;
                
                procesoProgressContainer.appendChild(procesoProgressBar);
                porcentajeProcesoCell.appendChild(procesoProgressContainer);
                porcentajeProcesoCell.appendChild(procesoPercentText);
                
                procesoRow.appendChild(siglaVaciaCell2);
                procesoRow.appendChild(estadoProcesoCell);
                procesoRow.appendChild(cantidadProcesoCell);
                procesoRow.appendChild(porcentajeProcesoCell);
                tbody.appendChild(procesoRow);
                
                // Fila No Aceptados
                const noAceptadosRow = document.createElement('tr');
                
                const siglaVaciaCell3 = document.createElement('td');
                siglaVaciaCell3.className = 'px-6 py-4 whitespace-nowrap text-sm';
                
                const estadoNoAceptadosCell = document.createElement('td');
                estadoNoAceptadosCell.className = 'px-6 py-4 whitespace-nowrap text-sm';
                
                const noAceptadosWrapper = document.createElement('div');
                noAceptadosWrapper.className = 'flex items-center';
                
                const noAceptadosIndicator = document.createElement('span');
                noAceptadosIndicator.className = 'w-3 h-3 rounded-full mr-2';
                noAceptadosIndicator.style.backgroundColor = '#f87171';
                
                const noAceptadosText = document.createTextNode('No Aceptados');
                
                noAceptadosWrapper.appendChild(noAceptadosIndicator);
                noAceptadosWrapper.appendChild(noAceptadosText);
                estadoNoAceptadosCell.appendChild(noAceptadosWrapper);
                
                const cantidadNoAceptadosCell = document.createElement('td');
                cantidadNoAceptadosCell.className = 'px-6 py-4 whitespace-nowrap text-sm';
                cantidadNoAceptadosCell.textContent = unscheduled;
                
                const porcentajeNoAceptadosCell = document.createElement('td');
                porcentajeNoAceptadosCell.className = 'px-6 py-4 whitespace-nowrap text-sm';
                
                const unscheduledPct = ((unscheduled / totalSigla) * 100).toFixed(1);
                
                const noAceptadosProgressContainer = document.createElement('div');
                noAceptadosProgressContainer.className = 'w-full bg-gray-200 rounded-full h-2.5';
                
                const noAceptadosProgressBar = document.createElement('div');
                noAceptadosProgressBar.className = 'bg-red-400 h-2.5 rounded-full';
                noAceptadosProgressBar.style.width = `${unscheduledPct}%`;
                
                const noAceptadosPercentText = document.createElement('span');
                noAceptadosPercentText.className = 'text-xs';
                noAceptadosPercentText.textContent = `${unscheduledPct}%`;
                
                noAceptadosProgressContainer.appendChild(noAceptadosProgressBar);
                porcentajeNoAceptadosCell.appendChild(noAceptadosProgressContainer);
                porcentajeNoAceptadosCell.appendChild(noAceptadosPercentText);
                
                noAceptadosRow.appendChild(siglaVaciaCell3);
                noAceptadosRow.appendChild(estadoNoAceptadosCell);
                noAceptadosRow.appendChild(cantidadNoAceptadosCell);
                noAceptadosRow.appendChild(porcentajeNoAceptadosCell);
                tbody.appendChild(noAceptadosRow);
            }
            
            // Agregar divisor si no es el último elemento
            if (rawData.indexOf(item) < rawData.length - 1) {
                const dividerRow = document.createElement('tr');
                dividerRow.className = 'h-4';
                
                const dividerCell = document.createElement('td');
                dividerCell.colSpan = 4;
                dividerCell.className = 'border-b border-gray-200';
                
                dividerRow.appendChild(dividerCell);
                tbody.appendChild(dividerRow);
            }
        });
        
        table.appendChild(tbody);
        tableContainer2.innerHTML = '';
        tableContainer2.appendChild(table);
    }
    
    // 4) Preparar datos para la gráfica ordenados por total
    let allSiglas = Object.keys(grouping).sort((a, b) => {
        return grouping[b].total - grouping[a].total;
    });
    
    // Limitar a 10 siglas si hay muchas, agrupando el resto como "Otras"
    if (allSiglas.length > 10) {
        const topSiglas = allSiglas.slice(0, 9);
        const otherSiglas = allSiglas.slice(9);
        
        // Crear categoría "Otras"
        grouping['Otras'] = {
            delivered: 0,
            in_process: 0,
            unscheduled: 0,
            total: 0
        };
        
        // Sumar los valores de las siglas restantes
        otherSiglas.forEach(sigla => {
            grouping['Otras'].delivered += grouping[sigla].delivered;
            grouping['Otras'].in_process += grouping[sigla].in_process;
            grouping['Otras'].unscheduled += grouping[sigla].unscheduled;
            grouping['Otras'].total += grouping[sigla].total;
        });
        
        // Actualizar la lista de siglas
        allSiglas = [...topSiglas, 'Otras'];
    }
    
    const datasets = [
        {
            label: 'Aceptados',
            data: allSiglas.map(sigla => grouping[sigla].delivered),
            backgroundColor: statusColors.delivered,
            hoverBackgroundColor: '#22c55e',
            barPercentage: 0.8,
            borderWidth: 0
        },
        {
            label: 'En Proceso de Aceptación',
            data: allSiglas.map(sigla => grouping[sigla].in_process),
            backgroundColor: statusColors.in_process,
            hoverBackgroundColor: '#eab308',
            barPercentage: 0.8,
            borderWidth: 0
        },
        {
            label: 'No Aceptados',
            data: allSiglas.map(sigla => grouping[sigla].unscheduled),
            backgroundColor: statusColors.unscheduled,
            hoverBackgroundColor: '#ef4444',
            barPercentage: 0.8,
            borderWidth: 0
        }
    ];

    // Crear un mapa para totales (para tooltips)
    const totalBySigla = {};
    allSiglas.forEach(sigla => {
        totalBySigla[sigla] = grouping[sigla].total;
    });

    // 5) Crear la gráfica con mejoras visuales
    const ctx = document.getElementById('deliveryStatusBySiglaChart')?.getContext('2d');
    if (!ctx) return;
    
    const chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: allSiglas,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: allSiglas.length > 5 ? 'y' : 'x', // Usar barras horizontales si hay muchas siglas
            scales: {
                x: { 
                    stacked: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                y: { 
                    stacked: true, 
                    beginAtZero: true,
                    grid: {
                        borderDash: [2, 2]
                    },
                    ticks: {
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#000',
                    bodyColor: '#333',
                    borderColor: '#ddd',
                    borderWidth: 1,
                    padding: 10,
                    boxPadding: 6,
                    usePointStyle: true,
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        title: function(tooltipItems) {
                            return tooltipItems[0].label;
                        },
                        label: function(context) {
                            const value = context.raw;
                            const total = totalBySigla[context.label] || 0;
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : "0%";
                            let pointStyle = context.datasetIndex === 0 ? 'circle' : (context.datasetIndex === 1 ? 'triangle' : 'rect');
                            let color = context.datasetIndex === 0 ? statusColors.delivered : (context.datasetIndex === 1 ? statusColors.in_process : statusColors.unscheduled);
                            
                            return [
                                context.dataset.label + ': ' + value + ' expedientes (' + percentage + ')',
                                'Total en Sigla: ' + total + ' expedientes'
                            ];
                        }
                    }
                },
                datalabels: {
                    display: function(context) {
                        // Solo mostrar etiquetas para valores significativos
                        return context.dataset.data[context.dataIndex] > 0;
                    },
                    formatter: function(value, context) {
                        const total = totalBySigla[context.chart.data.labels[context.dataIndex]] || 0;
                        return total > 0 ? ((value / total) * 100).toFixed(0) + '%' : '';
                    },
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 11
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            },
            onClick: function(e, elements, chart) {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const datasetIndex = elements[0].datasetIndex;
                    const sigla = chart.data.labels[index];
                    const status = chart.data.datasets[datasetIndex].label;
                    
                    // Aquí se podría implementar un filtro adicional o una acción al hacer clic
                    console.log(`Clicked on ${status} for ${sigla}`);
                }
            }
        },
        plugins: [{
            id: 'customCanvasBackgroundColor',
            beforeDraw: (chart) => {
                const ctx = chart.canvas.getContext('2d');
                ctx.save();
                ctx.globalCompositeOperation = 'destination-over';
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, chart.width, chart.height);
                ctx.restore();
            }
        }]
    });
    
    // Ocultar loader una vez que se ha cargado la gráfica
    chartLoader.classList.add('hidden');
    
    // 6) Implementar filtrado por leyendas
    const legendItems = [
        document.getElementById('legend-delivered'),
        document.getElementById('legend-in-process'),
        document.getElementById('legend-unscheduled')
    ];
    
    // Configurar los controladores de eventos para las leyendas
    legendItems.forEach(item => {
        if (item) {
            item.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                const isActive = this.getAttribute('data-active') === 'true';
                
                // Cambiar el estado activo
                this.setAttribute('data-active', !isActive);
                
                // Actualizar visualización de la leyenda
                if (isActive) {
                    // Desactivar
                    this.classList.add('opacity-50');
                    this.querySelector('div').classList.add('opacity-50');
                } else {
                    // Activar
                    this.classList.remove('opacity-50');
                    this.querySelector('div').classList.remove('opacity-50');
                }
                
                // Actualizar visibilidad del dataset
                const dataset = chartInstance.data.datasets[index];
                dataset.hidden = isActive;
                
                // Actualizar la tabla también si está visible
                const stateLabels = ['Aceptados', 'En Proceso', 'No Aceptados'];
                const rows = document.querySelectorAll(`td div.flex.items-center`);
                rows.forEach(row => {
                    if (row.textContent.includes(stateLabels[index])) {
                        const parentRow = row.closest('tr');
                        if (parentRow) {
                            parentRow.style.display = isActive ? 'none' : '';
                        }
                    }
                });
                
                // Actualizar la gráfica
                chartInstance.update();
            });
        }
    });
    
    // 7) Descarga de datos en CSV
    downloadCsvBtn.addEventListener('click', function() {
        let csvContent = "data:text/csv;charset=utf-8,";
        
        // Encabezados
        csvContent += "Sigla,Estado,Cantidad,Porcentaje\n";
        
        // Datos
        allSiglas.forEach(sigla => {
            const totalSigla = grouping[sigla].total;
            const delivered = grouping[sigla].delivered;
            const in_process = grouping[sigla].in_process;
            const unscheduled = grouping[sigla].unscheduled;
            
            // Calcular porcentajes
            const deliveredPct = totalSigla > 0 ? ((delivered / totalSigla) * 100).toFixed(2) : "0";
            const inProcessPct = totalSigla > 0 ? ((in_process / totalSigla) * 100).toFixed(2) : "0";
            const unscheduledPct = totalSigla > 0 ? ((unscheduled / totalSigla) * 100).toFixed(2) : "0";
            
            // Agregar filas
            csvContent += `${sigla},Total,${totalSigla},100\n`;
            csvContent += `${sigla},Aceptados,${delivered},${deliveredPct}\n`;
            csvContent += `${sigla},En Proceso,${in_process},${inProcessPct}\n`;
            csvContent += `${sigla},No Aceptados,${unscheduled},${unscheduledPct}\n`;
        });
        
        // Crear enlace de descarga
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "entregas_por_sigla_" + new Date().toISOString().split('T')[0] + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});
</script>
@endpush