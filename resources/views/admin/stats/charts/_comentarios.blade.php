{{-- resources/views/admin/stats/charts/_comentarios.blade.php --}}
<section id="comentarios-before-accepted" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">
        Expedientes Aceptados que Tuvieron Devolución
    </h3>
    {{-- 
        Se pide "Desagregarlos por AE y UAA". 
        Lo guardamos en `acceptedWithDevoluciones`.
    --}}
    <canvas id="devueltosAceptadosChart" height="100"></canvas>
    <div id="table-devueltos-aceptados" class="overflow-x-auto"></div>

</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    // Obtenemos la data => "acceptedWithDevoluciones"
    const data = window.dashboardData?.acceptedWithDevoluciones;
    if(!data) return;

    // data: Array con => { auditoria_especial, uaa, total }

    // 1) Crear tabla
    //    Cabeceras: AE, UAA, Total
    const sorted = [...data].sort((a,b) => b.total - a.total);

    const table = createTable(
        ['AE','UAA','Total'],
        sorted.map(item => ({
            'AE': item.auditoria_especial ?? 'N/A',
            'UAA': item.uaa ?? 'N/A',
            'Total': item.total
        }))
    );
    document.getElementById('table-devueltos-aceptados')?.appendChild(table);

    // 2) Crear gráfico, por ejemplo "bar"
    const ctx = document.getElementById('devueltosAceptadosChart')?.getContext('2d');
    if(!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: sorted.map(i => `${i.auditoria_especial ?? 'AE?'} / ${i.uaa ?? 'UAA?'}`),
            datasets: [{
                label: 'Expedientes Aceptados con Devolución',
                data: sorted.map(i => i.total),
                backgroundColor: sorted.map((_, idx) => getColor(idx))
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
