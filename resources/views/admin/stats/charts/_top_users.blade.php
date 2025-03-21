{{-- resources/views/admin/stats/charts/_dg_users_comparative.blade.php --}}
<section id="dg-users-comparative" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Expedientes por DG y Usuario (Comparativa)</h3>

    <!-- Gráfico de barras agrupadas -->
    <canvas id="dgUsersChart" height="100"></canvas>

    <!-- Tabla -->
    <div id="table-dg-users" class="overflow-x-auto mb-4"></div>

    <p class="text-sm text-gray-600 mt-2">
        * Cada color representa un Usuario, cada grupo de barras corresponde a una DG.
    </p>
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    const rawData = window.dashboardData?.dgUsersComparative;
    if (!rawData) return;

    const dgGroups = {};
    let globalTotal = 0;

    // Agrupar datos por DG y Usuario
    rawData.forEach(item => {
        const dgName = item.dgseg_ef_valor;
        if (!dgGroups[dgName]) {
            dgGroups[dgName] = {
                total: 0,
                userMap: {}
            };
        }
        dgGroups[dgName].userMap[item.user_name] = item.total_changes;
        dgGroups[dgName].total += item.total_changes;
        globalTotal += item.total_changes;
    });

    // Ordenar DGs por total descendente
    const sortedData = Object.entries(dgGroups).map(([dg, details]) => ({
        dg,
        total: details.total,
        userMap: details.userMap
    })).sort((a, b) => b.total - a.total);

    // Construcción de la tabla con totales y porcentajes
    let tableData = [];
    sortedData.forEach(item => {
        tableData.push({
            'DG': item.dg,
            'Usuario': 'Total por DG',
            'Total': item.total,
            'Porcentaje': '100%'
        });
        Object.entries(item.userMap).forEach(([user, count]) => {
            tableData.push({
                'DG': '',
                'Usuario': user,
                'Total': count,
                'Porcentaje': ((count / item.total) * 100).toFixed(2) + '%'
            });
        });
    });

    // Agregar fila de Gran Total
    tableData.push({
        'DG': 'Gran Total',
        'Usuario': '',
        'Total': globalTotal,
        'Porcentaje': '100%'
    });

    // Crear tabla
    const tableDiv = document.getElementById('table-dg-users');
    if (tableDiv) {
        const tableEl = createTable(['DG', 'Usuario', 'Total', 'Porcentaje'], tableData);
        tableDiv.appendChild(tableEl);
    }

    // Construcción de la gráfica
    const allDGs = sortedData.map(d => d.dg);
    const allUsers = [...new Set(sortedData.flatMap(d => Object.keys(d.userMap)))];

    const datasets = allUsers.map((usr, idx) => ({
        label: usr,
        data: sortedData.map(d => d.userMap[usr] || 0),
        backgroundColor: getColor(idx)
    }));

    // Crear un mapa para conocer el total de cada DG (para los tooltips)
    const totalByDG = {};
    sortedData.forEach(item => {
        totalByDG[item.dg] = item.total;
    });

    // Crear la gráfica con Chart.js
    const ctx = document.getElementById('dgUsersChart')?.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: allDGs,
            datasets: datasets
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
                            const totalValue = tooltipItem.raw;          // Valor total de ese usuario
                            const sumDG = totalByDG[tooltipItem.label];  // Total de la DG
                            const percentage = ((totalValue / sumDG) * 100).toFixed(2);
                            return `${tooltipItem.dataset.label}: ${totalValue} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
