@php
    // Numeración del apartado
    if (isset($is_subrow) && $is_subrow === false) {
        if ($loop->iteration <= 2) {
            $currentIteration = 0;
        } else {
            $currentIteration = $loop->iteration - 2;
        }
    } else {
        $currentIteration = "$parentIteration.$loop->iteration";
    }

    // Verificar si el apartado tiene subapartados
    $hasSubapartados = $apartado->subapartados->isNotEmpty();

    // Obtener el formato de la acción de auditoría basado en la clave
    $formato = explode('-', $auditoria->catClaveAccion->valor)[5];

    // Obtener los valores predefinidos desde la tabla apartado_plantillas
    $plantillaDatos = $apartado->plantillas->firstWhere('plantilla', $formato);

    // Establecer los valores predefinidos o null si no están disponibles
    $es_aplicable = $plantillaDatos->es_aplicable ?? null;
    $es_obligatorio = $plantillaDatos->es_obligatorio ?? null;
    $se_integra = $plantillaDatos->se_integra ?? 'En su caso';

    // Determinar si se debe mostrar la fila
    $mostrarFila = $es_aplicable !== 0 && $es_aplicable !== '0' && $es_aplicable !== false;
@endphp

@if($mostrarFila)
<tr 
    class="{{ is_null($apartado->parent_id) ? 'bg-gray-50' : 'bg-white parent-'.$apartado->parent_id }} 
           {{ isset($is_subrow) && $is_subrow ? 'hidden' : '' }} 
           {{ (($es_obligatorio === 1 || $es_obligatorio === '1') && !$hasSubapartados) ? 'mandatory' : '' }}" 
        @if(($es_obligatorio === 1 || $es_obligatorio === '1') && !$hasSubapartados)
            data-nombre-apartado="{{ $apartado->nombre }}"
        @endif
    >
    <!-- Inputs ocultos -->
    <input type="hidden" name="apartados[{{ $apartado->id }}][id]" value="{{ $apartado->id }}">
    <input type="hidden" name="apartados[{{ $apartado->id }}][es_aplicable]" value="{{ $es_aplicable }}">
    <input type="hidden" name="apartados[{{ $apartado->id }}][es_obligatorio]" value="{{ $es_obligatorio }}">

    <!-- Numeración -->
    <td class="px-4 py-3 text-center text-gray-700 font-medium">
        {{ $currentIteration }}
    </td>

    @if($hasSubapartados)
        <!-- Nombre del apartado con toggle -->
        <td colspan="5" class="px-4 py-3 text-gray-800 font-semibold">
            <button type="button" class="toggle-subapartado focus:outline-none" data-parent-id="{{ $apartado->id }}">
                <svg class="w-4 h-4 inline-block mr-2 transition-transform transform toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
            {{ $apartado->nombre }}
        </td>
        <!-- Inputs ocultos para los demás campos -->
        <input type="hidden" name="apartados[{{ $apartado->id }}][se_integra]" value="">
        <input type="hidden" name="apartados[{{ $apartado->id }}][observaciones]" value="">
        <input type="hidden" name="apartados[{{ $apartado->id }}][comentarios_uaa]" value="">
    @else
        <!-- Nombre del apartado -->
        <td class="px-4 py-3 text-gray-700" style="padding-left: {{ $apartado->depth * 20 }}px;">
            {{ $apartado->nombre }}
        </td>

        <!-- ¿Obligatorio? -->
        <td class="px-4 py-3 text-center text-gray-700">
            @if($es_obligatorio === 1 || $es_obligatorio === '1')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Sí
                </span>
            @elseif($es_obligatorio === 0 || $es_obligatorio === '0')
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                    No
                </span>
            @else
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    En su caso
                </span>
            @endif
        </td>
        
        <!-- ¿Se Integra? -->
        <td class="px-4 py-3 text-center">
            @role('admin|Jefe de Departamento')
                <input 
                    type="checkbox" 
                    name="apartados[{{ $apartado->id }}][se_integra]" 
                    value="1"
                    {{ (optional($checklist->where('apartado_id', $apartado->id)->first())->se_integra ?? false) ? 'checked' : '' }}
                    class="form-checkbox h-5 w-5 text-indigo-600 transition duration-150 ease-in-out">
            @else
                @php
                    $seIntegra = optional($checklist->where('apartado_id', $apartado->id)->first())->se_integra;
                    if ($seIntegra === 1) {
                        $displayText = 'Si';
                    } elseif ($seIntegra === 0) {
                        $displayText = 'No';
                    } else {
                        $displayText = 'Sin revisión de seguimiento';
                    }
                @endphp
                <span class="text-gray-700">{{ $displayText }}</span>
                <input 
                type="hidden" 
                name="apartados[{{ $apartado->id }}][se_integra]" 
                value="1"
                {{ (optional($checklist->where('apartado_id', $apartado->id)->first())->se_integra ?? false) ? 'checked' : '' }}
                class="form-checkbox h-5 w-5 text-indigo-600 transition duration-150 ease-in-out">
            @endrole
        </td>

        <!-- Observaciones -->
        <td class="px-4 py-3">
            @if ( auth()->user()->roles->pluck('name')[0] === 'Jefe de Departamento' || auth()->user()->roles->pluck('name')[0] === 'admin' )
                <textarea name="apartados[{{ $apartado->id }}][observaciones]"
                    class="form-textarea mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    rows="2">{{ optional($checklist->where('apartado_id', $apartado->id)->first())->observaciones }}</textarea>
            @elseif ( auth()->user()->roles->pluck('name')[0] !== 'Jefe de Departamento' && auth()->user()->roles->pluck('name')[0] !== 'admin' )
                {{ optional($checklist->where('apartado_id', $apartado->id)->first())->observaciones ?? 'Sin Observaciones de seguimiento' }}
                <input type="hidden" name="apartados[{{ $apartado->id }}][observaciones]"
                    class="form-textarea mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    rows="2" value="{{ optional($checklist->where('apartado_id', $apartado->id)->first())->observaciones }}">
            @endif
        </td>

        <!-- Comentarios UAA -->
        <td class="px-4 py-3">
            <textarea name="apartados[{{ $apartado->id }}][comentarios_uaa]"
                class="form-textarea mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                rows="2">{{ optional($checklist->where('apartado_id', $apartado->id)->first())->comentarios_uaa }}</textarea>
        </td>
    @endif
</tr>
@endif

<!-- Subapartados -->
@if ($apartado->subapartados)
    @foreach ($apartado->subapartados as $subapartado)
        @include('partials.apartado_row', [
            'apartado' => $subapartado,
            'parentIteration' => $currentIteration,
            'is_subrow' => true,
            'auditoria' => $auditoria,
            'checklist' => $checklist,
        ])
    @endforeach
@endif
