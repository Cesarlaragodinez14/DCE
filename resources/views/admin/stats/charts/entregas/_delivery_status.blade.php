{{-- resources/views/admin/stats/charts/_delivery_status.blade.php --}}
<section id="delivery-status" class="mb-8 bg-white rounded-lg shadow-md p-5" style="padding: 20px">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-gray-800">Estatus de la entrega de expedientes de Acción.</h3>
        <div class="flex space-x-2">
            <button id="viewDoughnutBtn" class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Gráfico</button>
            <button id="viewTableBtn" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">Tabla</button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500 flex flex-col">
            <span class="text-sm text-gray-500">Aceptados</span>
            <div class="flex items-center mt-1">
                <span id="deliveredCount" class="text-2xl font-bold text-blue-600">0</span>
                <span id="deliveredPercent" class="ml-2 text-sm font-medium text-blue-800 bg-blue-100 px-2 py-0.5 rounded-full">0%</span>
            </div>
        </div>
        
        <div class="bg-amber-50 rounded-lg p-4 border-l-4 border-amber-500 flex flex-col">
            <span class="text-sm text-gray-500">En Proceso de Aceptación</span>
            <div class="flex items-center mt-1">
                <span id="inProcessCount" class="text-2xl font-bold text-amber-600">0</span>
                <span id="inProcessPercent" class="ml-2 text-sm font-medium text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full">0%</span>
            </div>
        </div>
        
        <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-500 flex flex-col">
            <span class="text-sm text-gray-500">No Entregados</span>
            <div class="flex items-center mt-1">
                <span id="unscheduledCount" class="text-2xl font-bold text-red-600">0</span>
                <span id="unscheduledPercent" class="ml-2 text-sm font-medium text-red-800 bg-red-100 px-2 py-0.5 rounded-full">0%</span>
            </div>
        </div>
    </div>
    
    <div id="chartContainer" class="relative" style="height: 250px;">
        <canvas id="deliveryStatusChart"></canvas>
    </div>
    
    <div id="tableContainer" class="hidden overflow-hidden rounded-lg border border-gray-200 mt-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Porcentaje</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="deliveryStatusTable">
                <!-- Los datos se llenarán con JavaScript -->
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th scope="col" id="totalCount" class="px-6 py-3 text-left text-xs font-medium text-gray-700">0</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700">100%</th>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <p class="text-sm text-gray-600 mt-3 italic">
    </p>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos
    const data = window.dashboardData?.deliveryStatus;
    if (!data) return;
    
    // Configuración de colores
    const colors = {
        delivered: {
            bg: '#3b82f6',
            border: '#2563eb',
            bgLight: '#93c5fd'
        },
        inProcess: {
            bg: '#f59e0b',
            border: '#d97706',
            bgLight: '#fcd34d'
        },
        unscheduled: {
            bg: '#ef4444',
            border: '#dc2626',
            bgLight: '#fca5a5'
        }
    };

    // Actualizar contadores y porcentajes
    updateSummaryCards(data);
    
    // Llenar la tabla
    populateTable(data, colors);
    
    // Crear el gráfico
    createChart(data, colors);
    
    // Configurar la funcionalidad de cambio entre gráfico y tabla
    setupViewToggle();
    
    // Función para actualizar las tarjetas de resumen
    function updateSummaryCards(data) {
        // Actualizar contadores 
        document.getElementById('deliveredCount').textContent = data.delivered.toLocaleString();
        document.getElementById('inProcessCount').textContent = data.in_process.toLocaleString();
        document.getElementById('unscheduledCount').textContent = data.unscheduled.toLocaleString();
        document.getElementById('totalCount').textContent = data.total.toLocaleString();
        
        // Calcular porcentajes
        const deliveredPercent = (data.delivered / data.total * 100).toFixed(1);
        const inProcessPercent = (data.in_process / data.total * 100).toFixed(1);
        const unscheduledPercent = (data.unscheduled / data.total * 100).toFixed(1);
        
        // Actualizar badges de porcentaje
        document.getElementById('deliveredPercent').textContent = `${deliveredPercent}%`;
        document.getElementById('inProcessPercent').textContent = `${inProcessPercent}%`;
        document.getElementById('unscheduledPercent').textContent = `${unscheduledPercent}%`;
    }
    
    // Función para llenar la tabla
    function populateTable(data, colors) {
        const tableBody = document.getElementById('deliveryStatusTable');
        tableBody.innerHTML = ''; // Limpiar tabla
        
        // Crear filas con datos
        const statusData = [
            {
                name: 'Aceptados',
                value: data.delivered,
                color: colors.delivered.bg
            },
            {
                name: 'En Proceso de Aceptación',
                value: data.in_process,
                color: colors.inProcess.bg
            },
            {
                name: 'No Entregados',
                value: data.unscheduled,
                color: colors.unscheduled.bg
            }
        ];
        
        // Agregar cada fila a la tabla
        statusData.forEach(item => {
            const percent = (item.value / data.total * 100).toFixed(1);
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <span class="h-3 w-3 rounded-full mr-2" style="background-color: ${item.color};"></span>
                        <div class="text-sm font-medium text-gray-900">${item.name}</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.value.toLocaleString()}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${percent}%</td>
            `;
            tableBody.appendChild(row);
        });
    }
    
    // Función para crear el gráfico
    function createChart(data, colors) {
        const ctx = document.getElementById('deliveryStatusChart').getContext('2d');
        
        // Crear el gráfico
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Aceptados', 'En Proceso de Aceptación', 'No Entregados'],
                datasets: [{
                    data: [data.delivered, data.in_process, data.unscheduled],
                    backgroundColor: [
                        colors.delivered.bg,
                        colors.inProcess.bg,
                        colors.unscheduled.bg
                    ],
                    borderColor: [
                        colors.delivered.border,
                        colors.inProcess.border,
                        colors.unscheduled.border
                    ],
                    borderWidth: 1,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            boxWidth: 12,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        padding: 10,
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Función para configurar la funcionalidad de cambio entre gráfico y tabla
    function setupViewToggle() {
        const viewDoughnutBtn = document.getElementById('viewDoughnutBtn');
        const viewTableBtn = document.getElementById('viewTableBtn');
        const chartContainer = document.getElementById('chartContainer');
        const tableContainer = document.getElementById('tableContainer');
        
        // Función para cambiar a vista de gráfico
        viewDoughnutBtn.addEventListener('click', function() {
            // Mostrar gráfico, ocultar tabla
            chartContainer.classList.remove('hidden');
            tableContainer.classList.add('hidden');
            
            // Actualizar estilo de botones
            viewDoughnutBtn.classList.remove('bg-gray-200', 'text-gray-700');
            viewDoughnutBtn.classList.add('bg-blue-600', 'text-white');
            
            viewTableBtn.classList.remove('bg-blue-600', 'text-white');
            viewTableBtn.classList.add('bg-gray-200', 'text-gray-700');
        });
        
        // Función para cambiar a vista de tabla
        viewTableBtn.addEventListener('click', function() {
            // Mostrar tabla, ocultar gráfico
            chartContainer.classList.add('hidden');
            tableContainer.classList.remove('hidden');
            
            // Actualizar estilo de botones
            viewDoughnutBtn.classList.remove('bg-blue-600', 'text-white');
            viewDoughnutBtn.classList.add('bg-gray-200', 'text-gray-700');
            
            viewTableBtn.classList.remove('bg-gray-200', 'text-gray-700');
            viewTableBtn.classList.add('bg-blue-600', 'text-white');
        });
    }
});
</script>
@endpush