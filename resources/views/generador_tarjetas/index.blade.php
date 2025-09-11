<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Generador de Tarjetas') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Zona de carga del archivo Excel -->
                <div id="upload-container" class="mb-8">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-indigo-500 transition-colors duration-200 ease-in-out cursor-pointer" id="drop-area">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="mt-4 flex text-sm text-gray-600">
                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Seleccionar archivo Excel</span>
                                    <input id="file-upload" name="file-upload" type="file" class="sr-only" accept=".xlsx">
                                </label><br>
                                <p class="pl-1">o arrastre y suelte aquí</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Solo archivos XLSX hasta 10 MB</p>
                        </div>
                    </div>

                    <!-- Barra de progreso -->
                    <div id="progress-container" class="mt-4 hidden">
                        <div class="relative pt-1">
                            <div class="flex mb-2 items-center justify-between">
                                <div>
                                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-indigo-600 bg-indigo-200">
                                        Progreso
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span id="progress-percentage" class="text-xs font-semibold inline-block text-indigo-600">
                                        0%
                                    </span>
                                </div>
                            </div>
                            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-200">
                                <div id="progress-bar" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenedor para mensajes de error -->
                <div id="error-container" class="hidden mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    <p class="font-bold">Error</p>
                    <p id="error-message"></p>
                </div>

                <!-- Tabla de datos -->
                <div id="table-container" class="hidden mt-6">
                    <!-- Panel de herramientas de la tabla -->
                    <div class="flex flex-col md:flex-row justify-between mb-4 gap-3 items-center bg-gray-50 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-gray-700 font-medium">Registros cargados: <span id="total-records" class="text-indigo-600 font-bold">0</span></h3>
                            <div class="h-4 w-px bg-gray-300"></div>
                            <div id="search-container" class="relative flex-1">
                                <input type="text" id="quick-search" class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Búsqueda rápida...">
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button id="advanced-search-btn" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors duration-200 ease-in-out flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                                </svg>
                                Búsqueda Avanzada
                            </button>
                            <button id="exportar" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors duration-200 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Exportar Excel
                            </button>
                        </div>
                    </div>

                    <!-- Espacio para el constructor de búsqueda avanzada -->
                    <div id="search-builder-container" class="mb-4 hidden bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200 transition-all duration-300">
                        <!-- SearchBuilder se renderizará aquí -->
                    </div>

                    <!-- Tabla con datos -->
                    <div class="overflow-hidden border border-gray-200 rounded-lg shadow-md">
                        <table id="tabla" class="w-full min-w-full divide-y divide-gray-200 table-fixed">
                            <thead class="bg-gray-50">
                                <tr>
                                    <!-- Las columnas se agregarán dinámicamente mediante JavaScript -->
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Las filas se agregarán dinámicamente mediante JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Información y paginación -->
                    <div id="pagination-info" class="mt-4 flex flex-col md:flex-row justify-between items-center text-sm text-gray-600">
                        <div id="table-info" class="mb-2 md:mb-0">
                            Mostrando <span class="font-medium">0</span> de <span class="font-medium">0</span> registros
                        </div>
                        <div id="table-pagination" class="flex items-center space-x-1">
                            <!-- Paginación se agregará dinámicamente -->
                        </div>
                    </div>
                </div>

                <!-- Sección de generación de tarjetas informativas -->
                <div id="tarjeta-generator" class="mt-8 hidden">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Generación de Tarjeta Informativa</h3>
                        
                        <!-- Generador de resumen ejecutivo -->
                        <div class="mb-6" id="resumen-ejecutivo-section">
                            <h4 class="font-medium text-gray-700 mb-2">Resumen Ejecutivo con IA</h4>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div id="resumen-ejecutivo" class="mb-4">
                                    <p class="text-gray-500 text-sm">Seleccione una entidad y genere un resumen ejecutivo inteligente de los datos filtrados.</p>
                                </div>
                                <button id="generar-resumen" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors duration-200 ease-in-out flex items-center disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Generar Resumen con IA
                                </button>
                            </div>
                        </div>
                        
                        <!-- Selección de entidad y campos -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                            <!-- Selector de Entidad Responsable -->
                            <div>
                                <label for="entidad-selector" class="block text-sm font-medium text-gray-700 mb-2">Entidad Responsable de la Acción</label>
                                <select id="entidad-selector" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Seleccione una entidad</option>
                                    <!-- Las opciones se cargarán dinámicamente -->
                                </select>
                            </div>
                            
                            <!-- Control de cantidad de registros -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cantidad de registros a mostrar</label>
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-2">
                                        <input type="number" id="cantidad-registros" min="1" max="1000" value="10" class="w-20 pl-3 pr-2 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <span class="text-sm text-gray-600">registros principales</span>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="todos-registros" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label for="todos-registros" class="ml-2 text-sm text-gray-600">Mostrar todos los registros</label>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Por defecto se muestran las 10 acciones con mayor monto. Puede cambiar la cantidad o seleccionar todos los registros disponibles.
                                    </p>
                                    <p class="text-xs text-green-600 mt-1">
                                        <strong>Procesamiento Optimizado:</strong> El sistema procesa automáticamente todos los registros en lotes de 50 para evitar timeouts. Con muchos registros, el proceso puede tomar varios minutos pero completará toda la información.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Selector de campos a incluir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Campos a incluir en la tarjeta</label>
                                <div id="campos-container" class="grid grid-cols-2 gap-2 bg-gray-50 p-3 rounded-md border border-gray-200 max-h-48 overflow-y-auto">
                                    <!-- Los checkboxes se cargarán dinámicamente -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Previsualización y botones de acción -->
                        <div class="flex flex-col space-y-4">
                            <div id="preview-container" class="hidden">
                                <h4 class="font-medium text-gray-700 mb-2">Previsualización de la Tarjeta</h4>
                                <div id="tarjeta-preview" class="bg-gray-50 p-4 rounded-lg border border-gray-200 min-h-[200px]">
                                    <!-- Aquí se mostrará la previsualización -->
                                </div>
                                
                                <!-- Formulario para editar los datos de la tarjeta -->
                                <div id="tarjeta-editor" class="mt-4 bg-white p-4 rounded-lg border border-gray-200 hidden">
                                    <h5 class="font-medium text-gray-800 mb-3">Editar datos de la tarjeta</h5>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label for="tarjeta-titulo" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                                            <input type="text" id="tarjeta-titulo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label for="tarjeta-subtitulo" class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                                            <input type="text" id="tarjeta-subtitulo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label for="tarjeta-periodo" class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                                            <input type="text" id="tarjeta-periodo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        <div class="flex items-end justify-end">
                                            <button id="agregar-fila-tabla" class="px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors duration-200 ease-in-out flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Agregar fila
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Panel de pestañas para las diferentes secciones -->
                                    <div class="mb-4 border-b border-gray-200">
                                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="tarjeta-tabs">
                                            <li class="mr-2">
                                                <a href="#" class="inline-block p-4 border-b-2 border-indigo-600 rounded-t-lg text-indigo-600 active" id="tab-resumen">A. Resumen</a>
                                            </li>
                                            <li class="mr-2">
                                                <a href="#" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="tab-acciones">B. Acciones ordenadas</a>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <!-- Contenido de la pestaña Resumen -->
                                    <div id="contenido-resumen" class="mb-4">
                                        <div class="overflow-x-auto">
                                            <table id="tarjeta-tabla-editor" class="min-w-full divide-y divide-gray-200 border">
                                                <thead>
                                                    <tr>
                                                        <th class="bg-indigo-100 px-4 py-2 text-left text-sm font-medium text-gray-700">Estatus</th>
                                                        <th class="bg-indigo-100 px-4 py-2 text-left text-sm font-medium text-gray-700">Monto en pesos</th>
                                                        <th class="bg-indigo-100 px-4 py-2 text-left text-sm font-medium text-gray-700">Porcentaje</th>
                                                        <th class="bg-indigo-100 px-4 py-2 text-left text-sm font-medium text-gray-700">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 bg-white">
                                                    <!-- Filas de datos serán agregadas dinámicamente -->
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="bg-indigo-100 px-4 py-2 text-left text-sm font-medium text-gray-700">Total general</td>
                                                        <td class="bg-indigo-100 px-4 py-2 text-left text-sm font-medium text-gray-700">
                                                            <input type="text" id="tarjeta-total-monto" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                        </td>
                                                        <td class="bg-indigo-100 px-4 py-2 text-left text-sm font-medium text-gray-700">
                                                            <input type="text" id="tarjeta-total-porcentaje" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white" value="100%">
                                                        </td>
                                                        <td class="bg-indigo-100 px-4 py-2"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- Contenido de la pestaña Acciones ordenadas -->
                                    <div id="contenido-acciones" class="mb-4 hidden">
                                        <div class="mb-3 flex justify-between items-center">
                                            <h6 class="text-sm font-medium text-gray-700">Acciones ordenadas de mayor a menor monto:</h6>
                                            <button id="agregar-accion" class="px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors duration-200 ease-in-out flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Agregar acción
                                            </button>
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table id="tarjeta-acciones-editor" class="min-w-full divide-y divide-gray-200 border text-sm">
                                                <thead>
                                                    <tr>
                                                        <th class="bg-indigo-100 px-3 py-1.5 text-center text-xs font-medium text-gray-700 w-10">No.</th>
                                                        <th class="bg-indigo-100 px-3 py-1.5 text-center text-xs font-medium text-gray-700 w-16">Cuenta Pública</th>
                                                        <th class="bg-indigo-100 px-3 py-1.5 text-center text-xs font-medium text-gray-700">Título de la Auditoría</th>
                                                        <th class="bg-indigo-100 px-3 py-1.5 text-center text-xs font-medium text-gray-700 w-20">Clave de Acción</th>
                                                        <th class="bg-indigo-100 px-3 py-1.5 text-center text-xs font-medium text-gray-700 w-16">Tipo de Acción</th>
                                                        <th class="bg-indigo-100 px-3 py-1.5 text-center text-xs font-medium text-gray-700">Descripción</th>
                                                        <th class="bg-indigo-100 px-3 py-1.5 text-center text-xs font-medium text-gray-700 w-20">Monto en pesos</th>
                                                        <th class="bg-indigo-100 px-3 py-1.5 text-center text-xs font-medium text-gray-700 w-16">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 bg-white">
                                                    <!-- Filas de acciones serán agregadas dinámicamente -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 flex justify-end space-x-3">
                                        <button id="actualizar-tarjeta" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors duration-200 ease-in-out flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                            </svg>
                                            Actualizar vista previa
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button id="preview-tarjeta" class="px-4 py-2 bg-gray-600 text-dark rounded hover:bg-gray-700 transition-colors duration-200 ease-in-out flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    Previsualizar
                                </button>
                                <button id="generar-tarjeta" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors duration-200 ease-in-out flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd" />
                                    </svg>
                                    Generar Tarjeta
                                </button>
                                <button id="descargar-tarjeta" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors duration-200 ease-in-out flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Descargar PDF
                                </button>
                                <button id="descargar-docx" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200 ease-in-out flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                    Descargar DOCX
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/searchbuilder/1.5.0/css/searchBuilder.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.1/css/dataTables.dateTime.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="{{ asset('generador_tarjetas/assets/css/tarjetas.css') }}">
    @endpush

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/searchbuilder/1.5.0/js/dataTables.searchBuilder.min.js"></script>
        <script src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <!-- Bibliotecas para generación de PDF -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <!-- Bibliotecas para exportación a DOCX -->
        <script src="https://unpkg.com/docx@7.8.2/build/index.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
        <script>
            // Asegurar que docx esté disponible globalmente
            window.addEventListener('DOMContentLoaded', function() {
                if (typeof window.docx === 'undefined' && typeof docx !== 'undefined') {
                    window.docx = docx;
                    console.log('Biblioteca docx asignada globalmente');
                }
            });
        </script>
        <script src="{{ asset('generador_tarjetas/assets/js/tarjetas.js') }}"></script>
    @endpush

    <!-- Pantalla de carga para operaciones largas -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-container">
            <div class="loading-spinner"></div>
            <p class="text-gray-800 font-medium" id="loading-message">Procesando información...</p>
        </div>
    </div>
</x-app-layout> 