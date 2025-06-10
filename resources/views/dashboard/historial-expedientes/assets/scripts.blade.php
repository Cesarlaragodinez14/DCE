<script>
    // Función para alternar la visibilidad de los filtros
    function toggleFiltersVisibility() {
        const filtersContainer = document.getElementById('filtersContainer');
        const toggleButton = document.getElementById('toggleFilters');
        
        if (filtersContainer.classList.contains('hidden')) {
            filtersContainer.classList.remove('hidden');
            toggleButton.querySelector('span').textContent = 'Ocultar';
            toggleButton.querySelector('svg').innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            `;
        } else {
            filtersContainer.classList.add('hidden');
            toggleButton.querySelector('span').textContent = 'Mostrar';
            toggleButton.querySelector('svg').innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            `;
        }
    }
    
    // Función para enviar el formulario de filtro automáticamente
    function submitFilterForm() {
        document.getElementById('filtrosForm').submit();
    }
    
    // Manejo de notificaciones
    function showSuccess(message) {
        const successToast = document.getElementById('successToast');
        const successMessage = document.getElementById('successMessage');
        
        successMessage.textContent = message;
        successToast.classList.remove('hidden');
        
        setTimeout(() => {
            hideSuccess();
        }, 3000);
    }
    
    function hideSuccess() {
        const successToast = document.getElementById('successToast');
        successToast.classList.add('hidden');
    }
    
    function showError(errors) {
        const errorAlert = document.getElementById('errorAlert');
        const errorList = document.getElementById('errorList');
        
        errorList.innerHTML = '';
        
        if (typeof errors === 'string') {
            const li = document.createElement('li');
            li.textContent = errors;
            errorList.appendChild(li);
        } else {
            for (const error of errors) {
                const li = document.createElement('li');
                li.textContent = error;
                errorList.appendChild(li);
            }
        }
        
        errorAlert.classList.remove('hidden');
    }
    
    function hideError() {
        const errorAlert = document.getElementById('errorAlert');
        errorAlert.classList.add('hidden');
    }
    
    // Inicialización cuando DOM está listo
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar el botón de toggle de filtros
        const toggleFiltersBtn = document.getElementById('toggleFilters');
        if (toggleFiltersBtn) {
            toggleFiltersBtn.addEventListener('click', toggleFiltersVisibility);
        }
        
        // Cerrar alertas de error
        const closeErrorBtn = document.getElementById('closeErrorBtn');
        if (closeErrorBtn) {
            closeErrorBtn.addEventListener('click', hideError);
        }
        
        // Cerrar mensaje de éxito
        const closeSuccessBtn = document.getElementById('closeSuccessBtn');
        if (closeSuccessBtn) {
            closeSuccessBtn.addEventListener('click', hideSuccess);
        }
        
        // Verificar si jQuery está disponible
        if (typeof window.jQuery !== 'undefined') {
            // jQuery está disponible, podemos usarlo
            const $ = window.jQuery;
            
            // Aplicar DataTables si está disponible
            if (typeof $.fn.DataTable !== 'undefined') {
                try {
                    $('#historial-table').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                        },
                        pageLength: 25,
                        responsive: true,
                        order: []
                    });
                } catch (error) {
                    console.error('Error al inicializar DataTables:', error);
                }
            }
    
            // Inicializar selectores personalizados si Select2 está disponible
            if (typeof $.fn.select2 !== 'undefined') {
                try {
                    $('.form-select').select2({
                        placeholder: 'Seleccionar...',
                        allowClear: true,
                        width: '100%'
                    });
                } catch (error) {
                    console.error('Error al inicializar Select2:', error);
                }
            }
        } else {
            console.log('jQuery no está disponible. Algunas funcionalidades pueden estar limitadas.');
        }
    });
</script>
