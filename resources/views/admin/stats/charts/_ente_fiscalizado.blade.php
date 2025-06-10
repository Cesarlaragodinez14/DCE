<section id="ente-fiscalizado" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Expedientes de Acción por Ente Fiscalizado</h3>

    <!-- Gráfico -->
    <canvas id="enteFiscalizadoChart" height="100"></canvas>

    <!-- Tabla -->
    <div id="table-ente-fiscalizado" class="overflow-x-auto mb-4"></div>
    
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    // 1) Recuperar data
    const data = window.dashboardData?.countsByEnteFiscalizado;
    if(!data) return;

    // 2) Ordenar desc por total, para que la barra con mayor valor aparezca primero
    const sorted = [...data].sort((a, b) => b.total - a.total);

    // 3) Crear la tabla con TODOS los datos
    // Mostramos dos columnas: [Ente Fiscalizado, Total]
    const tableElement = createTable(
        ['Ente Fiscalizado', 'Total'],
        sorted.map(item => ({
            'Ente Fiscalizado': item.cat_ente_fiscalizado?.valor ?? 'Sin Datos',
            'Total': item.total
        }))
    );
    document.getElementById('table-ente-fiscalizado')?.appendChild(tableElement);

    // 4) Preparar el gráfico de barras con SOLO LOS 10 PRIMEROS
    const ctx = document.getElementById('enteFiscalizadoChart')?.getContext('2d');
    if(!ctx) return;

    // Limitar a los 10 primeros elementos para el gráfico
    const sortedTop10 = sorted.slice(0, 10);

    // Eje X => nombre del Ente Fiscalizado (solo top 10)
    // Eje Y => total (solo top 10)
    const labels = sortedTop10.map(i => i.cat_ente_fiscalizado?.valor ?? 'Sin Datos');
    const values = sortedTop10.map(i => i.total);

    new Chart(ctx, {
        type: 'bar',          // CAMBIO: de "pie" a "bar"
        data: {
            labels: labels,   // array con nombres (solo top 10)
            datasets: [{
                label: 'Total de Expedientes',
                data: values,
                backgroundColor: labels.map((_, idx) => getColor(idx))
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { 
                    beginAtZero: true 
                }
            }
        }
    });
});
</script>
@endpush
