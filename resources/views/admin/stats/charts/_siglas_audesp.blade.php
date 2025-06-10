{{-- resources/views/admin/stats/charts/_siglas_audesp.blade.php --}}
<section id="siglas-audesp" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">
        Estatus de la revisión de Expedientes de Acción por Auditoría Especial
    </h3>

    <!-- Gráfico -->
    <canvas id="siglasAudEspChart" height="100"></canvas>
    
    <!-- Tabla (opcional) -->
    <div id="table-siglas-audesp" class="overflow-x-auto mt-4"></div>
    
    <p class="text-sm text-gray-600 mt-2">
    </p>
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    // 1) Recuperar data procesada desde el backend.
    // Se espera un array de objetos con:
    // siglas_auditoria_especial, estatus_checklist, total, catSiglasAuditoriaEspecial
    const rawData = window.dashboardData?.countsBySiglasAuditoriaEspecialEstatus;
    if (!rawData) return;

    // 2) Crear un "grouping" que sume los totales para cada combinación de sigla y estatus.
    const grouping = {};
    const statusSet = new Set();
    rawData.forEach(item => {
        // Usamos la propiedad correcta: "catSiglasAuditoriaEspecial" (camelCase)
        const siglaName = item.catSiglasAuditoriaEspecial?.valor ?? 'Sin Datos';
        const estatus   = item.estatus_checklist;
        const total     = parseInt(item.total);
        
        if (!grouping[siglaName]) {
            grouping[siglaName] = {};
        }
        // Sumar los totales por estatus
        grouping[siglaName][estatus] = (grouping[siglaName][estatus] || 0) + total;
        statusSet.add(estatus);
    });

    // 3) Preparar datos para la tabla con totales
    const sortedData = Object.entries(grouping).map(([sigla, estatusMap]) => {
        const totalSigla = Object.values(estatusMap).reduce((sum, val) => sum + val, 0);
        return { sigla, total: totalSigla, estatusMap };
    }).sort((a, b) => b.total - a.total);

    // Calcular total global
    let globalTotal = 0;
    sortedData.forEach(item => {
        globalTotal += item.total;
    });

    // Construir la estructura para la tabla: se agrega una fila por cada sigla con su total y luego las filas por cada estatus
    let tableData = [];
    sortedData.forEach(item => {
        tableData.push({
            'Auditoria Especial': item.sigla,
            'Estatus de la revisión': 'Total por Auditoria Especial',
            'Total de Auditoria Especial': item.total,
            'Porcentaje': '100%'
        });
        Object.entries(item.estatusMap).forEach(([estatus, count]) => {
            tableData.push({
                'Siglas Auditoría Especial': '',
                'Estatus de la revisión': estatus,
                'Total de Auditoria Especial': count,
                'Porcentaje': ((count / item.total) * 100).toFixed(2) + '%'
            });
        });
    });
    // Agregar fila de Gran Total
    tableData.push({
        'Siglas Auditoría Especial': 'Gran Total',
        'Estatus de la revisión': '',
        'Total de Auditoria Especial': globalTotal,
        'Porcentaje': '100%'
    });

    // 4) Si se dispone de una función global "createTable", crear la tabla y agregarla al contenedor.
    const tableContainer = document.getElementById('table-siglas-audesp');
    if (tableContainer && typeof createTable === 'function') {
        const tableEl = createTable(['Siglas Auditoría Especial', 'Estatus', 'Total de Auditoria Especial', 'Porcentaje'], tableData);
        tableContainer.appendChild(tableEl);
    }

    // 5) Preparar los datos para la gráfica de barras apiladas.
    const allSiglas = sortedData.map(d => d.sigla); // etiquetas del eje X
    const allStatuses = [...statusSet]; // lista de estatus

    const datasets = allStatuses.map((estatus, idx) => {
        return {
            label: estatus,
            data: sortedData.map(d => d.estatusMap[estatus] || 0),
            backgroundColor: getColor(idx) // se asume que getColor está definida globalmente
        };
    });

    // 6) Crear la gráfica con Chart.js
    const ctx = document.getElementById('siglasAudEspChart')?.getContext('2d');
    if (!ctx) return;

    // Creamos la gráfica
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: allSiglas,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const value = tooltipItem.raw;
                            // Obtenemos el total de la sigla (evitamos división por cero)
                            const totalForSigla = sortedData.find(d => d.sigla === tooltipItem.label)?.total || 1;
                            const percentage = ((value / totalForSigla) * 100).toFixed(2);
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
