<section id="estatus-ae-uaa" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Expedientes por Estatus, AE y UAA</h3>
    <canvas id="chartEstatusAeUaa" height="100"></canvas>
    <div id="table-estatus-ae-uaa" class="overflow-x-auto"></div>
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    const data = window.dashboardData?.countsByStatusAeUaa;
    if(!data) return;

    // Calcular el gran total
    const grandTotal = data.reduce((sum, item) => sum + item.total, 0);
    
    // Ordenar los datos de mayor a menor
    const sorted = [...data].sort((a,b) => b.total - a.total);

    // Formatear datos con porcentaje
    const formattedData = sorted.map(item => {
        const percentage = ((item.total / grandTotal) * 100).toFixed(2) + '%';
        return {
            'Estatus': item.estatus_checklist,
            'AE': item.auditoria_especial ?? 'N/A',
            'UAA': item.uaa ?? 'N/A',
            'Total': `${item.total} - ${percentage}`
        };
    });

    // Crear y agregar la tabla
    const table = createTable(['Estatus', 'AE', 'UAA', 'Total'], formattedData);
    document.getElementById('table-estatus-ae-uaa')?.appendChild(table);

    // Gráfico
    const ctx = document.getElementById('chartEstatusAeUaa')?.getContext('2d');
    if(!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: sorted.map(i => i.auditoria_especial + '/' + i.uaa),
            datasets: [{
                label: 'Total',
                data: sorted.map(i => i.total),
                backgroundColor: sorted.map((_, idx) => getColor(idx))
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const value = tooltipItem.raw;
                            const percentage = ((value / grandTotal) * 100).toFixed(2);
                            return `${value} - ${percentage}%`;
                        }
                    }
                }
            }
        }
    });
});

</script>
@endpush
