@php
    // Si es el nivel principal (nivel 1), manejar numeración específica para los primeros 2 apartados
    if (isset($is_subrow) && $is_subrow === false) {
        // Numerar apartados principales (si están en la posición 1 o 2, serán 0, sino el número normal)
        if ($loop->iteration <= 2) {
            $currentIteration = 0;
        } else {
            $currentIteration = $loop->iteration - 2;
        }
    } else {
        // Para los subapartados, concatenar la numeración del apartado padre
        $currentIteration = "$parentIteration.$loop->iteration";
    }

    // Verificar si el apartado tiene subapartados
    $hasSubapartados = $apartado->subapartados->isNotEmpty();
@endphp

<tr class="{{ is_null($apartado->parent_id) ? 'bg-gray-100' : '' }}">

    <!-- Campo oculto para enviar el id del apartado (importante que siempre se envíe el ID del apartado) -->
    <input type="hidden" name="apartados[{{ $apartado->id }}][id]" value="{{ $apartado->id }}">

    <!-- Numeración y nombre -->
    <td style="text-align: center">{{ $currentIteration }}</td>

    <!-- Si el apartado tiene subapartados -->
    @if($hasSubapartados)
        <td colspan="5">{!! str_repeat('&emsp;', $apartado->depth) !!} {{ $apartado->nombre }}</td>

        <!-- Asegurarse de enviar campos vacíos si el apartado tiene subapartados, para evitar problemas en el backend -->
        <input type="hidden" name="apartados[{{ $apartado->id }}][se_aplica]" value="">
        <input type="hidden" name="apartados[{{ $apartado->id }}][es_obligatorio]" value="">
        <input type="hidden" name="apartados[{{ $apartado->id }}][se_integra]" value="">
        <input type="hidden" name="apartados[{{ $apartado->id }}][observaciones]" value="">
        <input type="hidden" name="apartados[{{ $apartado->id }}][comentarios_uaa]" value="">
    @else
        <!-- Si NO tiene subapartados, mostramos los inputs -->
        <td>{!! str_repeat('&emsp;', $apartado->depth) !!} {{ $apartado->nombre }}</td>
        <td style="text-align: center">
            <input type="checkbox" name="apartados[{{ $apartado->id }}][se_aplica]" value="1" 
                {{ optional($checklist->where('apartado_id', $apartado->id)->first())->se_aplica ? 'checked' : '' }}>
        </td>

        <td style="text-align: center">
            <input type="checkbox" name="apartados[{{ $apartado->id }}][es_obligatorio]" value="1" 
                {{ optional($checklist->where('apartado_id', $apartado->id)->first())->es_obligatorio ? 'checked' : '' }}>
        </td>

        <td style="text-align: center">
            <input type="checkbox" name="apartados[{{ $apartado->id }}][se_integra]" value="1" 
                {{ optional($checklist->where('apartado_id', $apartado->id)->first())->se_integra ? 'checked' : '' }}>
        </td>

        <td style="text-align: center">
            <input type="text" name="apartados[{{ $apartado->id }}][observaciones]" value="{{ optional($checklist->where('apartado_id', $apartado->id)->first())->observaciones }}" 
                class="form-input rounded-md shadow-sm w-full">
        </td>

        <td style="text-align: center">
            <input type="text" name="apartados[{{ $apartado->id }}][comentarios_uaa]" value="{{ optional($checklist->where('apartado_id', $apartado->id)->first())->comentarios_uaa }}" 
                class="form-input rounded-md shadow-sm w-full">
        </td>
    @endif
</tr>

<!-- Recursividad para subapartados -->
@if ($apartado->subapartados)
    @foreach ($apartado->subapartados as $subapartado)
        @include('partials.apartado_row', ['apartado' => $subapartado, 'parentIteration' => $currentIteration, 'is_subrow' => true])
    @endforeach
@endif
