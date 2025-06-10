/**
 * Generador de Tarjetas
 * ---------------------
 * Módulo para procesar archivos Excel en el navegador sin persistencia en BD,
 * utilizando SheetJS, DataTables con SearchBuilder y Buttons.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Columnas requeridas en el archivo Excel
    const REQUIRED_COLS = [
        "Cuenta Pública", 
        "Título de la Auditoría", 
        "Clave de Acción", 
        "Tipo de Acción",
        "Siglas Tipo de Acción",
        "Clave de Auditoría Especial",
        "Siglas de Auditoría Especial",
        "Descripción de Auditoría Especial",
        "UAA",
        "Ente de la Acción", 
        "Ente Fiscalizado",
        "Dirección General o Secretaría Técnica o EF",
        "Grupo Funcional",
        "Sector",
        "Entidad Federativa",
        "Municipio o Alcaldía",
        "Monto por Aclarar",
        "Monto por Recuperar",
        "Recuperaciones Operadas",
        "Seguimiento",
        "Pronunciamiento",
        "Observación",
        "Tema y Subtema",
        "Acción Superada",
        "Acción Concluida",
        "Solicitud de Aclaración",
        "Responsabilidad Administrativa Sancionatoria",
        "Carpetas de Investigación"
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

    let table; // Variable para almacenar la instancia de DataTable

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
                const rows = XLSX.utils.sheet_to_json(worksheet, { defval: '' });
                
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
        
        // Obtener las columnas para DataTables
        const columns = Object.keys(data[0]).map(key => ({
            title: key,
            data: key
        }));

        // Inicializar o actualizar DataTable
        if (table) {
            // Si la tabla ya existe, limpiar y agregar nuevos datos
            table.clear().rows.add(data).draw();
        } else {
            // Inicializar la tabla por primera vez
            table = $('#tabla').DataTable({
                data: data,
                columns: columns,
                dom: 'Bfrtip',
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                buttons: [
                    'searchBuilder'
                ],
                deferRender: true,
                scrollY: '70vh',
                scrollCollapse: true,
                paging: true,
                responsive: true,
                initComplete: function() {
                    // Habilitar el botón de exportar
                    exportBtn.removeAttribute('disabled');
                    
                    // Configurar atajo de teclado Ctrl+F para abrir SearchBuilder
                    $(document).on('keydown', function(e) {
                        if (e.ctrlKey && e.key === 'f') {
                            e.preventDefault();
                            table.searchBuilder.container().find('button').trigger('click');
                        }
                    });
                }
            });
        }
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
}); 