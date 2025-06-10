{{-- resources/views/admin/stats/charts/_dgseg_ef.blade.php --}}
<section id="dgseg-ef" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Estatus de la revisión de expedientes de acción por DGS</h3>
    <canvas id="dgsegEfChart" height="100"></canvas>
    <div id="table-dgseg-ef" class="overflow-x-auto mt-4"></div>
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    const data = window.dashboardData?.countsByDgsegEf;
    if (!data) return;

    // 1. Agrupar datos por DG SEG y Estatus
    const processedData = {};
    const statusSet = new Set();
    const totalSumByDgsegEf = {}; // Para almacenar el total de cada DG SEG

    data.forEach(item => {
        const dgsegEf = item.dgseg_ef_valor;
        const estatus = item.estatus_checklist;
        const total = item.total;

        if (!processedData[dgsegEf]) {
            processedData[dgsegEf] = {};
            totalSumByDgsegEf[dgsegEf] = 0;
        }
        processedData[dgsegEf][estatus] = (processedData[dgsegEf][estatus] || 0) + total;
        totalSumByDgsegEf[dgsegEf] += total; // Sumar total de cada DG SEG
        statusSet.add(estatus);
    });

    const allDgsegEfs = Object.keys(processedData);
    const allStatuses = [...statusSet];

    // 2. Construcción de la tabla con totales por DG SEG y porcentaje por DG SEG
    let tableData = [];

    allDgsegEfs.forEach(dgsegEf => {
        const totalDgsegEf = totalSumByDgsegEf[dgsegEf];

        // Agregar fila de total por DG SEG antes de los detalles
        tableData.push({
            'DG SEG': dgsegEf,
            'Estatus de la revisión': 'Total por DG SEG',
            'Total de Expedientes': totalDgsegEf,
            'Porcentaje': '100%'
        });

        allStatuses.forEach(estatus => {
            const totalEstatus = processedData[dgsegEf][estatus] || 0;
            tableData.push({
                'DG SEG': '',
                'Estatus de la revisión': estatus,
                'Total de Expedientes de acción': totalEstatus,
                'Porcentaje': ((totalEstatus / totalDgsegEf) * 100).toFixed(2) + '%'
            });
        });
    });

    // Generar la tabla utilizando createTable()
    const table = createTable(['DG SEG', 'Estatus de la revisión', 'Total de Expedientes de acción', 'Porcentaje'], tableData);
    document.getElementById('table-dgseg-ef')?.appendChild(table);

    // 3. Construcción del gráfico apilado **con valores totales** pero mostrando porcentaje en tooltip
    const datasets = allStatuses.map((estatus, idx) => ({
        label: estatus,
        data: allDgsegEfs.map(dgsegEf => processedData[dgsegEf][estatus] || 0),
        backgroundColor: getColor(idx)
    }));

    // 4. Crear gráfico con Chart.js mostrando valores totales pero con porcentaje en tooltip
    const ctx = document.getElementById('dgsegEfChart')?.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: allDgsegEfs,
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const total = tooltipItem.raw;
                            const totalDgsegEf = totalSumByDgsegEf[tooltipItem.label];
                            const percentage = ((total / totalDgsegEf) * 100).toFixed(2);
                            return `${tooltipItem.dataset.label}: ${total} (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true }
            }
        }
    });
});
</script>
@endpush
