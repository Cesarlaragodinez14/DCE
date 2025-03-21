{{-- resources/views/admin/stats/charts/_auditoria_especial.blade.php --}}

<section id="auditoria-especial" class="chart-container animate-fade-in animate-delay-200">
    <div class="chart-header">
        <h3 class="chart-title">
            <ion-icon name="analytics-outline" class="chart-icon"></ion-icon>
            Expedientes de Acción por Número de Auditoria
            <span class="ml-1 text-xs text-gray-500">(Agrupado AE y UAA)</span>
        </h3>
        <div class="chart-actions">
            <button id="toggleTableBtn" class="chart-action-btn tooltip" onclick="toggleAuditoriaTable()">
                <ion-icon name="list-outline"></ion-icon>
                <span class="tooltip-text">Ver tabla de datos</span>
            </button>
            <button class="chart-action-btn tooltip" onclick="exportChart('auditoriaEspecialChart', 'Expedientes_por_Auditoria')">
                <ion-icon name="download-outline"></ion-icon>
                <span class="tooltip-text">Descargar gráfico</span>
            </button>
            <div class="relative inline-block">
                <button id="chartTypeBtn" class="chart-action-btn tooltip">
                    <ion-icon name="options-outline"></ion-icon>
                    <span class="tooltip-text">Cambiar tipo de gráfico</span>
                </button>
                <div id="chartTypeMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10 border border-gray-200">
                    <div class="py-1">
                        <button onclick="changeChartType('bar')" class="chart-type-option">
                            <ion-icon name="bar-chart-outline" class="mr-2"></ion-icon> Barras
                        </button>
                        <button onclick="changeChartType('horizontalBar')" class="chart-type-option">
                            <ion-icon name="stats-chart-outline" class="mr-2"></ion-icon> Barras horizontales
                        </button>
                        <button onclick="changeChartType('pie')" class="chart-type-option">
                            <ion-icon name="pie-chart-outline" class="mr-2"></ion-icon> Circular
                        </button>
                        <button onclick="changeChartType('line')" class="chart-type-option">
                            <ion-icon name="trending-up-outline" class="mr-2"></ion-icon> Línea
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="chart-body">
        <div id="chart-container-wrapper" class="relative">
            <div class="absolute top-2 right-2 z-10 bg-white bg-opacity-75 rounded px-2 py-1 text-xs text-gray-600 font-medium hidden" id="dataSummary">
                <!-- El resumen de datos se llenará con JavaScript -->
            </div>
            <canvas id="auditoriaEspecialChart" height="300"></canvas>
        </div>
        <div class="flex justify-end mt-2">
            <div class="flex items-center text-xs text-gray-500">
                <span id="totalRecords" class="font-medium"></span>
                <button id="showAllBtn" class="ml-2 text-blue-600 hover:text-blue-800 hidden" onclick="toggleShowAll()">
                    Ver todos
                </button>
            </div>
        </div>
        <div id="table-auditoria-especial" class="mt-4 hidden">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-medium text-gray-700">Tabla de datos</h4>
                <div class="flex space-x-2">
                    <div class="relative">
                        <input type="text" id="tableSearch" placeholder="Buscar..." class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <ion-icon name="search-outline" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400"></ion-icon>
                    </div>
                    <button onclick="exportTableToCSV('auditoria-table', 'Expedientes_por_Auditoria')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm py-1 px-3 rounded flex items-center">
                        <ion-icon name="document-outline" class="mr-1"></ion-icon> Exportar CSV
                    </button>
                </div>
            </div>
            <div class="table-responsive max-h-96 overflow-y-auto">
                <!-- La tabla se insertará aquí desde JavaScript -->
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
"use strict";

// Variables globales para este componente
let auditoriaChart = null;
let auditoriaFullData = [];
let auditoriaLimitedData = [];
let isShowingAll = false;
let currentChartType = 'bar';

