<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inicialización de elementos
        initTableSorting();
        initErrorHandling();
        initSearchInput();

        // Detectar notificaciones de éxito o error al cargar
        checkForNotifications();
    });

    // Inicialización de la funcionalidad de ordenación de tablas
    function initTableSorting() {
        // Si se hace clic en un encabezado de columna con wire:click, Livewire se encargará
        // No es necesario agregar más funcionalidad aquí
    }

    // Inicialización del manejo de errores
    function initErrorHandling() {
        const closeErrorBtn = document.getElementById('closeErrorBtn');
        if (closeErrorBtn) {
            closeErrorBtn.addEventListener('click', function() {
                hideError();
            });
        }

        const closeSuccessBtn = document.getElementById('closeSuccessBtn');
        if (closeSuccessBtn) {
            closeSuccessBtn.addEventListener('click', function() {
                hideSuccess();
            });
        }
    }

    // Inicialización de la funcionalidad de búsqueda
    function initSearchInput() {
        const searchInput = document.querySelector('input[wire\\:model\\.debounce\\.300ms="search"]');
        if (searchInput) {
            searchInput.addEventListener('focus', function() {
                this.classList.add('ring-2', 'ring-primary-color/20');
            });
            
            searchInput.addEventListener('blur', function() {
                this.classList.remove('ring-2', 'ring-primary-color/20');
            });
        }
    }

    // Comprobar si hay notificaciones al cargar la página
    function checkForNotifications() {
        // Si hay mensajes de sesión, ya se muestran a través del HTML
    }

    // Ocultar alerta de error
    function hideError() {
        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert) {
            errorAlert.classList.add('hidden');
        }
    }

    // Ocultar alerta de éxito
    function hideSuccess() {
        const successToast = document.getElementById('successToast');
        if (successToast) {
            successToast.classList.add('hidden');
        }
    }

    // Mostrar alerta de error con mensajes específicos
    function showError(messages) {
        const errorAlert = document.getElementById('errorAlert');
        const errorList = document.getElementById('errorList');
        
        if (errorAlert && errorList) {
            errorList.innerHTML = '';
            
            if (typeof messages === 'string') {
                const li = document.createElement('li');
                li.textContent = messages;
                errorList.appendChild(li);
            } else if (Array.isArray(messages)) {
                messages.forEach(message => {
                    const li = document.createElement('li');
                    li.textContent = message;
                    errorList.appendChild(li);
                });
            } else if (typeof messages === 'object') {
                Object.values(messages).forEach(message => {
                    if (Array.isArray(message)) {
                        message.forEach(msg => {
                            const li = document.createElement('li');
                            li.textContent = msg;
                            errorList.appendChild(li);
                        });
                    } else {
                        const li = document.createElement('li');
                        li.textContent = message;
                        errorList.appendChild(li);
                    }
                });
            }
            
            errorAlert.classList.remove('hidden');
            
            // Auto ocultar después de 5 segundos
            setTimeout(hideError, 5000);
        }
    }

    // Mostrar alerta de éxito con mensaje específico
    function showSuccess(message) {
        const successToast = document.getElementById('successToast');
        const successMessage = document.getElementById('successMessage');
        
        if (successToast && successMessage) {
            successMessage.textContent = message;
            successToast.classList.remove('hidden');
            
            // Auto ocultar después de 3 segundos
            setTimeout(hideSuccess, 3000);
        }
    }

    // Escuchar eventos Livewire
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('success', (message) => {
            showSuccess(message);
        });
        
        Livewire.on('error', (message) => {
            showError(message);
        });
    });
</script> 