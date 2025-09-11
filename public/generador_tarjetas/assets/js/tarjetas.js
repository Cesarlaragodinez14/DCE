/**
 * Generador de Tarjetas
 * ---------------------
 * Módulo para procesar archivos Excel en el navegador sin persistencia en BD,
 * utilizando SheetJS, DataTables con SearchBuilder y Buttons.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Columnas requeridas en el archivo Excel - Agregamos solo "Clave de Acción" como requerida
    const REQUIRED_COLS = [
        "Clave de Acción"
    ];

    // Referencias a elementos del DOM
    const dropArea = document.getElementById('drop-area');
    const fileInput = document.getElementById('file-upload');
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const progressPercentage = document.getElementById('progress-percentage');
    const tableContainer = document.getElementById('table-container');
    const errorContainer = document.getElementById('error-container');
    const errorMessage = document.getElementById('error-message');
    const exportBtn = document.getElementById('exportar');
    const advancedSearchBtn = document.getElementById('advanced-search-btn');
    const searchBuilderContainer = document.getElementById('search-builder-container');
    const totalRecords = document.getElementById('total-records');
    const quickSearch = document.getElementById('quick-search');
    const tableInfo = document.getElementById('table-info');
    const tablePagination = document.getElementById('table-pagination');
    
    // Elementos del generador de tarjetas
    const tarjetaGenerator = document.getElementById('tarjeta-generator');
    const entidadSelector = document.getElementById('entidad-selector');
    const camposContainer = document.getElementById('campos-container');
    const cantidadRegistrosInput = document.getElementById('cantidad-registros');
    const todosRegistrosCheckbox = document.getElementById('todos-registros');
    const generarResumenBtn = document.getElementById('generar-resumen');
    const resumenEjecutivo = document.getElementById('resumen-ejecutivo');
    const previewTarjetaBtn = document.getElementById('preview-tarjeta');
    const generarTarjetaBtn = document.getElementById('generar-tarjeta');
    const descargarTarjetaBtn = document.getElementById('descargar-tarjeta');
    const descargarDocxBtn = document.getElementById('descargar-docx');
    const tarjetaPreview = document.getElementById('tarjeta-preview');
    const previewContainer = document.getElementById('preview-container');
    
    // Elementos del editor de tarjeta
    const tarjetaEditor = document.getElementById('tarjeta-editor');
    const tarjetaTitulo = document.getElementById('tarjeta-titulo');
    const tarjetaSubtitulo = document.getElementById('tarjeta-subtitulo');
    const tarjetaPeriodo = document.getElementById('tarjeta-periodo');
    const tarjetaTablaEditor = document.getElementById('tarjeta-tabla-editor');
    const tarjetaAccionesEditor = document.getElementById('tarjeta-acciones-editor');
    const tarjetaTotalMonto = document.getElementById('tarjeta-total-monto');
    const tarjetaTotalPorcentaje = document.getElementById('tarjeta-total-porcentaje');
    const agregarFilaTablaBtn = document.getElementById('agregar-fila-tabla');
    const agregarAccionBtn = document.getElementById('agregar-accion');
    const actualizarTarjetaBtn = document.getElementById('actualizar-tarjeta');
    
    // Elementos de navegación por pestañas
    const tabResumen = document.getElementById('tab-resumen');
    const tabAcciones = document.getElementById('tab-acciones');
    const contenidoResumen = document.getElementById('contenido-resumen');
    const contenidoAcciones = document.getElementById('contenido-acciones');

    // Elementos del DOM para la pantalla de carga
    const loadingOverlay = document.getElementById('loading-overlay');
    const loadingMessage = document.getElementById('loading-message');

    let table; // Variable para almacenar la instancia de DataTable
    let isSearchBuilderVisible = false;
    let excelData = []; // Variable para almacenar los datos del Excel
    let selectedEntidad = ''; // Entidad seleccionada actualmente
    let selectedFields = []; // Campos seleccionados para la tarjeta
    let cantidadRegistros = 10; // Cantidad de registros a mostrar por defecto
    let mostrarTodosRegistros = false; // Flag para mostrar todos los registros
    let tarjetaData = { // Datos para la tarjeta en formato de tabla
        titulo: '',
        subtitulo: 'Resumen',
        periodo: '',
        filas: [],
        totalMonto: 0,
        acciones: [], // Nueva propiedad para las acciones ordenadas (incluye camposAdicionales)
        resumenIA: '' // Propiedad para almacenar el resumen generado por IA
    };

    // Habilitar la zona de drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Destacar la zona de drop cuando se arrastra un archivo sobre ella
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropArea.classList.add('border-indigo-500');
        dropArea.classList.add('bg-indigo-50');
    }

    function unhighlight() {
        dropArea.classList.remove('border-indigo-500');
        dropArea.classList.remove('bg-indigo-50');
    }

    // Manejar el evento de soltar un archivo
    dropArea.addEventListener('drop', handleDrop, false);
    fileInput.addEventListener('change', handleFileSelect, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length) {
            handleFiles(files[0]);
        }
    }

    function handleFileSelect(e) {
        const files = e.target.files;
        
        if (files.length) {
            handleFiles(files[0]);
        }
    }

    function handleFiles(file) {
        // Validar el tipo de archivo (.xlsx)
        if (!file.name.endsWith('.xlsx')) {
            showError('Solo se permiten archivos Excel (.xlsx)');
            return;
        }

        // Validar el tamaño del archivo (máximo 10 MB)
        if (file.size > 10 * 1024 * 1024) {
            showError('El archivo no debe superar los 10 MB');
            return;
        }

        // Ocultar mensajes de error anteriores
        errorContainer.classList.add('hidden');
        
        // Mostrar la barra de progreso
        progressContainer.classList.remove('hidden');
        updateProgress(0);

        // Leer el archivo Excel
        readExcelFile(file);
    }

    function showError(message, duration = 3000) {
        errorContainer.classList.remove('hidden');
        errorMessage.textContent = message;
        progressContainer.classList.add('hidden');
        
        setTimeout(() => {
            errorContainer.classList.add('hidden');
        }, duration);
    }

    function updateProgress(percent) {
        progressBar.style.width = `${percent}%`;
        progressPercentage.textContent = `${Math.round(percent)}%`;
    }

    function readExcelFile(file) {
        const reader = new FileReader();

        reader.onprogress = function(e) {
            if (e.lengthComputable) {
                const percent = (e.loaded / e.total) * 100;
                updateProgress(percent / 2); // Primera mitad del progreso (lectura del archivo)
            }
        };

        reader.onload = function(e) {
            try {
                updateProgress(50); // Lectura completa, ahora procesaremos el archivo

                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array', cellDates: true });
                
                // Tomar la primera hoja
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                
                // Convertir a array de objetos
                const rows = XLSX.utils.sheet_to_json(worksheet, { 
                    defval: '',
                    raw: false,      // Convertir a strings para evitar problemas con tipos
                    dateNF: 'yyyy-mm-dd'  // Formato de fecha para evitar problemas
                });
                
                if (rows.length === 0) {
                    showError('El archivo no contiene datos');
                    return;
                }

                updateProgress(75); // Procesamiento de datos en progreso

                // Validar encabezados
                try {
                    validateHeaders(rows);
                } catch (error) {
                    showError(error);
                    return;
                }

                // Procesar y mostrar los datos
                processData(rows);
                updateProgress(100); // Proceso completado
                
                // Ocultar la barra de progreso después de un breve retraso
                setTimeout(() => {
                    progressContainer.classList.add('hidden');
                }, 500);
                
            } catch (error) {
                showError('Error al procesar el archivo: ' + error.message);
                console.error(error);
            }
        };

        reader.onerror = function() {
            showError('Error al leer el archivo');
        };

        // Leer el archivo como ArrayBuffer
        reader.readAsArrayBuffer(file);
    }

    function validateHeaders(rows) {
        if (rows.length === 0) return;
        
        const headers = Object.keys(rows[0]);
        
        const missing = REQUIRED_COLS.filter(req => 
            !headers.some(h => h.trim().toLowerCase() === req.trim().toLowerCase())
        );
        
        if (missing.length) {
            throw `Faltan columnas requeridas: ${missing.join(', ')}`;
        }
    }

    function processData(data) {
        // Mostrar el contenedor de la tabla
        tableContainer.classList.remove('hidden');
        tableContainer.classList.add('animate-fade-in');
        
        // Normalizamos los datos para evitar problemas con los nombres de columnas
        const normalizedData = normalizeData(data);
        excelData = normalizedData; // Guardamos los datos para usarlos en el generador de tarjetas
        
        // Actualizar contador de registros
        totalRecords.textContent = normalizedData.length;
        
        // Obtener las columnas para DataTables
        const columns = Object.keys(normalizedData[0]).map(key => ({
            title: key,
            data: key
        }));

        // Inicializar o actualizar DataTable
        if (table) {
            // Si la tabla ya existe, destruirla para evitar errores
            table.destroy();
        }
        
        // Inicializar la tabla
        table = $('#tabla').DataTable({
            data: normalizedData,
            columns: columns,
            deferRender: true,
            scrollY: '60vh',
            scrollCollapse: true,
            scroller: true,
            paging: true,
            responsive: true,
            dom: 'Bfrtip',
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                searchBuilder: {
                    add: 'Añadir condición',
                    button: 'Filtros avanzados',
                    clearAll: 'Limpiar todo',
                    condition: 'Condición',
                    data: 'Columna',
                    deleteTitle: 'Eliminar',
                    leftTitle: 'Mover a la izquierda',
                    logicAnd: 'Y',
                    logicOr: 'O',
                    rightTitle: 'Mover a la derecha',
                    title: {
                        0: 'Constructor de búsqueda',
                        _: 'Constructor de búsqueda (%d)'
                    },
                    value: 'Valor'
                }
            },
            buttons: [
                {
                    extend: 'searchBuilder',
                    config: {
                        depthLimit: 2,
                        container: $('#search-builder-container'),
                    }
                }
            ],
            order: [],
            columnDefs: [
                {
                    targets: '_all',
                    className: 'border-x border-gray-100'
                }
            ],
            initComplete: function() {
                // Habilitar el botón de exportar
                exportBtn.removeAttribute('disabled');
                
                        // Evento de búsqueda rápida
        quickSearch.addEventListener('keyup', function() {
            table.search(this.value).draw();
            updateTableInfo();
        });
        

                
                // Personalizar paginación
                customizePagination();
                
                // Actualizar información de la tabla
                updateTableInfo();
            },
            drawCallback: function() {
                // Actualizar información cada vez que se redibuja la tabla
                customizePagination();
                updateTableInfo();
            }
        });
        
        // Configurar el botón de búsqueda avanzada
        advancedSearchBtn.addEventListener('click', toggleSearchBuilder);
        
        // Mostrar el generador de tarjetas
        tarjetaGenerator.classList.remove('hidden');
        
        // Inicializar el selector de entidades y los campos
        initTarjetaGenerator(columns);
    }
    
    // Función para personalizar la paginación
    function customizePagination() {
        if (!table) return;
        
        const info = table.page.info();
        tablePagination.innerHTML = '';
        
        // Botón anterior
        const prevButton = document.createElement('button');
        prevButton.className = `pagination-button ${info.page === 0 ? 'disabled' : ''}`;
        prevButton.textContent = 'Anterior';
        prevButton.disabled = info.page === 0;
        prevButton.addEventListener('click', function() {
            if (info.page > 0) {
                table.page('previous').draw('page');
            }
        });
        tablePagination.appendChild(prevButton);
        
        // Páginas
        const pageCount = info.pages;
        const currentPage = info.page;
        const pagesToShow = 5;
        
        let startPage = Math.max(0, currentPage - Math.floor(pagesToShow / 2));
        let endPage = Math.min(pageCount - 1, startPage + pagesToShow - 1);
        
        if (endPage - startPage + 1 < pagesToShow && startPage > 0) {
            startPage = Math.max(0, endPage - pagesToShow + 1);
        }
        
        // Botón primera página
        if (startPage > 0) {
            const firstPageButton = document.createElement('button');
            firstPageButton.className = 'pagination-button';
            firstPageButton.textContent = '1';
            firstPageButton.addEventListener('click', function() {
                table.page(0).draw('page');
            });
            tablePagination.appendChild(firstPageButton);
            
            if (startPage > 1) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'px-2';
                ellipsis.textContent = '...';
                tablePagination.appendChild(ellipsis);
            }
        }
        
        // Páginas numéricas
        for (let i = startPage; i <= endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.className = `pagination-button ${i === currentPage ? 'active' : ''}`;
            pageButton.textContent = i + 1;
            pageButton.addEventListener('click', function() {
                table.page(i).draw('page');
            });
            tablePagination.appendChild(pageButton);
        }
        
        // Botón última página
        if (endPage < pageCount - 1) {
            if (endPage < pageCount - 2) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'px-2';
                ellipsis.textContent = '...';
                tablePagination.appendChild(ellipsis);
            }
            
            const lastPageButton = document.createElement('button');
            lastPageButton.className = 'pagination-button';
            lastPageButton.textContent = pageCount;
            lastPageButton.addEventListener('click', function() {
                table.page(pageCount - 1).draw('page');
            });
            tablePagination.appendChild(lastPageButton);
        }
        
        // Botón siguiente
        const nextButton = document.createElement('button');
        nextButton.className = `pagination-button ${info.page === info.pages - 1 ? 'disabled' : ''}`;
        nextButton.textContent = 'Siguiente';
        nextButton.disabled = info.page === info.pages - 1;
        nextButton.addEventListener('click', function() {
            if (info.page < info.pages - 1) {
                table.page('next').draw('page');
            }
        });
        tablePagination.appendChild(nextButton);
    }
    
    // Función para actualizar información de la tabla
    function updateTableInfo() {
        if (!table) return;
        
        const info = table.page.info();
        const filteredCount = info.recordsDisplay;
        const totalCount = info.recordsTotal;
        
        const start = info.start + 1;
        const end = info.end;
        
        tableInfo.innerHTML = `
            Mostrando <span class="font-medium">${start}</span> a 
            <span class="font-medium">${end}</span> de 
            <span class="font-medium">${filteredCount}</span> registros
            ${filteredCount < totalCount ? `(filtrados de <span class="font-medium">${totalCount}</span> registros totales)` : ''}
        `;
    }
    
    // Función para mostrar/ocultar el constructor de búsqueda
    function toggleSearchBuilder() {
        if (isSearchBuilderVisible) {
            searchBuilderContainer.classList.add('hidden');
            advancedSearchBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                </svg>
                Búsqueda Avanzada
            `;
        } else {
            // Asegúrate de que exista la tabla antes de mostrar SearchBuilder
            if (table) {
                // Renderizar el builder si no está inicializado
                if (searchBuilderContainer.children.length === 0) {
                    table.button('.buttons-search').trigger();
                }
                
                searchBuilderContainer.classList.remove('hidden');
                advancedSearchBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Cerrar Filtros
                `;
            }
        }
        isSearchBuilderVisible = !isSearchBuilderVisible;
    }
    
    // Función para normalizar los datos y evitar problemas con nombres de columnas
    function normalizeData(data) {
        // Creamos un nuevo array para los datos normalizados
        const normalizedData = [];
        
        // Iteramos sobre cada fila de datos
        for (let i = 0; i < data.length; i++) {
            const row = data[i];
            const newRow = {};
            
            // Procesamos cada propiedad de la fila
            for (const key in row) {
                // Normalizamos el nombre de la columna (quitamos espacios adicionales)
                const normalizedKey = key.trim();
                
                // Procesamos valores de fecha para asegurar compatibilidad con DataTables
                let value = row[key];
                if (value instanceof Date) {
                    value = value.toISOString().split('T')[0]; // Formato YYYY-MM-DD
                }
                
                // Asignamos el valor a la nueva fila
                newRow[normalizedKey] = value;
            }
            
            normalizedData.push(newRow);
        }
        
        return normalizedData;
    }

    // Manejar el evento de clic en el botón de exportar
    exportBtn.addEventListener('click', exportFilteredData);
    exportBtn.setAttribute('disabled', 'disabled'); // Deshabilitar hasta que haya datos

    function exportFilteredData() {
        if (!table) return;
        
        // Obtener solo las filas que coinciden con los filtros aplicados
        const filteredData = table.rows({ search: 'applied' }).data().toArray();
        
        if (filteredData.length === 0) {
            showError('No hay datos para exportar');
            return;
        }
        
        // Crear una nueva hoja de cálculo con los datos filtrados
        const ws = XLSX.utils.json_to_sheet(filteredData);
        
        // Crear un nuevo libro y añadir la hoja
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Tarjetas');
        
        // Descargar el archivo
        XLSX.writeFile(wb, 'tarjetas_filtradas.xlsx');
    }
    
    // Configurar atajo de teclado Ctrl+F para abrir SearchBuilder
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            if (!isSearchBuilderVisible) {
                toggleSearchBuilder();
            }
            // Enfocar en la búsqueda rápida si el builder no está visible
            else {
                quickSearch.focus();
            }
        }
    });

    // Función para inicializar el generador de tarjetas
    function initTarjetaGenerator(columns) {
        // Obtener las entidades únicas para el selector
        populateEntidadesSelector();
        
        // Poblar el contenedor de campos con checkboxes
        populateCamposSelector(columns);
        
        // Configurar eventos para los botones de la tarjeta
        setupTarjetaEvents();
    }
    
    // Función para popular el selector de entidades
    function populateEntidadesSelector() {
        if (!excelData || excelData.length === 0) return;
        
        // Buscar la columna que contiene información de entidades
        const entidadColumn = findEntidadColumn();
        if (!entidadColumn) {
            // Si no se encuentra una columna específica de entidad, usar otra columna categórica
            const columns = Object.keys(excelData[0]);
            const potentialColumns = columns.filter(col => {
                // Buscar columnas que parezcan categóricas (con menos de 30 valores únicos)
                const values = [...new Set(excelData.map(row => row[col]))].filter(Boolean);
                return values.length > 1 && values.length <= 30;
            });
            
            // Mostrar un mensaje al usuario
            showError('No se detectó una columna de entidad específica. Se utilizarán otras columnas como alternativa.', 5000);
            
            // Limpiar selector
            entidadSelector.innerHTML = '<option value="">Seleccione un valor para agrupar</option>';
            
            // Si encontramos columnas alternativas, usar la primera
            if (potentialColumns.length > 0) {
                const alternativeColumn = potentialColumns[0];
                populateSelectWithColumn(alternativeColumn);
                return;
            } else {
                // Si no hay columnas alternativas, mostrar mensaje de error
                entidadSelector.innerHTML = '<option value="">No se detectaron columnas adecuadas</option>';
                entidadSelector.disabled = true;
                return;
            }
        }
        
        // Poblar el selector con la columna de entidad
        populateSelectWithColumn(entidadColumn);
    }
    
    // Función auxiliar para poblar el selector con valores de una columna
    function populateSelectWithColumn(columnName) {
        // Obtener valores únicos
        const values = [...new Set(excelData.map(row => row[columnName]))].filter(Boolean);
        
        // Limpiar selector
        entidadSelector.innerHTML = `<option value="">Seleccione un valor de ${columnName}</option>`;
        
        // Ordenar alfabéticamente
        values.sort((a, b) => {
            if (typeof a === 'string' && typeof b === 'string') {
                return a.localeCompare(b, 'es');
            }
            return String(a).localeCompare(String(b), 'es');
        });
        
        // Agregar opciones
        values.forEach(value => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            entidadSelector.appendChild(option);
        });
        
        // Habilitar el selector
        entidadSelector.disabled = false;
        
        // Configurar evento change
        entidadSelector.addEventListener('change', function() {
            selectedEntidad = this.value;
            
            // Habilitar/deshabilitar el botón de generar resumen
            if (selectedEntidad) {
                generarResumenBtn.disabled = false;
                generarResumenBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                generarResumenBtn.classList.add('hover:bg-indigo-700');
            } else {
                generarResumenBtn.disabled = true;
                generarResumenBtn.classList.add('opacity-50', 'cursor-not-allowed');
                generarResumenBtn.classList.remove('hover:bg-indigo-700');
                
                // Limpiar el resumen ejecutivo
                resumenEjecutivo.innerHTML = '<p class="text-gray-500 text-sm">Seleccione una entidad y genere un resumen ejecutivo inteligente de los datos filtrados.</p>';
            }
            
            // Si el editor de tarjeta está visible, actualizar tarjeta con la nueva entidad
            if (selectedEntidad && !tarjetaEditor.classList.contains('hidden')) {
                // Actualizar título
                tarjetaTitulo.value = selectedEntidad;
                tarjetaData.titulo = selectedEntidad;
                
                // Si hay un botón de actualizar, simular su clic
                if (actualizarTarjetaBtn) {
                    actualizarTarjetaBtn.click();
                }
            }
            
            // Actualizar resumen si ya se generó previamente
            if (selectedEntidad && resumenEjecutivo.innerHTML !== '' && !resumenEjecutivo.innerHTML.includes('Seleccione una entidad')) {
                const entidadColumn = findEntidadColumn() || Object.keys(excelData[0])[0];
                const filteredData = excelData.filter(row => row[entidadColumn] === selectedEntidad);
                
                if (filteredData.length > 0) {
                    generateResumen(filteredData);
                }
            }
            
            // Reprocesar campos seleccionados para la nueva entidad
            if (selectedFields && selectedFields.length > 0 && excelData && excelData.length > 0) {
                console.log('🔄 Reprocesando campos para nueva entidad:', selectedEntidad);
                const entidadColumn = findEntidadColumn();
                let dataForEntity = [];
                if (selectedEntidad && entidadColumn) {
                    dataForEntity = excelData.filter(row => row[entidadColumn] === selectedEntidad);
                    console.log('🏢 Datos filtrados para entidad:', dataForEntity.length, 'registros');
                } else {
                    dataForEntity = excelData;
                    console.log('📊 Usando todos los datos:', dataForEntity.length, 'registros');
                }
                
                if (dataForEntity.length > 0) {
                    procesarCamposSeleccionados(dataForEntity);
                }
            }
        });
    }
    
    // Función para encontrar la columna que contiene información de entidades
    function findEntidadColumn() {
        if (!excelData || excelData.length === 0) return null;
        
        // Nombres posibles para la columna de entidades
        const possibleNames = [
            'Entidad Responsable de la Acción',
            'Entidad Responsable',
            'Entidad',
            'Ente Fiscalizado',
            'Ente Responsable'
        ];
        
        // Buscar si alguna de las columnas coincide
        const columns = Object.keys(excelData[0]);
        for (const name of possibleNames) {
            const match = columns.find(col => 
                col.toLowerCase().includes(name.toLowerCase())
            );
            if (match) return match;
        }
        
        // Si no se encuentra, usar la primera columna que contenga "entidad"
        return columns.find(col => col.toLowerCase().includes('entidad'));
    }
    
    // Función para popular el selector de campos
    function populateCamposSelector(columns) {
        // Limpiar contenedor y lista de campos seleccionados
        camposContainer.innerHTML = '';
        selectedFields = [];
        
        // Categorizar los campos para una mejor organización
        const categorias = {
            identificadores: {
                title: 'Identificadores',
                fields: []
            },
            montos: {
                title: 'Valores/Montos',
                fields: []
            },
            fechas: {
                title: 'Fechas/Periodos',
                fields: []
            },
            estatus: {
                title: 'Estatus/Categorías',
                fields: []
            },
            otros: {
                title: 'Otros campos',
                fields: []
            }
        };
        
        // Distribuir campos en categorías
        columns.forEach(column => {
            const fieldName = column.data.toLowerCase();
            const originalName = column.data;
            
            if (fieldName.includes('id') || fieldName.includes('clave') || fieldName.includes('código') || fieldName.includes('codigo') || fieldName.includes('folio')) {
                categorias.identificadores.fields.push(originalName);
            } 
            else if (fieldName.includes('monto') || fieldName.includes('importe') || fieldName.includes('valor') || fieldName.includes('precio') || fieldName.includes('total')) {
                categorias.montos.fields.push(originalName);
            }
            else if (fieldName.includes('fecha') || fieldName.includes('año') || fieldName.includes('periodo') || fieldName.includes('ejercicio')) {
                categorias.fechas.fields.push(originalName);
            }
            else if (fieldName.includes('estatus') || fieldName.includes('estado') || fieldName.includes('tipo') || fieldName.includes('categoria') || fieldName.includes('categoría')) {
                categorias.estatus.fields.push(originalName);
            }
            else {
                categorias.otros.fields.push(originalName);
            }
        });
        
        // Crear secciones para cada categoría
        Object.values(categorias).forEach(categoria => {
            if (categoria.fields.length === 0) return;
            
            // Crear encabezado de categoría
            const categoryHeader = document.createElement('div');
            categoryHeader.className = 'w-full col-span-2 mt-2 mb-1 pb-1 border-b border-gray-200';
            categoryHeader.innerHTML = `<span class="font-medium text-sm text-gray-600">${categoria.title}</span>`;
            camposContainer.appendChild(categoryHeader);
            
            // Agregar campos de esta categoría
            categoria.fields.forEach(fieldName => {
                const field = document.createElement('div');
                field.className = 'flex items-center';
                
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.id = `campo-${fieldName}`;
                checkbox.value = fieldName;
                checkbox.className = 'rounded border-gray-300 text-indigo-600 mr-2';
                
                // Pre-seleccionar campos importantes según su categoría
                const shouldSelect = (
                    // Seleccionar automáticamente el primer campo de estatus
                    (categoria.title === 'Estatus/Categorías' && categoria.fields.indexOf(fieldName) === 0) ||
                    // Seleccionar automáticamente el primer monto
                    (categoria.title === 'Valores/Montos' && categoria.fields.indexOf(fieldName) === 0) ||
                    // Seleccionar automáticamente la entidad si existe
                    fieldName.toLowerCase().includes('entidad') ||
                    // Seleccionar automáticamente el identificador principal
                    (categoria.title === 'Identificadores' && categoria.fields.indexOf(fieldName) === 0)
                );
                
                if (shouldSelect) {
                    checkbox.checked = true;
                    selectedFields.push(fieldName);
                }
                
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        selectedFields.push(this.value);
                    } else {
                        selectedFields = selectedFields.filter(field => field !== this.value);
                    }
                    
                    console.log('🔄 Campos seleccionados actualizados:', selectedFields);
                    
                    // Actualizar el contador de campos seleccionados
                    updateSelectedFieldsCounter();
                    
                    // Si hay una entidad seleccionada y datos, procesar automáticamente
                    if (selectedEntidad && excelData && excelData.length > 0) {
                        console.log('🔄 Auto-procesando campos por cambio en selección');
                        const entidadColumn = findEntidadColumn();
                        let dataForEntity = [];
                        if (selectedEntidad && entidadColumn) {
                            dataForEntity = excelData.filter(row => row[entidadColumn] === selectedEntidad);
                        } else {
                            dataForEntity = excelData;
                        }
                        
                        if (dataForEntity.length > 0) {
                            procesarCamposSeleccionados(dataForEntity);
                        }
                    }
                });
                
                const label = document.createElement('label');
                label.htmlFor = `campo-${fieldName}`;
                label.textContent = fieldName;
                label.className = 'text-sm text-gray-700 truncate max-w-[200px]';
                label.title = fieldName; // Para mostrar el nombre completo al hacer hover
                
                field.appendChild(checkbox);
                field.appendChild(label);
                camposContainer.appendChild(field);
            });
        });
        
        // Agregar contador de campos seleccionados
        const counterDiv = document.createElement('div');
        counterDiv.id = 'campos-counter';
        counterDiv.className = 'w-full col-span-2 mt-3 text-right text-xs text-gray-500';
        counterDiv.innerHTML = `Campos seleccionados: <span class="font-medium">${selectedFields.length}</span>`;
        camposContainer.appendChild(counterDiv);
        
        // Si no se seleccionó ningún campo automáticamente, seleccionar al menos uno
        if (selectedFields.length === 0 && columns.length > 0) {
            const firstCheckbox = camposContainer.querySelector('input[type="checkbox"]');
            if (firstCheckbox) {
                firstCheckbox.checked = true;
                selectedFields.push(firstCheckbox.value);
                updateSelectedFieldsCounter();
            }
        }
    }
    
    // Actualizar el contador de campos seleccionados
    function updateSelectedFieldsCounter() {
        const counter = document.getElementById('campos-counter');
        if (counter) {
            counter.innerHTML = `Campos seleccionados: <span class="font-medium">${selectedFields.length}</span>`;
        }
    }
    
    // Configurar eventos para los botones de la tarjeta
    function setupTarjetaEvents() {
        // Configurar eventos para el control de cantidad de registros
        if (cantidadRegistrosInput) {
            // Inicializar el valor por defecto
            cantidadRegistrosInput.value = cantidadRegistros;
            
            cantidadRegistrosInput.addEventListener('change', function() {
                const valor = parseInt(this.value);
                if (isNaN(valor) || valor < 1) {
                    cantidadRegistros = 1;
                    this.value = 1;
                    showError('La cantidad mínima de registros es 1', 3000);
                } else if (valor > 1000) {
                    cantidadRegistros = 1000;
                    this.value = 1000;
                    showError('La cantidad máxima de registros es 1000', 3000);
                } else {
                    cantidadRegistros = valor;
                }
                console.log('📊 Cantidad de registros actualizada:', cantidadRegistros);
            });
            
            cantidadRegistrosInput.addEventListener('input', function() {
                const valor = parseInt(this.value);
                if (!isNaN(valor) && valor >= 1 && valor <= 1000) {
                    cantidadRegistros = valor;
                }
            });
        }
        
        if (todosRegistrosCheckbox) {
            todosRegistrosCheckbox.addEventListener('change', function() {
                mostrarTodosRegistros = this.checked;
                console.log('📊 Mostrar todos los registros:', mostrarTodosRegistros ? 'SÍ' : 'NO');
                
                // Habilitar/deshabilitar el input de cantidad
                if (cantidadRegistrosInput) {
                    cantidadRegistrosInput.disabled = mostrarTodosRegistros;
                    if (mostrarTodosRegistros) {
                        cantidadRegistrosInput.style.opacity = '0.5';
                    } else {
                        cantidadRegistrosInput.style.opacity = '1';
                    }
                }
            });
        }
        
        // Botón para generar resumen
        generarResumenBtn.addEventListener('click', function() {
            if (!excelData || excelData.length === 0) {
                showError('No hay datos para generar el resumen');
                return;
            }
            
            // Si hay una entidad seleccionada, filtrar por esa entidad
            let dataToProcess = excelData;
            const entidadColumn = findEntidadColumn();
            
            if (selectedEntidad && entidadColumn) {
                dataToProcess = excelData.filter(row => row[entidadColumn] === selectedEntidad);
            }
            
            generateResumen(dataToProcess);
        });
        
        // Botón para previsualizar tarjeta
        previewTarjetaBtn.addEventListener('click', function() {
            console.log('🎯 INICIANDO PREVISUALIZACIÓN');
            console.log('📊 Entidad seleccionada:', selectedEntidad);
            console.log('📋 Campos seleccionados:', selectedFields);
            
            if (!selectedEntidad) {
                showError('Seleccione una entidad para generar la tarjeta');
                return;
            }
            
            if (selectedFields.length === 0) {
                showError('Seleccione al menos un campo para la tarjeta');
                return;
            }
            
            // Inicializar datos de la tarjeta
            console.log('🚀 Ejecutando initTarjetaData()');
            const success = initTarjetaData();
            
            if (success) {
                console.log('✅ initTarjetaData() exitoso');
                console.log('📊 Acciones generadas en initTarjetaData:', tarjetaData.acciones.length);
                console.log('💰 Top 3 acciones por monto:', tarjetaData.acciones.slice(0, 3).map(a => ({
                    no: a.no,
                    monto: a.monto,
                    clave: a.claveAccion,
                    titulo: a.titulo.substring(0, 30) + '...'
                })));
                
                // Mostrar el editor de tarjeta
                showTarjetaEditor();
                
                // Generar vista previa inicial
                console.log('🖼️ Generando vista previa');
                generateTarjetaPreview();
            } else {
                console.log('❌ initTarjetaData() falló');
            }
        });
        
        // Botón para generar tarjeta
        generarTarjetaBtn.addEventListener('click', function() {
            if (!selectedEntidad) {
                showError('Seleccione una entidad para generar la tarjeta');
                return;
            }
            
            if (selectedFields.length === 0) {
                showError('Seleccione al menos un campo para la tarjeta');
                return;
            }
            
            // Si no se ha inicializado la tarjeta aún, hacerlo ahora
            if (tarjetaEditor.classList.contains('hidden') && !initTarjetaData()) {
                return;
            }
            
            generateTarjeta();
        });
        
        // Botón para descargar tarjeta como PDF
        descargarTarjetaBtn.addEventListener('click', function() {
            if (!tarjetaPreview.innerHTML) {
                showError('Primero debe generar una tarjeta para descargarla');
                return;
            }
            
            downloadTarjetaAsPDF();
        });
        
        // Botón para descargar tarjeta como DOCX
        descargarDocxBtn.addEventListener('click', function() {
            if (!tarjetaPreview.innerHTML) {
                showError('Primero debe generar una tarjeta para descargarla');
                return;
            }
            
            downloadTarjetaAsDOCX();
        });
        
        // Botón para agregar fila a la tabla
        agregarFilaTablaBtn.addEventListener('click', function() {
            addRowToTarjetaTable('', 0, 0);
            updateTotalMonto();
        });
        
        // Botón para agregar acción ordenada
        agregarAccionBtn.addEventListener('click', function() {
            addRowToAccionesTable();
        });
        
        // Botón para actualizar la vista previa
        actualizarTarjetaBtn.addEventListener('click', function() {
            console.log('🔄 Actualizando vista previa - Preservando acciones del Excel');
            console.log('📊 Acciones antes de actualizar:', tarjetaData.acciones.length);
            
            // SOLO actualizar los datos básicos (título, subtítulo, período, filas de resumen)
            // NO actualizar las acciones para preservar las TOP 10 del Excel
            const accionesOriginales = [...tarjetaData.acciones]; // Preservar acciones
            
            // Actualizar solo título, subtítulo y período
            tarjetaData.titulo = tarjetaTitulo.value;
            tarjetaData.subtitulo = tarjetaSubtitulo.value;
            tarjetaData.periodo = tarjetaPeriodo.value;
            
            const montoTotal = parseFloat(tarjetaTotalMonto.value.replace(/[^\d.-]/g, '')) || 0;
            tarjetaData.totalMonto = montoTotal;
            
            // Actualizar filas de resumen
            tarjetaData.filas = [];
            const filas = tarjetaTablaEditor.querySelectorAll('tbody tr');
            
            filas.forEach(fila => {
                const estatus = fila.querySelector('.tarjeta-estatus').value;
                const montoText = fila.querySelector('.tarjeta-monto').value;
                const porcentajeText = fila.querySelector('.tarjeta-porcentaje').value;
                
                const monto = parseFloat(montoText.replace(/[^\d.-]/g, '')) || 0;
                const porcentaje = parseFloat(porcentajeText.replace(/[^\d.%]/g, '')) || 0;
                
                tarjetaData.filas.push({ estatus, monto, porcentaje });
            });
            
            // RESTAURAR las acciones originales del Excel
            tarjetaData.acciones = accionesOriginales;
            
            console.log('✅ Datos actualizados - Acciones preservadas:', tarjetaData.acciones.length);
            console.log('💰 Montos preservados:', tarjetaData.acciones.map(a => ({ no: a.no, monto: a.monto })));
            
            // Regenerar la vista previa
            generateTarjetaPreview();
        });
        
        // Manejo de pestañas
        tabResumen.addEventListener('click', function(e) {
            e.preventDefault();
            activateTab('resumen');
        });
        
        tabAcciones.addEventListener('click', function(e) {
            e.preventDefault();
            activateTab('acciones');
        });
    }
    
    // Función para activar una pestaña
    function activateTab(tabName) {
        // Desactivar todas las pestañas
        tabResumen.classList.remove('border-indigo-600', 'text-indigo-600');
        tabResumen.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
        tabAcciones.classList.remove('border-indigo-600', 'text-indigo-600');
        tabAcciones.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
        
        // Ocultar todos los contenidos
        contenidoResumen.classList.add('hidden');
        contenidoAcciones.classList.add('hidden');
        
        // Activar la pestaña seleccionada
        if (tabName === 'resumen') {
            tabResumen.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
            tabResumen.classList.add('border-indigo-600', 'text-indigo-600');
            contenidoResumen.classList.remove('hidden');
        } else if (tabName === 'acciones') {
            tabAcciones.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
            tabAcciones.classList.add('border-indigo-600', 'text-indigo-600');
            contenidoAcciones.classList.remove('hidden');
        }
    }
    
    // Agregar fila a la tabla de acciones ordenadas
    function addRowToAccionesTable(data = null) {
        const tbody = tarjetaAccionesEditor.querySelector('tbody');
        const rowCount = tbody.querySelectorAll('tr').length + 1;
        const tr = document.createElement('tr');
        
        // Crear datos por defecto si no se proporcionan
        if (!data) {
            data = {
                no: rowCount,
                cuentaPublica: new Date().getFullYear() - 1,
                titulo: '',
                claveAccion: '',
                tipoAccion: 'PO', // Por defecto "Pliegos de Observaciones"
                descripcion: '',
                monto: 0
            };
        }
        
        tr.innerHTML = `
            <td class="px-3 py-1.5 text-center">${data.no}</td>
            <td class="px-3 py-1.5">
                <input type="text" class="accion-cuenta-publica w-full text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" value="${data.cuentaPublica}">
            </td>
            <td class="px-3 py-1.5">
                <input type="text" class="accion-titulo w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" value="${data.titulo}">
            </td>
            <td class="px-3 py-1.5">
                <input type="text" class="accion-clave w-full text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" value="${data.claveAccion}">
            </td>
            <td class="px-3 py-1.5">
                <select class="accion-tipo w-full text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                    <option value="PO" ${data.tipoAccion === 'PO' ? 'selected' : ''}>PO</option>
                    <option value="PRAS" ${data.tipoAccion === 'PRAS' ? 'selected' : ''}>PRAS</option>
                    <option value="SA" ${data.tipoAccion === 'SA' ? 'selected' : ''}>SA</option>
                    <option value="DH" ${data.tipoAccion === 'DH' ? 'selected' : ''}>DH</option>
                    <option value="PIIC" ${data.tipoAccion === 'PIIC' ? 'selected' : ''}>PIIC</option>
                </select>
            </td>
            <td class="px-3 py-1.5">
                <textarea class="accion-descripcion w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" rows="2">${data.descripcion}</textarea>
            </td>
            <td class="px-3 py-1.5">
                <input type="text" class="accion-monto w-full text-right rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" value="${formatMonto(data.monto)}">
            </td>
            <td class="px-3 py-1.5 text-center">
                <button class="eliminar-accion px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors duration-200 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        `;
        
        // Agregar evento para eliminar acción
        tr.querySelector('.eliminar-accion').addEventListener('click', function() {
            tr.remove();
            // Renumerar acciones
            renumberAcciones();
        });
        
        tbody.appendChild(tr);
    }
    
    // Renumerar las acciones tras eliminar una
    function renumberAcciones() {
        const rows = tarjetaAccionesEditor.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
    }
    
    // Actualizar los datos de la tarjeta desde el editor
    function updateTarjetaDataFromEditor() {
        tarjetaData.titulo = tarjetaTitulo.value;
        tarjetaData.subtitulo = tarjetaSubtitulo.value;
        tarjetaData.periodo = tarjetaPeriodo.value;
        
        const montoTotal = parseFloat(tarjetaTotalMonto.value.replace(/[^\d.-]/g, '')) || 0;
        tarjetaData.totalMonto = montoTotal;
        
        // Obtener filas de la tabla de resumen
        tarjetaData.filas = [];
        const filas = tarjetaTablaEditor.querySelectorAll('tbody tr');
        
        filas.forEach(fila => {
            const estatus = fila.querySelector('.tarjeta-estatus').value;
            const montoText = fila.querySelector('.tarjeta-monto').value;
            const porcentajeText = fila.querySelector('.tarjeta-porcentaje').value;
            
            const monto = parseFloat(montoText.replace(/[^\d.-]/g, '')) || 0;
            const porcentaje = parseFloat(porcentajeText.replace(/[^\d.%]/g, '')) || 0;
            
            tarjetaData.filas.push({ estatus, monto, porcentaje });
        });
        
        // Obtener filas de la tabla de acciones ordenadas
        const nuevasAcciones = [];
        const accionesFilas = tarjetaAccionesEditor.querySelectorAll('tbody tr');
        
        accionesFilas.forEach((fila, index) => {
            const no = parseInt(fila.querySelector('td:first-child').textContent);
            const cuentaPublica = fila.querySelector('.accion-cuenta-publica').value;
            const titulo = fila.querySelector('.accion-titulo').value;
            const claveAccion = fila.querySelector('.accion-clave').value;
            const tipoAccion = fila.querySelector('.accion-tipo').value;
            const descripcion = fila.querySelector('.accion-descripcion').value;
            const montoText = fila.querySelector('.accion-monto').value;
            
            const monto = parseFloat(montoText.replace(/[^\d.-]/g, '')) || 0;
            
            // Crear la nueva acción
            const nuevaAccion = {
                no,
                cuentaPublica,
                titulo,
                claveAccion,
                tipoAccion,
                descripcion,
                monto,
                camposAdicionales: {}
            };
            
            // Preservar camposAdicionales de la acción original si existe
            if (tarjetaData.acciones[index] && tarjetaData.acciones[index].camposAdicionales) {
                nuevaAccion.camposAdicionales = { ...tarjetaData.acciones[index].camposAdicionales };
            } else {
                                 // Si no existe, regenerar desde los datos originales
                 if (selectedFields && selectedFields.length > 0) {
                     // Filtrar campos para evitar duplicados
                     const columnasBase = ['Cuenta Pública', 'Título de la Auditoría', 'Clave de Acción', 'Tipo de Acción', 'Descripción'];
                     const camposFiltrados = selectedFields.filter(campo => {
                         // Excluir campos base
                         const esColumnaBase = columnasBase.some(colBase => 
                             campo.toLowerCase().includes(colBase.toLowerCase()) || 
                             colBase.toLowerCase().includes(campo.toLowerCase())
                         );
                         
                         // Excluir cualquier campo que contenga palabras relacionadas con montos para evitar duplicación
                         const palabrasMontos = ['monto', 'importe', 'valor', 'total', 'suma', 'cantidad'];
                         const contieneMontoGenerico = palabrasMontos.some(palabra => 
                             campo.toLowerCase().includes(palabra)
                         );
                         
                         return !esColumnaBase && !contieneMontoGenerico;
                     });
                     
                     // Buscar en los datos originales el registro correspondiente
                     const entidadColumn = findEntidadColumn();
                     let dataForEntity = [];
                     if (selectedEntidad && entidadColumn) {
                         dataForEntity = excelData.filter(row => row[entidadColumn] === selectedEntidad);
                     } else {
                         dataForEntity = excelData;
                     }
                     
                     // Intentar encontrar el registro por clave de acción
                     const claveAccionColumn = findClaveAccionColumn();
                     if (claveAccionColumn && dataForEntity.length > 0) {
                         const registroOriginal = dataForEntity.find(row => row[claveAccionColumn] === claveAccion);
                         if (registroOriginal) {
                             camposFiltrados.forEach(campo => {
                                 nuevaAccion.camposAdicionales[campo] = registroOriginal[campo] || 'N/A';
                             });
                         } else {
                             // Si no se encuentra el registro, usar valores por defecto
                             camposFiltrados.forEach(campo => {
                                 nuevaAccion.camposAdicionales[campo] = 'N/A';
                             });
                         }
                     }
                 }
            }
            
            nuevasAcciones.push(nuevaAccion);
        });
        
        tarjetaData.acciones = nuevasAcciones;
        
        console.log('🔄 Acciones actualizadas desde editor:', tarjetaData.acciones);
        console.log('📊 Campos adicionales preservados:', tarjetaData.acciones.map(a => ({ claveAccion: a.claveAccion, camposAdicionales: a.camposAdicionales })));
        
        // Volver a procesar los campos seleccionados con los datos filtrados actualizados
        const entidadColumn = findEntidadColumn();
        let dataForEntity = [];
        if (selectedEntidad && entidadColumn) {
            dataForEntity = excelData.filter(row => row[entidadColumn] === selectedEntidad);
        } else {
            dataForEntity = excelData;
        }
        
        console.log('🔄 Reprocesando campos seleccionados desde updateTarjetaDataFromEditor');
        console.log('📊 Entidad seleccionada:', selectedEntidad);
        console.log('📋 Datos filtrados:', dataForEntity.length, 'registros');
        
        if (dataForEntity.length > 0) {
            procesarCamposSeleccionados(dataForEntity);
        }
    }

    // Función para generar la previsualización de la tarjeta
    function generateTarjetaPreview() {
        showLoading('Generando previsualización de tarjeta...');
        
        setTimeout(() => {
            // Mostrar el contenedor de previsualización
            previewContainer.classList.remove('hidden');
            
            // Generar el contenido HTML de la tarjeta
            const tarjetaHtml = generateTarjetaHtml();
            
            // Actualizar la previsualización
            tarjetaPreview.innerHTML = tarjetaHtml;
            
            // Efecto de aparición
            setTimeout(() => {
                previewContainer.classList.add('show');
            }, 50);
            
            hideLoading();
        }, 500);
    }
    
    // Función para generar la tarjeta final
    function generateTarjeta() {
        showLoading('Generando tarjeta informativa...');
        
        console.log('🎯 ANTES de generar resumen - Acciones disponibles:', tarjetaData.acciones.length);
        console.log('💰 Montos de las acciones:', tarjetaData.acciones.map(a => ({ no: a.no, monto: a.monto, clave: a.claveAccion })));
        
        // Primero, obtener las descripciones para generar el resumen con IA
        generateDescriptionSummary().then(() => {
            setTimeout(() => {
                console.log('🎯 DESPUÉS del resumen - Acciones disponibles:', tarjetaData.acciones.length);
                console.log('💰 Montos después del resumen:', tarjetaData.acciones.map(a => ({ no: a.no, monto: a.monto, clave: a.claveAccion })));
                
                // NO actualizar datos desde el editor para evitar sobrescribir las acciones generadas
                // updateTarjetaDataFromEditor(); // COMENTADO PARA EVITAR SOBRESCRIBIR
                
                // Generar previsualización directamente con las acciones correctas
                previewContainer.classList.remove('hidden');
                const tarjetaHtml = generateTarjetaHtml();
                tarjetaPreview.innerHTML = tarjetaHtml;
                previewContainer.classList.add('show');
                
                // Mostrar mensaje de éxito
                const successMsg = document.createElement('div');
                successMsg.className = 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-4';
                const tipoMostrar = mostrarTodosRegistros ? 'todas las acciones disponibles' : `las ${tarjetaData.acciones.length} acciones con mayor monto`;
                successMsg.innerHTML = `
                    <p class="font-bold">¡Tarjeta generada con éxito!</p>
                    <p>Mostrando ${tipoMostrar} ordenadas de mayor a menor monto.</p>
                `;
                
                // Insertar antes de la previsualización
                previewContainer.parentNode.insertBefore(successMsg, previewContainer);
                
                // Eliminar el mensaje después de 5 segundos
                setTimeout(() => {
                    successMsg.remove();
                }, 5000);
                
                hideLoading();
            }, 800);
        }).catch(error => {
            console.error('Error generando resumen:', error);
            hideLoading();
            // Continuar sin el resumen si hay error
            setTimeout(() => {
                // NO actualizar datos desde el editor
                // updateTarjetaDataFromEditor(); // COMENTADO
                previewContainer.classList.remove('hidden');
                const tarjetaHtml = generateTarjetaHtml();
                tarjetaPreview.innerHTML = tarjetaHtml;
                previewContainer.classList.add('show');
                hideLoading();
            }, 800);
        });
    }
    
    // Nueva función para generar resumen de descripciones usando IA
    function generateDescriptionSummary() {
        return new Promise((resolve, reject) => {
            console.log('📋 Iniciando generación de resumen ejecutivo');
            console.log('🎯 Acciones disponibles para resumen:', tarjetaData.acciones.length);
            console.log('💰 Montos ANTES del resumen:', tarjetaData.acciones.map(a => ({ no: a.no, monto: a.monto, clave: a.claveAccion })));
            
            // Verificar si hay acciones con descripciones
            if (!tarjetaData.acciones || tarjetaData.acciones.length === 0) {
                console.log('⚠️ No hay acciones disponibles para generar resumen');
                resolve(); // Continuar sin resumen si no hay acciones
                return;
            }
            
            // IMPORTANTE: Crear copia de seguridad de las acciones AL INICIO
            const accionesOriginales = JSON.parse(JSON.stringify(tarjetaData.acciones));
            console.log('✅ Copia de seguridad creada de', accionesOriginales.length, 'acciones');
            console.log('💰 Verificando copia de seguridad - montos:', accionesOriginales.map(a => ({ no: a.no, monto: a.monto, clave: a.claveAccion })));
            
            // Obtener las descripciones de las acciones seleccionadas con mayor monto
            const descriptions = tarjetaData.acciones
                .map(accion => accion.descripcion)
                .filter(desc => desc && desc.trim() !== '');
            
            console.log('📝 Descripciones encontradas:', descriptions.length);
            
            if (descriptions.length === 0) {
                console.log('⚠️ No hay descripciones para resumir');
                resolve(); // No hay descripciones para resumir
                return;
            }
            
            // Obtener el token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            // Primero, obtener resúmenes individuales de las descripciones
            let descriptionsToSummarize = tarjetaData.acciones.map(accion => accion.descripcion || '');
            
            // Advertencia para muchos registros
            if (descriptions.length > 100) {
                console.warn(`⚠️ Gran cantidad de registros (${descriptions.length}). El procesamiento puede tomar varios minutos.`);
                loadingMessage.textContent = `Procesando ${descriptions.length} descripciones con IA. Esto puede tomar varios minutos...`;
            }
            
            // No limitar - procesaremos todos los registros en lotes
            console.log(`📊 Procesando todos los ${descriptions.length} registros en lotes de 50`);
            
            // Advertencia para datasets muy grandes
            if (descriptions.length > 500) {
                const estimatedTime = Math.ceil(descriptions.length / 50) * 2; // 2 minutos por lote aproximadamente
                showError(`Procesando ${descriptions.length} registros en lotes de 50. Tiempo estimado: ${estimatedTime} minutos.`, 10000);
            }
            
            // Procesar en lotes secuenciales de 50
            processDescriptionsInBatches(descriptionsToSummarize, csrfToken, loadingMessage)
            .then(data => {
                console.log('📝 Resúmenes por lotes recibidos - Estructura completa:', JSON.stringify(data, null, 2));
                
                // Continuar con el procesamiento normal
                return processAllSummaries(data);
            })
            .then(() => {
                resolve();
            })
            .catch(error => {
                console.error('Error en procesamiento por lotes:', error);
                handleSummaryError(error);
                resolve();
            });
            
            // Función para procesar en lotes secuenciales
            async function processDescriptionsInBatches(descriptions, token, loadingElement) {
                const BATCH_SIZE = 50;
                const totalBatches = Math.ceil(descriptions.length / BATCH_SIZE);
                let allSummaries = {};
                let processedCount = 0;
                
                console.log(`🔀 Iniciando procesamiento en ${totalBatches} lotes de ${BATCH_SIZE} registros`);
                
                for (let batchIndex = 0; batchIndex < totalBatches; batchIndex++) {
                    const startIndex = batchIndex * BATCH_SIZE;
                    const endIndex = Math.min(startIndex + BATCH_SIZE, descriptions.length);
                    const batchDescriptions = descriptions.slice(startIndex, endIndex);
                    
                    // Crear mapeo de índices para el lote actual
                    const batchIndexMap = {};
                    batchDescriptions.forEach((desc, localIndex) => {
                        batchIndexMap[localIndex] = startIndex + localIndex;
                    });
                    
                    loadingElement.textContent = `Procesando lote ${batchIndex + 1}/${totalBatches} (${batchDescriptions.length} registros)...`;
                    console.log(`📦 Procesando lote ${batchIndex + 1}/${totalBatches}: registros ${startIndex + 1}-${endIndex}`);
                    
                    try {
                        // Configurar timeout específico para este lote
                        const batchController = new AbortController();
                        const batchTimeout = setTimeout(() => batchController.abort(), 180000); // 3 minutos por lote
                        
                        const response = await fetch('/dashboard/ai/summarize-descriptions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                descriptions: batchDescriptions,
                                maxLength: 200,
                                batch_size: 10
                            }),
                            signal: batchController.signal
                        });
                        
                        clearTimeout(batchTimeout);
                        
                        if (!response.ok) {
                            throw new Error(`Error en lote ${batchIndex + 1}: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        
                        // Mapear los resúmenes del lote a los índices globales
                        if (data.summaries) {
                            Object.keys(data.summaries).forEach(localIndex => {
                                const globalIndex = batchIndexMap[localIndex];
                                allSummaries[globalIndex] = data.summaries[localIndex];
                            });
                        }
                        
                        processedCount += batchDescriptions.length;
                        console.log(`✅ Lote ${batchIndex + 1} completado. Progreso: ${processedCount}/${descriptions.length}`);
                        
                        // Pequeña pausa entre lotes para no sobrecargar el servidor
                        if (batchIndex < totalBatches - 1) {
                            await new Promise(resolve => setTimeout(resolve, 1000)); // 1 segundo de pausa
                        }
                        
                    } catch (error) {
                        console.error(`❌ Error en lote ${batchIndex + 1}:`, error);
                        
                        // En caso de error, llenar con descripciones truncadas para este lote
                        batchDescriptions.forEach((desc, localIndex) => {
                            const globalIndex = batchIndexMap[localIndex];
                            allSummaries[globalIndex] = desc.length > 200 ? desc.substring(0, 197) + '...' : desc;
                        });
                        
                        if (error.name === 'AbortError') {
                            showError(`Timeout en lote ${batchIndex + 1}. Continuando con el siguiente...`, 3000);
                        } else {
                            showError(`Error en lote ${batchIndex + 1}. Continuando con el siguiente...`, 3000);
                        }
                    }
                }
                
                console.log(`🎉 Procesamiento completo: ${processedCount} registros procesados en ${totalBatches} lotes`);
                loadingElement.textContent = 'Aplicando resúmenes generados...';
                
                // Simular la estructura de respuesta esperada por el código existente
                const mockResponse = {
                    ok: true,
                    status: 200,
                    json: () => Promise.resolve({ summaries: allSummaries })
                };
                
                return mockResponse.json();
            }
            
            // Función para procesar todos los resúmenes una vez completados los lotes
            function processAllSummaries(data) {
                console.log('📝 Tipo de data.summaries:', typeof data.summaries);
                console.log('📝 Array data.summaries?:', Array.isArray(data.summaries));
                console.log('📝 Longitud data.summaries:', data.summaries ? Object.keys(data.summaries).length : 'UNDEFINED');
                
                // GUARDAR los resúmenes individuales para usar después
                window.resumenesIndividuales = data.summaries || {};
                
                // Actualizar las descripciones en tarjetaData con las versiones resumidas
                if (data.summaries && Object.keys(data.summaries).length > 0) {
                    console.log('✅ Aplicando resúmenes individuales a las acciones');
                    console.log('📊 Cantidad de resúmenes recibidos:', Object.keys(data.summaries).length);
                    console.log('📊 Cantidad de acciones disponibles:', tarjetaData.acciones.length);
                    
                    tarjetaData.acciones.forEach((accion, index) => {
                        if (data.summaries[index] && data.summaries[index].trim() !== '' && data.summaries[index] !== '...') {
                            console.log(`📝 Acción ${accion.no}: Aplicando resumen completo de IA`);
                            console.log(`   Original (${accion.descripcion.length} chars): ${accion.descripcion.substring(0, 100)}...`);
                            console.log(`   Resumido (${data.summaries[index].length} chars): ${data.summaries[index]}`);
                            
                            // Guardar la descripción original y usar el resumen completo
                            accion.descripcionOriginal = accion.descripcion;
                            accion.descripcion = data.summaries[index];
                            console.log(`✅ Resumen aplicado exitosamente para acción ${accion.no}`);
                        } else {
                            console.log(`⚠️ Acción ${accion.no}: No hay resumen válido en el índice ${index} - manteniendo descripción original completa`);
                        }
                    });
                } else {
                    console.log('⚠️ No se recibieron resúmenes individuales válidos');
                    console.log('   data.summaries es:', data.summaries);
                }
                
                // Ahora generar el resumen ejecutivo general
                loadingMessage.textContent = 'Generando resumen ejecutivo con IA...';
                
                // Configurar timeout para resumen ejecutivo
                const summaryTimeoutController = new AbortController();
                const summaryTimeoutId = setTimeout(() => summaryTimeoutController.abort(), 60000); // 1 minuto timeout
                
                return fetch('/dashboard/ai/generate-summary', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        descriptions: descriptions, // Usar las descripciones originales para el resumen general
                        entity: selectedEntidad,
                        context: `Periodo: ${tarjetaData.periodo || 'No especificado'}. Se muestran las ${descriptions.length} acciones principales ordenadas por monto.`
                    }),
                    signal: summaryTimeoutController.signal
                })
                .then(response => {
                    clearTimeout(summaryTimeoutId);
                    return response;
                })
                .then(response => {
                    console.log('📡 Respuesta del servidor para resumen general:', response.status);
                    if (!response.ok) {
                        throw new Error(`Error en la respuesta del servidor: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    return finalizeProcessing(data);
                });
            }
            
            // Función para finalizar el procesamiento
            function finalizeProcessing(data) {
                console.log('📄 Resumen ejecutivo general recibido:', JSON.stringify(data, null, 2));
                
                if (data.summary && data.summary.trim() !== '') {
                    console.log('✅ Resumen ejecutivo válido recibido:', data.summary.length, 'caracteres');
                    
                    // Guardar el resumen en tarjetaData para usarlo en la sección B
                    tarjetaData.resumenIA = data.summary;
                    
                    // También actualizar el resumen ejecutivo en la sección superior
                    resumenEjecutivo.innerHTML = `
                        <div class="space-y-4">
                            <h4 class="font-medium text-indigo-700">${selectedEntidad}</h4>
                            <div class="bg-indigo-50 p-4 rounded-md">
                                <p class="font-medium mb-2">Resumen ejecutivo generado por IA:</p>
                                <p class="text-sm text-gray-700">${data.summary}</p>
                            </div>
                            <p class="text-xs text-gray-500 italic">Este resumen fue generado automáticamente por inteligencia artificial basándose en las descripciones de las acciones.</p>
                        </div>
                    `;
                    
                    console.log('📄 Resumen ejecutivo mostrado en la interfaz');
                } else {
                    console.log('⚠️ No se recibió un resumen ejecutivo válido');
                    console.log('   data.summary es:', data.summary);
                }
                
                console.log('🔄 ANTES de restaurar - Acciones actuales:', tarjetaData.acciones.length);
                console.log('💰 ANTES de restaurar - Montos actuales:', tarjetaData.acciones.map(a => ({ no: a.no, monto: a.monto })));
                
                // IMPORTANTE: Restaurar las acciones originales antes de actualizar la tabla del editor
                console.log('🔄 Restaurando acciones originales para preservar los datos completos');
                tarjetaData.acciones = JSON.parse(JSON.stringify(accionesOriginales));
                
                console.log('🔄 DESPUÉS de restaurar - Acciones restauradas:', tarjetaData.acciones.length);
                console.log('💰 DESPUÉS de restaurar - Montos restaurados:', tarjetaData.acciones.map(a => ({ no: a.no, monto: a.monto, clave: a.claveAccion })));
                
                // Para la visualización, aplicar TODOS los resúmenes recibidos
                if (window.resumenesIndividuales && Object.keys(window.resumenesIndividuales).length > 0) {
                    console.log('📝 Aplicando TODOS los resúmenes individuales recibidos');
                    console.log('📊 Resúmenes disponibles:', Object.keys(window.resumenesIndividuales).length);
                    tarjetaData.acciones.forEach((accion, index) => {
                        if (window.resumenesIndividuales[index] && 
                            window.resumenesIndividuales[index].trim() !== '' &&
                            window.resumenesIndividuales[index] !== '...') {
                            console.log(`📝 Aplicando resumen completo para acción ${accion.no}:`);
                            console.log(`   Original (${accion.descripcion.length} chars): ${accion.descripcion.substring(0, 100)}...`);
                            console.log(`   Resumido (${window.resumenesIndividuales[index].length} chars): ${window.resumenesIndividuales[index]}`);
                            accion.descripcionOriginal = accion.descripcion;
                            accion.descripcion = window.resumenesIndividuales[index];
                            console.log(`✅ Resumen completo aplicado para acción ${accion.no}`);
                        } else {
                            console.log(`⚠️ No hay resumen válido para acción ${accion.no} - manteniendo descripción original completa`);
                        }
                    });
                } else {
                    console.log('⚠️ No hay resúmenes individuales disponibles - manteniendo descripciones originales completas');
                }
                
                // Actualizar la tabla del editor con las descripciones resumidas
                updateAccionesTableWithSummaries();
                
                console.log('✅ Resumen generado - Acciones preservadas:', tarjetaData.acciones.length);
                console.log('💰 Verificación final de montos:', tarjetaData.acciones.map(a => ({ no: a.no, monto: a.monto, clave: a.claveAccion })));
                
                // Limpiar la variable temporal
                delete window.resumenesIndividuales;
            }
            
            // Función para manejar errores en el procesamiento
            function handleSummaryError(error) {
                console.error('Error al generar resúmenes con IA:', error);
                
                // Generar un resumen básico de las acciones
                const montoTotal = tarjetaData.acciones.reduce((sum, accion) => sum + accion.monto, 0);
                const resumenBasico = `Se identificaron ${tarjetaData.acciones.length} acciones principales con un monto total de $${montoTotal.toLocaleString('es-MX')}. Las acciones corresponden principalmente a observaciones de tipo ${tarjetaData.acciones[0]?.tipoAccion || 'PO'} relacionadas con la gestión de recursos públicos durante el periodo ${tarjetaData.periodo}.`;
                
                tarjetaData.resumenIA = resumenBasico;
                
                // Mostrar resumen básico
                resumenEjecutivo.innerHTML = `
                    <div class="space-y-4">
                        <h4 class="font-medium text-indigo-700">${selectedEntidad}</h4>
                        <div class="bg-indigo-50 p-4 rounded-md">
                            <p class="font-medium mb-2">Resumen ejecutivo:</p>
                            <p class="text-sm text-gray-700">${resumenBasico}</p>
                        </div>
                    </div>
                `;
                
                console.log('❌ ERROR en resumen de IA:', error);
                console.log('🔄 ANTES de restaurar (error) - Acciones actuales:', tarjetaData.acciones.length);
                
                // IMPORTANTE: Restaurar las acciones originales en caso de error también
                console.log('🔄 Error en IA - Restaurando acciones originales');
                tarjetaData.acciones = JSON.parse(JSON.stringify(accionesOriginales));
                
                console.log('🔄 DESPUÉS de restaurar (error) - Acciones restauradas:', tarjetaData.acciones.length);
                console.log('💰 DESPUÉS de restaurar (error) - Montos restaurados:', tarjetaData.acciones.map(a => ({ no: a.no, monto: a.monto, clave: a.claveAccion })));
                
                // Si hay error pero tenemos resúmenes guardados, aplicarlos
                if (window.resumenesIndividuales && Object.keys(window.resumenesIndividuales).length > 0) {
                    console.log('📝 Aplicando resúmenes guardados a pesar del error en resumen ejecutivo');
                    tarjetaData.acciones.forEach((accion, index) => {
                        if (window.resumenesIndividuales[index] && 
                            window.resumenesIndividuales[index].trim() !== '' &&
                            window.resumenesIndividuales[index] !== '...') {
                            console.log(`📝 Aplicando resumen completo para acción ${accion.no} (modo error)`);
                            accion.descripcionOriginal = accion.descripcion;
                            accion.descripcion = window.resumenesIndividuales[index];
                            console.log(`✅ Resumen completo aplicado para acción ${accion.no} (modo error)`);
                        } else {
                            console.log(`⚠️ No hay resumen válido para acción ${accion.no} (modo error) - manteniendo descripción original completa`);
                        }
                    });
                } else {
                    // Si no hay resúmenes de IA disponibles, mantener las descripciones originales completas
                    console.log('ℹ️ No hay resúmenes de IA disponibles, manteniendo descripciones originales completas');
                    tarjetaData.acciones.forEach(accion => {
                        console.log(`📝 Manteniendo descripción completa para acción ${accion.no}: ${accion.descripcion.length} caracteres`);
                    });
                }
                
                updateAccionesTableWithSummaries();
                
                console.log('✅ Error manejado - Acciones preservadas:', tarjetaData.acciones.length);
                console.log('💰 Verificación final de montos tras error:', tarjetaData.acciones.map(a => ({ no: a.no, monto: a.monto, clave: a.claveAccion })));
                
                // Limpiar la variable temporal
                delete window.resumenesIndividuales;
            }
                 });
    }
    
    // Nueva función para actualizar la tabla de acciones con las descripciones resumidas
    function updateAccionesTableWithSummaries() {
        const tbodyAcciones = tarjetaAccionesEditor.querySelector('tbody');
        const rows = tbodyAcciones.querySelectorAll('tr');
        
        rows.forEach((row, index) => {
            if (tarjetaData.acciones[index]) {
                const textarea = row.querySelector('.accion-descripcion');
                if (textarea) {
                    textarea.value = tarjetaData.acciones[index].descripcion;
                    // Agregar un título para mostrar la descripción completa al pasar el mouse
                    if (tarjetaData.acciones[index].descripcionOriginal) {
                        textarea.title = tarjetaData.acciones[index].descripcionOriginal;
                        // Agregar un indicador visual de que la descripción fue resumida
                        textarea.style.backgroundColor = '#f0f9ff'; // Azul muy claro
                    }
                }
            }
        });
    }
    
    // Función para generar un resumen básico sin IA
    function generateBasicSummary(data) {
        const montoColumn = findMontoColumn();
        
        let resumenContent = '<div class="space-y-4">';
        
        // Agregar información de la entidad
        if (selectedEntidad) {
            resumenContent += `<h4 class="font-medium text-indigo-700">${selectedEntidad}</h4>`;
        }
        
        // Agregar estadísticas si hay montos
        if (montoColumn && data.length > 0) {
            try {
                const montos = data.map(row => {
                    const rawValue = row[montoColumn];
                    if (typeof rawValue === 'string') {
                        return parseFloat(rawValue.replace(/[$,]/g, ''));
                    }
                    return parseFloat(rawValue);
                }).filter(monto => !isNaN(monto));
                
                const totalMonto = montos.reduce((sum, monto) => sum + monto, 0);
                
                resumenContent += `
                <div class="bg-indigo-50 p-3 rounded-md">
                    <p class="font-medium">Estadísticas financieras:</p>
                    <ul class="list-disc list-inside text-sm ml-2">
                        <li>Total de acciones: ${data.length}</li>
                        <li>Monto total: $${totalMonto.toLocaleString('es-MX')}</li>
                        <li>Monto promedio: $${(totalMonto / data.length).toLocaleString('es-MX')}</li>
                    </ul>
                </div>`;
            } catch (e) {
                console.error("Error al calcular estadísticas:", e);
            }
        }
        
        resumenContent += '</div>';
        resumenEjecutivo.innerHTML = resumenContent;
    }
    
    // Función para generar el HTML de la tarjeta
    function generateTarjetaHtml() {
        // Debugging: Verificar descripciones resumidas
        console.log('🎯 GENERANDO HTML - Verificando descripciones:');
        tarjetaData.acciones.forEach((accion, index) => {
            if (accion.descripcionOriginal) {
                console.log(`✅ Acción ${accion.no}: TIENE resumen aplicado`);
                console.log(`   Original: ${accion.descripcionOriginal.substring(0, 50)}...`);
                console.log(`   Resumido: ${accion.descripcion.substring(0, 50)}...`);
            } else {
                console.log(`⚠️ Acción ${accion.no}: NO tiene resumen`);
                console.log(`   Descripción: ${accion.descripcion.substring(0, 50)}...`);
            }
        });
        
        // Generar HTML de la tarjeta en formato tabla
        let html = `
        <div class="tarjeta-info border border-gray-300 rounded-lg overflow-hidden text-center">
            <div class="bg-white text-gray-800 p-6">
                <h2 class="text-2xl font-bold mb-1">${tarjetaData.titulo}</h2>
                <h3 class="text-xl mb-1">${tarjetaData.subtitulo}</h3>
                <p class="text-lg mb-6">${tarjetaData.periodo}</p>
                
                <!-- Sección A. Resumen -->
                <div class="mb-10">
                    <h4 class="text-left font-bold mb-2">A. Resumen:</h4>
                    <div class="max-w-md mx-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="bg-indigo-200 border border-gray-300 p-2 text-left">Estatus</th>
                                    <th class="bg-indigo-200 border border-gray-300 p-2 text-center">Monto en pesos</th>
                                    <th class="bg-indigo-200 border border-gray-300 p-2 text-center">Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>`;
        
        // Agregar filas de datos (resumen)
        tarjetaData.filas.forEach(fila => {
            html += `
                <tr>
                    <td class="border border-gray-300 p-2 text-left">${fila.estatus}</td>
                    <td class="border border-gray-300 p-2 text-right">${formatMonto(fila.monto)}</td>
                    <td class="border border-gray-300 p-2 text-center">${formatPorcentaje(fila.porcentaje)}</td>
                </tr>`;
        });
        
        // Agregar fila de totales
        html += `
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="bg-indigo-200 border border-gray-300 p-2 text-left font-bold">Total general</td>
                                    <td class="bg-indigo-200 border border-gray-300 p-2 text-right font-bold">${formatMonto(tarjetaData.totalMonto)}</td>
                                    <td class="bg-indigo-200 border border-gray-300 p-2 text-center font-bold">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>`;
                
        // Sección B. Acciones ordenadas (solo si hay acciones)
        if (tarjetaData.acciones && tarjetaData.acciones.length > 0) {
            html += `
                <!-- Sección B. Acciones ordenadas -->
                <div>
                    <h4 class="text-left font-bold mb-2">B. Acciones ordenadas de mayor a menor monto:</h4>`;
                    
            // Agregar el resumen generado por IA si existe
            if (tarjetaData.resumenIA) {
                html += `
                    <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h5 class="font-medium text-gray-700 mb-2">Resumen ejecutivo:</h5>
                        <p class="text-sm text-gray-700">${tarjetaData.resumenIA}</p>
                        <p class="text-xs text-gray-500 italic mt-2">Este resumen fue generado automáticamente por inteligencia artificial basándose en las descripciones de las acciones.</p>
                    </div>`;
            }
                    
            html += `
                    <div class="mx-auto overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300 text-sm">
                            <thead>
                                <tr>
                                    <th class="bg-indigo-200 border border-gray-300 p-2 text-center">No.</th>
                                    <th class="bg-indigo-200 border border-gray-300 p-2 text-center">Cuenta Pública</th>
                                    <th class="bg-indigo-200 border border-gray-300 p-2 text-center">Título de la Auditoría</th>
                                    <th class="bg-indigo-200 border border-gray-300 p-2 text-center">Clave de Acción</th>
                                    <th class="bg-indigo-200 border border-gray-300 p-2 text-center">Tipo de Acción</th>
                                    <th class="bg-indigo-200 border border-gray-300 p-2 text-center">Descripción</th>`;
            
            // Agregar encabezados de campos seleccionados (excluyendo los que ya están en las columnas base)
            if (selectedFields && selectedFields.length > 0) {
                const columnasBase = ['Cuenta Pública', 'Título de la Auditoría', 'Clave de Acción', 'Tipo de Acción', 'Descripción', 'Monto en pesos'];
                const camposFiltrados = selectedFields.filter(campo => {
                    // Excluir campos base
                    const esColumnaBase = columnasBase.some(colBase => 
                        campo.toLowerCase().includes(colBase.toLowerCase()) || 
                        colBase.toLowerCase().includes(campo.toLowerCase())
                    );
                    
                    // Excluir cualquier campo que contenga palabras relacionadas con montos para evitar duplicación
                    const palabrasMontos = ['monto', 'importe', 'valor', 'total', 'suma', 'cantidad'];
                    const contieneMontoGenerico = palabrasMontos.some(palabra => 
                        campo.toLowerCase().includes(palabra)
                    );
                    
                    return !esColumnaBase && !contieneMontoGenerico;
                });
                
                camposFiltrados.forEach(campo => {
                    html += `<th class="bg-green-200 border border-gray-300 p-2 text-center">${campo}</th>`;
                });
            }
            
            html += `<th class="bg-indigo-200 border border-gray-300 p-2 text-center">Monto en pesos</th>
                                </tr>
                            </thead>
                            <tbody>`;
            
            // Agregar filas de acciones ordenadas
            tarjetaData.acciones.forEach(accion => {
                // Preparar la descripción con tooltip si fue resumida
                let descripcionHtml = accion.descripcion;
                if (accion.descripcionOriginal) {
                    // Si la descripción fue resumida, agregar un indicador y tooltip
                    descripcionHtml = `<span class="cursor-help relative group">
                        ${accion.descripcion}
                        <span class="absolute hidden group-hover:block bottom-full left-0 bg-gray-800 text-white text-xs rounded p-2 whitespace-normal max-w-md z-50">
                            ${accion.descripcionOriginal}
                        </span>
                        <span class="text-blue-500 text-xs ml-1">[Resumido por IA]</span>
                    </span>`;
                }
                
                html += `
                    <tr>
                        <td class="border border-gray-300 p-2 text-center">${accion.no}</td>
                        <td class="border border-gray-300 p-2 text-center">${accion.cuentaPublica}</td>
                        <td class="border border-gray-300 p-2 text-left">${accion.titulo}</td>
                        <td class="border border-gray-300 p-2 text-center">${accion.claveAccion}</td>
                        <td class="border border-gray-300 p-2 text-center">${accion.tipoAccion}</td>
                        <td class="border border-gray-300 p-2 text-left">${descripcionHtml}</td>`;
                
                // Agregar celdas de campos seleccionados (excluyendo duplicados)
                if (selectedFields && selectedFields.length > 0) {
                    const columnasBase = ['Cuenta Pública', 'Título de la Auditoría', 'Clave de Acción', 'Tipo de Acción', 'Descripción', 'Monto en pesos'];
                    const camposFiltrados = selectedFields.filter(campo => {
                        // Excluir campos base
                        const esColumnaBase = columnasBase.some(colBase => 
                            campo.toLowerCase().includes(colBase.toLowerCase()) || 
                            colBase.toLowerCase().includes(campo.toLowerCase())
                        );
                        
                        // Excluir cualquier campo que contenga palabras relacionadas con montos para evitar duplicación
                        const palabrasMontos = ['monto', 'importe', 'valor', 'total', 'suma', 'cantidad'];
                        const contieneMontoGenerico = palabrasMontos.some(palabra => 
                            campo.toLowerCase().includes(palabra)
                        );
                        
                        return !esColumnaBase && !contieneMontoGenerico;
                    });
                    
                    camposFiltrados.forEach(campo => {
                        const valor = accion.camposAdicionales && accion.camposAdicionales[campo] ? 
                                     accion.camposAdicionales[campo] : 'N/A';
                        console.log(`🎯 Generando celda para ${campo} en acción ${accion.no}: ${valor}`, accion.camposAdicionales);
                        html += `<td class="border border-gray-300 p-2 text-center bg-green-50">${valor}</td>`;
                    });
                }
                
                html += `<td class="border border-gray-300 p-2 text-right">${formatMonto(accion.monto)}</td>
                    </tr>`;
            });
            
            html += `
                            </tbody>
                        </table>
                    </div>
                </div>`;
        }
        
        // Agregar nota sobre campos seleccionados si están incluidos en la tabla B
        if (selectedFields && selectedFields.length > 0 && tarjetaData.acciones && tarjetaData.acciones.length > 0) {
            const columnasBase = ['Cuenta Pública', 'Título de la Auditoría', 'Clave de Acción', 'Tipo de Acción', 'Descripción', 'Monto en pesos'];
            const camposFiltrados = selectedFields.filter(campo => {
                // Excluir campos base
                const esColumnaBase = columnasBase.some(colBase => 
                    campo.toLowerCase().includes(colBase.toLowerCase()) || 
                    colBase.toLowerCase().includes(campo.toLowerCase())
                );
                
                // Excluir cualquier campo que contenga palabras relacionadas con montos para evitar duplicación
                const palabrasMontos = ['monto', 'importe', 'valor', 'total', 'suma', 'cantidad'];
                const contieneMontoGenerico = palabrasMontos.some(palabra => 
                    campo.toLowerCase().includes(palabra)
                );
                
                return !esColumnaBase && !contieneMontoGenerico;
            });
            
            if (camposFiltrados.length > 0) {
                html += `
                    <div class="mt-4 bg-green-50 p-3 rounded-lg border border-green-200">
                        <p class="text-sm text-green-700">
                            <strong>Nota:</strong> Las columnas resaltadas en verde muestran los campos adicionales seleccionados: 
                            <em>${camposFiltrados.join(', ')}</em>
                        </p>
                        <p class="text-xs text-green-600 mt-1">
                            ${mostrarTodosRegistros ? 
                                `Mostrando todas las ${tarjetaData.acciones.length} acciones disponibles en orden descendente por monto.` : 
                                `Mostrando las ${tarjetaData.acciones.length} acciones principales con mayor monto en orden descendente.`
                            }
                        </p>
                    </div>`;
            }
        }
        
        // Cerrar contenedores
        html += `
            </div>
        </div>`;
        
        return html;
    }
    
    // Función para mostrar la pantalla de carga
    function showLoading(message = 'Procesando información...') {
        loadingMessage.textContent = message;
        loadingOverlay.classList.add('show');
    }
    
    // Función para ocultar la pantalla de carga
    function hideLoading() {
        loadingOverlay.classList.remove('show');
    }
    
    // Función para generar el resumen ejecutivo
    function generateResumen(data) {
        showLoading('Generando resumen ejecutivo con IA...');
        
        setTimeout(() => {
            if (!data || data.length === 0) {
                resumenEjecutivo.innerHTML = '<p class="text-gray-500">No hay datos para generar el resumen.</p>';
                hideLoading();
                return;
            }
            
            // Buscar la columna "Resumen" o similar
            const resumenColumn = findResumenColumn();
            if (!resumenColumn) {
                // Si no hay columna de resumen, generar resumen básico usando estadísticas
                generateBasicSummary(data);
                hideLoading();
                return;
            }
            
            // Extraer y unir los valores de resumen (no vacíos)
            let resumenValues = data
                .map(row => row[resumenColumn])
                .filter(val => val && val.trim() !== '');
            
            if (resumenValues.length === 0) {
                resumenEjecutivo.innerHTML = '<p class="text-gray-500">No hay información de resumen disponible para esta selección.</p>';
                hideLoading();
                return;
            }
            
            // Obtener el token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            // Construir contexto adicional con estadísticas
            const entidadColumn = findEntidadColumn();
            const montoColumn = findMontoColumn();
            let contextoAdicional = `Total de registros: ${data.length}`;
            
            if (selectedEntidad) {
                contextoAdicional += `. Entidad: ${selectedEntidad}`;
            }
            
            // Agregar estadísticas de montos si están disponibles
            if (montoColumn) {
                try {
                    const montos = data.map(row => {
                        const rawValue = row[montoColumn];
                        if (typeof rawValue === 'string') {
                            return parseFloat(rawValue.replace(/[$,]/g, ''));
                        }
                        return parseFloat(rawValue);
                    }).filter(monto => !isNaN(monto));
                    
                    if (montos.length > 0) {
                        const totalMonto = montos.reduce((sum, monto) => sum + monto, 0);
                        contextoAdicional += `. Monto total: $${totalMonto.toLocaleString('es-MX')}`;
                    }
                } catch (e) {
                    console.error("Error al calcular estadísticas para contexto:", e);
                }
            }
            
            // Llamar a la API de IA para generar el resumen
            fetch('/dashboard/ai/generate-summary', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    descriptions: resumenValues, // Enviar todos los valores de resumen
                    entity: selectedEntidad || 'Entidad seleccionada',
                    context: contextoAdicional
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.summary) {
                    // Mostrar el resumen generado por IA
                    let resumenContent = '<div class="space-y-4">';
                    
                    // Agregar información de la entidad si está seleccionada
                    if (selectedEntidad) {
                        resumenContent += `<h4 class="font-medium text-indigo-700">${selectedEntidad}</h4>`;
                    }
                    
                    // Agregar el resumen generado por IA
                    resumenContent += `
                    <div class="bg-indigo-50 p-4 rounded-md">
                        <p class="font-medium mb-2">Resumen ejecutivo generado por IA:</p>
                        <p class="text-sm text-gray-700">${data.summary}</p>
                    </div>`;
                    
                    // Agregar estadísticas si hay montos
                    const montoColumn = findMontoColumn();
                    if (montoColumn) {
                        try {
                            const montos = excelData.map(row => {
                                const rawValue = row[montoColumn];
                                if (typeof rawValue === 'string') {
                                    return parseFloat(rawValue.replace(/[$,]/g, ''));
                                }
                                return parseFloat(rawValue);
                            }).filter(monto => !isNaN(monto));
                            
                            if (montos.length > 0) {
                                const totalMonto = montos.reduce((sum, monto) => sum + monto, 0);
                                
                                resumenContent += `
                                <div class="bg-gray-50 p-3 rounded-md">
                                    <p class="font-medium">Estadísticas financieras:</p>
                                    <ul class="list-disc list-inside text-sm ml-2">
                                        <li>Total de acciones procesadas: ${excelData.length}</li>
                                        <li>Acciones en esta selección: ${data.length || resumenValues.length}</li>
                                        <li>Monto total: $${totalMonto.toLocaleString('es-MX')}</li>
                                        <li>Monto promedio: $${(totalMonto / (data.length || resumenValues.length)).toLocaleString('es-MX')}</li>
                                    </ul>
                                </div>`;
                            }
                        } catch (e) {
                            console.error("Error al calcular estadísticas:", e);
                        }
                    }
                    
                    resumenContent += '<p class="text-xs text-gray-500 italic">Este resumen fue generado automáticamente por inteligencia artificial basándose en la información disponible.</p>';
                    resumenContent += '</div>';
                    
                    // Actualizar el contenido
                    resumenEjecutivo.innerHTML = resumenContent;
                } else {
                    throw new Error('No se recibió un resumen válido del servidor');
                }
                
                hideLoading();
            })
            .catch(error => {
                console.error('Error al generar resumen con IA:', error);
                
                // En caso de error, generar un resumen básico
                generateBasicSummaryWithValues(data, resumenValues);
                hideLoading();
            });
        }, 500); // Pequeño retraso para mostrar la animación
    }
    
    // Nueva función auxiliar para generar resumen básico cuando hay valores de resumen pero falla la IA
    function generateBasicSummaryWithValues(data, resumenValues) {
        const montoColumn = findMontoColumn();
        
        let resumenContent = '<div class="space-y-4">';
        
        // Agregar información de la entidad
        if (selectedEntidad) {
            resumenContent += `<h4 class="font-medium text-indigo-700">${selectedEntidad}</h4>`;
        }
        
        // Mostrar un resumen básico basado en los valores disponibles
        resumenContent += `
        <div class="bg-amber-50 p-4 rounded-md border border-amber-200">
            <p class="font-medium mb-2">Resumen de información disponible:</p>
            <p class="text-sm text-gray-700">${resumenValues[0]}</p>
            ${resumenValues.length > 1 ? `<p class="text-xs text-gray-500 mt-2">Y ${resumenValues.length - 1} registro(s) adicional(es).</p>` : ''}
        </div>`;
        
        // Agregar estadísticas si hay montos
        if (montoColumn && data.length > 0) {
            try {
                const montos = data.map(row => {
                    const rawValue = row[montoColumn];
                    if (typeof rawValue === 'string') {
                        return parseFloat(rawValue.replace(/[$,]/g, ''));
                    }
                    return parseFloat(rawValue);
                }).filter(monto => !isNaN(monto));
                
                if (montos.length > 0) {
                    const totalMonto = montos.reduce((sum, monto) => sum + monto, 0);
                    
                    resumenContent += `
                    <div class="bg-indigo-50 p-3 rounded-md">
                        <p class="font-medium">Estadísticas financieras:</p>
                        <ul class="list-disc list-inside text-sm ml-2">
                            <li>Total de acciones: ${data.length}</li>
                            <li>Monto total: $${totalMonto.toLocaleString('es-MX')}</li>
                            <li>Monto promedio: $${(totalMonto / data.length).toLocaleString('es-MX')}</li>
                        </ul>
                    </div>`;
                }
            } catch (e) {
                console.error("Error al calcular estadísticas:", e);
            }
        }
        
        resumenContent += '<p class="text-xs text-gray-500 italic">Resumen básico generado. La función de IA no estuvo disponible.</p>';
        resumenContent += '</div>';
        
        resumenEjecutivo.innerHTML = resumenContent;
    }
    
    // Función para encontrar la columna de resumen
    function findResumenColumn() {
        if (!excelData || excelData.length === 0) return null;
        
        const columns = Object.keys(excelData[0]);
        
        // Buscar columnas que contengan "resumen" en su nombre
        const resumenCol = columns.find(col => 
            col.toLowerCase().includes('resumen')
        );
        
        if (resumenCol) return resumenCol;
        
        // Si no encuentra, buscar columnas relacionadas
        const alternativeNames = ['descripción', 'descripcion', 'observación', 'observacion', 'comentario'];
        
        for (const name of alternativeNames) {
            const match = columns.find(col => 
                col.toLowerCase().includes(name)
            );
            if (match) return match;
        }
        
        // Si no se encuentra nada, usar la columna más larga en promedio
        let longestCol = '';
        let maxLength = 0;
        
        for (const col of columns) {
            const avgLength = excelData.reduce((sum, row) => {
                const val = row[col];
                return sum + (val ? String(val).length : 0);
            }, 0) / excelData.length;
            
            if (avgLength > maxLength) {
                maxLength = avgLength;
                longestCol = col;
            }
        }
        
        return longestCol;
    }
    
    // Función para encontrar la columna de monto
    function findMontoColumn() {
        if (!excelData || excelData.length === 0) return null;
        
        const columns = Object.keys(excelData[0]);
        
        // Buscar columnas que contengan "monto" o "importe" en su nombre
        const montoCol = columns.find(col => 
            col.toLowerCase().includes('monto') || 
            col.toLowerCase().includes('importe')
        );
        
        return montoCol;
    }
    
    // Función para descargar la tarjeta como PDF
    function downloadTarjetaAsPDF() {
        showLoading('Preparando documento PDF...');
        
        try {
            // Acceder a los objetos de jsPDF
            const { jsPDF } = window.jspdf;
            
            // Actualizar los datos desde el editor si está visible
            if (!tarjetaEditor.classList.contains('hidden')) {
                updateTarjetaDataFromEditor();
            }
            
            // Crear una copia del elemento de tarjeta para manipularlo
            const tarjetaElement = tarjetaPreview.querySelector('.tarjeta-info');
            
            if (!tarjetaElement) {
                hideLoading();
                showError('No se pudo generar el PDF. Intente nuevamente.');
                return;
            }
            
            // Crear una copia de la tarjeta para evitar modificar la original
            const tarjetaClone = tarjetaElement.cloneNode(true);
            tarjetaClone.classList.add('for-pdf'); // Agregar clase específica para PDF
            
            // Asegurar que los colores de fondo se muestren en el PDF
            const headers = tarjetaClone.querySelectorAll('.bg-indigo-200');
            headers.forEach(header => {
                header.style.backgroundColor = '#c7d2fe'; // Color indigo-200 explícito
                header.style.color = '#1f2937'; // Color del texto
            });
            
            // Asegurar que los colores verdes de los campos seleccionados se muestren en el PDF
            const headersVerdes = tarjetaClone.querySelectorAll('.bg-green-200');
            headersVerdes.forEach(header => {
                header.style.backgroundColor = '#bbf7d0'; // Color verde-200 explícito
                header.style.color = '#0f172a'; // Color del texto
                header.style.fontWeight = 'bold';
            });
            
            // Crear un contenedor con estilo específico para PDF
            const container = document.createElement('div');
            container.className = 'pdf-container';
            container.appendChild(tarjetaClone);
            container.style.position = 'absolute';
            container.style.left = '-9999px';
            container.style.top = '-9999px';
            document.body.appendChild(container);
            
            // Procesar el nombre del archivo
            let safeEntityName = tarjetaData.titulo.replace(/[^a-z0-9áéíóúüñÁÉÍÓÚÜÑ]/gi, '_');
            // Limitar longitud del nombre
            if (safeEntityName.length > 30) {
                safeEntityName = safeEntityName.substring(0, 30);
            }
            const filename = `Tarjeta_${safeEntityName}_${new Date().toISOString().slice(0,10)}.pdf`;
            
            // Mostrar mensaje de procesamiento
            loadingMessage.textContent = 'Generando imagen para PDF...';
            
            // Usar html2canvas con opciones mejoradas para convertir la tarjeta a una imagen
            html2canvas(container, {
                scale: 2, // Mayor calidad
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                logging: false, // Desactivar logs
                onclone: function(clonedDoc) {
                    // Aplicar estilos adicionales al clon antes de la captura
                    const clonedTarjeta = clonedDoc.querySelector('.tarjeta-info.for-pdf');
                    if (clonedTarjeta) {
                        // Forzar colores para impresión
                        const headers = clonedTarjeta.querySelectorAll('th, td.bg-indigo-200');
                        headers.forEach(header => {
                            header.style.backgroundColor = '#c7d2fe';
                            header.style.color = '#1f2937';
                            header.style.fontWeight = 'bold';
                        });
                        
                        // Forzar colores verdes para los campos seleccionados
                        const headersVerdes = clonedTarjeta.querySelectorAll('th.bg-green-200, td.bg-green-200');
                        headersVerdes.forEach(header => {
                            header.style.backgroundColor = '#bbf7d0';
                            header.style.color = '#0f172a';
                            header.style.fontWeight = 'bold';
                        });
                        
                        // Alinear correctamente los números
                        const montosCells = clonedTarjeta.querySelectorAll('td:nth-child(2)');
                        montosCells.forEach(cell => {
                            cell.style.textAlign = 'right';
                        });
                        
                        const porcentajeCells = clonedTarjeta.querySelectorAll('td:nth-child(3)');
                        porcentajeCells.forEach(cell => {
                            cell.style.textAlign = 'center';
                        });
                    }
                }
            }).then(canvas => {
                try {
                    // Eliminar el contenedor temporal
                    document.body.removeChild(container);
                    
                    loadingMessage.textContent = 'Generando documento PDF...';
                    
                    // Crear un nuevo PDF
                    const pdf = new jsPDF({
                        orientation: 'portrait',
                        unit: 'mm',
                        format: 'a4'
                    });
                    
                    // Calcular dimensiones
                    const imgData = canvas.toDataURL('image/jpeg', 1.0);
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    let pdfHeight = (canvas.height * pdfWidth) / canvas.width;
                    
                    // Si la altura es mayor que el tamaño de página, ajustar escala
                    const maxHeight = pdf.internal.pageSize.getHeight() - 20; // margen
                    
                    if (pdfHeight > maxHeight) {
                        pdfHeight = maxHeight;
                    }
                    
                    // Añadir la imagen al PDF con margen
                    pdf.addImage(imgData, 'JPEG', 10, 10, pdfWidth - 20, pdfHeight);
                    
                    // Añadir información adicional en el pie de página
                    const footerY = pdfHeight + 15;
                    pdf.setFontSize(8);
                    pdf.setTextColor(100, 100, 100);
                    pdf.text(`Generado el ${new Date().toLocaleDateString('es-MX')} - Sistema de Generación de Tarjetas`, pdfWidth / 2, footerY, { align: 'center' });
                    
                    // Agregar metadatos al PDF
                    pdf.setProperties({
                        title: `Tarjeta Informativa - ${tarjetaData.titulo}`,
                        subject: `${tarjetaData.subtitulo} ${tarjetaData.periodo}`,
                        creator: 'Sistema de Generación de Tarjetas',
                        author: 'ASF - Auditoría Superior de la Federación'
                    });
                    
                    loadingMessage.textContent = 'Descargando PDF...';
                    
                    // Guardar el PDF
                    pdf.save(filename);
                    
                    // Mostrar mensaje de éxito
                    const successMsg = document.createElement('div');
                    successMsg.className = 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-4';
                    successMsg.innerHTML = `
                        <p class="font-bold">¡PDF generado con éxito!</p>
                        <p>El archivo "${filename}" se ha descargado.</p>
                    `;
                    
                    // Insertar antes de la previsualización
                    previewContainer.parentNode.insertBefore(successMsg, previewContainer);
                    
                    // Eliminar el mensaje después de 5 segundos
                    setTimeout(() => {
                        successMsg.remove();
                    }, 5000);
                    
                    hideLoading();
                } catch (err) {
                    console.error('Error al generar PDF:', err);
                    hideLoading();
                    showError('Ocurrió un error al generar el PDF: ' + err.message);
                }
            }).catch(err => {
                if (container.parentNode) {
                    document.body.removeChild(container);
                }
                console.error('Error en html2canvas:', err);
                hideLoading();
                showError('Ocurrió un error al convertir la tarjeta a imagen para el PDF.');
            });
        } catch (err) {
            console.error('Error inicial en la generación de PDF:', err);
            hideLoading();
            
            // Verificar si el error es por falta de la biblioteca
            if (err.toString().includes('jspdf is not defined') || !window.jspdf) {
                showError('No se pudo cargar la biblioteca de generación de PDF. Verifique su conexión a internet e intente nuevamente.');
            } else {
                showError('Ocurrió un error inesperado al iniciar la generación del PDF.');
            }
        }
    }

    // Función para descargar la tarjeta como DOCX
    function downloadTarjetaAsDOCX() {
        showLoading('Preparando documento DOCX...');
        
        try {
            // Verificar si la biblioteca está disponible
            if (typeof docx === 'undefined') {
                throw new Error('La biblioteca docx no está cargada correctamente');
            }
            
            // Intentar cargar la biblioteca desde window si está disponible así
            const docxLib = window.docx || docx;
            
            // Accedemos a la biblioteca docx.js
            const { Document, Packer, Paragraph, Table, TableRow, TableCell, TextRun, AlignmentType, HeadingLevel, BorderStyle } = docxLib;
            
            // Verificar que todos los componentes necesarios estén disponibles
            if (!Document || !Packer || !Paragraph || !Table || !TableRow || !TableCell || !TextRun || !AlignmentType || !HeadingLevel || !BorderStyle) {
                throw new Error('No se pudieron cargar todos los componentes necesarios de la biblioteca docx');
            }
            
            // Actualizar los datos desde el editor si está visible
            if (!tarjetaEditor.classList.contains('hidden')) {
                updateTarjetaDataFromEditor();
            }
            
            // Verificar que existan datos para generar el documento
            if (!tarjetaData || !tarjetaData.filas || tarjetaData.filas.length === 0) {
                throw new Error('No hay datos suficientes para generar el documento');
            }
            
            console.log('Generando documento con los siguientes datos:', tarjetaData);
            
            // Crear children para la sección principal
            const children = [];
            
            // Título del documento
            children.push(
                new Paragraph({
                    text: tarjetaData.titulo || 'Tarjeta Informativa',
                    heading: HeadingLevel.HEADING_1,
                    alignment: AlignmentType.CENTER,
                    spacing: {
                        after: 200
                    }
                })
            );
            
            // Subtítulo
            children.push(
                new Paragraph({
                    text: tarjetaData.subtitulo || 'Resumen',
                    heading: HeadingLevel.HEADING_2,
                    alignment: AlignmentType.CENTER
                })
            );
            
            // Periodo
            children.push(
                new Paragraph({
                    text: tarjetaData.periodo || new Date().getFullYear().toString(),
                    heading: HeadingLevel.HEADING_3,
                    alignment: AlignmentType.CENTER,
                    spacing: {
                        after: 500
                    }
                })
            );
            
            // Sección A: Resumen
            children.push(
                new Paragraph({
                    text: "A. Resumen:",
                    heading: HeadingLevel.HEADING_3,
                    spacing: {
                        after: 200
                    }
                })
            );
            
            // Tabla de Resumen
            const resumenTable = createResumenTable(docxLib);
            if (resumenTable) {
                children.push(resumenTable);
            }
            
            // Espacio entre secciones
            children.push(
                new Paragraph({
                    text: "",
                    spacing: {
                        after: 400
                    }
                })
            );
            
            // Añadir la sección B solo si hay acciones
            if (tarjetaData.acciones && tarjetaData.acciones.length > 0) {
                // Añadir título de la sección B
                children.push(
                    new Paragraph({
                        text: "B. Acciones ordenadas de mayor a menor monto:",
                        heading: HeadingLevel.HEADING_3,
                        spacing: {
                            after: 200
                        }
                    })
                );
                
                // Añadir el resumen generado por IA si existe
                if (tarjetaData.resumenIA) {
                    children.push(
                        new Paragraph({
                            text: "Resumen ejecutivo:",
                            heading: HeadingLevel.HEADING_4,
                            spacing: {
                                after: 100
                            }
                        })
                    );
                    
                    children.push(
                        new Paragraph({
                            text: tarjetaData.resumenIA,
                            spacing: {
                                after: 100
                            }
                        })
                    );
                    
                    children.push(
                        new Paragraph({
                            text: "Este resumen fue generado automáticamente por inteligencia artificial basándose en las descripciones de las acciones.",
                            italics: true,
                            size: 20, // Tamaño más pequeño
                            color: "666666", // Gris
                            spacing: {
                                after: 300
                            }
                        })
                    );
                }
                
                // Añadir tabla de acciones
                const accionesTable = createAccionesTable(docxLib);
                if (accionesTable) {
                    children.push(accionesTable);
                }
            }
            
            // Añadir nota sobre campos seleccionados si están incluidos en la tabla B
            if (selectedFields && selectedFields.length > 0 && tarjetaData.acciones && tarjetaData.acciones.length > 0) {
                const columnasBase = ['Cuenta Pública', 'Título de la Auditoría', 'Clave de Acción', 'Tipo de Acción', 'Descripción', 'Monto en pesos'];
                const camposFiltrados = selectedFields.filter(campo => {
                    // Excluir campos base
                    const esColumnaBase = columnasBase.some(colBase => 
                        campo.toLowerCase().includes(colBase.toLowerCase()) || 
                        colBase.toLowerCase().includes(campo.toLowerCase())
                    );
                    
                    // Excluir cualquier campo que contenga palabras relacionadas con montos para evitar duplicación
                    const palabrasMontos = ['monto', 'importe', 'valor', 'total', 'suma', 'cantidad'];
                    const contieneMontoGenerico = palabrasMontos.some(palabra => 
                        campo.toLowerCase().includes(palabra)
                    );
                    
                    return !esColumnaBase && !contieneMontoGenerico;
                });
                
                if (camposFiltrados.length > 0) {
                    // Espacio entre secciones
                    children.push(
                        new Paragraph({
                            text: "",
                            spacing: {
                                after: 200
                            }
                        })
                    );
                    
                    // Añadir nota explicativa
                    const notaTexto = mostrarTodosRegistros ? 
                        `Nota: Las columnas resaltadas en verde muestran los campos adicionales seleccionados: ${camposFiltrados.join(', ')}. Mostrando todas las ${tarjetaData.acciones.length} acciones disponibles en orden descendente por monto.` :
                        `Nota: Las columnas resaltadas en verde muestran los campos adicionales seleccionados: ${camposFiltrados.join(', ')}. Mostrando las ${tarjetaData.acciones.length} acciones principales con mayor monto en orden descendente.`;
                    
                    children.push(
                        new Paragraph({
                            text: notaTexto,
                            italics: true,
                            size: 20, // Tamaño más pequeño
                            color: "666666", // Gris
                            spacing: {
                                after: 300
                            }
                        })
                    );
                }
            }
            
            // Crear un nuevo documento con todas las secciones
            const doc = new Document({
                sections: [{
                    properties: {},
                    children: children
                }]
            });
            
            // Procesar el nombre del archivo
            let safeEntityName = (tarjetaData.titulo || 'Tarjeta').replace(/[^a-z0-9áéíóúüñÁÉÍÓÚÜÑ]/gi, '_');
            // Limitar longitud del nombre
            if (safeEntityName.length > 30) {
                safeEntityName = safeEntityName.substring(0, 30);
            }
            const filename = `Tarjeta_${safeEntityName}_${new Date().toISOString().slice(0,10)}.docx`;
            
            loadingMessage.textContent = 'Generando documento DOCX...';
            
            // Generar y descargar el documento
            Packer.toBlob(doc).then(blob => {
                saveAs(blob, filename);
                
                // Mostrar mensaje de éxito
                const successMsg = document.createElement('div');
                successMsg.className = 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-4';
                successMsg.innerHTML = `
                    <p class="font-bold">¡DOCX generado con éxito!</p>
                    <p>El archivo "${filename}" se ha descargado. Puede editarlo en Microsoft Word o aplicaciones compatibles.</p>
                `;
                
                // Insertar antes de la previsualización
                previewContainer.parentNode.insertBefore(successMsg, previewContainer);
                
                // Eliminar el mensaje después de 5 segundos
                setTimeout(() => {
                    successMsg.remove();
                }, 5000);
                
                hideLoading();
            }).catch(error => {
                console.error('Error al generar DOCX:', error);
                hideLoading();
                showError('Ocurrió un error al generar el documento DOCX: ' + error.message);
            });
            
        } catch (err) {
            console.error('Error al generar DOCX:', err);
            hideLoading();
            
            // Verificar si el error es por falta de la biblioteca
            if (err.toString().includes('docx is not defined') || !window.docx) {
                showError('No se pudo cargar la biblioteca de generación de DOCX. Verifique su conexión a internet e intente nuevamente.');
            } else {
                showError('Ocurrió un error inesperado al iniciar la generación del DOCX.');
            }
        }
    }
    
    // Función auxiliar para crear la tabla de resumen en DOCX
    function createResumenTable(docxLib) {
        const { Table, TableRow, TableCell, Paragraph, TextRun, BorderStyle } = docxLib;
        
        // Crear las filas para la tabla
        const rows = [];
        
        // Fila de encabezado
        rows.push(
            new TableRow({
                tableHeader: true,
                children: [
                    createHeaderCell("Estatus"),
                    createHeaderCell("Monto en pesos"),
                    createHeaderCell("Porcentaje")
                ]
            })
        );
        
        // Filas de datos
        tarjetaData.filas.forEach(fila => {
            rows.push(
                new TableRow({
                    children: [
                        createTableCell(fila.estatus, 'left'),
                        createTableCell(formatMonto(fila.monto), 'right'),
                        createTableCell(formatPorcentaje(fila.porcentaje), 'center')
                    ]
                })
            );
        });
        
        // Fila de total
        rows.push(
            new TableRow({
                children: [
                    createHeaderCell("Total general"),
                    createHeaderCell(formatMonto(tarjetaData.totalMonto), 'right'),
                    createHeaderCell("100%", 'center')
                ]
            })
        );
        
        // Crear y devolver la tabla
        return new Table({
            width: {
                size: 100,
                type: 'pct'
            },
            rows: rows
        });
    }
    
    // Función auxiliar para crear la tabla de acciones en DOCX
    function createAccionesTable(docxLib) {
        const { Table, TableRow, TableCell, Paragraph, TextRun, BorderStyle } = docxLib;
        
        // Crear las filas para la tabla
        const rows = [];
        
        // Crear encabezados base
        const headerCells = [
            createHeaderCell("No."),
            createHeaderCell("Cuenta Pública"),
            createHeaderCell("Título de la Auditoría"),
            createHeaderCell("Clave de Acción"),
            createHeaderCell("Tipo de Acción"),
            createHeaderCell("Descripción")
        ];
        
        // Agregar encabezados de campos seleccionados (excluyendo duplicados)
        if (selectedFields && selectedFields.length > 0) {
            const columnasBase = ['Cuenta Pública', 'Título de la Auditoría', 'Clave de Acción', 'Tipo de Acción', 'Descripción', 'Monto en pesos'];
            const camposFiltrados = selectedFields.filter(campo => {
                // Excluir campos base
                const esColumnaBase = columnasBase.some(colBase => 
                    campo.toLowerCase().includes(colBase.toLowerCase()) || 
                    colBase.toLowerCase().includes(campo.toLowerCase())
                );
                
                // Excluir cualquier campo que contenga palabras relacionadas con montos para evitar duplicación
                const palabrasMontos = ['monto', 'importe', 'valor', 'total', 'suma', 'cantidad'];
                const contieneMontoGenerico = palabrasMontos.some(palabra => 
                    campo.toLowerCase().includes(palabra)
                );
                
                return !esColumnaBase && !contieneMontoGenerico;
            });
            
            camposFiltrados.forEach(campo => {
                headerCells.push(createHeaderCellVerde(campo));
            });
        }
        
        // Agregar encabezado de monto al final
        headerCells.push(createHeaderCell("Monto en pesos"));
        
        // Fila de encabezado
        rows.push(
            new TableRow({
                tableHeader: true,
                children: headerCells
            })
        );
        
        // Filas de datos
        tarjetaData.acciones.forEach(accion => {
            // Crear celdas base
            const dataCells = [
                createTableCell(accion.no.toString(), 'center'),
                createTableCell(accion.cuentaPublica.toString(), 'center'),
                createTableCell(accion.titulo, 'left'),
                createTableCell(accion.claveAccion, 'center'),
                createTableCell(accion.tipoAccion, 'center'),
                createTableCell(accion.descripcion, 'left')
            ];
            
            // Agregar celdas de campos seleccionados (excluyendo duplicados)
            if (selectedFields && selectedFields.length > 0) {
                const columnasBase = ['Cuenta Pública', 'Título de la Auditoría', 'Clave de Acción', 'Tipo de Acción', 'Descripción', 'Monto en pesos'];
                const camposFiltrados = selectedFields.filter(campo => {
                    // Excluir campos base
                    const esColumnaBase = columnasBase.some(colBase => 
                        campo.toLowerCase().includes(colBase.toLowerCase()) || 
                        colBase.toLowerCase().includes(campo.toLowerCase())
                    );
                    
                    // Excluir cualquier campo que contenga palabras relacionadas con montos para evitar duplicación
                    const palabrasMontos = ['monto', 'importe', 'valor', 'total', 'suma', 'cantidad'];
                    const contieneMontoGenerico = palabrasMontos.some(palabra => 
                        campo.toLowerCase().includes(palabra)
                    );
                    
                    return !esColumnaBase && !contieneMontoGenerico;
                });
                
                camposFiltrados.forEach(campo => {
                    const valor = accion.camposAdicionales && accion.camposAdicionales[campo] ? 
                                 accion.camposAdicionales[campo] : 'N/A';
                    console.log(`📄 DOCX: Generando celda para ${campo} en acción ${accion.no}: ${valor}`, accion.camposAdicionales);
                    dataCells.push(createTableCell(valor.toString(), 'center'));
                });
            }
            
            // Agregar celda de monto al final
            dataCells.push(createTableCell(formatMonto(accion.monto), 'right'));
            
            rows.push(
                new TableRow({
                    children: dataCells
                })
            );
        });
        
        // Crear y devolver la tabla
        return new Table({
            width: {
                size: 100,
                type: 'pct'
            },
            rows: rows
        });
    }
    

    
    // Función auxiliar para crear celdas de encabezado verdes (para campos seleccionados)
    function createHeaderCellVerde(text, alignment = 'left') {
        const { TableCell, Paragraph, TextRun, BorderStyle } = window.docx || docx;
        
        return new TableCell({
            borders: {
                top: { style: BorderStyle.SINGLE, size: 1, color: "auto" },
                bottom: { style: BorderStyle.SINGLE, size: 1, color: "auto" },
                left: { style: BorderStyle.SINGLE, size: 1, color: "auto" },
                right: { style: BorderStyle.SINGLE, size: 1, color: "auto" }
            },
            shading: {
                fill: "BBF7D0"  // Color verde-200 para campos seleccionados
            },
            children: [
                new Paragraph({
                    children: [
                        new TextRun({
                            text: text,
                            bold: true
                        })
                    ],
                    alignment: getAlignment(alignment)
                })
            ],
            width: {
                size: getColumnWidth(text),
                type: "auto"
            }
        });
    }
    
    // Función auxiliar para crear celdas de encabezado
    function createHeaderCell(text, alignment = 'left') {
        const { TableCell, Paragraph, TextRun, BorderStyle } = window.docx || docx;
        
        return new TableCell({
            borders: {
                top: { style: BorderStyle.SINGLE, size: 1, color: "auto" },
                bottom: { style: BorderStyle.SINGLE, size: 1, color: "auto" },
                left: { style: BorderStyle.SINGLE, size: 1, color: "auto" },
                right: { style: BorderStyle.SINGLE, size: 1, color: "auto" }
            },
            shading: {
                fill: "D1D5F6"  // Color similar al indigo-200
            },
            children: [
                new Paragraph({
                    children: [
                        new TextRun({
                            text: text,
                            bold: true
                        })
                    ],
                    alignment: getAlignment(alignment)
                })
            ],
            width: {
                size: getColumnWidth(text),
                type: "auto"
            }
        });
    }
    
    // Función auxiliar para crear celdas de tabla normales
    function createTableCell(text, alignment = 'left') {
        const { TableCell, Paragraph, TextRun, BorderStyle } = window.docx || docx;
        
        return new TableCell({
            borders: {
                top: { style: BorderStyle.SINGLE, size: 1, color: "auto" },
                bottom: { style: BorderStyle.SINGLE, size: 1, color: "auto" },
                left: { style: BorderStyle.SINGLE, size: 1, color: "auto" },
                right: { style: BorderStyle.SINGLE, size: 1, color: "auto" }
            },
            children: [
                new Paragraph({
                    children: [
                        new TextRun({
                            text: text
                        })
                    ],
                    alignment: getAlignment(alignment)
                })
            ]
        });
    }
    
    // Función auxiliar para obtener el tipo de alineación para DOCX
    function getAlignment(alignment) {
        const { AlignmentType } = window.docx || docx;
        
        switch(alignment) {
            case 'right':
                return AlignmentType.RIGHT;
            case 'center':
                return AlignmentType.CENTER;
            case 'left':
            default:
                return AlignmentType.LEFT;
        }
    }
    
    // Función auxiliar para determinar el ancho de la columna basada en el contenido
    function getColumnWidth(text) {
        // Proporcionar un valor por defecto en caso de que text sea undefined
        if (!text) return 30;
        
        // Anchos predefinidos para ciertos tipos de columnas
        const columnWidths = {
            "No.": 5,
            "Cuenta Pública": 15,
            "Clave de Acción": 20,
            "Tipo de Acción": 15,
            "Monto en pesos": 20,
            "Porcentaje": 15,
            "Estatus": 30
        };
        
        return columnWidths[text] || 50; // Valor por defecto para otras columnas
    }

    // Inicializar los datos de la tarjeta
    function initTarjetaData() {
        console.log('🔧 INICIANDO initTarjetaData');
        console.log('📊 Datos disponibles - excelData:', excelData ? excelData.length : 'NO DISPONIBLE');
        console.log('🎯 Entidad seleccionada:', selectedEntidad);
        
        // Usar excelData como fuente de datos
        const dataSource = excelData;
        
        if (!dataSource || dataSource.length === 0) {
            console.error('❌ No hay datos disponibles para procesar');
            return false;
        }
        
        console.log('📂 Usando fuente de datos con', dataSource.length, 'registros');
        
        const entidadColumn = findEntidadColumn();
        const montoColumn = findMontoColumn();
        
        console.log('🔍 Columnas encontradas - Entidad:', entidadColumn, '| Monto:', montoColumn);
        
        // Filtrar datos según la entidad seleccionada
        let dataForEntity = [];
        if (selectedEntidad && entidadColumn) {
            dataForEntity = dataSource.filter(row => row[entidadColumn] === selectedEntidad);
            console.log('🎯 Filtrado por entidad completado:', dataForEntity.length, 'registros');
        } else {
            dataForEntity = dataSource;
            console.log('📋 Usando todos los datos disponibles:', dataForEntity.length, 'registros');
        }
        
        if (dataForEntity.length === 0) {
            showError('No se encontraron datos para la entidad seleccionada');
            return false;
        }
        
        // Inicializar título con la entidad seleccionada
        tarjetaData.titulo = selectedEntidad || 'Entidad';
        tarjetaData.subtitulo = 'Resumen';
        
        // Intentar obtener un periodo de los datos
        const periodoColumn = findPeriodoColumn();
        if (periodoColumn && dataForEntity.length > 0) {
            const periodos = dataForEntity.map(row => row[periodoColumn]).filter(Boolean);
            if (periodos.length > 0) {
                // Intentar encontrar un rango de años
                const años = periodos.map(p => {
                    const match = p.toString().match(/\b(20\d{2})\b/g);
                    return match ? match[0] : null;
                }).filter(Boolean);
                
                if (años.length > 0) {
                    const minAño = Math.min(...años.map(a => parseInt(a)));
                    const maxAño = Math.max(...años.map(a => parseInt(a)));
                    
                    if (minAño !== maxAño) {
                        tarjetaData.periodo = `${minAño} - ${maxAño}`;
                    } else {
                        tarjetaData.periodo = minAño.toString();
                    }
                }
            }
        }
        
        if (!tarjetaData.periodo) {
            tarjetaData.periodo = new Date().getFullYear().toString();
        }
        
        // Reiniciar filas de datos
        tarjetaData.filas = [];
        
        // Comprobar si hay campos seleccionados
        if (selectedFields.length === 0) {
            showError('No hay campos seleccionados para generar la tarjeta');
            return false;
        }
        
        // Estrategia 1: Si hay una columna de estatus y monto, agrupar por estatus
        const estatusColumn = findEstatusColumn();
        if (estatusColumn && montoColumn && dataForEntity.length > 0) {
            // Agrupar por estatus y sumar montos
            const grouped = {};
            let totalMonto = 0;
            
            dataForEntity.forEach(row => {
                const estatus = row[estatusColumn] || 'Sin especificar';
                const montoRaw = row[montoColumn];
                let monto = 0;
                
                // Convertir el monto a número
                if (typeof montoRaw === 'string') {
                    monto = parseFloat(montoRaw.replace(/[^\d.-]/g, '')) || 0;
                } else if (typeof montoRaw === 'number') {
                    monto = montoRaw;
                }
                
                // Acumular en el grupo
                if (!grouped[estatus]) {
                    grouped[estatus] = 0;
                }
                grouped[estatus] += monto;
                totalMonto += monto;
            });
            
            // Convertir a filas
            tarjetaData.filas = Object.entries(grouped).map(([estatus, monto]) => {
                const porcentaje = totalMonto > 0 ? (monto / totalMonto * 100) : 0;
                return {
                    estatus,
                    monto,
                    porcentaje
                };
            });
            
            tarjetaData.totalMonto = totalMonto;
        } 
        // Estrategia 2: Si hay campos seleccionados que parecen categorías, usarlos como estatus
        else if (selectedFields.length > 0 && dataForEntity.length > 0) {
            // Intentar encontrar campos que parezcan categorías o clasificaciones
            const potentialStatusFields = selectedFields.filter(field => 
                !field.toLowerCase().includes('monto') && 
                !field.toLowerCase().includes('importe') &&
                !field.toLowerCase().includes('fecha') &&
                !field.toLowerCase().includes('año') &&
                !field.toLowerCase().includes('periodo')
            );
            
            // Buscar campos que pudieran servir como montos
            const potentialMontoFields = selectedFields.filter(field => 
                field.toLowerCase().includes('monto') || 
                field.toLowerCase().includes('importe') ||
                field.toLowerCase().includes('valor') ||
                field.toLowerCase().includes('precio') ||
                field.toLowerCase().includes('total')
            );
            
            if (potentialStatusFields.length > 0 && potentialMontoFields.length > 0) {
                const statusField = potentialStatusFields[0]; // Usar el primer campo como estatus
                const montoField = potentialMontoFields[0]; // Usar el primer campo como monto
                
                // Agrupar por el campo de estatus
                const grouped = {};
                let totalMonto = 0;
                
                dataForEntity.forEach(row => {
                    const estatus = row[statusField] || 'Sin especificar';
                    const montoRaw = row[montoField];
                    let monto = 0;
                    
                    // Convertir el monto a número
                    if (typeof montoRaw === 'string') {
                        monto = parseFloat(montoRaw.replace(/[^\d.-]/g, '')) || 0;
                    } else if (typeof montoRaw === 'number') {
                        monto = montoRaw;
                    }
                    
                    // Acumular en el grupo
                    if (!grouped[estatus]) {
                        grouped[estatus] = 0;
                    }
                    grouped[estatus] += monto;
                    totalMonto += monto;
                });
                
                // Convertir a filas
                tarjetaData.filas = Object.entries(grouped).map(([estatus, monto]) => {
                    const porcentaje = totalMonto > 0 ? (monto / totalMonto * 100) : 0;
                    return {
                        estatus,
                        monto,
                        porcentaje
                    };
                });
                
                tarjetaData.totalMonto = totalMonto;
            }
            // Estrategia 3: Usar los campos seleccionados de la primera fila como ejemplo
            else {
                // Si no se puede determinar una estructura adecuada, crear filas de ejemplo con los campos seleccionados
                const firstRow = dataForEntity[0];
                tarjetaData.filas = [];
                
                // Intentar detectar un campo de monto para el total
                let foundMontoField = null;
                for (const field of selectedFields) {
                    const value = firstRow[field];
                    if (typeof value === 'number' || (typeof value === 'string' && !isNaN(parseFloat(value.replace(/[^\d.-]/g, ''))))) {
                        foundMontoField = field;
                        break;
                    }
                }
                
                // Calcular un monto total si se encontró un campo de monto
                let totalMonto = 0;
                if (foundMontoField) {
                    dataForEntity.forEach(row => {
                        const montoRaw = row[foundMontoField];
                        let monto = 0;
                        
                        if (typeof montoRaw === 'string') {
                            monto = parseFloat(montoRaw.replace(/[^\d.-]/g, '')) || 0;
                        } else if (typeof montoRaw === 'number') {
                            monto = montoRaw;
                        }
                        
                        totalMonto += monto;
                    });
                    
                    tarjetaData.totalMonto = totalMonto;
                } else {
                    // Si no se encontró un campo de monto, usar ejemplo
                    tarjetaData.totalMonto = 55000000;
                }
                
                // Crear filas basadas en campos seleccionados o ejemplos
                if (selectedFields.length >= 3) {
                    // Usar los primeros tres campos seleccionados como ejemplos
                    tarjetaData.filas = [
                        { 
                            estatus: firstRow[selectedFields[0]] || 'Solventadas', 
                            monto: 10000000, 
                            porcentaje: 18 
                        },
                        { 
                            estatus: selectedFields.length > 1 ? firstRow[selectedFields[1]] || 'En seguimiento' : 'En seguimiento', 
                            monto: 20000000, 
                            porcentaje: 36 
                        },
                        { 
                            estatus: selectedFields.length > 2 ? firstRow[selectedFields[2]] || 'En DGI' : 'En DGI', 
                            monto: 25000000, 
                            porcentaje: 45 
                        }
                    ];
                } else {
                    // Usar datos de ejemplo por defecto
                    tarjetaData.filas = [
                        { estatus: 'Solventadas', monto: 10000000, porcentaje: 18 },
                        { estatus: 'En seguimiento', monto: 20000000, porcentaje: 36 },
                        { estatus: 'En DGI', monto: 25000000, porcentaje: 45 }
                    ];
                }
            }
        } else {
            // Datos de ejemplo por defecto
            tarjetaData.filas = [
                { estatus: 'Solventadas', monto: 10000000, porcentaje: 18 },
                { estatus: 'En seguimiento', monto: 20000000, porcentaje: 36 },
                { estatus: 'En DGI', monto: 25000000, porcentaje: 45 }
            ];
            tarjetaData.totalMonto = 55000000;
        }
        
        // También inicializar algunas acciones de ejemplo para la sección B
        tarjetaData.acciones = [];
        
        // Buscar campos para la sección de acciones
        const cuentaPublicaColumn = findCuentaPublicaColumn();
        const tituloAuditoriaColumn = findTituloAuditoriaColumn();
        const claveAccionColumn = findClaveAccionColumn();
        const tipoAccionColumn = findTipoAccionColumn();
        const descripcionColumn = findDescripcionColumn();
        
        // AUTOMÁTICO: Obtener las acciones con mayor valor en orden descendente
        if (montoColumn && dataForEntity.length > 0) {
            const cantidadMostrar = mostrarTodosRegistros ? dataForEntity.length : cantidadRegistros;
            console.log('🎯 Procesando automáticamente las acciones con mayor monto');
            console.log('📊 Total de registros disponibles:', dataForEntity.length);
            console.log('📊 Cantidad a mostrar:', mostrarTodosRegistros ? 'TODOS' : cantidadMostrar);
            
            // Ordenar TODOS los datos por monto (de mayor a menor)
            const sortedData = [...dataForEntity].sort((a, b) => {
                const montoA = parseFloat(a[montoColumn].toString().replace(/[^\d.-]/g, '')) || 0;
                const montoB = parseFloat(b[montoColumn].toString().replace(/[^\d.-]/g, '')) || 0;
                return montoB - montoA; // Orden descendente
            });
            
            console.log('💰 Top 5 montos encontrados:', sortedData.slice(0, 5).map(row => ({
                monto: parseFloat(row[montoColumn].toString().replace(/[^\d.-]/g, '')) || 0,
                clave: row[claveAccionColumn] || 'Sin clave'
            })));
            
            // Tomar las acciones según la configuración del usuario
            const topRecords = mostrarTodosRegistros ? 
                sortedData : 
                sortedData.slice(0, Math.min(cantidadMostrar, sortedData.length));
            
            console.log(`✅ Seleccionadas ${topRecords.length} acciones con mayor monto para la tarjeta`);
            console.log('💰 Configuración aplicada:', {
                mostrarTodos: mostrarTodosRegistros,
                cantidadSolicitada: cantidadMostrar,
                cantidadObtenida: topRecords.length
            });
            console.log('💰 Rango de montos:', {
                mayor: parseFloat(topRecords[0][montoColumn].toString().replace(/[^\d.-]/g, '')) || 0,
                menor: parseFloat(topRecords[topRecords.length - 1][montoColumn].toString().replace(/[^\d.-]/g, '')) || 0
            });
            
            // Verificar que efectivamente están ordenadas por monto (mayor a menor)
            const montosVerificacion = topRecords.map(row => parseFloat(row[montoColumn].toString().replace(/[^\d.-]/g, '')) || 0);
            const estaOrdenado = montosVerificacion.every((monto, index) => 
                index === 0 || monto <= montosVerificacion[index - 1]
            );
            console.log('🔍 Verificación de orden descendente:', estaOrdenado ? '✅ CORRECTO' : '❌ ERROR');
            if (!estaOrdenado) {
                console.error('⚠️ ERROR: Los registros NO están ordenados correctamente por monto');
                console.log('💰 Montos encontrados:', montosVerificacion);
            }
            
            // Crear acciones para cada registro
            tarjetaData.acciones = topRecords.map((row, index) => {
                const accion = {
                    no: index + 1,
                    cuentaPublica: cuentaPublicaColumn ? row[cuentaPublicaColumn] || new Date().getFullYear() - 1 : new Date().getFullYear() - 1,
                    titulo: tituloAuditoriaColumn ? row[tituloAuditoriaColumn] || 'Auditoría' : 'Auditoría',
                    claveAccion: claveAccionColumn ? row[claveAccionColumn] || `AUTO-${index + 1}` : `AUTO-${index + 1}`,
                    tipoAccion: tipoAccionColumn ? row[tipoAccionColumn] || 'PO' : 'PO',
                    descripcion: descripcionColumn ? row[descripcionColumn] || '' : '',
                    monto: parseFloat(row[montoColumn].toString().replace(/[^\d.-]/g, '')) || 0,
                    camposAdicionales: {}
                };
                
                // Agregar campos seleccionados adicionales (excluyendo duplicados)
                if (selectedFields && selectedFields.length > 0) {
                    const columnasBase = ['Cuenta Pública', 'Título de la Auditoría', 'Clave de Acción', 'Tipo de Acción', 'Descripción'];
                    const camposFiltrados = selectedFields.filter(campo => {
                        // Excluir campos base
                        const esColumnaBase = columnasBase.some(colBase => 
                            campo.toLowerCase().includes(colBase.toLowerCase()) || 
                            colBase.toLowerCase().includes(campo.toLowerCase())
                        );
                        
                        // Excluir cualquier campo que contenga palabras relacionadas con montos para evitar duplicación
                        const palabrasMontos = ['monto', 'importe', 'valor', 'total', 'suma', 'cantidad'];
                        const contieneMontoGenerico = palabrasMontos.some(palabra => 
                            campo.toLowerCase().includes(palabra)
                        );
                        
                        return !esColumnaBase && !contieneMontoGenerico;
                    });
                    
                    camposFiltrados.forEach(campo => {
                        accion.camposAdicionales[campo] = row[campo] || 'N/A';
                    });
                }
                
                return accion;
            });
            
            // Verificación final: Asegurar que las acciones están correctamente ordenadas
            console.log('🎯 VERIFICACIÓN FINAL - Acciones creadas:', tarjetaData.acciones.length);
            console.log('💰 Orden final de montos:', tarjetaData.acciones.map(a => ({ no: a.no, monto: a.monto, clave: a.claveAccion })));
            
            const montosFinales = tarjetaData.acciones.map(a => a.monto);
            const ordenFinalCorrecto = montosFinales.every((monto, index) => 
                index === 0 || monto <= montosFinales[index - 1]
            );
            
            if (ordenFinalCorrecto) {
                console.log('✅ PERFECTO: Las acciones están correctamente ordenadas por monto descendente');
            } else {
                console.error('❌ ERROR CRÍTICO: Las acciones NO están ordenadas correctamente');
                console.log('💰 Montos desordenados:', montosFinales);
            }
            
        } else if (dataForEntity.length > 0) {
            // Crear al menos una acción de ejemplo
            const accionEjemplo = {
                no: 1,
                cuentaPublica: '2020',
                titulo: 'Participaciones Federales a Entidades Federativas',
                claveAccion: '2020-A-03000-19-0541-06-001',
                tipoAccion: 'PO',
                descripcion: 'Se presume un probable daño o perjuicio por no proporcionar la información contractual (número de contrato, proveedor, registro federal de contribuyentes, monto específico del clasificador por objeto del gasto, número de póliza), por lo que se desconoce su aplicación, en materia de adquisiciones.',
                monto: 1000000,
                camposAdicionales: {}
            };
            
            // Agregar datos de ejemplo para campos seleccionados (excluyendo duplicados)
            if (selectedFields && selectedFields.length > 0 && dataForEntity.length > 0) {
                const columnasBase = ['Cuenta Pública', 'Título de la Auditoría', 'Clave de Acción', 'Tipo de Acción', 'Descripción'];
                const camposFiltrados = selectedFields.filter(campo => {
                    // Excluir campos base
                    const esColumnaBase = columnasBase.some(colBase => 
                        campo.toLowerCase().includes(colBase.toLowerCase()) || 
                        colBase.toLowerCase().includes(campo.toLowerCase())
                    );
                    
                    // Excluir cualquier campo que contenga palabras relacionadas con montos para evitar duplicación
                    const palabrasMontos = ['monto', 'importe', 'valor', 'total', 'suma', 'cantidad'];
                    const contieneMontoGenerico = palabrasMontos.some(palabra => 
                        campo.toLowerCase().includes(palabra)
                    );
                    
                    return !esColumnaBase && !contieneMontoGenerico;
                });
                
                const firstRow = dataForEntity[0];
                camposFiltrados.forEach(campo => {
                    accionEjemplo.camposAdicionales[campo] = firstRow[campo] || 'Ejemplo';
                });
            }
            
            tarjetaData.acciones = [accionEjemplo];
        }
        
        // Actualizar los elementos del editor
        tarjetaTitulo.value = tarjetaData.titulo;
        tarjetaSubtitulo.value = tarjetaData.subtitulo;
        tarjetaPeriodo.value = tarjetaData.periodo;
        tarjetaTotalMonto.value = formatMonto(tarjetaData.totalMonto);
        
        // Limpiar y rellenar la tabla de resumen
        const tbody = tarjetaTablaEditor.querySelector('tbody');
        tbody.innerHTML = '';
        
        tarjetaData.filas.forEach(fila => {
            addRowToTarjetaTable(fila.estatus, fila.monto, fila.porcentaje);
        });
        
        // Limpiar y rellenar la tabla de acciones
        const tbodyAcciones = tarjetaAccionesEditor.querySelector('tbody');
        tbodyAcciones.innerHTML = '';
        
        tarjetaData.acciones.forEach(accion => {
            addRowToAccionesTable(accion);
        });
        
        // Procesar campos seleccionados para generar estadísticas
        procesarCamposSeleccionados(dataForEntity);
        
        return true;
    }
    
    // Función para procesar los campos seleccionados (simplificada)
    function procesarCamposSeleccionados(dataForEntity) {
        console.log('🔍 Campos seleccionados ya se procesarán automáticamente en las acciones');
        console.log('📊 Datos disponibles:', dataForEntity.length, 'registros');
        console.log('📋 Campos a incluir como columnas:', selectedFields);
        
        // Ya no necesitamos procesar estadísticas aquí
        // Los campos se agregarán directamente como columnas en la tabla B
    }
    
    // Formato de montos y porcentajes
    function formatMonto(monto) {
        return new Intl.NumberFormat('es-MX').format(monto);
    }
    
    function formatPorcentaje(porcentaje) {
        return `${Math.round(porcentaje)}%`;
    }
    
    // Funciones adicionales para encontrar columnas relevantes para la sección B
    function findCuentaPublicaColumn() {
        if (!excelData || excelData.length === 0) return null;
        
        const columns = Object.keys(excelData[0]);
        
        // Buscar columnas relacionadas con cuenta pública
        const keywords = ['cuenta pública', 'cuenta publica', 'ejercicio fiscal'];
        
        for (const keyword of keywords) {
            const match = columns.find(col => 
                col.toLowerCase().includes(keyword)
            );
            if (match) return match;
        }
        
        return null;
    }
    
    function findTituloAuditoriaColumn() {
        if (!excelData || excelData.length === 0) return null;
        
        const columns = Object.keys(excelData[0]);
        
        // Buscar columnas relacionadas con títulos de auditoría
        const keywords = ['título', 'titulo', 'auditoría', 'auditoria', 'nombre'];
        
        for (const keyword of keywords) {
            const match = columns.find(col => 
                col.toLowerCase().includes(keyword)
            );
            if (match) return match;
        }
        
        return null;
    }
    
    function findClaveAccionColumn() {
        if (!excelData || excelData.length === 0) return null;
        
        const columns = Object.keys(excelData[0]);
        
        // Buscar columnas relacionadas con claves de acción
        const keywords = ['clave', 'número de acción', 'numero de accion', 'folio'];
        
        for (const keyword of keywords) {
            const match = columns.find(col => 
                col.toLowerCase().includes(keyword)
            );
            if (match) return match;
        }
        
        return null;
    }
    
    function findTipoAccionColumn() {
        if (!excelData || excelData.length === 0) return null;
        
        const columns = Object.keys(excelData[0]);
        
        // Buscar columnas relacionadas con tipos de acción
        const keywords = ['tipo de acción', 'tipo de accion', 'tipo', 'clasificación', 'clasificacion'];
        
        for (const keyword of keywords) {
            const match = columns.find(col => 
                col.toLowerCase().includes(keyword)
            );
            if (match) return match;
        }
        
        return null;
    }
    
    function findDescripcionColumn() {
        if (!excelData || excelData.length === 0) return null;
        
        const columns = Object.keys(excelData[0]);
        
        // Buscar columnas relacionadas con descripciones
        const keywords = ['descripción', 'descripcion', 'observación', 'observacion', 'comentario', 'detalle'];
        
        for (const keyword of keywords) {
            const match = columns.find(col => 
                col.toLowerCase().includes(keyword)
            );
            if (match) return match;
        }
        
        return null;
    }
    
    // Buscar columna de período
    function findPeriodoColumn() {
        if (!excelData || excelData.length === 0) return null;
        
        const columns = Object.keys(excelData[0]);
        
        // Buscar columnas relacionadas con períodos
        const keywords = ['periodo', 'año', 'ejercicio', 'vigencia', 'fecha'];
        
        for (const keyword of keywords) {
            const match = columns.find(col => 
                col.toLowerCase().includes(keyword)
            );
            if (match) return match;
        }
        
        return null;
    }
    
    // Buscar columna de estatus
    function findEstatusColumn() {
        if (!excelData || excelData.length === 0) return null;
        
        const columns = Object.keys(excelData[0]);
        
        // Buscar columnas relacionadas con estatus
        const keywords = ['estatus', 'estado', 'situación', 'situacion', 'condición', 'condicion', 'categoría', 'categoria', 'tipo'];
        
        for (const keyword of keywords) {
            const match = columns.find(col => 
                col.toLowerCase().includes(keyword)
            );
            if (match) return match;
        }
        
        return null;
    }
    
    // Mostrar el editor de tarjeta
    function showTarjetaEditor() {
        tarjetaEditor.classList.remove('hidden');
    }
    
    // Agregar fila a la tabla del editor
    function addRowToTarjetaTable(estatus, monto, porcentaje) {
        const tbody = tarjetaTablaEditor.querySelector('tbody');
        const tr = document.createElement('tr');
        
        tr.innerHTML = `
            <td class="px-4 py-2">
                <input type="text" class="tarjeta-estatus w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="${estatus}">
            </td>
            <td class="px-4 py-2">
                <input type="text" class="tarjeta-monto w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="${formatMonto(monto)}">
            </td>
            <td class="px-4 py-2">
                <input type="text" class="tarjeta-porcentaje w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="${formatPorcentaje(porcentaje)}">
            </td>
            <td class="px-4 py-2">
                <button class="eliminar-fila px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors duration-200 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        `;
        
        // Agregar evento para eliminar fila
        tr.querySelector('.eliminar-fila').addEventListener('click', function() {
            tr.remove();
            updateTotalMonto();
        });
        
        // Agregar eventos para actualizar total y porcentajes
        tr.querySelector('.tarjeta-monto').addEventListener('change', updateTotalMonto);
        tr.querySelector('.tarjeta-porcentaje').addEventListener('change', updateTotalPorcentaje);
        
        tbody.appendChild(tr);
    }
    
    // Actualizar el monto total basado en las filas
    function updateTotalMonto() {
        const montoInputs = tarjetaTablaEditor.querySelectorAll('.tarjeta-monto');
        let total = 0;
        
        montoInputs.forEach(input => {
            const montoText = input.value;
            const monto = parseFloat(montoText.replace(/[^\d.-]/g, '')) || 0;
            total += monto;
        });
        
        tarjetaTotalMonto.value = formatMonto(total);
        
        // Actualizar porcentajes
        updatePorcentajes();
    }
    
    // Actualizar los porcentajes basados en los montos
    function updatePorcentajes() {
        const montoInputs = tarjetaTablaEditor.querySelectorAll('.tarjeta-monto');
        const porcentajeInputs = tarjetaTablaEditor.querySelectorAll('.tarjeta-porcentaje');
        
        const totalMonto = parseFloat(tarjetaTotalMonto.value.replace(/[^\d.-]/g, '')) || 0;
        
        if (totalMonto <= 0) return;
        
        montoInputs.forEach((input, index) => {
            if (index < porcentajeInputs.length) {
                const monto = parseFloat(input.value.replace(/[^\d.-]/g, '')) || 0;
                const porcentaje = totalMonto > 0 ? (monto / totalMonto * 100) : 0;
                porcentajeInputs[index].value = formatPorcentaje(porcentaje);
            }
        });
    }
    
    // Actualizar el total de porcentaje (siempre debe ser 100%)
    function updateTotalPorcentaje() {
        tarjetaTotalPorcentaje.value = '100%';
    }
});