document.addEventListener('DOMContentLoaded', function() {
    // Obtener los datos del dashboard
    const data = window.dashboardData?.countsByAuditoriaEspecial;
    if(!data || data.length === 0) {
        document.getElementById('auditoria-especial').innerHTML = `
            <div class="p-8 text-center">
                <ion-icon name="alert-circle-outline" class="text-4xl text-gray-400"></ion-icon>
                <p class="mt-2 text-gray-500">No hay datos disponibles para mostrar</p>
            </div>
        `;
        return;
    }

    // Guardar los datos ordenados para uso posterior
    auditoriaFullData = [...data].sort((a, b) => b.total - a.total);
    
    // Limitar los datos a mostrar (máximo 15 para mejor visualización)
    const dataLimit = 15;
    auditoriaLimitedData = auditoriaFullData.slice(0, dataLimit);
    
    // Mostrar información de registros
    document.getElementById('totalRecords').textContent = `Mostrando ${auditoriaLimitedData.length} de ${auditoriaFullData.length} registros`;
    
    // Mostrar botón "Ver todos" si hay más registros
    if (auditoriaFullData.length > dataLimit) {
        document.getElementById('showAllBtn').classList.remove('hidden');
    }
    
    // Crear la tabla de datos
    createAuditoriaTable(auditoriaLimitedData);
    
    // Inicializar el gráfico
    initAuditoriaChart();
    
    // Añadir resumen estadístico
    updateDataSummary(auditoriaLimitedData);
    
    // Configurar el menú desplegable de tipos de gráfico
    setupChartTypeMenu();
    
    // Configurar el buscador de la tabla
    setupTableSearch();
});

// Función para inicializar el gráfico
function initAuditoriaChart() {
    const ctx = document.getElementById('auditoriaEspecialChart')?.getContext('2d');
    if(!ctx) return;
    
    // Configurar los datos para el gráfico
    const chartData = prepareChartData(auditoriaLimitedData, currentChartType);
    
    // Configurar las opciones para el gráfico
    const chartOptions = getChartOptions(currentChartType);
    
    // Crear el gráfico
    auditoriaChart = new Chart(ctx, {
        type: currentChartType === 'horizontalBar' ? 'bar' : currentChartType,
        data: chartData,
        options: chartOptions
    });
}

// Función para preparar los datos del gráfico según el tipo
function prepareChartData(data, type) {
    const labels = data.map(i => `${i.auditoria_especial || 'N/A'}/${i.uaa || 'N/A'}`);
    const values = data.map(i => i.total);
    const colors = data.map((_, idx) => getColor(idx));
    
    // Configuración base
    const chartData = {
        labels: labels,
        datasets: [{
            label: 'Total de Expedientes',
            data: values,
            backgroundColor: colors,
            borderColor: type === 'line' ? '#3b82f6' : colors,
            borderWidth: 1
        }]
    };
    
    // Ajustes específicos por tipo de gráfico
    if (type === 'line') {
        chartData.datasets[0].fill = false;
        chartData.datasets[0].tension = 0.1;
        chartData.datasets[0].backgroundColor = '#3b82f6';
    }
    
    return chartData;
}

// Función para obtener opciones de configuración según el tipo de gráfico
function getChartOptions(type) {
    // Opciones base
    const options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: ['pie', 'doughnut'].includes(type),
                position: 'top',
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                },
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            label += context.parsed.y;
                        } else if (context.parsed !== null) {
                            label += context.parsed;
                        }
                        return label + ' expedientes';
                    }
                }
            }
        },
        animation: {
            duration: 1000,
            easing: 'easeOutQuart'
        }
    };
    
    // Añadir opciones específicas según el tipo de gráfico
    if (['bar', 'line'].includes(type) || type === 'horizontalBar') {
        options.scales = {
            y: { 
                beginAtZero: true,
                title: {
                    display: true,
                    text: type === 'horizontalBar' ? 'Auditoría Especial / UAA' : 'Cantidad de Expedientes',
                    font: {
                        weight: 'bold'
                    }
                },
                ticks: {
                    precision: 0
                }
            },
            x: {
                ticks: {
                    maxRotation: type === 'horizontalBar' ? 0 : 45,
                    minRotation: type === 'horizontalBar' ? 0 : 45,
                    autoSkip: true,
                    maxTicksLimit: 20
                },
                title: {
                    display: true,
                    text: type === 'horizontalBar' ? 'Cantidad de Expedientes' : 'Auditoría Especial / UAA',
                    font: {
                        weight: 'bold'
                    }
                }
            }
        };
        
        // Para barras horizontales, intercambiar los ejes
        if (type === 'horizontalBar') {
            options.indexAxis = 'y';
        }
    }
    
    return options;
}

