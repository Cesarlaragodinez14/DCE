{{-- resources/views/admin/stats/charts/_status.blade.php --}}

<section id="estatus" style="background:#fff">
    <div class="chart-header">
        <h3 class="chart-title">
            <ion-icon name="pie-chart-outline" class="chart-icon"></ion-icon>
            Expedientes por Estatus
            <span class="badge-count ml-2"></span>
        </h3>
        <div class="chart-actions">
            <button id="toggleStatusTableBtn" class="chart-action-btn tooltip" onclick="toggleStatusTable()">
                <ion-icon name="list-outline"></ion-icon>
                <span class="tooltip-text">Ver tabla de datos</span>
            </button>
            <button class="chart-action-btn tooltip" onclick="exportStatusToImage()">
                <ion-icon name="download-outline"></ion-icon>
                <span class="tooltip-text">Descargar gráfico</span>
            </button>
            <div class="relative inline-block">
                <button id="statusChartTypeBtn" class="chart-action-btn tooltip">
                    <ion-icon name="options-outline"></ion-icon>
                    <span class="tooltip-text">Cambiar tipo de gráfico</span>
                </button>
                <div id="statusChartTypeMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10 border border-gray-200">
                    <div class="py-1">
                        <button onclick="changeStatusChartType('bar')" class="chart-type-option">
                            <ion-icon name="bar-chart-outline" class="mr-2"></ion-icon> Barras
                        </button>
                        <button onclick="changeStatusChartType('horizontalBar')" class="chart-type-option">
                            <ion-icon name="stats-chart-outline" class="mr-2"></ion-icon> Barras horizontales
                        </button>
                        <button onclick="changeStatusChartType('pie')" class="chart-type-option">
                            <ion-icon name="pie-chart-outline" class="mr-2"></ion-icon> Circular
                        </button>
                        <button onclick="changeStatusChartType('doughnut')" class="chart-type-option">
                            <ion-icon name="ellipse-outline" class="mr-2"></ion-icon> Dona
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="chart-body">
        <div class="status-summary p-3 bg-gray-50 rounded-lg mb-4 flex flex-wrap gap-2 md:gap-4">
            <!-- El resumen de estado se insertará mediante JavaScript -->
        </div>
        <div class="relative">
            <canvas id="statusChart" height="300"></canvas>
        </div>
        <div id="table-status" class="mt-6 hidden">
            <div class="flex justify-between items-center mb-3">
                <div class="flex space-x-2">
                    <div class="relative">
                        <input type="text" id="statusTableSearch" placeholder="Buscar estatus..." class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <ion-icon name="search-outline" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400"></ion-icon>
                    </div>
                    <button onclick="exportStatusTableToCSV()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm py-1 px-3 rounded flex items-center">
                        <ion-icon name="document-outline" class="mr-1"></ion-icon> Exportar CSV
                    </button>
                </div>
            </div>
            <div class="status-table-container overflow-x-auto">
                <!-- La tabla se insertará aquí desde JavaScript -->
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
"use strict";

// Variables globales para este componente
let statusChart = null;
let statusData = [];
let currentStatusChartType = 'bar';

// Nueva paleta de colores más vibrante y distintiva
const statusColors = {
    'En Proceso de Revisión del Checklist': '#2563EB',        // Azul real
    'Pendiente': '#D97706',         // Ámbar oscuro
    'Concluido': '#059669',         // Verde esmeralda
    'Rechazado': '#DC2626',         // Rojo intenso
    'No Iniciado': '#475569',       // Gris pizarra
    'Entregado': '#7C3AED',         // Púrpura intenso
    'Revisado': '#0E7490',          // Cian oscuro
    'Aceptado': '#65A30D',          // Verde oliva
    'En Revisión': '#BE185D',       // Rosa oscuro
    'Programado': '#F59E0B',        // Naranja
    'Cancelado': '#B45309',         // Naranja oscuro
    'Asignado': '#0369A1',          // Azul oscuro
    'En Espera': '#6D28D9',         // Púrpura
    'Suspendido': '#A16207',        // Ámbar oscuro
    'Terminado': '#15803D',         // Verde oscuro
    'Otros': '#374151'              // Gris oscuro
};

