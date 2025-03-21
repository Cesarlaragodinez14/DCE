<!-- resources/views/dashboard/historial-expedientes/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historial de Movimientos de Expedientes') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-6">

        <!-- FILTROS DE BÚSQUEDA -->
        <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Filtros de búsqueda</h2>
            <form action="{{ route('programacion-historial.index') }}" method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-5 gap-4">

                    <!-- Clave de Acción (input texto) -->
                    <div class="space-y-1">
                        <label for="clave_accion" class="block text-sm font-medium text-gray-700">
                            Clave de Acción
                        </label>
                        <input
                            type="text"
                            name="clave_accion"
                            id="clave_accion"
                            value="{{ request('clave_accion', $claveAccion) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md 
                                focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Ej. ACC-123"
                        >
                    </div>

                    <!-- Filtro "Generado por" -->
                    <div class="space-y-1">
                        <label for="generado_por" class="block text-sm font-medium text-gray-700">
                            Generado por
                        </label>
                        <select 
                            name="generado_por" 
                            id="generado_por" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md 
                                focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">-- Seleccione Usuario --</option>
                            @foreach($generados as $usr)
                                <option value="{{ $usr->name }}"
                                    @if(request('generado_por', $generadoPor) == $usr->name) selected @endif>
                                    {{ $usr->name }} - ({{ $usr->total }}) Expedientes Firmados
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado -->
                    <div class="space-y-1">
                        <label for="estado" class="block text-sm font-medium text-gray-700">
                            Estado
                        </label>
                        <select 
                            name="estado" 
                            id="estado" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md 
                                focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">-- Seleccione Estado --</option>
                            @foreach($estados as $est)
                                <option value="{{ $est }}"
                                    @if(request('estado', $estado) == $est) selected @endif>
                                    {{ $est }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Fecha de Recepción -->
                    <div class="space-y-1">
                        <label for="fecha_recepcion" class="block text-sm font-medium text-gray-700">
                            Fecha de Recepción
                        </label>
                        <select 
                            name="fecha_recepcion" 
                            id="fecha_recepcion" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md 
                                focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">-- Seleccione Fecha --</option>
                            @foreach($fechasRecepcion as $f)
                                @php
                                    // Formatear si quieres, por ejemplo a YYYY-MM-DD
                                    $fechaForm = \Carbon\Carbon::parse($f)->format('Y-m-d');
                                @endphp
                                <option value="{{ $fechaForm }}"
                                    @if(request('fecha_recepcion', $fechaRecepcion) == $fechaForm) selected @endif>
                                    {{ $fechaForm }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <!-- Botones -->
                <div class="flex items-center gap-3 mt-6 justify-end">
                    <a href="{{ route('programacion-historial.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md 
                            border border-gray-300 hover:bg-gray-200 transition-colors">
                        Limpiar filtros
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 
                                flex items-center justify-center border border-transparent transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Buscar
                    </button>
                </div>
            </form>
        </div>

        <!-- TABLA DE RESULTADOS -->
        <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Movimientos de Expediente</h3>

            @if($movimientos->count())
                <x-ui.table>
                    <x-slot name="head">
                        <x-ui.table.header>Clave de Acción</x-ui.table.header>
                        <x-ui.table.header>Generado Por</x-ui.table.header>
                        <x-ui.table.header>Estado Historial</x-ui.table.header>
                        <x-ui.table.header>Fecha Estado</x-ui.table.header>
                        <x-ui.table.header>PDF</x-ui.table.header>
                        <x-ui.table.header>Fecha de Recepción</x-ui.table.header>
                        <x-ui.table.header>Estado Actual</x-ui.table.header>
                    </x-slot>

                    <x-slot name="body">
                        @foreach($movimientos as $mov)
                            <x-ui.table.row>
                                <!-- Clave de Acción (e.clave_accion) -->
                                <x-ui.table.column>
                                    {{ $mov->clave_accion ?? 'N/A' }}
                                </x-ui.table.column>

                                <!-- DCE => e.responsable -->
                                <x-ui.table.column>
                                    {{ $mov->responsable ?? 'N/A' }}
                                </x-ui.table.column>

                                <!-- Estado Historial => eh.estado (alias hist_estado) -->
                                <x-ui.table.column>
                                    {{ $mov->hist_estado ?? 'N/A' }}
                                </x-ui.table.column>

                                <!-- Fecha Estado => eh.fecha_estado -->
                                <x-ui.table.column>
                                    {{ $mov->fecha_estado ?? 'N/A' }}
                                </x-ui.table.column>

                                <!-- PDF => eh.pdf_path (alias hist_pdf) -->
                                <x-ui.table.column>
                                    @if(!empty($mov->hist_pdf))
                                        <a href="{{ asset('storage/' . $mov->hist_pdf) }}"
                                           target="_blank"
                                           class="text-blue-600 hover:underline">
                                            Ver PDF
                                        </a>
                                    @else
                                        No disponible
                                    @endif
                                </x-ui.table.column>

                                <!-- Fecha de Recepción => e.fecha_real_entrega -->
                                <x-ui.table.column>
                                    {{ $mov->fecha_real_entrega ?? '---' }}
                                </x-ui.table.column>

                                <!-- Estado Actual => e.estado (alias estado_actual) -->
                                <x-ui.table.column>
                                    {{ $mov->estado_actual ?? 'N/A' }}
                                </x-ui.table.column>
                            </x-ui.table.row>
                        @endforeach
                    </x-slot>
                </x-ui.table>
            @else
                <p class="text-gray-600">No se encontraron movimientos.</p>
            @endif
        </div>
    </div>
</x-app-layout>