// Función para crear la tabla de datos
function createAuditoriaTable(data) {
    const tableContainer = document.querySelector('#table-auditoria-especial .table-responsive');
    if (!tableContainer) return;
    
    // Crear encabezados
    const headers = ['AE', 'UAA', 'Total', '% del Total'];
    
    // Calcular el total general para porcentajes
    const grandTotal = data.reduce((sum, item) => sum + item.total, 0);
    
    // Preparar los datos de filas
    const rows = data.map(item => ({
        'AE': item.auditoria_especial ?? 'N/A',
        'UAA': item.uaa ?? 'N/A',
        'Total': item.total,
        '% del Total': ((item.total / grandTotal) * 100).toFixed(1) + '%'
    }));
    
    // Crear la tabla con HTML directamente para poder añadir más estilos
    let tableHTML = `
        <table class="stats-table w-full" id="auditoria-table">
            <thead>
                <tr>
                    ${headers.map(header => `<th>${header}</th>`).join('')}
                </tr>
            </thead>
            <tbody>
                ${rows.map(row => `
                    <tr>
                        <td>${row['AE']}</td>
                        <td>${row['UAA']}</td>
                        <td class="font-medium">${row['Total']}</td>
                        <td>${row['% del Total']}</td>
                    </tr>
                `).join('')}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="font-bold text-right">Total:</td>
                    <td class="font-bold">${grandTotal}</td>
                    <td class="font-bold">100%</td>
                </tr>
            </tfoot>
        </table>
    `;
    
    tableContainer.innerHTML = tableHTML;
}

// Función para actualizar el resumen de datos
function updateDataSummary(data) {
    const summaryEl = document.getElementById('dataSummary');
    if (!summaryEl) return;
    
    // Calcular estadísticas básicas
    const total = data.reduce((sum, item) => sum + item.total, 0);
    const max = Math.max(...data.map(item => item.total));
    const min = Math.min(...data.map(item => item.total));
    const avg = (total / data.length).toFixed(1);
    
    // Encontrar las AE/UAA con mayor y menor número
    const maxItem = data.find(item => item.total === max);
    const minItem = data.find(item => item.total === min);
    
    summaryEl.innerHTML = `
        <div class="font-bold mb-1">Resumen:</div>
        <div>Total: ${total} expedientes</div>
        <div>Promedio: ${avg} por AE/UAA</div>
    `;
    
    summaryEl.classList.remove('hidden');
}

// Función para alternar entre mostrar/ocultar la tabla
function toggleAuditoriaTable() {
    const tableContainer = document.getElementById('table-auditoria-especial');
    const toggleBtn = document.getElementById('toggleTableBtn');
    
    if (tableContainer.classList.contains('hidden')) {
        tableContainer.classList.remove('hidden');
        toggleBtn.innerHTML = '<ion-icon name="eye-off-outline"></ion-icon>';
        toggleBtn.querySelector('.tooltip-text').textContent = 'Ocultar tabla';
    } else {
        tableContainer.classList.add('hidden');
        toggleBtn.innerHTML = '<ion-icon name="list-outline"></ion-icon>';
        toggleBtn.querySelector('.tooltip-text').textContent = 'Ver tabla de datos';
    }
}

// Función para configurar el menú desplegable de tipos de gráfico
function setupChartTypeMenu() {
    const chartTypeBtn = document.getElementById('chartTypeBtn');
    const chartTypeMenu = document.getElementById('chartTypeMenu');
    
    // Estilo para las opciones del menú
    const options = document.querySelectorAll('.chart-type-option');
    options.forEach(option => {
        option.classList.add('block', 'px-4', 'py-2', 'text-sm', 'text-gray-700', 'hover:bg-gray-100', 'hover:text-gray-900', 'cursor-pointer', 'w-full', 'text-left', 'flex', 'items-center');
    });
    
    // Toggle del menú
    chartTypeBtn.addEventListener('click', function() {
        chartTypeMenu.classList.toggle('hidden');
    });
    
    // Cerrar el menú al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!chartTypeBtn.contains(e.target) && !chartTypeMenu.contains(e.target)) {
            chartTypeMenu.classList.add('hidden');
        }
    });
}

