<section id="checklist-changes" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Cambios en Checklist Apartados (Por semana)</h3>
    <canvas id="checklistChangesChart" height="100"></canvas>
    <div id="table-checklist-changes" class="overflow-x-auto"></div>
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    const data = window.dashboardData?.checklistChangesByWeek;
    if(!data) return;

    // Tabla (orden descendente)
    const sorted = [...data].sort((a,b) => b.total_changes - a.total_changes);
    const table = createTable(
        ['Semana (AñoSemana)', 'Total Cambios'],
        sorted.map(item => ({
            'Semana (AñoSemana)': 'Semana ' + item.week,
            'Total Cambios': item.total_changes
        }))
    );
    document.getElementById('table-checklist-changes')?.appendChild(table);

    // Gráfico en orden cronológico
    const chronological = [...data].sort((a,b) => a.week - b.week);

    const ctx = document.getElementById('checklistChangesChart')?.getContext('2d');
    if(!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chronological.map(i => 'Semana ' + i.week),
            datasets: [{
                label: 'Cambios por semana',
                data: chronological.map(i => i.total_changes),
                backgroundColor: chronological.map((_, idx) => getColor(idx))
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
@endpush
