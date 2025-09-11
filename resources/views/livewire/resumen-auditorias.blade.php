<div>
    <!-- Encabezado -->

    <div class="flex items-center mb-8">
        <div class="flex-shrink-0">
            <div class="h-12 w-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
                <ion-icon name="analytics-outline" class="text-2xl text-white"></ion-icon>
            </div>
        </div>
        <div class="ml-4">
            <h3 class="text-3xl font-bold text-gray-900">Resumen de Auditorías</h3>
            <p class="text-lg text-gray-600 mt-1">
                Vista consolidada con historial de comentarios y observaciones por clave de acción</p>
        </div>
    </div>


    <div class="bg-white shadow-lg rounded-xl p-8 mb-8">
        <!-- Filtros -->
        <div class="space-y-6 mb-8">

            <!-- Segunda fila de filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Filtro Entrega -->
                <div>
                    <label for="filtroEntrega" class="block text-sm font-medium text-gray-700 mb-2">
                        <ion-icon name="folder-outline" class="text-base mr-1"></ion-icon>
                        Entrega
                    </label>
                    <select
                        wire:model.live="filtroEntrega"
                        id="filtroEntrega"
                        class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-150 ease-in-out"
                    >
                        <option value="">Todas las entregas</option>
                        @foreach($entregas as $entrega)
                            <option value="{{ $entrega->id }}">{{ $entrega->valor }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro Cuenta Pública -->
                <div>
                    <label for="filtroCuentaPublica" class="block text-sm font-medium text-gray-700 mb-2">
                        <ion-icon name="document-text-outline" class="text-base mr-1"></ion-icon>
                        Cuenta Pública
                    </label>
                    <select
                        wire:model.live="filtroCuentaPublica"
                        id="filtroCuentaPublica"
                        class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-150 ease-in-out"
                    >
                        <option value="">Todas las cuentas públicas</option>
                        @foreach($cuentasPublicas as $cuentaPublica)
                            <option value="{{ $cuentaPublica->id }}">{{ $cuentaPublica->valor }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro Tipo de Acción -->
                <div>
                    <label for="filtroTipoAccion" class="block text-sm font-medium text-gray-700 mb-2">
                        <ion-icon name="play-outline" class="text-base mr-1"></ion-icon>
                        Tipo de Acción
                    </label>
                    <select
                        wire:model.live="filtroTipoAccion"
                        id="filtroTipoAccion"
                        class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-150 ease-in-out"
                    >
                        <option value="">Todos los tipos</option>
                        @foreach($tiposAccion as $tipoAccion)
                            <option value="{{ $tipoAccion->id }}">{{ $tipoAccion->valor }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tercera fila de filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Filtro Dirección General -->
                <div>
                    <label for="filtroDireccionGeneral" class="block text-sm font-medium text-gray-700 mb-2">
                        <ion-icon name="business-outline" class="text-base mr-1"></ion-icon>
                        Dirección General
                    </label>
                    <select
                        wire:model.live="filtroDireccionGeneral"
                        id="filtroDireccionGeneral"
                        class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-150 ease-in-out"
                    >
                        <option value="">Todas las direcciones</option>
                        @foreach($direccionesGenerales as $direccion)
                            <option value="{{ $direccion->id }}">{{ $direccion->valor }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro Ente de la Acción -->
                <div>
                    <label for="filtroEnteDeLaAccion" class="block text-sm font-medium text-gray-700 mb-2">
                        <ion-icon name="people-outline" class="text-base mr-1"></ion-icon>
                        Ente de la Acción
                    </label>
                    <select
                        wire:model.live="filtroEnteDeLaAccion"
                        id="filtroEnteDeLaAccion"
                        class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-150 ease-in-out"
                    >
                        <option value="">Todos los entes</option>
                        @foreach($entesAccion as $enteAccion)
                            <option value="{{ $enteAccion->id }}">{{ $enteAccion->valor }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro Estatus Checklist -->
                <div>
                    <label for="filtroEstatusChecklist" class="block text-sm font-medium text-gray-700 mb-2">
                        <ion-icon name="checkmark-circle-outline" class="text-base mr-1"></ion-icon>
                        Estatus Checklist
                    </label>
                    <select
                        wire:model.live="filtroEstatusChecklist"
                        id="filtroEstatusChecklist"
                        class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-150 ease-in-out"
                    >
                        <option value="">Todos los estatus</option>
                        @foreach($estatusChecklist as $estatus)
                            <option value="{{ $estatus }}">{{ $estatus }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Indicador de filtros activos y botón limpiar -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-2">
                @if($this->hasFiltrosActivos())
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        <ion-icon name="filter-outline" class="text-sm mr-1"></ion-icon>
                        Filtros activos
                    </span>
                    @if($search)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                            Búsqueda: "{{ $search }}"
                        </span>
                    @endif
                    @if($filtroClaveAccion)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                            Clave: "{{ $filtroClaveAccion }}"
                        </span>
                    @endif
                    @if($filtroEntrega)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                            Entrega filtrada
                        </span>
                    @endif
                    @if($filtroCuentaPublica)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                            Cuenta Pública filtrada
                        </span>
                    @endif
                    @if($filtroTipoAccion)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                            Tipo de Acción filtrado
                        </span>
                    @endif
                    @if($filtroDireccionGeneral)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                            Dirección filtrada
                        </span>
                    @endif
                    @if($filtroEnteDeLaAccion)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                            Ente de Acción filtrado
                        </span>
                    @endif
                    @if($filtroEstatusChecklist)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                            Estatus: "{{ $filtroEstatusChecklist }}"
                        </span>
                    @endif
                @endif
            </div>
            <button
                wire:click="limpiarFiltros"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-150 ease-in-out"
            >
                <ion-icon name="refresh-outline" class="text-base mr-2"></ion-icon>
                Limpiar Filtros
            </button>
        </div>
    </div>

    <!-- Estadísticas de la selección actual -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
                <div class="h-10 w-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                    <ion-icon name="stats-chart-outline" class="text-xl text-white"></ion-icon>
                </div>
            </div>
            <div class="ml-3">
                <h4 class="text-lg font-semibold text-gray-900">Estadísticas Totales</h4>
                <p class="text-sm text-gray-600">Resumen de cambios de todos los registros filtrados ({{ number_format($totalAuditorias) }} auditorías)</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total de Cambios en Comentarios -->
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 p-4 rounded-lg border border-emerald-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <ion-icon name="chatbubbles-outline" class="text-2xl text-emerald-600"></ion-icon>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-emerald-600">Total Cambios Comentarios</p>
                        <p class="text-2xl font-bold text-emerald-900">{{ number_format($totalCambiosComentarios) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total de Cambios en Observaciones -->
            <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-4 rounded-lg border border-amber-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <ion-icon name="document-text-outline" class="text-2xl text-amber-600"></ion-icon>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-amber-600">Total Cambios Observaciones</p>
                        <p class="text-2xl font-bold text-amber-900">{{ number_format($totalCambiosObservaciones) }}</p>
                    </div>
                </div>
            </div>

            <!-- Auditorías con Comentarios -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <ion-icon name="document-outline" class="text-2xl text-blue-600"></ion-icon>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-600">Auditorías con Comentarios</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $auditoriasConComentarios }}</p>
                        <p class="text-xs text-blue-700">{{ $totalAuditorias > 0 ? round(($auditoriasConComentarios / $totalAuditorias) * 100, 1) : 0 }}% del total</p>
                    </div>
                </div>
            </div>

            <!-- Auditorías con Observaciones -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <ion-icon name="clipboard-outline" class="text-2xl text-purple-600"></ion-icon>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-purple-600">Auditorías con Observaciones</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $auditoriasConObservaciones }}</p>
                        <p class="text-xs text-purple-700">{{ $totalAuditorias > 0 ? round(($auditoriasConObservaciones / $totalAuditorias) * 100, 1) : 0 }}% del total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de Etiquetas IA -->
        <div class="mt-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 bg-gradient-to-r from-pink-500 to-rose-600 rounded-lg flex items-center justify-center">
                        <ion-icon name="pricetags-outline" class="text-lg text-white"></ion-icon>
                    </div>
                </div>
                <div class="ml-3">
                    <h5 class="text-md font-semibold text-gray-900">Estadísticas de Etiquetas IA</h5>
                    <p class="text-xs text-gray-600">Etiquetas generadas automáticamente por apartado</p>
                    <p class="text-xs text-orange-600 font-medium">* Excluye la etiqueta "Procesado"</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Total Etiquetas Generadas -->
                <div class="bg-gradient-to-br from-pink-50 to-pink-100 p-4 rounded-lg border border-pink-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <ion-icon name="pricetag-outline" class="text-2xl text-pink-600"></ion-icon>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-pink-600">Total Etiquetas</p>
                            <p class="text-2xl font-bold text-pink-900">{{ number_format($this->totalEtiquetas) }}</p>
                            <p class="text-xs text-pink-700">En {{ $this->auditoriasConEtiquetas }} auditorías</p>
                        </div>
                    </div>
                </div>

                <!-- Apartados Etiquetados -->
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 rounded-lg border border-indigo-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <ion-icon name="folder-outline" class="text-2xl text-indigo-600"></ion-icon>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-indigo-600">Apartados Únicos</p>
                            <p class="text-2xl font-bold text-indigo-900">{{ number_format($this->apartadosUnicos) }}</p>
                            <p class="text-xs text-indigo-700">Con etiquetas asignadas</p>
                        </div>
                    </div>
                </div>

                <!-- Confianza Promedio -->
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 p-4 rounded-lg border border-emerald-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <ion-icon name="analytics-outline" class="text-2xl text-emerald-600"></ion-icon>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-600">Confianza Promedio</p>
                            <p class="text-2xl font-bold text-emerald-900">{{ number_format($this->confianzaPromedio, 1) }}%</p>
                            <p class="text-xs text-emerald-700">De las etiquetas IA</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Apartados con etiquetas ordenados por cantidad -->
            @if(count($this->topApartados) > 0)
                <div class="mt-4 p-4 bg-gradient-to-r from-violet-50 to-purple-50 border border-violet-200 rounded-lg">
                    <div class="mb-3">
                        <h6 class="text-sm font-semibold text-violet-800 flex items-center">
                            <ion-icon name="analytics-outline" class="text-sm mr-2"></ion-icon>
                            Apartados con Etiquetas ({{ count($this->topApartados) }} apartados)
                        </h6>
                        <p class="text-xs text-orange-600 font-medium mt-1">* No incluye la etiqueta "Procesado"</p>
                    </div>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($this->topApartados as $apartado)
                            <div class="bg-white p-4 rounded-lg border border-violet-200 shadow-sm hover:shadow-md transition-shadow">
                                <!-- Cabecera del apartado -->
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h7 class="text-sm font-semibold text-violet-900 leading-tight" title="{{ $apartado['nombre'] }}">
                                            {{ $apartado['nombre'] }}
                                        </h7>
                                        <p class="text-xs text-violet-600 mt-1">
                                            Total: {{ $apartado['total_etiquetas'] }} etiqueta{{ $apartado['total_etiquetas'] > 1 ? 's' : '' }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-violet-100 text-violet-800 ml-3">
                                        {{ $apartado['total_etiquetas'] }}
                                    </span>
                                </div>
                                
                                <!-- Detalles de etiquetas -->
                                <div class="border-t border-violet-100 pt-3">
                                    <p class="text-xs font-medium text-violet-700 mb-2">Etiquetas por tipo:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($apartado['etiquetas_detalle'] as $etiquetaDetalle)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $etiquetaDetalle['color_css'] }} border">
                                                <ion-icon name="pricetag-outline" class="text-xs mr-1"></ion-icon>
                                                {{ $etiquetaDetalle['nombre'] }}
                                                <span class="ml-1 font-bold">{{ $etiquetaDetalle['cantidad'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Formato de texto como lo solicita el usuario -->
                                    <div class="mt-2 p-2 bg-gray-50 rounded text-xs text-gray-700">
                                        <strong>Resumen:</strong> 
                                        @foreach($apartado['etiquetas_detalle'] as $index => $etiquetaDetalle)
                                            {{ $etiquetaDetalle['nombre'] }} {{ $etiquetaDetalle['cantidad'] }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        @if($totalCambiosComentarios > 0 || $totalCambiosObservaciones > 0)
            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <ion-icon name="checkmark-circle-outline" class="text-green-600 text-lg mr-2"></ion-icon>
                    <p class="text-sm text-green-800">
                        <strong>Actividad registrada:</strong> Se han detectado 
                        <span class="font-semibold">{{ number_format($totalCambiosComentarios + $totalCambiosObservaciones) }}</span> 
                        cambios totales en todas las auditorías que coinciden con los filtros aplicados.
                    </p>
                </div>
            </div>
        @else
            <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                <div class="flex items-center">
                    <ion-icon name="information-circle-outline" class="text-gray-500 text-lg mr-2"></ion-icon>
                    <p class="text-sm text-gray-600">
                        No se han detectado cambios en comentarios u observaciones en las auditorías que coinciden con los filtros aplicados.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Información adicional -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <ion-icon name="information-circle-outline" class="text-xl text-blue-400"></ion-icon>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Información:</strong> 
                    Esta vista muestra un resumen consolidado de todas las auditorías con 
                    su historial de cambios en comentarios y observaciones. 
                    🆕 <strong>Sistema de etiquetas IA optimizado</strong> - ahora procesa por apartado padre eliminando duplicados y mostrando información detallada del apartado y confianza de cada etiqueta. <br>
                    <strong>Nota:</strong> 
                    Los filtros se aplican a la tabla de resultados, no a las estadísticas. Las etiquetas con <span class="font-semibold text-green-700">nueva estructura</span> son más precisas y eficientes.
                </p>
            </div>
        </div>
    </div>

    <!-- Acciones y Exportación -->
    <div class="mb-6" style="">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <h4 class="text-lg font-semibold text-gray-900">Resultados</h4>
                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                    {{ number_format($auditorias->total()) }} registro{{ $auditorias->total() !== 1 ? 's' : '' }} total{{ $auditorias->total() !== 1 ? 'es' : '' }}
                </span>
            </div>
            
            <div class="flex items-center space-x-3">
                <button
                    wire:click="exportarExcel"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <div wire:loading wire:target="exportarExcel" class="mr-2">
                        <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                    </div>
                    <div wire:loading.remove wire:target="exportarExcel">
                        <ion-icon name="download-outline" class="text-base mr-2"></ion-icon>
                    </div>
                    <span wire:loading.remove wire:target="exportarExcel">Exportar a Excel</span>
                    <span wire:loading wire:target="exportarExcel">Exportando...</span>
                </button>

                @if($this->puedeGenerarEtiquetas())
                    <button
                        wire:click="abrirModalGenerarTodasEtiquetas"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                        title="Generar etiquetas automáticamente para todas las auditorías"
                    >
                        <div wire:loading wire:target="abrirModalGenerarTodasEtiquetas" class="mr-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                        </div>
                        <div wire:loading.remove wire:target="abrirModalGenerarTodasEtiquetas">
                            <ion-icon name="sparkles-outline" class="text-base mr-2"></ion-icon>
                        </div>
                        <span wire:loading.remove wire:target="abrirModalGenerarTodasEtiquetas">Generar todas las etiquetas</span>
                        <span wire:loading wire:target="abrirModalGenerarTodasEtiquetas">Cargando...</span>
                    </button>
                @endif

                <!-- Botón para limpiar caché manualmente -->
                @if($this->puedeGenerarEtiquetas())
                    <button
                        wire:click="limpiarCacheEstadisticas"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                        title="Actualizar caché de estadísticas para mostrar datos más recientes"
                    >
                        <div wire:loading wire:target="limpiarCacheEstadisticas" class="mr-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                        </div>
                        <div wire:loading.remove wire:target="limpiarCacheEstadisticas">
                            <ion-icon name="refresh-outline" class="text-base mr-2"></ion-icon>
                        </div>
                        <span wire:loading.remove wire:target="limpiarCacheEstadisticas">Actualizar Estadísticas</span>
                        <span wire:loading wire:target="limpiarCacheEstadisticas">Actualizando...</span>
                    </button>
                @endif

                <!-- Botón para diagnosticar el caché -->
                @if($this->puedeGenerarEtiquetas())
                    <button
                        wire:click="diagnosticarCache"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                        title="Diagnosticar el estado del caché y verificar su funcionamiento"
                    >
                        <div wire:loading wire:target="diagnosticarCache" class="mr-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                        </div>
                        <div wire:loading.remove wire:target="diagnosticarCache">
                            <ion-icon name="bug-outline" class="text-base mr-2"></ion-icon>
                        </div>
                        <span wire:loading.remove wire:target="diagnosticarCache">Diagnosticar Caché</span>
                        <span wire:loading wire:target="diagnosticarCache">Diagnosticando...</span>
                    </button>
                @endif

                <!-- Botón para diagnosticar el cálculo de etiquetas -->
                @if($this->puedeGenerarEtiquetas())
                    <button
                        wire:click="diagnosticarCalculoEtiquetas"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                        title="Diagnosticar paso a paso por qué solo se van a procesar {{ number_format($this->totalEtiquetasAProcesar) }} apartados"
                    >
                        <div wire:loading wire:target="diagnosticarCalculoEtiquetas" class="mr-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                        </div>
                        <div wire:loading.remove wire:target="diagnosticarCalculoEtiquetas">
                            <ion-icon name="analytics-outline" class="text-base mr-2"></ion-icon>
                        </div>
                        <span wire:loading.remove wire:target="diagnosticarCalculoEtiquetas">Verificar Cálculo</span>
                        <span wire:loading wire:target="diagnosticarCalculoEtiquetas">Analizando...</span>
                    </button>
                @endif
                
                @if($this->hasFiltrosActivos())
                    <div class="text-xs text-gray-500 max-w-xs">
                        <ion-icon name="information-circle-outline" class="text-sm mr-1"></ion-icon>
                        Se exportarán todas las claves que coincidan con los filtros actuales
                    </div>
                @endif
            </div>
        </div>
        
        @if($auditorias->total() > $auditorias->perPage())
            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <ion-icon name="information-circle-outline" class="text-blue-500 text-lg mr-2"></ion-icon>
                    <p class="text-sm text-blue-700">
                        <strong>Nota:</strong> La tabla muestra {{ $auditorias->count() }} de {{ number_format($auditorias->total()) }} registros. 
                        El botón "Exportar a Excel" incluirá <strong>todos los {{ number_format($auditorias->total()) }} registros</strong> que coinciden con los filtros actuales.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Tabla de Resultados -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <!-- Paginación -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $auditorias->links() }}
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gradient-to-r from-purple-50 to-indigo-50">
                        <th class="px-6 py-4 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <ion-icon name="key-outline" class="text-sm text-purple-600 mr-2"></ion-icon>
                                Clave de Acción
                            </div>
                        </th>
                        <th class="px-6 py-4 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <ion-icon name="business-outline" class="text-sm text-indigo-600 mr-2"></ion-icon>
                                Dirección General
                            </div>
                        </th>
                        <th class="px-6 py-4 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <ion-icon name="information-circle-outline" class="text-sm text-blue-600 mr-2"></ion-icon>
                                Información Adicional
                            </div>
                        </th>
                        <th class="px-6 py-4 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <ion-icon name="chatbubbles-outline" class="text-sm text-emerald-600 mr-2"></ion-icon>
                                Historial Comentarios
                            </div>
                        </th>
                        <th class="px-6 py-4 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <ion-icon name="document-text-outline" class="text-sm text-amber-600 mr-2"></ion-icon>
                                Historial Observaciones
                            </div>
                        </th>
                        <th class="px-6 py-4 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <ion-icon name="pricetags-outline" class="text-sm text-rose-600 mr-2"></ion-icon>
                                Etiquetas
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($auditorias as $auditoria)
                        <tr class="hover:bg-purple-50 transition duration-200 ease-in-out">
                            <!-- Clave de Acción -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 mb-1">
                                        {{ $auditoria->clave_de_accion }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        Actualizado: {{ $auditoria->updated_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </td>

                            <!-- Dirección General -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800">
                                    {{ $auditoria->catDgsegEf->valor ?? 'No asignada' }}
                                </span>
                            </td>

                            <!-- Información Adicional -->
                            <td class="px-6 py-4">
                                <div class="space-y-1" style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    @if($auditoria->catEntrega)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                            <ion-icon name="folder-outline" class="text-xs mr-1"></ion-icon>
                                            &nbsp;{{ $auditoria->catEntrega->valor }}
                                        </span>
                                    @endif
                                    @if($auditoria->catCuentaPublica)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                            <ion-icon name="document-text-outline" class="text-xs mr-1"></ion-icon>
                                            {{ $auditoria->catCuentaPublica->valor }}
                                        </span>
                                    @endif
                                    @if($auditoria->catSiglasTipoAccion)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-orange-100 text-orange-800">
                                            <ion-icon name="play-outline" class="text-xs mr-1"></ion-icon>
                                            {{ $auditoria->catSiglasTipoAccion->valor }}
                                        </span>
                                    @endif
                                    @if($auditoria->catEnteDeLaAccion)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-teal-100 text-teal-800">
                                            <ion-icon name="people-outline" class="text-xs mr-1"></ion-icon>
                                            {{ $auditoria->catEnteDeLaAccion->valor }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Historial de Comentarios -->
                            <td class="px-6 py-4" style="width: 30%;">
                                @if($auditoria->total_cambios_comentarios > 0)
                                    <div class="space-y-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <ion-icon name="chatbubbles-outline" class="text-xs mr-1"></ion-icon>
                                            &nbsp;{{ $auditoria->total_cambios_comentarios }} cambio{{ $auditoria->total_cambios_comentarios > 1 ? 's' : '' }}
                                        </span>
                                        
                                        <div class="max-h-32 overflow-y-auto space-y-1">
                                            @foreach($auditoria->historial_comentarios as $comentario)
                                                <div class="text-xs p-2 bg-gray-50 rounded border-l-2 border-emerald-400">
                                                    <div class="flex justify-between items-center mb-1" style="display: flex; flex-direction: column-reverse;; gap: 0.5rem;">
                                                        <span class="font-medium text-emerald-700">{{ $comentario['usuario'] }}</span>
                                                        <span class="text-gray-500">{{ $comentario['fecha'] }}</span>
                                                    </div>
                                                    <div class="text-gray-700">
                                                            {{ $comentario['despues'] }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 italic">Sin cambios registrados</span>
                                @endif
                            </td>

                            <!-- Historial de Observaciones -->
                            <td class="px-6 py-4" style="width: 30%;">
                                @if($auditoria->total_cambios_observaciones > 0)
                                    <div class="space-y-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            <ion-icon name="document-text-outline" class="text-xs mr-1"></ion-icon>
                                            {{ $auditoria->total_cambios_observaciones }} cambio{{ $auditoria->total_cambios_observaciones > 1 ? 's' : '' }}
                                        </span>
                                        
                                        <div class="max-h-32 overflow-y-auto space-y-1">
                                            @foreach($auditoria->historial_observaciones as $observacion)
                                                <div class="text-xs p-2 bg-gray-50 rounded border-l-2 border-amber-400">
                                                    <div class="flex justify-between items-center mb-1" style="flex-direction: column-reverse;">
                                                        <div class="flex flex-col">
                                                            <span class="font-medium text-amber-700">{{ $observacion['usuario'] }}</span>
                                                            <span class="text-gray-600">{{ $observacion['apartado'] }} - {{ $observacion['tipo'] }}</span>
                                                        </div>
                                                        <span class="text-gray-500">{{ $observacion['fecha'] }}</span>
                                                    </div>
                                                    <div class="text-gray-700">
                                                            {{ $observacion['despues'] }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 italic">Sin cambios registrados</span>
                                @endif
                            </td>

                            <!-- Etiquetas -->
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    @if($auditoria->auditoriaEtiquetas->count() > 0)
                                        <!-- Mostrar etiquetas existentes con información del apartado -->
                                        <div class="max-h-32 overflow-y-auto space-y-1">
                                            @foreach($auditoria->auditoriaEtiquetas->take(3) as $auditoriaEtiqueta)
                                                <div class="border border-gray-200 rounded-lg p-2 hover:border-purple-300 transition">
                                                    <div class="flex flex-col space-y-1">
                                                        <!-- Etiqueta principal -->
                                                        <span 
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $auditoriaEtiqueta->etiqueta->color_css }} cursor-pointer hover:opacity-80"
                                                            wire:click="verEtiquetas({{ $auditoria->id }})"
                                                            title="Ver detalles de etiquetas"
                                                        >
                                                            <ion-icon name="pricetag-outline" class="text-xs mr-1"></ion-icon>
                                                            {{ $auditoriaEtiqueta->etiqueta->nombre }}
                                                        </span>
                                                        
                                                        <!-- Información del apartado -->
                                                        @if($auditoriaEtiqueta->apartado_id && $auditoriaEtiqueta->apartado)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                                <ion-icon name="folder-outline" class="text-xs mr-1"></ion-icon>
                                                                {{ Str::limit($auditoriaEtiqueta->apartado->nombre, 40) }}
                                                            </span>
                                                        @elseif($auditoriaEtiqueta->checklistApartado && $auditoriaEtiqueta->checklistApartado->apartado)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">
                                                                <ion-icon name="document-outline" class="text-xs mr-1"></ion-icon>
                                                                {{ Str::limit($auditoriaEtiqueta->checklistApartado->apartado->nombre, 40) }}
                                                                <span class="ml-1 text-orange-600">(Legacy)</span>
                                                            </span>
                                                        @endif
                                                        
                                                        <!-- Confianza IA -->
                                                        @if($auditoriaEtiqueta->confianza_ia > 0)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700">
                                                                <ion-icon name="analytics-outline" class="text-xs mr-1"></ion-icon>
                                                                {{ round($auditoriaEtiqueta->confianza_ia * 100) }}% confianza
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            
                                            @if($auditoria->auditoriaEtiquetas->count() > 3)
                                                <span 
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 cursor-pointer hover:bg-gray-200"
                                                    wire:click="verEtiquetas({{ $auditoria->id }})"
                                                    title="Ver todas las etiquetas"
                                                >
                                                    +{{ $auditoria->auditoriaEtiquetas->count() - 3 }} más
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if($this->puedeGenerarEtiquetas())
                                            <button
                                                wire:click="generarEtiquetas({{ $auditoria->id }})"
                                                wire:loading.attr="disabled"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 focus:outline-none disabled:opacity-50"
                                                title="Regenerar etiquetas"
                                            >
                                                <div wire:loading wire:target="generarEtiquetas({{ $auditoria->id }})" class="mr-1">
                                                    <div class="animate-spin rounded-full h-3 w-3 border border-blue-600 border-t-transparent"></div>
                                                </div>
                                                <ion-icon name="refresh-outline" class="text-xs mr-1"></ion-icon>
                                                <span wire:loading.remove wire:target="generarEtiquetas({{ $auditoria->id }})">Regenerar</span>
                                                <span wire:loading wire:target="generarEtiquetas({{ $auditoria->id }})">Procesando...</span>
                                            </button>
                                        @endif
                                    @else
                                        <!-- Sin etiquetas, mostrar botón para generar -->
                                        <div class="flex flex-col items-start space-y-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                <ion-icon name="pricetag-outline" class="text-xs mr-1"></ion-icon>
                                                Sin etiquetas
                                            </span>
                                            
                                            @if($this->puedeGenerarEtiquetas())
                                                <button
                                                    wire:click="generarEtiquetas({{ $auditoria->id }})"
                                                    wire:loading.attr="disabled"
                                                    style="background-color: #007bff; color: white; border-radius: 5px; padding: 5px 10px; font-size: 12px; font-weight: bold;"
                                                    title="Generar etiquetas automáticamente usando IA para este expediente"
                                                >
                                                    <div wire:loading wire:target="generarEtiquetas({{ $auditoria->id }})" class="mr-2">
                                                        <div class="animate-spin rounded-full h-3 w-3 border-2 border-white border-t-transparent"></div>
                                                    </div>
                                                    <div wire:loading.remove wire:target="generarEtiquetas({{ $auditoria->id }})">
                                                        <ion-icon name="sparkles-outline" class="text-xs mr-1"></ion-icon>
                                                    </div>
                                                    <span wire:loading.remove wire:target="generarEtiquetas({{ $auditoria->id }})">Generar etiquetas</span>
                                                    <span wire:loading wire:target="generarEtiquetas({{ $auditoria->id }})">Generando etiquetas...</span>
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Sin permisos para generar</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <ion-icon name="search-outline" class="text-6xl text-gray-400 mb-4"></ion-icon>
                                    <p class="text-xl font-medium text-gray-500 mb-2">No se encontraron auditorías</p>
                                    <p class="text-gray-400">Intenta ajustar los criterios de búsqueda o filtros</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
                 </div>
     </div>

     <!-- Modal de Etiquetas -->
     @if($modalEtiquetasAbierto)
         <div class="fixed inset-0 z-50 overflow-y-auto">
             <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                 <!-- Overlay -->
                 <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="cerrarModalEtiquetas"></div>

                 <!-- Modal -->
                 <div class="inline-block w-full max-w-4xl px-6 py-4 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl">
                     <!-- Header del Modal -->
                     <div class="flex items-center justify-between mb-6">
                         <div class="flex items-center">
                             <div class="flex-shrink-0">
                                 <div class="h-10 w-10 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                     <ion-icon name="pricetags-outline" class="text-xl text-white"></ion-icon>
                                 </div>
                             </div>
                             <div class="ml-3">
                                 <h3 class="text-lg font-semibold text-gray-900">
                                     Etiquetas de Auditoría
                                 </h3>
                                 @if($auditoriaSeleccionada)
                                     <p class="text-sm text-gray-600">
                                         Clave de Acción: <span class="font-medium">{{ $auditoriaSeleccionada->clave_de_accion }}</span>
                                     </p>
                                 @endif
                             </div>
                         </div>
                         <button
                             wire:click="cerrarModalEtiquetas"
                             class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150"
                         >
                             <ion-icon name="close-outline" class="text-2xl"></ion-icon>
                         </button>
                     </div>

                     <!-- Contenido del Modal -->
                     <div class="max-h-96 overflow-y-auto">
                         @if($etiquetasSeleccionadas && $etiquetasSeleccionadas->count() > 0)
                             <div class="space-y-4">
                                 @foreach($etiquetasSeleccionadas as $auditoriaEtiqueta)
                                     <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition duration-150">
                                         <!-- Etiqueta y detalles -->
                                         <div class="flex items-start justify-between mb-3">
                                             <div class="flex items-center space-x-3">
                                                 <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $auditoriaEtiqueta->etiqueta->color_css }}">
                                                     <ion-icon name="pricetag-outline" class="text-sm mr-2"></ion-icon>
                                                     {{ $auditoriaEtiqueta->etiqueta->nombre }}
                                                 </span>
                                                 
                                                 <!-- Información del apartado mejorada -->
                                                 @if($auditoriaEtiqueta->apartado_id && $auditoriaEtiqueta->apartado)
                                                     <!-- Nueva estructura: apartado padre -->
                                                     <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                                         <ion-icon name="folder-outline" class="text-xs mr-1"></ion-icon>
                                                         {{ $auditoriaEtiqueta->apartado->nombre }}
                                                     </span>
                                                 @elseif($auditoriaEtiqueta->checklistApartado && $auditoriaEtiqueta->checklistApartado->apartado)
                                                     <!-- Estructura legacy: apartado individual -->
                                                     <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                                         <ion-icon name="document-outline" class="text-xs mr-1"></ion-icon>
                                                         {{ Str::limit($auditoriaEtiqueta->checklistApartado->apartado->nombre, 50) }}
                                                     </span>
                                                     <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-orange-100 text-orange-800">
                                                         <ion-icon name="warning-outline" class="text-xs mr-1"></ion-icon>
                                                         Legacy
                                                     </span>
                                                 @else
                                                     <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800">
                                                         <ion-icon name="alert-circle-outline" class="text-xs mr-1"></ion-icon>
                                                         Sin apartado
                                                     </span>
                                                 @endif
                                             </div>
                                             
                                             <div class="flex items-center space-x-2 text-xs text-gray-500">
                                                 @if($auditoriaEtiqueta->confianza_ia > 0)
                                                     <span class="inline-flex items-center px-2 py-1 rounded-md bg-emerald-50 text-emerald-700">
                                                         <ion-icon name="analytics-outline" class="text-xs mr-1"></ion-icon>
                                                         {{ round($auditoriaEtiqueta->confianza_ia * 100) }}% confianza
                                                     </span>
                                                 @endif
                                                 <span class="bg-gray-100 px-2 py-1 rounded">{{ $auditoriaEtiqueta->procesado_en ? $auditoriaEtiqueta->procesado_en->format('d/m/Y H:i') : 'Sin fecha' }}</span>
                                             </div>
                                         </div>

                                         <!-- Razón de asignación -->
                                         <div class="bg-gray-50 rounded-md p-3">
                                             <h4 class="text-sm font-medium text-gray-700 mb-2">Razón de asignación:</h4>
                                             <p class="text-sm text-gray-600">{{ $auditoriaEtiqueta->razon_asignacion }}</p>
                                         </div>

                                         <!-- Comentario fuente (si existe) -->
                                         @if($auditoriaEtiqueta->comentario_fuente)
                                             <div class="mt-3 bg-blue-50 rounded-md p-3">
                                                 <h4 class="text-sm font-medium text-blue-700 mb-2">Comentario fuente:</h4>
                                                 @php
                                                     $comentarios = explode('|', $auditoriaEtiqueta->comentario_fuente);
                                                     $comentarios = array_map('trim', $comentarios);
                                                     $comentarios = array_filter($comentarios);
                                                     $conteoComentarios = array_count_values($comentarios);
                                                 @endphp
                                                 <div class="space-y-2">
                                                     @foreach($conteoComentarios as $comentario => $cantidad)
                                                         <div class="flex items-start space-x-2">
                                                             @if($cantidad > 1)
                                                                 <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 flex-shrink-0">
                                                                     {{ $cantidad }} apariciones
                                                                 </span>
                                                             @endif
                                                             <p class="text-sm text-blue-600 flex-1">
                                                                 {{ $comentario }}
                                                             </p>
                                                         </div>
                                                     @endforeach
                                                 </div>
                                             </div>
                                         @endif

                                         <!-- Usuario que procesó -->
                                         @if($auditoriaEtiqueta->procesadoPor)
                                             <div class="mt-2 text-xs text-gray-500">
                                                 <ion-icon name="person-outline" class="text-xs mr-1"></ion-icon>
                                                 Procesado por: {{ $auditoriaEtiqueta->procesadoPor->name }}
                                             </div>
                                         @endif
                                     </div>
                                 @endforeach
                             </div>
                         @else
                             <div class="text-center py-8">
                                 <ion-icon name="pricetags-outline" class="text-6xl text-gray-300 mb-4"></ion-icon>
                                 <p class="text-lg font-medium text-gray-500 mb-2">No hay etiquetas disponibles</p>
                                 <p class="text-gray-400">Esta auditoría aún no tiene etiquetas generadas.</p>
                             </div>
                         @endif
                     </div>

                     <!-- Footer del Modal -->
                     <div class="mt-6 flex justify-end space-x-3">
                         @if($this->puedeGenerarEtiquetas() && $auditoriaSeleccionada)
                             <button
                                 wire:click="generarEtiquetas({{ $auditoriaSeleccionada->id }})"
                                 wire:loading.attr="disabled"
                                 class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-150 ease-in-out disabled:opacity-50"
                             >
                                 <div wire:loading wire:target="generarEtiquetas({{ $auditoriaSeleccionada->id }})" class="mr-2">
                                     <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                                 </div>
                                 <ion-icon name="sparkles-outline" class="text-sm mr-2"></ion-icon>
                                 <span wire:loading.remove wire:target="generarEtiquetas({{ $auditoriaSeleccionada->id }})">Regenerar Etiquetas</span>
                                 <span wire:loading wire:target="generarEtiquetas({{ $auditoriaSeleccionada->id }})">Regenerando...</span>
                             </button>
                         @endif
                         
                         <button
                             wire:click="cerrarModalEtiquetas"
                             class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
                         >
                             Cerrar
                         </button>
                     </div>
                 </div>
             </div>
         </div>
     @endif

     <!-- Modal de Confirmación para Generar Todas las Etiquetas -->
     @if($modalGenerarTodasEtiquetas)
         <div class="fixed inset-0 z-50 overflow-y-auto">
             <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                 <!-- Overlay -->
                 <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="cerrarModalGenerarTodasEtiquetas"></div>

                 <!-- Modal -->
                 <div class="inline-block w-full max-w-2xl px-6 py-4 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl">
                     <!-- Header del Modal -->
                     <div class="flex items-center justify-between mb-6">
                         <div class="flex items-center">
                             <div class="flex-shrink-0">
                                 <div class="h-12 w-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center">
                                     <ion-icon name="warning-outline" class="text-2xl text-white"></ion-icon>
                                 </div>
                             </div>
                             <div class="ml-4">
                                 <h3 class="text-xl font-semibold text-gray-900">
                                     Confirmar Generación Masiva
                                 </h3>
                                 <p class="text-sm text-gray-600">
                                     Generación automática de etiquetas con IA
                                 </p>
                             </div>
                         </div>
                         <button
                             wire:click="cerrarModalGenerarTodasEtiquetas"
                             class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150"
                         >
                             <ion-icon name="close-outline" class="text-2xl"></ion-icon>
                         </button>
                     </div>

                     <!-- Contenido del Modal -->
                     <div class="mb-6">
                                                      <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                                 <div class="flex items-start">
                                     <div class="flex-shrink-0">
                                         <ion-icon name="alert-circle-outline" class="text-xl text-orange-600 mt-1"></ion-icon>
                                     </div>
                                     <div class="ml-3">
                                         <h4 class="text-lg font-medium text-orange-800 mb-2">
                                             ¿Estás seguro que deseas generar las {{ number_format($this->totalEtiquetasAProcesar) }} etiquetas para la selección actual?
                                         </h4>
                                         <p class="text-sm text-orange-700 mb-3">
                                             <strong>Solo se procesarán apartados que realmente lo necesiten.</strong>
                                             @if($auditorias->total() > 500)
                                                 <br><span class="text-blue-600">📊 Cantidad estimada basada en muestra de datos</span>
                                             @endif
                                         </p>
                                         <ul class="text-sm text-orange-700 space-y-1">
                                             <li class="flex items-center">
                                                 <ion-icon name="checkmark-circle-outline" class="text-sm mr-2"></ion-icon>
                                                 Se procesarán <strong>{{ number_format($this->totalEtiquetasAProcesar) }}</strong> apartados que necesitan etiquetas o actualizaciones
                                             </li>
                                             <li class="flex items-center">
                                                 <ion-icon name="shield-checkmark-outline" class="text-sm mr-2"></ion-icon>
                                                 Los apartados con etiquetas actualizadas <strong>NO serán reprocesados</strong>
                                             </li>
                                             <li class="flex items-center">
                                                 <ion-icon name="time-outline" class="text-sm mr-2"></ion-icon>
                                                 El proceso será <strong>más eficiente</strong> al evitar procesamiento innecesario
                                             </li>
                                             <li class="flex items-center">
                                                 <ion-icon name="card-outline" class="text-sm mr-2"></ion-icon>
                                                 Se optimizará el uso de tokens de la API de IA
                                             </li>
                                             <li class="flex items-center">
                                                 <ion-icon name="refresh-outline" class="text-sm mr-2"></ion-icon>
                                                 Solo se actualizarán apartados con comentarios nuevos o modificados
                                             </li>
                                         </ul>
                                     </div>
                                 </div>
                             </div>

                                                      @if($this->totalEtiquetasAProcesar > 0)
                                 <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                     <div class="flex items-start">
                                         <div class="flex-shrink-0">
                                             <ion-icon name="information-circle-outline" class="text-xl text-blue-600 mt-1"></ion-icon>
                                         </div>
                                         <div class="ml-3">
                                             <h4 class="text-sm font-medium text-blue-800 mb-2">Sistema de procesamiento inteligente:</h4>
                                             <ul class="text-sm text-blue-700 space-y-1">
                                                 <li>• Se aplicarán los filtros actuales de la vista</li>
                                                 <li>• Solo apartados con comentarios nuevos o sin etiquetas previas</li>
                                                 <li>• Apartados con etiquetas actualizadas se omiten automáticamente</li>
                                                 <li>• Verificación por fecha de última modificación vs. última etiqueta</li>
                                                 <li>• El sistema optimiza agrupando por apartado padre</li>
                                                 <li>• Notificación automática al completar el proceso</li>
                                             </ul>
                                             @if($auditorias->total() > 500)
                                                 <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs">
                                                     <strong>💡 Nota:</strong> Para grandes volúmenes de datos, se usa muestreo estadístico para calcular estimaciones rápidas.
                                                 </div>
                                             @endif
                                         </div>
                                     </div>
                                 </div>
                             @else
                             <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                 <div class="flex items-center">
                                     <ion-icon name="alert-circle-outline" class="text-xl text-gray-500 mr-3"></ion-icon>
                                     <p class="text-sm text-gray-700">
                                         No hay apartados con comentarios para procesar en la selección actual.
                                     </p>
                                 </div>
                             </div>
                         @endif
                     </div>

                     <!-- Footer del Modal -->
                     <div class="flex justify-end space-x-3">
                         <button
                             wire:click="cerrarModalGenerarTodasEtiquetas"
                             class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
                         >
                             <ion-icon name="close-outline" class="text-sm mr-2"></ion-icon>
                             Cancelar
                         </button>
                         
                         @if($this->totalEtiquetasAProcesar > 0)
                             <button
                                 wire:click="generarTodasLasEtiquetas"
                                 wire:loading.attr="disabled"
                                 class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out disabled:opacity-50"
                             >
                                 <div wire:loading wire:target="generarTodasLasEtiquetas" class="mr-2">
                                     <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                                 </div>
                                 <div wire:loading.remove wire:target="generarTodasLasEtiquetas">
                                     <ion-icon name="flash-outline" class="text-sm mr-2"></ion-icon>
                                 </div>
                                 <span wire:loading.remove wire:target="generarTodasLasEtiquetas">Sí, generar todas las etiquetas</span>
                                 <span wire:loading wire:target="generarTodasLasEtiquetas">Iniciando procesamiento...</span>
                             </button>
                         @endif
                     </div>
                 </div>
             </div>
         </div>
     @endif

 </div> 