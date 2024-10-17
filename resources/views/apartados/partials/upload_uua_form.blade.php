<!-- resources/views/apartados/partials/upload_uua_form.blade.php -->

<div class="container">
    <h4>Subir Firma de la UAA</h4>
    <form id="uploadUuaForm" enctype="multipart/form-data">
        @csrf
        <!-- Campo Oculto para auditoria_id -->
        <input type="hidden" name="auditoria_id" value="{{ $auditoria->id }}">

        <!-- Campo de archivo con área de arrastrar y soltar -->
        <div class="file-upload-wrapper">
            <label for="uua_archivo" class="file-upload-label">
                <div id="drop-zone" class="drop-zone">
                    <ion-icon name="cloud-upload-outline" class="upload-icon"></ion-icon>
                    <p class="upload-text">Haz clic para seleccionar un archivo o arrástralo y suéltalo aquí</p>
                </div>
            </label>
            <input id="uua_archivo" name="uua_archivo" type="file" accept=".pdf" class="file-input">
            <div id="file-preview" class="file-preview hidden">
                <ion-icon name="document-outline" class="file-icon"></ion-icon>
                <span id="file-name"></span>
                <button type="button" id="remove-file" class="remove-file-button" aria-label="Eliminar archivo seleccionado">
                    <ion-icon name="close-circle-outline"></ion-icon>
                </button>
            </div>
        </div>
        <span id="uuaError" class="error-message">Por favor, selecciona un archivo válido.</span>

        <!-- Botón de envío con spinner de carga -->
        <div class="submit-button-wrapper">
            <button type="submit" id="submitUuaButton" class="submit-button">
                <span id="submitUuaText">Subir Firma de la UAA</span>
                <ion-icon id="submitUuaSpinner" name="sync-outline" class="spinner hidden"></ion-icon>
            </button>
        </div>
    </form>

    <!-- Mensajes de Éxito o Error -->
    <div id="uuaMessage" class="message hidden">
        <span id="uuaMessageText"></span>
    </div>
</div>

