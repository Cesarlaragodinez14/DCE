<script>
    "use strict";
    
    let toastTimeout = null;

    // Inicializar la página cuando el DOM está listo
    document.addEventListener('DOMContentLoaded', function() {
        // Validación interactiva para el campo de confirmación en el modal de reset
        const confirmField = document.getElementById('confirmation_text');
        const confirmButton = document.getElementById('confirmResetBtn');
        const checkIcon = document.querySelector('.confirmation-check');
        
        if (confirmField) {
            confirmField.addEventListener('input', function() {
                const isValid = this.value === 'Deseo reiniciar esta clave de acción';
                
                // Habilitar/deshabilitar botón
                confirmButton.disabled = !isValid;
                
                // Mostrar/ocultar el ícono de verificación
                checkIcon.classList.toggle('opacity-0', !isValid);
                checkIcon.classList.toggle('opacity-100', isValid);
                
                // Estilizar el campo según validación
                if (this.value && !isValid) {
                    this.classList.add('border-red-300', 'bg-red-50');
                    this.classList.remove('border-green-300', 'bg-green-50');
                } else if (isValid) {
                    this.classList.add('border-green-300', 'bg-green-50');
                    this.classList.remove('border-red-300', 'bg-red-50');
                } else {
                    this.classList.remove('border-red-300', 'bg-red-50', 'border-green-300', 'bg-green-50');
                }
            });
        }
        
        // Configurar eventos para cerrar modales con Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeResetModal();
            }
            
            // Enviar el formulario al presionar Enter si la validación es correcta
            if (event.key === 'Enter' && document.activeElement === document.getElementById('confirmation_text')) {
                if (document.getElementById('confirmation_text').value === 'Deseo reiniciar esta clave de acción') {
                    submitReset();
                    event.preventDefault();
                }
            }
        });
        
        // Cerrar el modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('resetModal');
            if (event.target === modal) {
                closeResetModal();
            }
        }
    });

    // Función para abrir el modal de reset de auditorías
    function openResetModal(auditoriaId, claveAccion) {
        const modal = document.getElementById('resetModal');
        modal.classList.remove('hidden');
        modal.classList.add('visible');
        
        // Animar entrada
        const modalContent = modal.querySelector('.bg-white');
        modalContent.classList.add('animate-fade-in');
        
        // Configurar datos
        document.getElementById('modalClaveAccion').innerText = claveAccion;
        document.getElementById('auditoria_id').value = auditoriaId;
        document.getElementById('clave_accion').value = claveAccion;
        document.getElementById('resetForm').action = `/dashboard/all-auditorias/${auditoriaId}/reset`;
        
        // Restablecer campo de confirmación
        const confirmField = document.getElementById('confirmation_text');
        confirmField.value = '';
        confirmField.classList.remove('border-red-300', 'bg-red-50', 'border-green-300', 'bg-green-50');
        document.getElementById('confirmResetBtn').disabled = true;
        document.querySelector('.confirmation-check').classList.add('opacity-0');
        document.querySelector('.confirmation-check').classList.remove('opacity-100');
        
        // Enfocar en el campo de confirmación
        setTimeout(() => {
            confirmField.focus();
        }, 300);
        
        // Eliminar mensajes de error previos
        const errorMessage = document.querySelector('.confirmation-error');
        if (errorMessage) {
            errorMessage.remove();
        }
    }

    // Función para cerrar el modal con animación
    function closeResetModal() {
        const modal = document.getElementById('resetModal');
        
        if (!modal) return;
        
        // Animar salida
        const modalContent = modal.querySelector('.bg-white');
        modalContent.classList.remove('animate-fade-in');
        modalContent.classList.add('animate-fade-out');
        
        // Ocultar después de la animación
        setTimeout(() => {
            modal.classList.remove('visible');
            modal.classList.add('hidden');
            modalContent.classList.remove('animate-fade-out');
            const resetForm = document.getElementById('resetForm');
            if (resetForm) resetForm.reset();
        }, 300);
    }

    // Función para enviar el formulario con validación
    function submitReset() {
        const confirmationText = document.getElementById('confirmation_text').value;
        if (confirmationText === 'Deseo reiniciar esta clave de acción') {
            // Mostrar indicador de carga en el botón
            const confirmButton = document.getElementById('confirmResetBtn');
            confirmButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Procesando...
            `;
            confirmButton.disabled = true;
            
            // Enviar el formulario
            document.getElementById('resetForm').submit();
        } else {
            // Mostrar error con efectos visuales
            const inputElement = document.getElementById('confirmation_text');
            inputElement.classList.add('border-red-300', 'bg-red-50');
            inputElement.classList.remove('border-green-300', 'bg-green-50');
            
            // Agregar mensaje de error si no existe
            let errorContainer = document.querySelector('.confirmation-error');
            if (!errorContainer) {
                const errorMessage = document.createElement('p');
                errorMessage.className = 'text-xs text-red-600 mt-2 confirmation-error';
                errorMessage.innerHTML = `
                    <svg class="inline-block h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    La confirmación no coincide. Por favor, escribe exactamente la frase indicada.
                `;
                inputElement.parentNode.appendChild(errorMessage);
            }
            
            // Enfocar el campo de entrada y seleccionar todo el texto
            inputElement.focus();
            inputElement.select();
            
            // Sacudir el modal para indicar error
            const modal = document.querySelector('#resetModal > div > div.bg-white');
            modal.classList.add('animate-shake');
            setTimeout(() => {
                modal.classList.remove('animate-shake');
            }, 600);
        }
    }
    
    // Función para resetear filtros (para el mensaje de "no hay resultados")
    function resetFilters() {
        window.location.href = window.location.pathname;
    }
    
    // Funciones de utilidad para mostrar mensajes
    function showError(message) {
        // Limpiar cualquier toast anterior
        if (toastTimeout) {
            clearTimeout(toastTimeout);
            toastTimeout = null;
        }
        
        // Si existe un toast de error, agregamos el mensaje
        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert) {
            const errorList = document.getElementById('errorList');
            errorList.innerHTML = `<li>${message}</li>`;
            
            errorAlert.classList.remove('hidden');
            errorAlert.classList.add('visible');
            errorAlert.classList.add('animate-fade-in');
            
            // Auto-ocultar después de 5 segundos
            toastTimeout = setTimeout(() => {
                hideError();
            }, 5000);
        } else {
            // Fallback por si no hay un toast configurado
            console.error(message);
        }
    }
    
    function hideError() {
        const errorAlert = document.getElementById('errorAlert');
        if (!errorAlert) return;
        
        errorAlert.classList.remove('animate-fade-in');
        errorAlert.classList.add('animate-fade-out');
        
        // Después de la animación, ocultarlo completamente
        setTimeout(() => {
            errorAlert.classList.add('hidden');
            errorAlert.classList.remove('visible');
            errorAlert.classList.remove('animate-fade-out');
        }, 300);
    }
    
    function showSuccess(message) {
        // Limpiar cualquier toast anterior
        if (toastTimeout) {
            clearTimeout(toastTimeout);
            toastTimeout = null;
        }
        
        // Si existe un toast de éxito, agregamos el mensaje
        const successToast = document.getElementById('successToast');
        if (successToast) {
            const successMessage = document.getElementById('successMessage');
            successMessage.textContent = message;
            
            successToast.classList.remove('hidden');
            successToast.classList.add('visible');
            successToast.classList.add('animate-fade-in');
            
            // Auto-ocultar después de 3 segundos
            toastTimeout = setTimeout(() => {
                hideSuccess();
            }, 3000);
        } else {
            // Fallback a console
            console.log(message);
        }
    }
    
    function hideSuccess() {
        const successToast = document.getElementById('successToast');
        if (!successToast) return;
        
        successToast.classList.remove('animate-fade-in');
        successToast.classList.add('animate-fade-out');
        
        // Después de la animación, ocultarlo completamente
        setTimeout(() => {
            successToast.classList.add('hidden');
            successToast.classList.remove('visible');
            successToast.classList.remove('animate-fade-out');
        }, 300);
    }
    
    // Agregar animaciones globales si no existen
    if (!document.querySelector('style#custom-animations')) {
        const styleElement = document.createElement('style');
        styleElement.id = 'custom-animations';
        styleElement.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }
            .animate-shake {
                animation: shake 0.6s cubic-bezier(.36,.07,.19,.97) both;
            }
            
            @keyframes fadeInDown {
                from {
                    opacity: 0;
                    transform: translate3d(0, -20px, 0);
                }
                to {
                    opacity: 1;
                    transform: translate3d(0, 0, 0);
                }
            }
            .animate-fade-in-down {
                animation: fadeInDown 0.3s ease-out forwards;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            @keyframes fadeOut {
                from { opacity: 1; transform: translateY(0); }
                to { opacity: 0; transform: translateY(10px); }
            }
            
            .animate-fade-in {
                animation: fadeIn 0.3s ease-out forwards;
            }
            
            .animate-fade-out {
                animation: fadeOut 0.2s ease-in forwards;
            }
        `;
        document.head.appendChild(styleElement);
    }
</script>