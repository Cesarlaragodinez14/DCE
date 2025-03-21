{{-- resources/views/admin/stats/charts/_dgseg_ef.blade.php --}}
<section id="dgseg-ef" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Expedientes por Dirección General de Seguimiento</h3>
    <canvas id="dgsegEfChart" height="100"></canvas>
    <div id="table-dgseg-ef" class="overflow-x-auto mt-4"></div>
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    const data = window.dashboardData?.countsByDgsegEf;
    if (!data) return;

    // 1. Agrupar datos por DGSEG EF y Estatus
    const processedData = {};
    const statusSet = new Set();
    const totalSumByDgsegEf = {}; // Para almacenar el total de cada DGSEG EF

    data.forEach(item => {
        const dgsegEf = item.dgseg_ef_valor;
        const estatus = item.estatus_checklist;
        const total = item.total;

        if (!processedData[dgsegEf]) {
            processedData[dgsegEf] = {};
            totalSumByDgsegEf[dgsegEf] = 0;
        }
        processedData[dgsegEf][estatus] = (processedData[dgsegEf][estatus] || 0) + total;
        totalSumByDgsegEf[dgsegEf] += total; // Sumar total de cada DGSEG EF
        statusSet.add(estatus);
    });

    const allDgsegEfs = Object.keys(processedData);
    const allStatuses = [...statusSet];

    // 2. Construcción de la tabla con totales por DGSEG EF y porcentaje por DGSEG EF
    let tableData = [];

    allDgsegEfs.forEach(dgsegEf => {
        const totalDgsegEf = totalSumByDgsegEf[dgsegEf];

        // Agregar fila de total por DGSEG EF antes de los detalles
        tableData.push({
            'DGSEG EF': dgsegEf,
            'Estatus': 'Total por DGSEG EF',
            'Total': totalDgsegEf,
            'Porcentaje': '100%'
        });

        allStatuses.forEach(estatus => {
            const totalEstatus = processedData[dgsegEf][estatus] || 0;
            tableData.push({
                'DGSEG EF': '',
                'Estatus': estatus,
                'Total': totalEstatus,
                'Porcentaje': ((totalEstatus / totalDgsegEf) * 100).toFixed(2) + '%'
            });
        });
    });

    // Generar la tabla utilizando createTable()
    const table = createTable(['DGSEG EF', 'Estatus', 'Total', 'Porcentaje'], tableData);
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
                title: {
                    display: true,
                    text: `Expedientes por DGSEG EF`
                },
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