document.addEventListener('DOMContentLoaded', function() {
    // Obtener los datos del dashboard
    const data = window.dashboardData?.countsByStatus;
    if(!data || data.length === 0) {
        document.getElementById('estatus').innerHTML = `
            <div class="p-8 text-center">
                <ion-icon name="alert-circle-outline" class="text-4xl text-gray-400"></ion-icon>
                <p class="mt-2 text-gray-500">No hay datos de estatus disponibles</p>
            </div>
        `;
        return;
    }

    // Ordenar los datos por total, de mayor a menor
    statusData = [...data].sort((a, b) => b.total - a.total);
    
    // Calcular el total general
    const totalExpedientes = statusData.reduce((sum, item) => sum + item.total, 0);
    
    // Actualizar el badge con el total
    document.querySelector('#estatus .badge-count').textContent = `${totalExpedientes} expedientes`;
    
    // Crear el resumen visual
    createStatusSummary(statusData, totalExpedientes);
    
    // Crear la tabla de datos
    createStatusTable(statusData, totalExpedientes);
    
    // Inicializar el gráfico
    initStatusChart();
    
    // Configurar los eventos de UI
    setupStatusUIEvents();
});

// Función para inicializar el gráfico
function initStatusChart() {
    const ctx = document.getElementById('statusChart')?.getContext('2d');
    if(!ctx) return;
    
    // Calcular el total para porcentajes
    const totalSum = statusData.reduce((sum, item) => sum + item.total, 0);
    
    // Configurar datos según el tipo de gráfico
    const chartData = {
        labels: statusData.map(i => i.estatus_checklist),
        datasets: [{
            label: 'Expedientes',
            data: statusData.map(i => i.total),
            backgroundColor: statusData.map(i => getStatusColor(i.estatus_checklist)),
            borderColor: statusData.map(i => {
                const color = getStatusColor(i.estatus_checklist);
                return color.replace(')', ', 0.8)').replace('rgb', 'rgba');
            }),
            borderWidth: 1
        }]
    };
    
    // Configurar opciones del gráfico
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: ['pie', 'doughnut'].includes(currentStatusChartType),
                position: 'right'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const value = context.raw;
                        const percentage = ((value / totalSum) * 100).toFixed(1);
                        return `${context.label}: ${value} (${percentage}%)`;
                    }
                }
            }
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };
    
    // Añadir opciones específicas para gráficos de barras
    if (['bar', 'horizontalBar'].includes(currentStatusChartType)) {
        chartOptions.scales = {
            y: { 
                beginAtZero: true,
                ticks: { precision: 0 },
                title: {
                    display: true,
                    text: currentStatusChartType === 'horizontalBar' ? 'Estatus' : 'Cantidad de Expedientes',
                    font: { weight: 'bold' }
                }
            },
            x: {
                title: {
                    display: true,
                    text: currentStatusChartType === 'horizontalBar' ? 'Cantidad de Expedientes' : 'Estatus',
                    font: { weight: 'bold' }
                }
            }
        };
        
        // Para barras horizontales
        if (currentStatusChartType === 'horizontalBar') {
            chartOptions.indexAxis = 'y';
        }
    }
    
    // Crear el gráfico
    statusChart = new Chart(ctx, {
        type: currentStatusChartType === 'horizontalBar' ? 'bar' : currentStatusChartType,
        data: chartData,
        options: chartOptions
    });
}

// Función para obtener un color según el estatus
function getStatusColor(status) {
    // Si existe un color predefinido para este estatus, usarlo
    if (statusColors[status]) {
        return statusColors[status];
    }
    
    // Si no, generar un color usando la función getColor
    const index = Object.keys(statusColors).length;
    return getColor(index);
}

