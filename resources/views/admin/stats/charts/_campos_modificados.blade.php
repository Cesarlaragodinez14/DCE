{{-- resources/views/admin/stats/charts/_campos_modificados.blade.php --}}
<section id="campos-modificados" class="mb-8">
    <h2 class="text-xl font-semibold mb-4">Observaciones recurrentes en la revisión de los Expedientes de Acción (Por apartado de la lista de Verificación).</h2>

    <div class="mt-6 mb-4">
        <h3 style="display: none;" class="text-lg font-medium mb-2">Gráfico de Cambios en Apartados (Observaciones)</h3>
        <canvas id="fieldsChangesChart" height="100"></canvas>
    </div>

    <div class="overflow-x-auto">
        <table id="table-fields-changes" class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead>
                <!-- Se llenará dinámicamente con JavaScript -->
            </thead>
            <tbody>
                <!-- Se llena dinámicamente con JavaScript -->
            </tbody>
        </table>
    </div>
</section>

@push('scripts')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    // Se asume que “apartadosData” es un objeto con la forma:
    // {
    //    "1": { "nombre":"Apartado X", "observaciones":10 },
    //    "2": { "nombre":"Apartado Y", "observaciones":5 },
    //    ...
    // }
    const data = window.dashboardData?.apartadosData;
    if (!data) return;

    // Convertir el objeto en array
    //  -> [ { Apartado: "...", Observaciones: number }, ... ]
    const entries = Object.entries(data).map(([id, obj]) => ({
        'Apartado': obj.nombre ?? `ID ${id}`,
        'Observaciones': obj.observaciones ?? 0,
    }));

    // 1) Ordenar de mayor a menor por "Observaciones"
    entries.sort((a, b) => b.Observaciones - a.Observaciones);

    // 2) Crear la tabla: columnas -> [ "Apartado", "Observaciones" ]
    const table = document.getElementById('table-fields-changes');
    if (!table) return;

    const thead = table.querySelector('thead');
    if (thead) {
        const trHead = document.createElement('tr');
        ['Apartado', 'Observaciones'].forEach(header => {
            const th = document.createElement('th');
            th.className = "border border-gray-300 px-2 py-1 bg-gray-100 font-semibold text-gray-700";
            th.textContent = header;
            trHead.appendChild(th);
        });
        thead.appendChild(trHead);
    }

    const tbody = table.querySelector('tbody');
    if (tbody) {
        entries.forEach(row => {
            const tr = document.createElement('tr');
            ['Apartado', 'Observaciones'].forEach(header => {
                const td = document.createElement('td');
                td.className = "border border-gray-300 px-2 py-1";
                td.textContent = row[header];
                tr.appendChild(td);
            });
            tbody.appendChild(tr);
        });
    }

    // 3) Gráfico de barras horizontal con sólo Observaciones
    const ctx = document.getElementById('fieldsChangesChart')?.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            // Eje Y => lista de apartados (orden desc) 
            labels: entries.map(item => item['Apartado']),
            datasets: [{
                label: 'Observaciones',
                data: entries.map(item => item['Observaciones']),
                backgroundColor: 'rgba(75,192,192,0.6)',  // color ejemplo
            }]
        },
        options: {
            indexAxis: 'y',  // barras horizontales
            responsive: true,
            scales: {
                x: { beginAtZero: true },
                y: {
                    // Si quieres evitar recortes en labels largos:
                    // ticks: { callback: (val) => shortenLabel(val, 20) },
                }
            }
        }
    });
});
</script>
@endpush
