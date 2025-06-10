<section id="auditorias-changes" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Movimientos en el sistema por día</h3>
    <canvas id="auditoriasChangesChart" height="100"></canvas>
    <div id="table-auditorias-changes" class="overflow-x-auto"></div>
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    const data = window.dashboardData?.auditoriasChangesByDay;
    if(!data) return;

    // Tabla (orden descendente por total_changes)
    const sorted = [...data].sort((a,b) => b.total_changes - a.total_changes);

    const table = createTable(
        ['Fecha', 'Total Cambios'],
        sorted.map(item => ({
            'Fecha': item.date,
            'Total Cambios': item.total_changes
        }))
    );
    document.getElementById('table-auditorias-changes')?.appendChild(table);

    // Gráfico de línea en orden cronológico
    const chronological = [...data].sort((a,b) => (a.date > b.date ? 1 : -1));

    const ctx = document.getElementById('auditoriasChangesChart')?.getContext('2d');
    if(!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chronological.map(i => i.date),
            datasets: [{
                label: 'Cambios por día',
                data: chronological.map(i => i.total_changes),
                backgroundColor: 'rgba(54,162,235,0.2)',
                borderColor: 'rgba(54,162,235,1)',
                fill: true,
                tension: 0.1
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