// Función para crear el resumen visual de estatus
function createStatusSummary(data, total) {
    const summaryContainer = document.querySelector('.status-summary');
    if (!summaryContainer) return;
    
    // Limpiar el contenedor
    summaryContainer.innerHTML = '';
    
    // Mostrar solo los 5 estatus principales para el resumen
    const topStatuses = data.slice(0, 5);
    
    // Crear los indicadores para cada estatus
    topStatuses.forEach(item => {
        const percentage = ((item.total / total) * 100).toFixed(1);
        const statusColor = getStatusColor(item.estatus_checklist);
        
        const statusElement = document.createElement('div');
        statusElement.className = 'flex-grow md:flex-grow-0 flex items-center';
        statusElement.innerHTML = `
            <div class="w-3 h-3 rounded-full mr-2" style="background-color: ${statusColor}"></div>
            <div>
                <span class="text-xs text-gray-600">${item.estatus_checklist}</span>
                <div class="flex items-center">
                    <span class="font-bold text-sm mr-1">${item.total}</span>
                    <span class="text-xs text-gray-500">(${percentage}%)</span>
                </div>
            </div>
        `;
        
        summaryContainer.appendChild(statusElement);
    });
    
    // Si hay más estatus, añadir un indicador "Otros"
    if (data.length > 5) {
        const otherTotal = data.slice(5).reduce((sum, item) => sum + item.total, 0);
        const otherPercentage = ((otherTotal / total) * 100).toFixed(1);
        
        const otherElement = document.createElement('div');
        otherElement.className = 'flex-grow md:flex-grow-0 flex items-center';
        otherElement.innerHTML = `
            <div class="w-3 h-3 rounded-full mr-2 bg-gray-500"></div>
            <div>
                <span class="text-xs text-gray-600">Otros</span>
                <div class="flex items-center">
                    <span class="font-bold text-sm mr-1">${otherTotal}</span>
                    <span class="text-xs text-gray-500">(${otherPercentage}%)</span>
                </div>
            </div>
        `;
        
        summaryContainer.appendChild(otherElement);
    }
}

