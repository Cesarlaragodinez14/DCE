<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold" style="text-align: center">{{ __('Información de la Auditoría') }}</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <h2 class="text-center font-bold underline">Área que entrega</h2>
                <p><strong>Auditoría Especial:</strong> {{ $auditoria->catSiglasAuditoriaEspecial->descripcion ?? '' }}</p>
                <p><strong>Dirección General de la UAA:</strong> {{ $auditoria->catUaa->nombre ?? '' }}</p>
                <p><strong>Título de la Auditoría:</strong> {{ $auditoria->titulo }}</p>
                <p><strong>Número de Auditoría:</strong> {{ $auditoria->catAuditoriaEspecial->valor }}</p>
                <p><strong>Clave de la Acción:</strong> {{ $auditoria->catClaveAccion->valor ?? '' }}</p>
                <p><strong>Nombre del Ente de la Acción o Recomendación:</strong> {{ $auditoria->catEnteDeLaAccion->valor ?? '' }}</p>
            </div>
            <div>
                <h2 class="text-center font-bold underline">Área que recibe y revisa</h2>
                <p><strong>Dirección General:</strong> {{ $auditoria->catDgsegEf->valor ?? '' }}</p>
                <p><strong>Dirección de Área:</strong> {{ $auditoria->direccion_de_area ?? '' }}</p>
                <p><strong>Subdirección:</strong> {{ $auditoria->sub_direccion_de_area ?? '' }}</p>
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('apartados.checklist.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="auditoria_id" value="{{ $auditoria->id }}">
                    <!-- Select con formato mejorado -->
                    <div class="mb-4">
                        <label for="estatus_checklist" class="block text-sm font-medium text-gray-700">Estatus del Checklist</label>
                        <select name="estatus_checklist" id="estatus_checklist" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Selecciona</option>
                            <option value="1" {{ $auditoria->estatus_checklist == '1' ? 'selected' : '' }}>ACEPTA</option>
                            <option value="0" {{ $auditoria->estatus_checklist == '0' ? 'selected' : '' }}>DEVUELVE</option>
                        </select>
                    </div>
                    <!-- Tabla de Checklist -->
                    <table class="min-w-full table-auto mb-6">
                        <thead class="bg-gray-200">
                            <tr>
                                <th>N°</th>
                                <th>Apartado / Subapartado</th>
                                <th>¿Aplica?</th>
                                <th>¿Obligatorio?</th>
                                <th>¿Se Integra?</th>
                                <th>Observaciones</th>
                                <th>Comentarios UAA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Función recursiva para mostrar apartados y subapartados -->
                            @foreach ($apartados as $apartado)
                                @php
                                    // Obtener el formato de la acción de auditoría basado en el formato de clave
                                    $formato = explode('-', $auditoria->catClaveAccion->valor)[5]; // Extract format from the clave de acción

                                    // Obtener los valores predefinidos desde la tabla apartado_plantillas para el formato específico
                                    $plantilla = $apartado->plantillas->filter(function ($p) use ($formato) {
                                        return $p->plantilla === $formato;
                                    })->first();

                                    // Verificar la existencia de la plantilla y aplicar valores predefinidos si está disponible
                                    $se_aplica = $plantilla->es_aplicable ?? false;
                                    $es_obligatorio = $plantilla->es_obligatorio ?? false;
                                    $se_integra = $plantilla->se_integra ?? false;

                                @endphp

                                @include('partials.apartado_row', [
                                    'apartado' => $apartado, 
                                    'parentIteration' => $loop->iteration, 
                                    'is_subrow' => false, 
                                    'plantilla' => $plantilla // Pasar plantilla para los valores por defecto
                                ])
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-6">
                        <x-button type="submit" class="bg-blue-500 hover:bg-blue-700">
                            Guardar Checklist
                        </x-button>
                        <a href="{{ route('auditorias.pdf', $auditoria->id) }}" class="bg-blue-500 hover:bg-blue-700">
                            Descargar PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
