<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Programación de entrega de expedientes') }}
            <!-- Selectores para filtros -->
            <div class="ml-auto flex items-center space-x-4">
                <x-ui.filter-cp-en
                    :entregas="$entregas"
                    :cuentasPublicas="$cuentasPublicas"
                    route="dashboard.expedientes.entrega"
                    defaultEntregaLabel="Seleccionar Entrega"
                    defaultCuentaPublicaLabel="Seleccionar Cuenta Pública"
                />
            </div>
        </h2>
    </x-slot>

    <div class="py-12">        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-ui.breadcrumbs>
                <x-ui.breadcrumbs.link href="/dashboard">Dashboard</x-ui.breadcrumbs.link>
                <x-ui.breadcrumbs.separator />
                <x-ui.breadcrumbs.link active>
                    {{ __('Distribución de Expedientes de Acción') }}
                </x-ui.breadcrumbs.link>
            </x-ui.breadcrumbs>

            @if($auditorias->isEmpty())
                <div class="text-center p-6 bg-red-100 text-red-600">
                    <p>No hay información disponible para la selección actual.</p>
                </div>
            @else
                <x-ui.container.table>
                    <x-ui.table>
                        <x-slot name="head">
                            <x-ui.table.header>CP</x-ui.table.header>
                            <x-ui.table.header>Entrega</x-ui.table.header>
                            <x-ui.table.header>AE / DG</x-ui.table.header>
                            <x-ui.table.header>Clave de UA</x-ui.table.header>
                            <x-ui.table.header>Total a entregar</x-ui.table.header>
                            <x-ui.table.header>Total entregados</x-ui.table.header>
                            <x-ui.table.header>Pendientes de entregar</x-ui.table.header>
                            <x-ui.table.header>% de avance</x-ui.table.header>
                            <x-ui.table.header>Avance</x-ui.table.header>
                            <x-ui.table.header>Recordatorio</x-ui.table.header>
                            <x-ui.table.header>Expedientes Programados a entregar</x-ui.table.header>
                        </x-slot>

                        <x-slot name="body">
                            @foreach($auditorias as $auditoria)
                                @php
                                    // Calculamos dinámicamente
                                    $totalEntregar    = $auditoria->total_entregar;
                                    $totalEntregados  = $auditoria->total_entregados;
                                    $pendientes       = $totalEntregar - $totalEntregados;
                                    $porcentaje       = $totalEntregar > 0
                                        ? round(($totalEntregados / $totalEntregar) * 100, 2)
                                        : 0;
                                @endphp

                                <x-ui.table.row>
                                    <x-ui.table.column>{{ $auditoria->CP }}</x-ui.table.column>
                                    <x-ui.table.column>{{ $auditoria->entrega }}</x-ui.table.column>
                                    <x-ui.table.column>{{ $auditoria->auditoria_especial }}</x-ui.table.column>
                                    <x-ui.table.column>{{ $auditoria->uaa }}</x-ui.table.column>

                                    <!-- Total a entregar -->
                                    <x-ui.table.column>{{ $totalEntregar }}</x-ui.table.column>

                                    <!-- Total entregados -->
                                    <x-ui.table.column>
                                        {{ $totalEntregados }}
                                    </x-ui.table.column>

                                    <!-- Pendientes de entregar -->
                                    <x-ui.table.column>
                                        <a href="{{ route('expedientes.detalle', [
                                            'uaa' => $auditoria->uaa,
                                            'entrega' => request('entrega'),
                                            'cuenta_publica' => request('cuenta_publica')
                                        ]) }}">
                                            <small class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                {{ $pendientes }}
                                                <ion-icon style="margin-bottom: 0px;"
                                                    name="chevron-forward-circle-outline">
                                                </ion-icon>
                                            </small>
                                        </a>
                                    </x-ui.table.column>

                                    <!-- % de avance -->
                                    <x-ui.table.column>
                                        {{ $porcentaje }}%
                                    </x-ui.table.column>

                                    <!-- Avance (fracción) -->
                                    <x-ui.table.column>
                                        {{ $totalEntregados }} / {{ $totalEntregar }}
                                    </x-ui.table.column>

                                    <!-- Recordatorio -->
                                    <x-ui.table.column>
                                        0 
                                    </x-ui.table.column>

                                    <!-- Expedientes programados a entregar (ej. 0, si no se maneja)-->
                                    <x-ui.table.column>
                                        {{ $auditoria->total_programados }}
                                    </x-ui.table.column>
                                </x-ui.table.row>
                            @endforeach
                        </x-slot>
                    </x-ui.table>
                </x-ui.container.table>
            @endif
        </div>
    </div>
</x-app-layout>