// Función para cambiar el tipo de gráfico
function changeChartType(type) {
    // Ocultar el menú
    document.getElementById('chartTypeMenu').classList.add('hidden');
    
    // Si es el mismo tipo, no hacer nada
    if (type === currentChartType && 
        !(type === 'bar' && auditoriaChart.options.indexAxis === 'y')) {
        return;
    }
    
    // Actualizar el tipo actual
    currentChartType = type;
    
    // Destruir el gráfico actual
    auditoriaChart.destroy();
    
    // Inicializar nuevo gráfico
    initAuditoriaChart();
}

// Función para exportar el gráfico como imagen
function exportChart(chartId, filename) {
    const canvas = document.getElementById(chartId);
    if (!canvas) return;
    
    // Crear un enlace temporal
    const link = document.createElement('a');
    link.download = `${filename}_${new Date().toISOString().split('T')[0]}.png`;
    link.href = canvas.toDataURL('image/png', 1.0);
    
    // Simular clic y eliminar
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Función para exportar la tabla a CSV
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    // Obtener todos los datos de la tabla
    const rows = Array.from(table.querySelectorAll('tr'));
    
    // Convertir a CSV
    let csv = [];
    
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        const rowData = cells.map(cell => {
            // Escapar comillas y encerrar en comillas si tiene comas
            let data = cell.textContent.trim();
            if (data.includes(',') || data.includes('"') || data.includes('\n')) {
                data = '"' + data.replace(/"/g, '""') + '"';
            }
            return data;
        });
        csv.push(rowData.join(','));
    });
    
    // Combinar en una cadena
    const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
    
    // Crear enlace y descargar
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `${filename}_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Función para configurar la búsqueda en la tabla
function setupTableSearch() {
    const searchInput = document.getElementById('tableSearch');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#auditoria-table tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
}

// Función para alternar entre mostrar todos los datos o limitados
function toggleShowAll() {
    isShowingAll = !isShowingAll;
    const showBtn = document.getElementById('showAllBtn');
    
    if (isShowingAll) {
        // Mostrar todos los datos
        auditoriaChart.destroy();
        createAuditoriaTable(auditoriaFullData);
        auditoriaLimitedData = auditoriaFullData;
        initAuditoriaChart();
        showBtn.textContent = 'Ver menos';
        document.getElementById('totalRecords').textContent = `Mostrando todos los ${auditoriaFullData.length} registros`;
    } else {
        // Mostrar datos limitados
        auditoriaChart.destroy();
        auditoriaLimitedData = auditoriaFullData.slice(0, 15);
        createAuditoriaTable(auditoriaLimitedData);
        initAuditoriaChart();
        showBtn.textContent = 'Ver todos';
        document.getElementById('totalRecords').textContent = `Mostrando ${auditoriaLimitedData.length} de ${auditoriaFullData.length} registros`;
    }
    
    // Actualizar resumen
    updateDataSummary(auditoriaLimitedData);
}
</script>

<style>
/* Estilos adicionales específicos para este componente */
.stats-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.stats-table th,
.stats-table td {
    padding: 0.625rem;
    border: 1px solid #e5e7eb;
}

.stats-table th {
    background-color: #f9fafb;
    font-weight: 600;
    text-align: left;
}

.stats-table tbody tr:nth-child(even) {
    background-color: #f8fafc;
}

.stats-table tbody tr:hover {
    background-color: #f1f5f9;
}

.stats-table tfoot {
    background-color: #f3f4f6;
    font-weight: 500;
}

.chart-type-option {
    transition: all 0.2s ease;
}

.chart-type-option:hover ion-icon {
    color: #3b82f6;
}

/* Hacer la tabla responsive */
.table-responsive {
    display: block;
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
</style>
@endpush