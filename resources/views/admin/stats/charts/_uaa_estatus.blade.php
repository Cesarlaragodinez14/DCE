{{-- resources/views/admin/stats/charts/_uaa_estatus.blade.php --}}
<section id="uaa-estatus" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Estatus de la revisión de Expedientes de Acción por DG de las UAA´s</h3>
    <canvas id="uaaEstatusChart" height="100"></canvas>
    <div id="table-uaa-estatus" class="overflow-x-auto mt-4"></div>
    <p class="text-sm text-gray-600 mt-2">
        * Cada color representa un estatus, cada barra una UAA.
    </p>
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    // 1) Recuperar data (se espera que desde el backend se envíe en window.dashboardData.countsByUaaAndStatus)
    const rawData = window.dashboardData?.countsByUaaAndStatus;
    if (!rawData) return;

    // 2) Crear un "grouping" que sume los totales para cada UAA y cada estatus.
    const uaaGroups = {};
    let globalTotal = 0;
    rawData.forEach(item => {
        // Se espera que la relación 'catUaa' tenga la propiedad 'nombre' o 'valor'
        const uaaName = item.catUaa?.nombre ?? item.catUaa?.valor ?? 'Sin Datos';
        const estatus = item.estatus_checklist;
        const total = parseInt(item.total);

        globalTotal += total;

        if (!uaaGroups[uaaName]) {
            uaaGroups[uaaName] = {
                total: 0,
                estatusMap: {}
            };
        }
        // Sumar si ya existe; de lo contrario, asignar
        uaaGroups[uaaName].estatusMap[estatus] = (uaaGroups[uaaName].estatusMap[estatus] || 0) + total;
        uaaGroups[uaaName].total += total;
    });

    // 3) Ordenar los datos por total descendente
    const sortedData = Object.entries(uaaGroups).map(([uaa, details]) => ({
        uaa,
        total: details.total,
        estatusMap: details.estatusMap
    })).sort((a, b) => b.total - a.total);

    // Crear un mapa para el total por UAA (para los tooltips)
    const totalByUaa = {};
    sortedData.forEach(item => {
        totalByUaa[item.uaa] = item.total;
    });

    // 4) Construir la tabla con totales por UAA y porcentajes
    let tableData = [];
    sortedData.forEach(item => {
        tableData.push({
            'UAA': item.uaa,
            'Estatus': 'Total por UAA',
            'Total': item.total,
            'Porcentaje': '100%'
        });
        Object.entries(item.estatusMap).forEach(([estatus, count]) => {
            tableData.push({
                'UAA': '',
                'Estatus': estatus,
                'Total': count,
                'Porcentaje': ((count / item.total) * 100).toFixed(2) + '%'
            });
        });
    });
    tableData.push({
        'UAA': 'Gran Total',
        'Estatus': '',
        'Total': globalTotal,
        'Porcentaje': '100%'
    });

    const tableContainer = document.getElementById('table-uaa-estatus');
    if (tableContainer && typeof createTable === 'function') {
        const tableEl = createTable(['UAA', 'Estatus', 'Total', 'Porcentaje'], tableData);
        tableContainer.appendChild(tableEl);
    }

    // 5) Preparar los datos para la gráfica de barras apiladas
    const allUaas = sortedData.map(d => d.uaa);
    const allStatuses = [...new Set(sortedData.flatMap(d => Object.keys(d.estatusMap)))];

    const datasets = allStatuses.map((estatus, idx) => ({
        label: estatus,
        data: sortedData.map(d => d.estatusMap[estatus] || 0),
        backgroundColor: getColor(idx)
    }));

    // 6) Crear la gráfica con Chart.js
    const ctx = document.getElementById('uaaEstatusChart')?.getContext('2d');
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
                title: {
                    display: true,
                    text: `Estatus de la revisión de Expedientes de Acción por DG de las UAA´s`
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const value = tooltipItem.raw;
                            const totalForUaa = totalByUaa[tooltipItem.label] || 1; // Evitar división por cero
                            const percentage = ((value / totalForUaa) * 100).toFixed(2);
                            return `${tooltipItem.dataset.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
