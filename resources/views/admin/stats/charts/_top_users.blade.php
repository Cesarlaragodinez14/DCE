{{-- resources/views/admin/stats/charts/_dg_users_comparative.blade.php --}}
<section id="dg-users-comparative" class="mb-8">
    <h3 class="text-lg font-semibold mb-2">Cargas de trabajo por DG SEG y Jefe de Departamento</h3>

    <!-- Gráfico de barras agrupadas -->
    <canvas id="dgUsersChart" height="120"></canvas>

    <!-- Tabla -->
    <div id="table-dg-users" class="overflow-x-auto mb-4"></div>

    <p class="text-sm text-gray-600 mt-2">
        * Cada grupo de Barras representa a una DG SEG, cada color un jefe de departamento.
    </p>
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    const rawData = window.dashboardData?.dgUsersComparative;
    if (!rawData) return;

    // Función para normalizar nombres de usuarios
    function normalizeUserName(name) {
        if (!name || name.trim() === '' || name === 'null' || name === 'undefined') {
            return null;
        }
        
        return name
            .trim()
            .toLowerCase()
            // Normalizar caracteres especiales
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Remover acentos
            .replace(/ñ/g, 'n')
            .replace(/[^\w\s]/g, ' ') // Reemplazar caracteres especiales con espacios
            .replace(/\s+/g, ' ') // Múltiples espacios a uno solo
            .trim();
    }

    // Función para obtener el nombre original más completo
    function getBestOriginalName(names) {
        // Ordenar por longitud descendente y tomar el más completo
        return names
            .filter(name => name && name.trim())
            .sort((a, b) => {
                // Priorizar nombres con acentos y formato correcto
                const aHasAccents = /[áéíóúüñ]/i.test(a);
                const bHasAccents = /[áéíóúüñ]/i.test(b);
                
                if (aHasAccents && !bHasAccents) return -1;
                if (!aHasAccents && bHasAccents) return 1;
                
                // Si ambos tienen o no tienen acentos, priorizar el más largo
                return b.length - a.length;
            })[0];
    }

    // Filtrar usuarios vacíos o nulos
    const filteredData = rawData.filter(item => 
        item.user_name && 
        item.user_name.trim() !== '' && 
        item.user_name !== 'null' && 
        item.user_name !== 'undefined'
    );

    // Si no hay datos válidos, salir
    if (filteredData.length === 0) return;

    const dgGroups = {};
    let globalTotal = 0;

    // Primero, crear un mapa de nombres normalizados a nombres originales
    const userNameMap = {};
    filteredData.forEach(item => {
        const normalized = normalizeUserName(item.user_name);
        if (normalized) {
            if (!userNameMap[normalized]) {
                userNameMap[normalized] = [];
            }
            userNameMap[normalized].push(item.user_name);
        }
    });

    // Obtener el mejor nombre original para cada usuario normalizado
    const bestUserNames = {};
    Object.keys(userNameMap).forEach(normalized => {
        bestUserNames[normalized] = getBestOriginalName(userNameMap[normalized]);
    });

    // Agrupar datos por DG y Usuario (usando nombres normalizados)
    filteredData.forEach(item => {
        const dgName = item.dgseg_ef_valor;
        const normalizedUser = normalizeUserName(item.user_name);
        
        if (!normalizedUser) return;
        
        if (!dgGroups[dgName]) {
            dgGroups[dgName] = {
                total: 0,
                userMap: {}
            };
        }
        
        // Usar nombre normalizado como clave, pero mostrar el mejor nombre original
        if (!dgGroups[dgName].userMap[normalizedUser]) {
            dgGroups[dgName].userMap[normalizedUser] = 0;
        }
        
        dgGroups[dgName].userMap[normalizedUser] += item.total_changes;
        dgGroups[dgName].total += item.total_changes;
        globalTotal += item.total_changes;
    });

    // Ordenar DGs alfabéticamente
    const sortedData = Object.entries(dgGroups).map(([dg, details]) => ({
        dg,
        total: details.total,
        userMap: details.userMap
    })).sort((a, b) => a.dg.localeCompare(b.dg)); // Ordenamiento alfabético

    // Construcción de la tabla con totales y porcentajes
    let tableData = [];
    sortedData.forEach(item => {
        tableData.push({
            'DG SEG': item.dg,
            'Jefe de Departamento': 'Total por DG',
            'Total por DG SEG': item.total,
            'Porcentaje': '100%'
        });
        
        // Ordenar usuarios dentro de cada DG por total descendente
        const sortedUsers = Object.entries(item.userMap)
            .sort((a, b) => b[1] - a[1]); // Ordenar por total descendente
            
        sortedUsers.forEach(([normalizedUser, count]) => {
            tableData.push({
                'DG SEG': '',
                'Jefe de Departamento': bestUserNames[normalizedUser], // Usar el mejor nombre original
                'Total por DG SEG': count,
                'Porcentaje': ((count / item.total) * 100).toFixed(2) + '%'
            });
        });
    });

    // Agregar fila de Gran Total
    tableData.push({
        'DG SEG': 'Gran Total',
        'Jefe de Departamento': '',
        'Total por DG SEG': globalTotal,
        'Porcentaje': '100%'
    });

    // Crear tabla
    const tableDiv = document.getElementById('table-dg-users');
    if (tableDiv) {
        const tableEl = createTable(['DG SEG', 'Jefe de Departamento', 'Total', 'Porcentaje'], tableData);
        tableDiv.appendChild(tableEl);
    }

    // Construcción de la gráfica
    const allDGs = sortedData.map(d => d.dg);
    
    // Obtener todos los usuarios únicos (normalizados)
    const allNormalizedUsers = [...new Set(sortedData.flatMap(d => Object.keys(d.userMap)))];

    const datasets = allNormalizedUsers.map((normalizedUser, idx) => ({
        label: bestUserNames[normalizedUser], // Usar el mejor nombre original
        data: sortedData.map(d => d.userMap[normalizedUser] || 0),
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

    try {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: allDGs,
                datasets: datasets
            },
            options: {
                responsive: true,
                scales: {
                    y: { 
                        beginAtZero: true
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const totalValue = tooltipItem.raw;
                                const sumDG = totalByDG[tooltipItem.label];
                                const percentage = ((totalValue / sumDG) * 100).toFixed(2);
                                return `${tooltipItem.dataset.label}: ${totalValue} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error al crear la gráfica:', error);
    }
});
</script>
@endpush