@push('styles')
<style>
    /* Container General */
    .container {
        max-width: 500px;
        margin: 2rem auto;
        padding: 1.5rem;
        background-color: #ffffff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 0.5rem;
    }

    h4 {
        font-size: 1.5rem;
        color: #4b5563; /* Gris oscuro */
        margin-bottom: 1rem;
        text-align: center;
    }

    /* File Upload Wrapper */
    .file-upload-wrapper {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Hidden File Input */
    .file-input {
        display: none;
    }

    /* Drop Zone */
    .drop-zone {
        width: 100%;
        padding: 2rem;
        border: 2px dashed #d1d5db; /* Gris claro */
        border-radius: 0.375rem;
        background-color: #ffffff;
        cursor: pointer;
        transition: background-color 0.3s, border-color 0.3s;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .drop-zone:hover {
        border-color: #6366f1; /* Indigo */
        background-color: #f9fafb; /* Gris muy claro */
    }

    .upload-icon {
        font-size: 3rem;
        color: #9ca3af; /* Gris */
    }

    .upload-text {
        margin-top: 1rem;
        font-size: 1rem;
        color: #6b7280; /* Gris */
        text-align: center;
    }

    /* File Preview */
    .file-preview {
        margin-top: 1rem;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        width: 100%;
        background-color: #f3f4f6; /* Gris claro */
    }

    .file-preview ion-icon {
        font-size: 1.5rem;
        color: #6b7280; /* Gris */
        margin-right: 0.5rem;
    }

    .file-icon {
        font-size: 1.5rem;
        color: #6b7280; /* Gris */
        margin-right: 0.5rem;
    }

    #file-name {
        flex-grow: 1;
        font-size: 0.875rem;
        color: #374151; /* Gris medio */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .remove-file-button {
        background: none;
        border: none;
        cursor: pointer;
        color: #ef4444; /* Rojo */
        font-size: 1.25rem;
        padding: 0;
    }

    .remove-file-button:hover {
        color: #dc2626; /* Rojo oscuro */
    }

    /* Error Message */
    .error-message {
        font-size: 0.875rem;
        color: #dc2626; /* Rojo */
        display: none;
        margin-top: 0.5rem;
        text-align: center;
    }

    /* Submit Button */
    .submit-button-wrapper {
        margin-top: 1.5rem;
    }

    .submit-button {
        width: 100%;
        padding: 0.75rem 1rem;
        background-color: #6366f1; /* Indigo */
        color: #ffffff;
        font-size: 1rem;
        font-weight: 600;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.3s, opacity 0.3s;
    }

    .submit-button:hover {
        background-color: #4f46e5; /* Indigo oscuro */
    }

    .submit-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .spinner {
        margin-left: 0.75rem;
        font-size: 1.25rem;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Message */
    .message {
        margin-top: 1.5rem;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        text-align: center;
        font-size: 0.875rem;
    }

    .message.success {
        background-color: #d1fae5; /* Verde claro */
        color: #065f46; /* Verde oscuro */
    }

    .message.error {
        background-color: #fee2e2; /* Rojo claro */
        color: #991b1b; /* Rojo oscuro */
    }

    /* Responsive Design */
    @media (max-width: 600px) {
        .container {
            margin: 1rem;
            padding: 1rem;
        }

        h4 {
            font-size: 1.25rem;
        }

        .upload-icon {
            font-size: 2.5rem;
        }

        .upload-text {
            font-size: 0.875rem;
        }

        .file-preview {
            flex-direction: column;
            align-items: flex-start;
        }

        #file-name {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .remove-file-button {
            align-self: flex-end;
        }

        .submit-button {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }

        .spinner {
            margin-left: 0.5rem;
            font-size: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elementos del formulario
        const uuaForm = document.getElementById('uploadUuaForm');
        const uuaInput = document.getElementById('uua_archivo');
        const uuaError = document.getElementById('uuaError');
        const uuaMessage = document.getElementById('uuaMessage');
        const uuaMessageText = document.getElementById('uuaMessageText');
        const submitUuaButton = document.getElementById('submitUuaButton');
        const submitUuaText = document.getElementById('submitUuaText');
        const submitUuaSpinner = document.getElementById('submitUuaSpinner');
        const dropZone = document.getElementById('drop-zone');
        const filePreview = document.getElementById('file-preview');
        const fileName = document.getElementById('file-name');
        const removeFileButton = document.getElementById('remove-file');

        let isUploading = false; // Estado de carga

        // Función para actualizar el stepper
        function updateStepper(paso) {
            const stepElement = document.querySelector(`[data-step="${paso}"]`);
            if (stepElement) {
                const circle = stepElement.querySelector('div.relative div');
                const description = stepElement.querySelector('span.text-sm.font-medium');

                // Cambiar clases para indicar completado
                circle.classList.remove('bg-gray-300', 'text-gray-700');
                circle.classList.add('bg-green-500', 'text-white', 'animate__animated', 'animate__bounceIn');

                // Reemplazar el contenido con Ionicon de checkmark-circle
                circle.innerHTML = `<ion-icon name="checkmark-circle" class="text-2xl"></ion-icon>`;

                // Actualizar descripción si es necesario
                description.classList.add('text-green-500');
            }
        }

        // Función para mostrar mensajes
        function showMessage(message, type = 'success') {
            uuaMessageText.textContent = message;
            uuaMessage.classList.remove('hidden', 'success', 'error');
            if (type === 'success') {
                uuaMessage.classList.add('success');
            } else {
                uuaMessage.classList.add('error');
            }
        }

        // Validación en el cliente
        function validateFile(file) {
            const allowedTypes = ['application/pdf'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (!file) {
                return { valid: false, message: 'Por favor, selecciona un archivo.' };
            }

            if (!allowedTypes.includes(file.type)) {
                return { valid: false, message: 'Tipo de archivo no permitido. Solo se permiten PDF.' };
            }

            if (file.size > maxSize) {
                return { valid: false, message: 'El archivo excede el tamaño máximo de 2MB.' };
            }

            return { valid: true };
        }

        // Mostrar previsualización del archivo
        function showFilePreview(file) {
            fileName.textContent = file.name;
            filePreview.classList.remove('hidden');
        }

        // Ocultar previsualización del archivo
        function hideFilePreview() {
            fileName.textContent = '';
            filePreview.classList.add('hidden');
        }

        // Manejo de arrastrar y soltar
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('hover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('hover');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                uuaInput.files = files;
                handleFile(files[0]);
            }
        });

        // Manejo de selección de archivo
        uuaInput.addEventListener('change', function () {
            if (uuaInput.files.length > 0) {
                handleFile(uuaInput.files[0]);
            }
        });

        // Función para manejar el archivo seleccionado
        function handleFile(file) {
            const validation = validateFile(file);
            if (!validation.valid) {
                uuaError.textContent = validation.message;
                uuaError.style.display = 'block';
                hideFilePreview();
                return;
            }

            uuaError.style.display = 'none';
            showFilePreview(file);
        }

        // Manejo de eliminación de archivo
        if (removeFileButton) {
            removeFileButton.addEventListener('click', function () {
                uuaInput.value = '';
                hideFilePreview();
                uuaError.style.display = 'none';
            });
        }

        // Manejador de envío del formulario de UUA
        uuaForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (isUploading) return; // Evitar múltiples envíos

            // Limpiar mensajes anteriores
            uuaError.style.display = 'none';
            uuaMessage.classList.remove('success', 'error');
            uuaMessage.classList.add('hidden');
            uuaMessageText.textContent = '';

            const archivo = uuaInput.files[0];
            const validation = validateFile(archivo);

            if (!validation.valid) {
                uuaError.textContent = validation.message;
                uuaError.style.display = 'block';
                return;
            }

            // Preparar los datos para enviar
            const formData = new FormData(uuaForm);

            try {
                isUploading = true;
                // Mostrar el spinner y deshabilitar el botón
                submitUuaSpinner.classList.remove('hidden');
                submitUuaText.textContent = 'Subiendo...';
                submitUuaButton.disabled = true;

                const response = await fetch('{{ route('apartados.storeUua') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Mostrar mensaje de éxito
                    showMessage(data.message || 'Firma de la UAA cargada exitosamente.', 'success');

                    // Actualizar el stepper
                    updateStepper(2);

                    // Deshabilitar el formulario de UUA
                    submitUuaButton.disabled = true;
                } else {
                    // Mostrar mensaje de error
                    showMessage(data.message || 'Hubo un error al cargar la Firma de la UAA.', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                // Mostrar mensaje de error genérico
                showMessage('Hubo un error al cargar la Firma de la UAA. Por favor, inténtalo de nuevo más tarde.', 'error');
            } finally {
                // Ocultar el spinner y habilitar el botón si no se ha deshabilitado
                submitUuaSpinner.classList.add('hidden');
                if (!submitUuaButton.disabled) {
                    submitUuaText.textContent = 'Subir Firma de la UAA';
                    submitUuaButton.disabled = false;
                }
                isUploading = false;
            }
        });

        // Inicializar el estado de los formularios basados en las auditorías existentes
        @if($auditoria->archivo_uua)
            // Paso 2 completado
            updateStepper(2);

            // Deshabilitar el formulario de UUA
            submitUuaButton.disabled = true;
        @endif
    });
</script>
@endpush
