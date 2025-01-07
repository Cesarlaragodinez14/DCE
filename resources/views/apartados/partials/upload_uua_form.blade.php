<!-- resources/views/apartados/partials/upload_uua_form.blade.php -->

<div class="container">
    <h4>Firma de Confirmación de Conformidad de la UAA</h4>
    <form id="confirmUuaForm" action="{{ route('pdf.generateSignedChecklistPdf', ['auditoria_id' => $auditoria->id]) }}" method="POST">
        @csrf
        <!-- Campo Oculto para auditoria_id -->
        <input type="hidden" name="auditoria_id" value="{{ $auditoria->id }}">
    
        <p>Al confirmar esta acción, usted declara bajo protesta de decir verdad que está de acuerdo con la información proporcionada en el documento anterior generado por el área de seguimiento. ¿Desea continuar?</p>
        <!-- Botón de confirmación -->
        <div class="submit-button-wrapper">
            <button type="submit" id="submitUuaButton" class="submit-button">
                <span id="submitUuaText">Sí, Firmar de Conformidad</span>
            </button>
        </div>
    </form>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const uuaForm = document.getElementById('confirmUuaForm');
            const submitUuaButton = document.getElementById('submitUuaButton');
    
            submitUuaButton.addEventListener('click', function (e) {
                e.preventDefault();
    
                const confirmation = confirm('Al confirmar esta acción, usted declara bajo protesta de decir verdad que está de acuerdo con la información proporcionada en el documento anterior generado por el área de seguimiento. ¿Desea continuar?');
    
                if (confirmation) {
                    uuaForm.submit();
                } else {
                    // El usuario canceló la acción
                    return;
                }
            });
        });
    </script>
    @endpush
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

