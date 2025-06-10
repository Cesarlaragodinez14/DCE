<x-app-layout>
    <x-slot name="header" style="display: none;">
    
    </x-slot>

    <!-- CSS Variables -->
    @include('reportes.observaciones-apartados-styles')

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <x-ui.breadcrumbs>
                <x-ui.breadcrumbs.link href="/dashboard" class="hover:text-primary-color transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </x-ui.breadcrumbs.link>
                <x-ui.breadcrumbs.separator />
                <x-ui.breadcrumbs.link active>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('Reporte de Apartados - Por Entrega') }}
                </x-ui.breadcrumbs.link>
            </x-ui.breadcrumbs>

            <div class="card shadow">
                <div class="card-header">
                    <div class="filter-section-title">
                        Apartados con Observaciones
                    </div>
                    <button onclick="window.print()" class="btn btn-sm btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Imprimir
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <form action="{{ route('reportes.observaciones-apartados') }}" method="GET" class="d-flex align-items-center">
                            <div class="form-group flex-grow-1 mb-0 mr-2">
                                <label for="entrega_id" class="form-label">Entrega:</label>
                                <select name="entrega_id" id="entrega_id" class="form-select">
                                    @foreach($entregas as $entrega)
                                        <option value="{{ $entrega->id }}" {{ $entregaId == $entrega->id ? 'selected' : '' }}>
                                            {{ $entrega->valor }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filtrar
                            </button>
                        </form>
                    </div>

                    @if($resultadosFormateados->isEmpty())
                        <div class="alert alert-info">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                No se encontraron resultados para la entrega seleccionada.
                            </div>
                        </div>
                    @else
                        @php
                            // Procesamiento de datos para estadísticas
                            $agrupadosPorApartado = $resultadosFormateados->groupBy('nombre_apartado');
                            
                            // Total de claves de acción para calcular porcentajes
                            $totalClavesAccion = $resultadosFormateados->pluck('auditoria_clave')->unique()->count();
                        @endphp

                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="30%">Apartado</th>
                                        <th width="8%">UAA</th>
                                        <th width="8%">Siglas Aud. Especial</th>
                                        <th width="8%">Claves Acción</th>
                                        <th width="10%">Distribución (%)</th>
                                        <th width="10%">Distribución Apartado (%)</th>
                                        <th width="10%">Total Apartado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agrupadosPorApartado as $nombreApartado => $items)
                                        @php
                                            $nivel = $items->first()['nivel'];
                                            $indentacion = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $nivel - 1);
                                            $numeracion = $items->first()['numeracion'];
                                            $nombreSimple = $items->first()['nombre_original'];
                                            $displayNombre = $numeracion . ' - ' . (strlen($nombreSimple) > 20 ? substr($nombreSimple, 0, 20) . '...' : $nombreSimple);
                                            $sinObservaciones = isset($items->first()['sin_observaciones']) && $items->first()['sin_observaciones'];
                                            $numClaves = $items->pluck('auditoria_clave')->unique()->count();
                                            $porcentaje = $totalClavesAccion > 0 ? round(($numClaves / $totalClavesAccion) * 100, 2) : 0;
                                            $bgClass = '';
                                            
                                            // Color según nivel
                                            if ($nivel == 1) {
                                                $bgClass = 'bg-light';
                                            } elseif ($nivel == 2) {
                                                $bgClass = '';
                                            } elseif ($nivel == 3) {
                                                $bgClass = 'bg-white';
                                            }
                                            
                                            // Color para apartados sin observaciones
                                            if ($sinObservaciones) {
                                                $bgClass .= ' text-muted';
                                            }
                                            
                                            // Obtener la numeración jerárquica
                                            $numeracion = $items->first()['numeracion'] ?? '0';
                                            
                                            $totalApartado = $items->first()['total_apartado'];
                                            $distribucionApartado = $items->first()['distribucion_apartado'] ?? 0;
                                        @endphp
                                        <tr class="{{ $bgClass }}">
                                            <td class="text-center align-middle">{{ $numeracion }}</td>
                                            <td class="font-weight-{{ $nivel == 1 ? 'bold' : 'normal' }}">
                                                {!! $indentacion !!}
                                                @if($nivel == 1)
                                                    {{ $nombreApartado }}
                                                @elseif($nivel == 2)
                                                    @php
                                                        $partes = explode(' > ', $nombreApartado);
                                                    @endphp
                                                    {{ $partes[0] }} <i class="fas fa-angle-right text-muted mx-1"></i> {{ $partes[1] }}
                                                @elseif($nivel == 3)
                                                    @php
                                                        $partes = explode(' > ', $nombreApartado);
                                                    @endphp
                                                    {{ $partes[0] }} <i class="fas fa-angle-right text-muted mx-1"></i> {{ $partes[1] }} <i class="fas fa-angle-right text-muted mx-1"></i> {{ $partes[2] }}
                                                @endif
                                                
                                                @if($sinObservaciones)
                                                    <span class="badge badge-light">(Sin observaciones)</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$sinObservaciones)
                                                    @foreach($items->pluck('uaa')->unique() as $uaa)
                                                        <span class="badge badge-info mr-1">{{ $uaa }}</span>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$sinObservaciones)
                                                    @foreach($items->pluck('siglas_aud_esp')->unique() as $sigla)
                                                        <span class="badge badge-secondary mr-1">{{ $sigla }}</span>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="text-center font-weight-bold">
                                                @if(!$sinObservaciones)
                                                    {{ $numClaves }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$sinObservaciones)
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 mr-2" style="height: 15px;">
                                                            <div class="progress-bar" role="progressbar" style="width: {{ $porcentaje }}%;" 
                                                                aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span class="font-weight-bold">{{ $porcentaje }}%</span>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 mr-2" style="height: 15px;">
                                                            <div class="progress-bar" role="progressbar" style="width: 0%;" 
                                                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span class="font-weight-bold">0%</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($nivel == 1)
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 mr-2" style="height: 15px;">
                                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $distribucionApartado }}%;" 
                                                                aria-valuenow="{{ $distribucionApartado }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span class="font-weight-bold">{{ $distribucionApartado }}%</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center font-weight-bold">
                                                @if($nivel == 1)
                                                    @if($totalApartado > 0)
                                                        <span class="badge badge-primary" style="font-size: 0.9rem;">{{ $totalApartado }}</span>
                                                    @else
                                                        <span class="badge badge-light">0</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right font-weight-bold">Total:</td>
                                        <td class="text-center font-weight-bold">{{ $totalClavesAccion }}</td>
                                        <td class="font-weight-bold">100%</td>
                                        <td class="font-weight-bold">100%</td>
                                        <td class="text-center font-weight-bold">{{ $totalObservaciones }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Gráfica de distribución -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <div class="filter-section-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Distribución de Observaciones por Apartado
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="apartadosChart" height="300"></canvas>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Definir las variables para el gráfico -->
    <script>
        // Estas variables serán usadas por el script externo
        var chartLabels = [];
        var chartData = [];
        var chartPorcentajes = [];
        var chartTotal = []; // Para mostrar el conteo total del apartado y subapartados
        var chartDistribucion = []; // Para mostrar la distribución por apartado
        
        @php
            // Filtrar solo apartados de nivel 1 para el gráfico
            $apartadosNivel1 = $resultadosFormateados->where('nivel', 1)->groupBy('nombre_apartado');
            $totalClavesAccion = $resultadosFormateados->where('sin_observaciones', '!=', true)->pluck('auditoria_clave')->unique()->count();
        @endphp
        
        @foreach($apartadosNivel1 as $nombreApartado => $items)
            @php
                $numeracion = $items->first()['numeracion'];
                $nombreSimple = $items->first()['nombre_original'];
                $displayNombre = $numeracion . ' - ' . (strlen($nombreSimple) > 20 ? substr($nombreSimple, 0, 20) . '...' : $nombreSimple);
                $sinObservaciones = isset($items->first()['sin_observaciones']) && $items->first()['sin_observaciones'];
                
                if ($sinObservaciones) {
                    $clavesCount = 0;
                    $porcentaje = 0;
                } else {
                    $clavesCount = $items->pluck('auditoria_clave')->unique()->count();
                    $porcentaje = $totalClavesAccion > 0 ? round(($clavesCount / $totalClavesAccion) * 100, 2) : 0;
                }
                
                $totalApartado = $items->first()['total_apartado'];
                $distribucionApartado = $items->first()['distribucion_apartado'] ?? 0;
            @endphp
            chartLabels.push("{{ $displayNombre }}");
            chartData.push({{ $clavesCount }});
            chartPorcentajes.push({{ $porcentaje }});
            chartTotal.push({{ $totalApartado }});
            chartDistribucion.push({{ $distribucionApartado }});
        @endforeach
    </script>

    @push('scripts')
        @include('reportes.observaciones-apartados-scripts')
    @endpush
</x-app-layout>
