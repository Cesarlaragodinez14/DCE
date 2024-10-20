<!-- resources/views/apartados/partials/stepper.blade.php -->

<div class="mt-6">
    <div class="flex flex-col md:flex-row md:justify-between items-center">
        <!-- Título Principal -->
        <h3 class="text-2xl font-bold text-green-600 mb-4">
            El expediente ha sido aceptado por la DGSEG a cargo, en espera de la firma del responsable designado por la UAA.
        </h3>
    
        <!-- Paso 1: Descargar PDF -->
        <div class="flex flex-col items-center md:flex-1" data-step="1">
            <div class="relative">
                <div class="w-12 h-12 flex items-center justify-center rounded-full 
                    @if($auditoria->archivo_seguimiento || $auditoria->archivo_uua)
                        bg-green-500 text-white animate__animated animate__bounceIn
                    @else
                        bg-gray-300 text-gray-700
                    @endif
                    transition-colors duration-300
                ">
                    @if($auditoria->archivo_seguimiento || $auditoria->archivo_uua)
                        <ion-icon name="checkmark-circle" class="text-2xl"></ion-icon>
                    @else
                        <span class="font-bold text-lg">1</span>
                    @endif
                </div>
            </div>
            <a href="/auditorias/{{ $auditoria->id }}/pdf">
                <h4 class="mt-2 text-sm font-medium text-gray-700">Descargar PDF para su firma</h4>
            </a>
        </div>

        <!-- Línea de Conexión con Animación -->
        <div class="flex-1 flex justify-center md:justify-center my-4 md:my-0">
            <!-- Línea Horizontal para md y superiores -->
            <div class="hidden md:block w-full h-1 bg-gray-300 relative">
                @if($auditoria->archivo_seguimiento || $auditoria->archivo_uua)
                    <div class="absolute top-0 left-0 h-1 bg-green-500 animate__animated animate__fadeIn"></div>
                @endif
            </div>
            <!-- Línea Vertical para pantallas pequeñas -->
            <div class="block md:hidden w-1 h-full bg-gray-300 relative">
                @if($auditoria->archivo_seguimiento || $auditoria->archivo_uua)
                    <div class="absolute top-0 left-0 w-1 bg-green-500 animate__animated animate__fadeIn"></div>
                @endif
            </div>
        </div>

        <!-- Paso 2: Subir Firma de la UAA -->
        <div class="flex flex-col items-center md:flex-1" data-step="2">
            <div class="relative">
                <div class="w-12 h-12 flex items-center justify-center rounded-full 
                    @if($auditoria->archivo_uua)
                        bg-green-500 text-white animate__animated animate__bounceIn
                    @else
                        bg-gray-300 text-gray-700
                    @endif
                    transition-colors duration-300
                ">
                    @if($auditoria->archivo_uua)
                        <ion-icon name="checkmark-circle" class="text-2xl"></ion-icon>
                    @else
                        <span class="font-bold text-lg">2</span>
                    @endif
                </div>
            </div>
            <span class="mt-2 text-sm font-medium text-gray-700">Firma UAA</span>
        </div>
    </div>

    <!-- Descripciones de los Pasos con Animación -->
    <div class="mt-4 space-y-2">
        <div class="flex flex-col md:flex-row justify-between">
            <!-- Descripción Paso 1 -->
            <div class="text-center md:text-left md:w-1/2">
                <p class="text-sm font-semibold">Paso 1: Descargar PDF</p>
            </div>
            <!-- Descripción Paso 2 -->
            <div class="text-center md:text-right md:w-1/2 mt-2 md:mt-0">
                <p class="text-sm font-semibold">Paso 2: Subir Firma de la UAA</p>
            </div>
        </div>
    </div>
</div>
