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

    let table; // Variable para almacenar la instancia de DataTable
    let isSearchBuilderVisible = false;

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

    function showError(message) {
        errorContainer.classList.remove('hidden');
        errorMessage.textContent = message;
        progressContainer.classList.add('hidden');
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
});