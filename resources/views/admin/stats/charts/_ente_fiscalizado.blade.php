<section id="ente-fiscalizado" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Expedientes por Ente Fiscalizado</h3>

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

    // 3) Crear la tabla
    // Mostramos dos columnas: [Ente Fiscalizado, Total]
    const tableElement = createTable(
        ['Ente Fiscalizado', 'Total'],
        sorted.map(item => ({
            'Ente Fiscalizado': item.cat_ente_fiscalizado?.valor ?? 'Sin Datos',
            'Total': item.total
        }))
    );
    document.getElementById('table-ente-fiscalizado')?.appendChild(tableElement);

    // 4) Preparar el gráfico de barras
    const ctx = document.getElementById('enteFiscalizadoChart')?.getContext('2d');
    if(!ctx) return;

    // Eje X => nombre del Ente Fiscalizado
    // Eje Y => total
    const labels = sorted.map(i => i.cat_ente_fiscalizado?.valor ?? 'Sin Datos');
    const values = sorted.map(i => i.total);

    new Chart(ctx, {
        type: 'bar',          // CAMBIO: de "pie" a "bar"
        data: {
            labels: labels,   // array con nombres
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
