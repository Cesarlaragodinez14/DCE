{{-- resources/views/admin/dashboard.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Estadísticas Generales de Expedientes') }}
        </h2>
    </x-slot>

    <style>
        /* Navbar */
        .navbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #ffffff;
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            height: 60px;
            display: flex;
            align-items: center;
        }

        /* Contenedor de la Navbar */
        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            padding: 0 10px;
            display: flex;
            justify-content: center;
        }

        /* Enlaces de la Navbar */
        .navbar-links {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            width: 100%;
            overflow-x: auto; /* Permite desplazamiento horizontal si es necesario */
        }

        .navbar-links::-webkit-scrollbar {
            display: none; /* Oculta la barra de desplazamiento */
        }

        .navbar-links {
            -ms-overflow-style: none;  /* IE y Edge */
            scrollbar-width: none;  /* Firefox */
        }

        /* Cada Enlace */
        .navbar-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6B7280; /* Color gris */
            text-decoration: none;
            transition: color 0.3s ease;
            flex-shrink: 0; /* Evita que los enlaces se reduzcan */
            padding: 0 10px; /* Espacio entre enlaces */
        }

        /* Iconos */
        .navbar-icon {
            font-size: 24px;
            margin-bottom: 4px;
            transition: color 0.3s ease;
            color: inherit;
        }

        /* Texto de los Enlaces */
        .navbar-text {
            font-size: 12px;
            text-align: center;
            white-space: nowrap; /* Previene que el texto se divida en varias líneas */
            overflow: hidden;
            text-overflow: ellipsis; /* Añade puntos suspensivos si el texto es demasiado largo */
            max-width: 70px; /* Ancho máximo del texto */
        }

        /* Hover Effect */
        .navbar-link:hover {
            color: #3B82F6; /* Azul */
        }

        /* Sección Activa */
        .navbar-link.active {
            color: #3B82F6; /* Azul */
            font-weight: bold;
        }

        .navbar-link.active .navbar-icon {
            color: #3B82F6;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            .navbar {
                height: 55px;
            }

            .navbar-icon {
                font-size: 20px;
            }

            .navbar-text {
                font-size: 10px;
                max-width: 60px;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                height: 50px;
            }

            .navbar-icon {
                font-size: 18px;
            }

            .navbar-text {
                font-size: 8px;
                max-width: 50px;
            }
        }
    </style>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-8">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.link href="/dashboard">Dashboard</x-ui.breadcrumbs.link>
            <x-ui.breadcrumbs.separator />
            <x-ui.breadcrumbs.link active>Estadísticas</x-ui.breadcrumbs.link>
        </x-ui.breadcrumbs>

        {{-- Contenedores para las estadísticas --}}
        <div>
            <!-- 1. Expedientes por Estatus -->
            <section id="estatus">
                <h3 class="text-lg font-semibold mb-2">Expedientes por Estatus</h3>
                <div id="table-status" class="overflow-x-auto"></div>
                <canvas id="statusChart" height="100"></canvas>
            </section>

            <!-- 2. Expedientes por Ente Fiscalizado -->
            <section id="ente-fiscalizado">
                <h3 class="text-lg font-semibold mb-2">Expedientes por Ente Fiscalizado</h3>
                <div id="table-ente-fiscalizado" class="overflow-x-auto"></div>
                <canvas id="enteFiscalizadoChart" height="100"></canvas>
            </section>

            <!-- 3. Expedientes por Auditoría Especial -->
            <section id="auditoria-especial">
                <h3 class="text-lg font-semibold mb-2">Expedientes de Acción por Número de Auditoria</h3>
                <div id="table-auditoria-especial" class="overflow-x-auto"></div>
                <canvas id="auditoriaEspecialChart" height="100"></canvas>
            </section>

            <!-- 4. Expedientes por Siglas de Auditoría Especial -->
            <section id="siglas-audesp">
                <h3 class="text-lg font-semibold mb-2">Expedientes por Siglas de Auditoría Especial</h3>
                <div id="table-siglas-audesp" class="overflow-x-auto"></div>
                <canvas id="siglasAudEspChart" height="100"></canvas>
            </section>

            <!-- 5. Expedientes por Siglas Tipo Acción -->
            <section id="siglas-tipo-accion">
                <h3 class="text-lg font-semibold mb-2">Expedientes por Siglas Tipo Acción</h3>
                <div id="table-siglas-tipo-accion" class="overflow-x-auto"></div>
                <canvas id="siglasTipoAccionChart" height="100"></canvas>
            </section>

            <!-- 6. Expedientes por DGSEG EF -->
            <section id="dgseg-ef">
                <h3 class="text-lg font-semibold mb-2">Expedientes por Dirección General de Seguimiento</h3>
                <div id="table-dgseg-ef" class="overflow-x-auto"></div>
                <canvas id="dgsegEfChart" height="100"></canvas>
            </section>

            <!-- 7. Expedientes con Comentarios antes de ser Aceptadas -->
            <section id="comentarios-before-accepted">
                <h3 class="text-lg font-semibold mb-2">Expedientes con Comentarios antes de ser Aceptadas</h3>
                <p id="comentariosBeforeAccepted" class="text-xl font-bold"></p>
            </section>

            <!-- 8. Expedientes por UAA y Estatus -->
            <section id="uaa-estatus">
                <h3 class="text-lg font-semibold mb-2">Expedientes por UAA y Estatus</h3>
                <div id="table-uaa-estatus" class="overflow-x-auto"></div>
                <canvas id="uaaEstatusChart" height="100"></canvas>
                <p class="text-sm text-gray-600">* Cada color representa un estatus, cada barra una UAA.</p>
            </section>

            <!-- 9. Cambios en Expedientes (Últimos 30 días) -->
            <section id="auditorias-changes">
                <h3 class="text-lg font-semibold mb-2">Cambios en Expedientes (Últimos 30 días)</h3>
                <div id="table-auditorias-changes" class="overflow-x-auto"></div>
                <canvas id="auditoriasChangesChart" height="100"></canvas>
            </section>

            <!-- 10. Cambios en Checklist Apartados (Por semana) -->
            <section id="checklist-changes">
                <h3 class="text-lg font-semibold mb-2">Cambios en Checklist Apartados (Por semana)</h3>
                <div id="table-checklist-changes" class="overflow-x-auto"></div>
                <canvas id="checklistChangesChart" height="100"></canvas>
            </section>

            <!-- 11. Top 5 Usuarios con Más Cambios en Expedientes -->
            <section id="top-users">
                <h3 class="text-lg font-semibold mb-2">Usuarios con Más Cambios en Expedientes</h3>
                <div id="table-top-users" class="overflow-x-auto"></div>
                <canvas id="topUsersChart" height="100"></canvas>
            </section>

            <!-- 12. Campos más Modificados en Checklist Apartados -->
            <section id="campos-modificados">
                <h2 class="text-xl font-semibold mb-4">Apartados más Modificados</h2>
                <div class="overflow-x-auto">
                    <table id="table-fields-changes" class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                        <thead>
                        </thead>
                        <tbody>
                            <!-- La tabla se llenará dinámicamente con JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    <h3 class="text-lg font-medium mb-2">Gráfico de Cambios en Apartados</h3>
                    <canvas id="fieldsChangesChart" height="100"></canvas>
                </div>
            </section>
        </div>
        <!-- Barra de Navegación Fija en la Parte Inferior -->
        <nav aria-label="Barra de navegación principal" class="navbar">
            <div class="navbar-container">
                <div class="navbar-links">
                    <a href="#estatus" aria-label="Ir a Expedientes por Estatus" class="navbar-link">
                        <ion-icon name="stats-chart-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Estatus</span>
                    </a>
                    <a href="#ente-fiscalizado" aria-label="Ir a Expedientes por Ente Fiscalizado" class="navbar-link">
                        <ion-icon name="business-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Ente Fiscalizado</span>
                    </a>
                    <a href="#auditoria-especial" aria-label="Ir a Auditoría Especial" class="navbar-link">
                        <ion-icon name="clipboard-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Auditoría</span>
                    </a>
                    <a href="#siglas-audesp" aria-label="Ir a Siglas AudEsp" class="navbar-link">
                        <ion-icon name="short-text-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Siglas AudEsp</span>
                    </a>
                    <a href="#siglas-tipo-accion" aria-label="Ir a Siglas Tipo Acción" class="navbar-link">
                        <ion-icon name="text-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Tipo Acción</span>
                    </a>
                    <a href="#dgseg-ef" aria-label="Ir a DGSEG EF" class="navbar-link">
                        <ion-icon name="people-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">DGSEG EF</span>
                    </a>
                    <a href="#comentarios-before-accepted" aria-label="Ir a Comentarios antes de ser Aceptadas" class="navbar-link">
                        <ion-icon name="chatbubbles-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Comentarios</span>
                    </a>
                    <a href="#uaa-estatus" aria-label="Ir a UAA y Estatus" class="navbar-link">
                        <ion-icon name="school-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">UAA y Estatus</span>
                    </a>
                    <a href="#auditorias-changes" aria-label="Ir a Cambios en Expedientes (30D)" class="navbar-link">
                        <ion-icon name="time-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Cambios 30D</span>
                    </a>
                    <a href="#checklist-changes" aria-label="Ir a Cambios en Checklist" class="navbar-link">
                        <ion-icon name="checkmark-circle-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Checklist</span>
                    </a>
                    <a href="#top-users" aria-label="Ir a Top Usuarios" class="navbar-link">
                        <ion-icon name="people-circle-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Top Usuarios</span>
                    </a>
                    <a href="#campos-modificados" aria-label="Ir a Campos Modificados" class="navbar-link">
                        <ion-icon name="build-outline" class="navbar-icon"></ion-icon>
                        <span class="navbar-text">Campos Mod.</span>
                    </a>
                </div>
            </div>
        </nav>

    </div>

    {{-- Exponer los datos en JSON para JavaScript --}}
    @php
        $jsonData = json_encode($dashboardData);
    @endphp

    <script>
        // Exponer los datos a JavaScript
        const dashboardData = {!! $jsonData !!};
    </script>

    @push('scripts')
    <!-- Incluir Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Función para crear una tabla HTML dinámicamente
        function createTable(headers, data) {
            const table = document.createElement('table');
            table.className = "min-w-full border-collapse border border-gray-300 text-sm";

            // Crear el encabezado de la tabla
            const thead = document.createElement('thead');
            const trHead = document.createElement('tr');
            headers.forEach(header => {
                const th = document.createElement('th');
                th.className = "border border-gray-300 px-2 py-1 bg-gray-100";
                th.textContent = header;
                trHead.appendChild(th);
            });
            thead.appendChild(trHead);
            table.appendChild(thead);

            // Crear el cuerpo de la tabla
            const tbody = document.createElement('tbody');
            data.forEach(row => {
                const tr = document.createElement('tr');
                headers.forEach(header => {
                    const td = document.createElement('td');
                    td.className = "border border-gray-300 px-2 py-1";
                    td.textContent = row[header] ?? 'Sin Datos';
                    tr.appendChild(td);
                });
                tbody.appendChild(tr);
            });
            table.appendChild(tbody);

            return table;
        }

        // 1. Expedientes por Estatus
        if(dashboardData.countsByStatus) {
            // Clonar el array para evitar mutaciones si es necesario
            const sortedCountsByStatus = [...dashboardData.countsByStatus].sort((a, b) => b.total - a.total);
            
            // Crear la tabla ordenada
            const tableStatus = createTable(['Estatus', 'Total'], sortedCountsByStatus.map(item => ({
                'Estatus': item.estatus_checklist,
                'Total': item.total
            })));
            document.getElementById('table-status').appendChild(tableStatus);

            // Crear gráfico de barras ordenado
            new Chart(document.getElementById('statusChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: sortedCountsByStatus.map(item => item.estatus_checklist),
                    datasets: [{
                        label: 'Total de Expedientes',
                        data: sortedCountsByStatus.map(item => item.total),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // 2. Expedientes por Ente Fiscalizado
        if(dashboardData.countsByEnteFiscalizado) {
            const sortedCountsByEnteFiscalizado = [...dashboardData.countsByEnteFiscalizado].sort((a, b) => b.total - a.total);

            const tableEnteFiscalizado = createTable(['Ente Fiscalizado', 'Total'], sortedCountsByEnteFiscalizado.map(item => ({
                'Ente Fiscalizado': item.cat_ente_fiscalizado?.valor ?? 'Sin Datos',
                'Total': item.total
            })));
            document.getElementById('table-ente-fiscalizado').appendChild(tableEnteFiscalizado);

            // Crear gráfico de pie
            new Chart(document.getElementById('enteFiscalizadoChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: sortedCountsByEnteFiscalizado.map(item => item.cat_ente_fiscalizado?.valor ?? 'Sin Datos'),
                    datasets: [{
                        data: sortedCountsByEnteFiscalizado.map(item => item.total),
                        backgroundColor: [
                            '#f87171','#fbbf24','#34d399','#60a5fa',
                            '#a78bfa','#f472b6','#fde047','#4ade80',
                            '#22d3ee','#c084fc','#fca5a5','#fdba74'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }

        // 3. Expedientes por Auditoría Especial
        if(dashboardData.countsByAuditoriaEspecial) {
            const sortedCountsByAuditoriaEspecial = [...dashboardData.countsByAuditoriaEspecial].sort((a, b) => b.total - a.total);

            const tableAuditoriaEspecial = createTable(['Número de Auditoria', 'Total de Expedientes de Acción'], sortedCountsByAuditoriaEspecial.map(item => ({
                'Número de Auditoria': item.cat_auditoria_especial?.valor ?? 'Sin Datos',
                'Total de Expedientes de Acción': item.total
            })));
            document.getElementById('table-auditoria-especial').appendChild(tableAuditoriaEspecial);

            // Crear gráfico de barras
            new Chart(document.getElementById('auditoriaEspecialChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: sortedCountsByAuditoriaEspecial.map(item => item.cat_auditoria_especial?.valor ?? 'Sin Datos'),
                    datasets: [{
                        label: 'Total de Expedientes',
                        data: sortedCountsByAuditoriaEspecial.map(item => item.total),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // 4. Expedientes por Siglas de Auditoría Especial
        if(dashboardData.countsBySiglasAuditoriaEspecial) {

            const sortedCountsBySiglasAuditoriaEspecial = [...dashboardData.countsBySiglasAuditoriaEspecial].sort((a, b) => b.total - a.total);

            const tableSiglasAudEsp = createTable(['Siglas Auditoría Especial', 'Total'], sortedCountsBySiglasAuditoriaEspecial.map(item => ({
                'Siglas Auditoría Especial': item.cat_siglas_auditoria_especial?.valor ?? 'Sin Datos',
                'Total': item.total
            })));
            document.getElementById('table-siglas-audesp').appendChild(tableSiglasAudEsp);

            // Crear gráfico de pie
            new Chart(document.getElementById('siglasAudEspChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: sortedCountsBySiglasAuditoriaEspecial.map(item => item.cat_siglas_auditoria_especial?.valor ?? 'Sin Datos'),
                    datasets: [{
                        data: sortedCountsBySiglasAuditoriaEspecial.map(item => item.total),
                        backgroundColor: [
                            '#f87171','#fbbf24','#34d399','#60a5fa',
                            '#a78bfa','#f472b6','#fde047','#4ade80',
                            '#22d3ee','#c084fc','#fca5a5','#fdba74'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }

        // 5. Expedientes por Siglas Tipo Acción
        if(dashboardData.countsBySiglasTipoAccion) {

            const sortedCountsBySiglasTipoAccion = [...dashboardData.countsBySiglasTipoAccion].sort((a, b) => b.total - a.total);

            const tableSiglasTipoAccion = createTable(['Siglas Tipo Acción', 'Total'], sortedCountsBySiglasTipoAccion.map(item => ({
                'Siglas Tipo Acción': item.cat_siglas_tipo_accion?.valor ?? 'Sin Datos',
                'Total': item.total
            })));
            document.getElementById('table-siglas-tipo-accion').appendChild(tableSiglasTipoAccion);

            // Crear gráfico de doughnut
            new Chart(document.getElementById('siglasTipoAccionChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: sortedCountsBySiglasTipoAccion.map(item => item.cat_siglas_tipo_accion?.valor ?? 'Sin Datos'),
                    datasets: [{
                        data: sortedCountsBySiglasTipoAccion.map(item => item.total),
                        backgroundColor: [
                            '#fde047','#4ade80','#22d3ee','#c084fc',
                            '#fca5a5','#fdba74','#f87171','#fbbf24',
                            '#34d399','#60a5fa','#a78bfa','#f472b6'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }

        // 6. Expedientes por DGSEG EF
        if(dashboardData.countsByDgsegEf) {
            const sortedCountsByDgsegEf = [...dashboardData.countsByDgsegEf].sort((a, b) => b.total - a.total);

            const tableDgsegEf = createTable(['DGSEG EF', 'Total'], sortedCountsByDgsegEf.map(item => ({
                'DGSEG EF': item.cat_dgseg_ef?.valor ?? 'Sin Datos',
                'Total': item.total
            })));
            document.getElementById('table-dgseg-ef').appendChild(tableDgsegEf);

            // Crear gráfico de barras
            new Chart(document.getElementById('dgsegEfChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: sortedCountsByDgsegEf.map(item => item.cat_dgseg_ef?.valor ?? 'Sin Datos'),
                    datasets: [{
                        label: 'Total de Expedientes',
                        data: sortedCountsByDgsegEf.map(item => item.total),
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // 7. Expedientes con Comentarios antes de ser Aceptadas
        if(dashboardData.withCommentsBeforeAccepted !== null) {
            document.getElementById('comentariosBeforeAccepted').textContent = `Total: ${dashboardData.withCommentsBeforeAccepted}`;
        }

        // 8. Expedientes por UAA y Estatus
        if(dashboardData.countsByUaaAndStatus) {
            const tableUaaEstatus = createTable(['UAA', 'Estatus', 'Total'], dashboardData.countsByUaaAndStatus.map(item => ({
                'UAA': item.cat_uaa?.valor ?? 'Sin Datos',
                'Estatus': item.estatus_checklist,
                'Total': item.total
            })));
            document.getElementById('table-uaa-estatus').appendChild(tableUaaEstatus);

            // Preparar datos para gráfico de barras apiladas
            const uaaGroups = {};
            dashboardData.countsByUaaAndStatus.forEach(item => {
                const uaaName = item.cat_uaa?.valor ?? 'Sin Datos';
                if(!uaaGroups[uaaName]) uaaGroups[uaaName] = {};
                uaaGroups[uaaName][item.estatus_checklist] = item.total;
            });

            const allUaas = Object.keys(uaaGroups);
            const allEstatus = [...new Set(dashboardData.countsByUaaAndStatus.map(i => i.estatus_checklist))];

            const datasetsUaaEstatus = allEstatus.map((estatus, idx) => {
                const colors = [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ];
                return {
                    label: estatus,
                    data: allUaas.map(uaa => uaaGroups[uaa][estatus] ?? 0),
                    backgroundColor: colors[idx % colors.length],
                    borderColor: colors[idx % colors.length].replace('0.6', '1'),
                    borderWidth: 1
                }
            });

            new Chart(document.getElementById('uaaEstatusChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: allUaas,
                    datasets: datasetsUaaEstatus
                },
                options: {
                    responsive: true,
                    scales: { 
                        x: { stacked: true }, 
                        y: { stacked: true, beginAtZero: true } 
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Expedientes por UAA y Estatus (Apilado)'
                        }
                    }
                }
            });
        }
        // 9. Cambios en Expedientes (Últimos 30 días)
        if(dashboardData.auditoriasChangesByDay) {
            // Clonar y ordenar el array para la tabla (orden descendente por 'total_changes')
            const sortedAuditoriasChangesByDay = [...dashboardData.auditoriasChangesByDay].sort((a, b) => b.total_changes - a.total_changes);
            
            // Crear la tabla ordenada
            const tableAuditoriasChanges = createTable(['Fecha', 'Total Cambios'], sortedAuditoriasChangesByDay.map(item => ({
                'Fecha': item.date,
                'Total Cambios': item.total_changes
            })));
            document.getElementById('table-auditorias-changes').appendChild(tableAuditoriasChanges);

            // Crear gráfico de línea en orden cronológico
            new Chart(document.getElementById('auditoriasChangesChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: dashboardData.auditoriasChangesByDay.map(item => item.date),
                    datasets: [{
                        label: 'Cambios por día',
                        data: dashboardData.auditoriasChangesByDay.map(item => item.total_changes),
                        backgroundColor: 'rgba(54,162,235,0.2)',
                        borderColor: 'rgba(54,162,235,1)',
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // 10. Cambios en Checklist Apartados (Por semana)
        if(dashboardData.checklistChangesByWeek) {
            // Clonar y ordenar el array para la tabla (orden descendente por 'Total Cambios')
            const sortedChecklistChangesByWeek = [...dashboardData.checklistChangesByWeek]
                .map(item => ({
                    ...item,
                    total_changes_converted: Math.ceil(item.total_changes / 60) // Preprocesar 'Total Cambios'
                }))
                .sort((a, b) => b.total_changes_converted - a.total_changes_converted);
            
            // Crear la tabla ordenada
            const tableChecklistChanges = createTable(['Semana (AñoSemana)', 'Total Cambios'], sortedChecklistChangesByWeek.map(item => ({
                'Semana (AñoSemana)': 'Semana ' + item.week,
                'Total Cambios': item.total_changes_converted
            })));
            document.getElementById('table-checklist-changes').appendChild(tableChecklistChanges);

            // Crear gráfico de barras en orden cronológico
            const chronologicalChecklistChangesByWeek = [...dashboardData.checklistChangesByWeek]
                .map(item => ({
                    ...item,
                    total_changes_converted: Math.ceil(item.total_changes / 60)
                }))
                .sort((a, b) => a.week - b.week); // Asumiendo que 'week' es numérico y secuencial
            
            new Chart(document.getElementById('checklistChangesChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: chronologicalChecklistChangesByWeek.map(item => 'Semana ' + item.week),
                    datasets: [{
                        label: 'Cambios por semana',
                        data: chronologicalChecklistChangesByWeek.map(item => item.total_changes_converted),
                        backgroundColor: 'rgba(255,99,132,0.6)',
                        borderColor: 'rgba(255,99,132,1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // 11. Top Usuarios con Más Cambios en Expedientes
        if(dashboardData.topUsersChanges) {
            // Clonar y ordenar el array por 'Total Cambios' en orden descendente
            const sortedTopUsersChanges = [...dashboardData.topUsersChanges]
                .sort((a, b) => b.total_changes - a.total_changes);
            
            // Crear la tabla ordenada
            const tableTopUsers = createTable(['Usuario', 'Total Cambios'], sortedTopUsersChanges.map(item => ({
                'Usuario': item.user?.name ?? 'Usuario ID ' + item.changed_by,
                'Total Cambios': item.total_changes
            })));
            document.getElementById('table-top-users').appendChild(tableTopUsers);

            // Crear gráfico de dona ordenado
            new Chart(document.getElementById('topUsersChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: sortedTopUsersChanges.map(item => item.user?.name ?? 'Usuario ID ' + item.changed_by),
                    datasets: [{
                        data: sortedTopUsersChanges.map(item => item.total_changes),
                        backgroundColor: [
                            '#f87171','#fbbf24','#34d399','#60a5fa','#a78bfa',
                            '#fb923c','#8b5cf6','#10b981','#3b82f6','#ec4899' // Agregar más colores si es necesario
                        ],
                        borderColor: [
                            '#f87171','#fbbf24','#34d399','#60a5fa','#a78bfa',
                            '#fb923c','#8b5cf6','#10b981','#3b82f6','#ec4899'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    return `${label}: ${value} Cambios`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // 12. Campos más Modificados en Checklist Apartados
        if (dashboardData.apartadosData) {
            console.log(dashboardData.apartadosData);

            const fieldsChangesEntries = Object.entries(dashboardData.apartadosData)
                .map(([id, data]) => ({
                    Apartado: data.nombre,
                    'Se Integra': data.se_integran,
                    'Observaciones': data.observaciones,
                    'Total Cambios': data.total
                }));

            // Crear la tabla
            const tableFieldsChanges = createTable(['Apartado', 'Observaciones'], fieldsChangesEntries);
            document.getElementById('table-fields-changes').querySelector('tbody').appendChild(tableFieldsChanges);

            // Crear gráfico de barras horizontal
            new Chart(document.getElementById('fieldsChangesChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: fieldsChangesEntries.map(item => item.Apartado),
                    datasets: [{
                        label: 'Cantidad de Cambios',
                        data: fieldsChangesEntries.map(item => item['Total Cambios']),
                        backgroundColor: 'rgba(75,192,192,0.6)',
                        borderColor: 'rgba(75,192,192,1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y', // Gráfico horizontal
                    responsive: true,
                    scales: {
                        x: { beginAtZero: true } // Comienza en 0 para el eje X
                    }
                }
            });
        }


    });


        // Scroll suave
        document.querySelectorAll('nav a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Resaltado de la sección activa en el navbar
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('section');
            const scrollPos = window.scrollY + window.innerHeight / 2;

            sections.forEach(section => {
                if (section.offsetTop <= scrollPos && (section.offsetTop + section.offsetHeight) > scrollPos) {
                    const id = section.getAttribute('id');
                    document.querySelectorAll('nav a').forEach(a => {
                        a.classList.remove('text-blue-500', 'font-semibold');
                        a.classList.add('text-gray-500');
                        // Reset icon color
                        const icon = a.querySelector('ion-icon');
                        if (icon) {
                            icon.style.color = ''; // Reset to default
                        }

                        if (a.getAttribute('href') === `#${id}`) {
                            a.classList.remove('text-gray-500');
                            a.classList.add('text-blue-500', 'font-semibold');
                            if (icon) {
                                icon.style.color = '#3b82f6'; // Azul Tailwind
                            }
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
