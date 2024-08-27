<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Programación de entrega de expedientes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                            <x-ui.table.row>
                                <x-ui.table.column>{{ $auditoria->CP }}</x-ui.table.column>
                                <x-ui.table.column>{{ $auditoria->entrega }}</x-ui.table.column>
                                <x-ui.table.column>
                                    {{ $auditoria->auditoria_especial }}
                                </x-ui.table.column>
                                
                                <x-ui.table.column>{{ $auditoria->uaa }}</x-ui.table.column>
                                <x-ui.table.column>{{ $auditoria->total_entregar }}</x-ui.table.column>
                                <x-ui.table.column>0</x-ui.table.column>
                                <x-ui.table.column>
                                    <a href="{{ route('expedientes.detalle', ['uaa' => $auditoria->uaa]) }}">
                                        <small class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                            {{ $auditoria->total_entregar - 0 }} <ion-icon style="margin-bottom: 0px;" name="chevron-forward-circle-outline"></ion-icon>
                                        </small>
                                    </a>
                                </x-ui.table.column>
                                <x-ui.table.column>0%</x-ui.table.column>
                                <x-ui.table.column>0 / {{ $auditoria->total_entregar }}</x-ui.table.column>
                                <x-ui.table.column>0</x-ui.table.column>
                                <x-ui.table.column>0</x-ui.table.column>
                            </x-ui.table.row>
                        @endforeach
                    </x-slot>
                </x-ui.table>
            </x-ui.container.table>
        </div>
    </div>
</x-app-layout>