// Función para crear la tabla de datos
function createStatusTable(data, totalSum) {
    const tableContainer = document.querySelector('.status-table-container');
    if (!tableContainer) return;
    
    // Crear la tabla con HTML
    let tableHTML = `
        <table class="status-table w-full" id="status-table">
            <thead>
                <tr>
                    <th class="text-left">Estatus de la Revisión</th>
                    <th class="text-right">Total de Expedientes de acción</th>
                    <th class="text-center">Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                ${data.map(item => {
                    const percentage = ((item.total / totalSum) * 100).toFixed(1);
                    const barWidth = Math.max(5, percentage); // Al menos 5% para visibilidad
                    const statusColor = getStatusColor(item.estatus_checklist);
                    
                    return `
                        <tr>
                            <td class="status-name">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2" style="background-color: ${statusColor}"></div>
                                    ${item.estatus_checklist}
                                </div>
                            </td>
                            <td class="text-right font-medium">${item.total}</td>
                            <td class="text-center">${percentage}%</td>
                            <td>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full" 
                                         style="width: ${barWidth}%; background-color: ${statusColor}">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('')}
            </tbody>
            <tfoot>
                <tr>
                    <td class="font-bold">Total</td>
                    <td class="text-right font-bold">${totalSum}</td>
                    <td class="text-center font-bold">100%</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    `;
    
    tableContainer.innerHTML = tableHTML;
}

// Función para configurar eventos de la UI
function setupStatusUIEvents() {
    // Configurar el botón y menú de tipos de gráfico
    const chartTypeBtn = document.getElementById('statusChartTypeBtn');
    const chartTypeMenu = document.getElementById('statusChartTypeMenu');
    
    // Añadir clases a las opciones
    document.querySelectorAll('.chart-type-option').forEach(option => {
        option.classList.add('block', 'px-4', 'py-2', 'text-sm', 'text-gray-700', 'hover:bg-gray-100', 'w-full', 'text-left', 'flex', 'items-center');
    });
    
    // Toggle del menú
    if (chartTypeBtn && chartTypeMenu) {
        chartTypeBtn.addEventListener('click', () => {
            chartTypeMenu.classList.toggle('hidden');
        });
        
        // Cerrar al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!chartTypeBtn.contains(e.target) && !chartTypeMenu.contains(e.target)) {
                chartTypeMenu.classList.add('hidden');
            }
        });
    }
    
    // Configurar búsqueda en la tabla
    const searchInput = document.getElementById('statusTableSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#status-table tbody tr');
            
            tableRows.forEach(row => {
                const statusName = row.querySelector('.status-name').textContent.toLowerCase();
                row.style.display = statusName.includes(searchTerm) ? '' : 'none';
            });
        });
    }
}

// Función para cambiar el tipo de gráfico
function changeStatusChartType(type) {
    // Ocultar el menú
    document.getElementById('statusChartTypeMenu').classList.add('hidden');
    
    // Si es el mismo tipo, no hacer nada
    if (type === currentStatusChartType && 
        !(type === 'bar' && statusChart.options.indexAxis === 'y')) {
        return;
    }
    
    // Actualizar el tipo actual
    currentStatusChartType = type;
    
    // Destruir el gráfico actual
    statusChart.destroy();
    
    // Inicializar nuevo gráfico
    initStatusChart();
}

// Función para mostrar/ocultar la tabla
function toggleStatusTable() {
    const tableContainer = document.getElementById('table-status');
    const toggleBtn = document.getElementById('toggleStatusTableBtn');
    
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

// Función para exportar el gráfico como imagen
function exportStatusToImage() {
    const canvas = document.getElementById('statusChart');
    if (!canvas) return;
    
    // Crear enlace temporal para descargar
    const link = document.createElement('a');
    link.download = `Expedientes_por_Estatus_${new Date().toISOString().split('T')[0]}.png`;
    link.href = canvas.toDataURL('image/png', 1.0);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Función para exportar la tabla a CSV
function exportStatusTableToCSV() {
    const table = document.getElementById('status-table');
    if (!table) return;
    
    // Obtener filas de la tabla
    const rows = Array.from(table.querySelectorAll('tr'));
    
    // Convertir a CSV
    let csv = [];
    
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        // Solo tomar las tres primeras columnas (Estatus, Total, Porcentaje)
        const rowData = cells.slice(0, 3).map(cell => {
            // Limpiar de posibles elementos HTML
            let content = cell.textContent.trim();
            // Escapar comillas
            if (content.includes(',') || content.includes('"') || content.includes('\n')) {
                content = '"' + content.replace(/"/g, '""') + '"';
            }
            return content;
        });
        csv.push(rowData.join(','));
    });
    
    // Crear y descargar el archivo
    const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `Expedientes_por_Estatus_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<style>
/* Estilos adicionales para la tabla de estatus */
.status-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.status-table th,
.status-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.status-table th {
    background-color: #f9fafb;
    font-weight: 600;
}

.status-table tbody tr:hover {
    background-color: #f9fafb;
}

.status-table tfoot {
    background-color: #f3f4f6;
}

.status-table td:nth-child(2),
.status-table th:nth-child(2) {
    width: 15%;
}

.status-table td:nth-child(3),
.status-table th:nth-child(3) {
    width: 15%;
}

.status-table td:nth-child(4),
.status-table th:nth-child(4) {
    width: 25%;
}

/* Animación para las barras de progreso */
@keyframes progressAnimation {
    0% { width: 0; }
    100% { width: 100%; }
}

.status-table .h-2\.5 div {
    animation: progressAnimation 1s ease-out;
}

/* Badge para el contador */
.badge-count {
    background-color: #e5e7eb;
    color: #4b5563;
    font-size: 0.75rem;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    font-weight: 500;
}
</style>
@endpush