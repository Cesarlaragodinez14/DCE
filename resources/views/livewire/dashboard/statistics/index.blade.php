<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
    {{-- Breadcrumbs --}}
    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.link href="/dashboard">Dashboard</x-ui.breadcrumbs.link>
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link active>Estadísticas</x-ui.breadcrumbs.link>
    </x-ui.breadcrumbs>

    {{-- Barra de herramientas de filtrado (si lo requieres) --}}
    <div class="flex justify-between align-top py-4">
        <x-ui.input
            wire:model.live="search"
            type="text"
            placeholder="Buscar auditorías..."
        />
    </div>

    <div class="space-y-8">
        {{-- Estadísticas por Estatus --}}
        <div>
            <h2 class="text-xl font-semibold mb-4">Auditorías por Estatus</h2>
            <x-ui.container.table>
                <x-ui.table>
                    <x-slot name="head">
                        <x-ui.table.header>Estatus</x-ui.table.header>
                        <x-ui.table.header>Total</x-ui.table.header>
                    </x-slot>
                    <x-slot name="body">
                        @foreach($countsByStatus as $item)
                            <x-ui.table.row>
                                <x-ui.table.column>{{ $item->estatus_checklist }}</x-ui.table.column>
                                <x-ui.table.column>{{ $item->total }}</x-ui.table.column>
                            </x-ui.table.row>
                        @endforeach
                    </x-slot>
                </x-ui.table>
            </x-ui.container.table>

            {{-- Gráfico con Chart.js --}}
            <div class="mt-6">
                <h3 class="text-lg font-medium mb-2">Gráfico de Auditorías por Estatus</h3>
                <canvas id="statusChart" height="100"></canvas>
            </div>
        </div>

        {{-- Estadísticas por UAA y Estatus --}}
        <div>
            <h2 class="text-xl font-semibold mb-4">Auditorías por UAA y Estatus</h2>
            <x-ui.container.table>
                <x-ui.table>
                    <x-slot name="head">
                        <x-ui.table.header>UAA</x-ui.table.header>
                        <x-ui.table.header>Estatus</x-ui.table.header>
                        <x-ui.table.header>Total</x-ui.table.header>
                    </x-slot>
                    <x-slot name="body">
                        @foreach($countsByUaaAndStatus as $row)
                            <x-ui.table.row>
                                <x-ui.table.column>
                                    {{ optional($row->catUaa)->nombre ?? $row->uaa }}
                                </x-ui.table.column>
                                <x-ui.table.column>{{ $row->estatus_checklist }}</x-ui.table.column>
                                <x-ui.table.column>{{ $row->total }}</x-ui.table.column>
                            </x-ui.table.row>
                        @endforeach
                    </x-slot>
                </x-ui.table>
            </x-ui.container.table>
        </div>

        {{-- Auditorías con Comentarios antes de ser Aceptadas --}}
        <div>
            <h2 class="text-xl font-semibold mb-4">Auditorías con Comentarios antes de ser Aceptadas</h2>
            <p>Total: <strong>{{ $withCommentsBeforeAccepted }}</strong></p>
        </div>
    </div>
</div>

{{-- Scripts del componente --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('livewire:load', function () {
    const statusData = @json($countsByStatus);

    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: statusData.map(item => item.estatus_checklist),
            datasets: [{
                label: 'Total de Auditorías',
                data: statusData.map(item => item.total),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
    });
});
</script>
@endpush
