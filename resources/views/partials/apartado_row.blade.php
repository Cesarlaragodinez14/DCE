@php
    // Si es el nivel principal (nivel 1), manejar numeración específica para los primeros 2 apartados
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
    $plantillaDatos = $apartado->plantillas->filter(function ($p) use ($formato) {
        return $p->plantilla === $formato;
    })->first();

    // Establecer los valores predefinidos o mostrar 'nvp' si no están disponibles
    $es_aplicable = $plantillaDatos->es_aplicable ?? 'En su caso';
    $es_obligatorio = $plantillaDatos->es_obligatorio ?? 'En su caso';
    $se_integra = $plantillaDatos->se_integra ?? 'En su caso';
@endphp

<tr class="{{ is_null($apartado->parent_id) ? 'bg-gray-100' : '' }}">

    <input type="hidden" name="apartados[{{ $apartado->id }}][id]" value="{{ $apartado->id }}">

    <td style="text-align: center">{{ $currentIteration }}</td>

    @if($hasSubapartados)
        <td colspan="5">{!! str_repeat('&emsp;', $apartado->depth) !!} {{ $apartado->nombre }}</td>

        <input type="hidden" name="apartados[{{ $apartado->id }}][es_aplicable]" value="">
        <input type="hidden" name="apartados[{{ $apartado->id }}][es_obligatorio]" value="">
        <input type="hidden" name="apartados[{{ $apartado->id }}][se_integra]" value="">
        <input type="hidden" name="apartados[{{ $apartado->id }}][observaciones]" value="">
        <input type="hidden" name="apartados[{{ $apartado->id }}][comentarios_uaa]" value="">
    @else
        <td>{!! str_repeat('&emsp;', $apartado->depth) !!} {{ $apartado->nombre }}</td>

        <!-- Checkbox para "es_aplicable" -->
        <td style="text-align: center">
            <input type="checkbox" name="apartados[{{ $apartado->id }}][es_aplicable]" value="1"
                {{ (optional($checklist->where('apartado_id', $apartado->id)->first())->es_aplicable ?? ($plantillaDatos ? $plantillaDatos->es_aplicable : false)) ? 'checked' : '' }}>
            @if ($es_aplicable === 'En su caso')
                <span>{{ $es_aplicable }}</span>
            @endif
        </td>

        <!-- Checkbox para "es_obligatorio" -->
        <td style="text-align: center">
            <input type="checkbox" name="apartados[{{ $apartado->id }}][es_obligatorio]" value="1"
                {{ (optional($checklist->where('apartado_id', $apartado->id)->first())->es_obligatorio ?? ($plantillaDatos ? $plantillaDatos->es_obligatorio : false)) ? 'checked' : '' }}>
            @if ($es_obligatorio === 'En su caso')
                <span>{{ $es_obligatorio }}</span>
            @endif
        </td>

        <!-- Checkbox para "se_integra" -->
        <td style="text-align: center">
            <input type="checkbox" name="apartados[{{ $apartado->id }}][se_integra]" value="1"
                {{ (optional($checklist->where('apartado_id', $apartado->id)->first())->se_integra ?? ($plantillaDatos ? $plantillaDatos->se_integra : false)) ? 'checked' : '' }}>
            @if ($se_integra === 'En su caso')
                <span>{{ $se_integra }}</span>
            @endif
        </td>

        <!-- Campo de texto para observaciones -->
        <td style="text-align: center">
            <input type="text" name="apartados[{{ $apartado->id }}][observaciones]"
                value="{{ optional($checklist->where('apartado_id', $apartado->id)->first())->observaciones }}"
                class="form-input rounded-md shadow-sm w-full">
        </td>

        <!-- Campo de texto para comentarios UAA -->
        <td style="text-align: center">
            <input type="text" name="apartados[{{ $apartado->id }}][comentarios_uaa]"
                value="{{ optional($checklist->where('apartado_id', $apartado->id)->first())->comentarios_uaa }}"
                class="form-input rounded-md shadow-sm w-full">
        </td>
    @endif
</tr>

@if ($apartado->subapartados)
    @foreach ($apartado->subapartados as $subapartado)
        @include('partials.apartado_row', ['apartado' => $subapartado, 'parentIteration' => $currentIteration, 'is_subrow' => true])
    @endforeach
@endif
