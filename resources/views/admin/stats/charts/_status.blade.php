{{-- resources/views/admin/stats/charts/_ae_uaa_status_multiple.blade.php --}}
<section id="ae-uaa-status-multiple" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Estatus de la revisión de Expedientes de Acción por Auditoría Especial y DG.</h3>

    <!-- Contenedor principal -->
    <div id="ae-charts-container" class="space-y-8">
        <!-- Cada AE tendrá un contenedor con su <canvas> y su tabla -->
        <!-- Se llena dinámicamente con JS -->
    </div>

</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    const aeChartsData = window.dashboardData?.aeChartsData;
    if (!aeChartsData) return;

    const container = document.getElementById('ae-charts-container');
    if (!container) return;

    Object.keys(aeChartsData).forEach((aeSigla, index) => {
        // 1) Calcular total para toda la Auditoría Especial (AE)
        const dataRows = aeChartsData[aeSigla];
        const totalExp = dataRows.reduce((acc, row) => acc + row.total, 0);

        // 2) Crear contenedor y elementos para la AE
        const div = document.createElement('div');
        div.className = "border p-4 rounded-md";

        const h4 = document.createElement('h4');
        h4.className = "text-md font-semibold mb-2";
        h4.textContent = `Auditoría Especial: ${aeSigla} (Total: ${totalExp})`;
        div.appendChild(h4);

        const canvas = document.createElement('canvas');
        canvas.id = `chart-ae-${index}`;
        canvas.height = 100;
        div.appendChild(canvas);

        const tableDiv = document.createElement('div');
        tableDiv.className = "overflow-x-auto mt-2";
        div.appendChild(tableDiv);

        // Agregarlo al contenedor principal
        container.appendChild(div);

        // 3) Construir la gráfica y la tabla para esta AE
        buildAeChartAndTable(aeSigla, dataRows, canvas.id, tableDiv);
    });
});

function buildAeChartAndTable(aeSigla, dataRows, canvasId, tableContainer) {
    const processedData = {};

    // Agrupar datos por UAA y Estatus
    dataRows.forEach(row => {
        if (!processedData[row.uaa_valor]) {
            processedData[row.uaa_valor] = {
                total: 0,
                estatusMap: {}
            };
        }
        processedData[row.uaa_valor].total += row.total;
        processedData[row.uaa_valor].estatusMap[row.estatus_checklist] = row.total;
    });

    // Ordenar UAA por su total (descendente)
    const sortedData = Object.entries(processedData).map(([uaa, details]) => ({
        uaa,
        total: details.total,
        estatusMap: details.estatusMap
    })).sort((a, b) => b.total - a.total);

    // Construir la data para la tabla
    let tableData = [];
    sortedData.forEach(item => {
        // Fila de "Total por UAA"
        tableData.push({
            'UAA': item.uaa,
            'Estatus': 'Total por UAA',
            'Total': item.total,
            'Porcentaje': '100%'
        });

        // Filas por cada estatus
        Object.entries(item.estatusMap).forEach(([estatus, count]) => {
            tableData.push({
                'UAA': '',
                'Estatus': estatus,
                'Total': count,
                'Porcentaje': ((count / item.total) * 100).toFixed(2) + '%'
            });
        });
    });

    // Fila de "Gran Total" (sumatoria de todas las UAA de esta AE)
    const totalSum = sortedData.reduce((sum, item) => sum + item.total, 0);
    tableData.push({
        'UAA': 'Gran Total',
        'Estatus': '',
        'Total': totalSum,
        'Porcentaje': '100%'
    });

    // Crear la tabla con la columna "Porcentaje"
    const tableEl = createTable(['UAA', 'Estatus', 'Total', 'Porcentaje'], tableData);
    tableContainer.appendChild(tableEl);

    // Preparar datos para la gráfica
    const allUaas = sortedData.map(d => d.uaa);
    const allStatuses = [...new Set(sortedData.flatMap(d => Object.keys(d.estatusMap)))];

    const datasets = allStatuses.map((estatus, idx) => ({
        label: estatus,
        data: sortedData.map(d => d.estatusMap[estatus] || 0),
        backgroundColor: getColor(idx)
    }));

    // Crear un mapa para conocer el total de cada UAA (para los tooltips)
    const totalByUaa = {};
    sortedData.forEach(item => {
        totalByUaa[item.uaa] = item.total;
    });

    // Crear la gráfica con Chart.js
    const ctx = document.getElementById(canvasId)?.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: allUaas,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const totalValue = tooltipItem.raw;             // Valor total de ese estatus
                            const sumUaa = totalByUaa[tooltipItem.label];    // Total de la UAA
                            const percentage = ((totalValue / sumUaa) * 100).toFixed(2);
                            return `${tooltipItem.dataset.label}: ${totalValue} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}
</script>
@endpush
