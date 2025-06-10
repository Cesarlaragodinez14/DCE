<section id="siglas-tipo-accion" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Estatus de la revisión de expedientes por Tipo de Acción</h3>

    <!-- Tabla -->
    <canvas id="siglasTipoAccionChart" height="100"></canvas>
    <div id="table-siglas-tipo-accion" class="overflow-x-auto mb-4"></div>

    <!-- Gráfico -->
    <p class="text-sm text-gray-600 mt-2">
        *Color (estatus), barra (tipo de acción)
    </p>
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    // 1) Recuperar los datos procesados en backend
    const rawData = window.dashboardData?.countsBySiglasTipoAccion;
    
    // Verificar que los datos existen
    if (!rawData || Object.keys(rawData).length === 0) {
        console.error('No se encontraron datos para el gráfico de Tipo de acción');
        document.getElementById('table-siglas-tipo-accion').innerHTML = 
            '<div class="text-red-500">No hay datos disponibles para mostrar</div>';
        return;
    }
    
    console.log('Datos recibidos:', rawData);

    // 2) Transformar datos a un formato usable en la tabla y gráfica
    const processedData = Object.entries(rawData).map(([sigla, estatusMap]) => {
        const totalSigla = Object.values(estatusMap).reduce((sum, val) => sum + val, 0);
        return {
            sigla,
            total: totalSigla,
            estatusMap
        };
    }).sort((a, b) => b.total - a.total); // Ordenar de mayor a menor

    // 3) Extraer lista de Siglas y Estatus únicos
    const allSiglas = processedData.map(d => d.sigla);
    const allStatuses = [...new Set(processedData.flatMap(d => Object.keys(d.estatusMap)))];

    // 4) Construcción de la tabla con totales por Sigla y porcentajes relativos al total de la Sigla
    let tableData = [];

    processedData.forEach(item => {
        tableData.push({
            'Tipo de acción': item.sigla,
            'Estatus de la revisión del expediente': 'Total por Auditoria Especial',
            'Total': item.total,
            'Porcentaje': '100%' // La suma de cada Sigla es su 100%
        });

        Object.entries(item.estatusMap).forEach(([estatus, count]) => {
            tableData.push({
                'Tipo de acción': '',
                'Estatus de la revisión del expediente': estatus,
                'Total por tipo de acción': count,
                'Porcentaje': ((count / item.total) * 100).toFixed(2) + '%'
            });
        });
    });

    // Agregar total global
    const totalSum = processedData.reduce((sum, item) => sum + item.total, 0);
    tableData.push({
        'Tipo de acción': 'Gran Total',
        'Estatus de la revisión del expediente': '',
        'Total': totalSum,
        'Porcentaje': '100%'
    });

    // Definir la función createTable que faltaba
    function createTable(headers, data) {
        const table = document.createElement('table');
        table.className = 'min-w-full bg-white border-collapse';
        
        // Crear encabezado
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');
        
        headers.forEach(header => {
            const th = document.createElement('th');
            th.className = 'border border-gray-300 px-4 py-2 text-left bg-gray-100';
            th.textContent = header;
            headerRow.appendChild(th);
        });
        
        thead.appendChild(headerRow);
        table.appendChild(thead);
        
        // Crear cuerpo de la tabla
        const tbody = document.createElement('tbody');
        
        data.forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
            
            // Si es fila de total, darle estilo especial
            if (row['Tipo de acción'] === 'Gran Total') {
                tr.className = 'bg-gray-200 font-bold';
            }
            // Si es fila de total por sigla, darle estilo
            else if (row['Estatus de la revisión del expediente'] === 'Total por Auditoria Especial') {
                tr.className = 'bg-gray-100 font-semibold';
            }
            
            headers.forEach(header => {
                const td = document.createElement('td');
                td.className = 'border border-gray-300 px-4 py-2';
                td.textContent = row[header] || '';
                tr.appendChild(td);
            });
            
            tbody.appendChild(tr);
        });
        
        table.appendChild(tbody);
        return table;
    }

    const tableSiglasTipo = createTable(['Tipo de acción', 'Estatus de la revisión del expediente', 'Total', 'Porcentaje'], tableData);
    const tableContainer = document.getElementById('table-siglas-tipo-accion');
    if (tableContainer) {
        tableContainer.innerHTML = '';
        tableContainer.appendChild(tableSiglasTipo);
    } else {
        console.error('No se encontró el contenedor de la tabla');
    }

    // Definir la función getColor que faltaba
    function getColor(index) {
        // Lista de colores predefinidos para las diferentes categorías
        const colors = [
            '#4e73df', // Azul
            '#1cc88a', // Verde
            '#36b9cc', // Cyan
            '#f6c23e', // Amarillo
            '#e74a3b', // Rojo
            '#fd7e14', // Naranja
            '#6f42c1', // Púrpura
            '#20c9a6', // Verde teal
            '#858796', // Gris
            '#5a5c69'  // Gris oscuro
        ];
        
        // Si hay más categorías que colores, repetir los colores
        return colors[index % colors.length];
    }

    // 5) Construcción del gráfico con valores totales pero tooltips con porcentaje relativo a la Sigla
    const datasets = allStatuses.map((estatus, idx) => ({
        label: estatus,
        data: allSiglas.map(sigla => {
            const item = processedData.find(d => d.sigla === sigla);
            return item?.estatusMap[estatus] || 0;
        }),
        backgroundColor: getColor(idx)
    }));

    // 6) Crear la gráfica de barras apiladas con porcentajes en tooltips
    const ctx = document.getElementById('siglasTipoAccionChart')?.getContext('2d');
    if (!ctx) {
        console.error('No se encontró el canvas del gráfico');
        return;
    }

    try {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: allSiglas,
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const total = tooltipItem.raw;
                                const sigla = tooltipItem.label;
                                const totalSigla = processedData.find(d => d.sigla === sigla)?.total || 1;
                                const percentage = ((total / totalSigla) * 100).toFixed(2);
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
        console.log('Gráfica creada exitosamente');
    } catch (error) {
        console.error('Error al crear la gráfica:', error);
    }
});
</script>
@endpush
