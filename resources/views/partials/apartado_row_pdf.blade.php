@php
    // Obtener el formato de la acción de auditoría basado en la clave
    $formato = explode('-', $auditoria->catClaveAccion->valor)[5];

    // NUEVA LÓGICA: Usar plantilla superveniente si aplica (usar variable del controlador)
    $plantillaFormato = ($formato === '06' && $esSuperveniente) ? '06-superveniente' : $formato;

    // Obtener los valores predefinidos desde la tabla apartado_plantillas
    $plantillaDatos = $apartado->plantillas->firstWhere('plantilla', $plantillaFormato);

    // Establecer los valores predefinidos o null si no están disponibles
    $es_aplicable = $plantillaDatos->es_aplicable ?? null;
    $es_obligatorio = $plantillaDatos->es_obligatorio ?? null;
    $se_integra = $plantillaDatos->se_integra ?? 'En su caso';

    // Determinar si se debe mostrar la fila
    $mostrarFila = $es_aplicable !== 0 && $es_aplicable !== '0' && $es_aplicable !== false && $es_aplicable !== null;
    
    // NUEVA LÓGICA: Detectar apartados supervenientes (IDs 67-73)
    $esApartadoSuperveniente = $apartado->id >= 67 && $apartado->id <= 73;

    // Si es el nivel principal (nivel 1), manejar numeración específica para los primeros 2 apartados
    if (is_null($parent)) {
        // Numerar apartados principales (si están en la posición 1 o 2, serán 0, sino el número normal)
        if ($loop->iteration <= 2) {
            $currentIteration = 0;
        } else {
            // NUEVA LÓGICA: Numeración especial para apartados supervenientes
            if ($esSuperveniente && $formato === '06' && $apartado->id >= 67 && $apartado->id <= 73) {
                // Apartados supervenientes van del 14 al 20
                $currentIteration = 13 + ($apartado->id - 66); // 67->14, 68->15, 69->16, etc.
            } elseif ($esSuperveniente && $formato === '06' && ($apartado->id == 57 || $apartado->id == 60)) {
                // Apartados 57 y 60 van como 21 y 22 en supervenientes
                $currentIteration = ($apartado->id == 57) ? 21 : 22;
            } else {
                $currentIteration = $loop->iteration - 2;
            }
        }
    } else {
        // Para los subapartados, concatenar la numeración del apartado padre
        $currentIteration = "$iteration.$loop->iteration";
    }

    // Verificar si el apartado tiene subapartados
    $hasSubapartados = $apartado->subapartados->isNotEmpty();
@endphp

@if($mostrarFila)
<tr style="{{ is_null($apartado->parent_id) ? 'background:#d8e1f1;' : 'background:#fff' }}">
    <!-- Numeración del apartado -->
    <td style="text-align: center">{{ $currentIteration }}</td>

    <!-- Mostrar columnas sólo si no tiene subapartados (en caso de tener, se ocultan) -->
    @if (!$hasSubapartados)
        <!-- Indentación según el nivel de profundidad -->
        <td style="width:600px">
            {!! isset($parent) ? str_repeat('&emsp;', $parent->depth + 1) : '' !!}
            {!! $apartado->nombre !!}
        </td>
        
        <!-- Mostrar contenido sólo si el apartado no tiene subapartados -->
        <td style="text-align: center">
            @if (isset($checklist[$apartado->id]) && $checklist[$apartado->id]->se_aplica)
                Sí
            @else
                No
            @endif
        </td>
        <td style="text-align: center">
            @if (isset($checklist[$apartado->id]) && $checklist[$apartado->id]->es_obligatorio)
                Sí
            @else
                No
            @endif
        </td>
        <td style="text-align: center">
            @if (isset($checklist[$apartado->id]) && $checklist[$apartado->id]->se_integra)
                Sí
            @else
                No
            @endif
        </td>
        <td style="text-align: center">{{ $checklist[$apartado->id]->observaciones ?? '' }}</td>
        <td style="text-align: center">{{ $checklist[$apartado->id]->comentarios_uaa ?? '' }}</td>
    @else
        <!-- Apartado principal con subapartados, se ocultan las celdas -->
        <td colspan="6" style="background:#d9e2f1">{!! isset($parent) ? str_repeat('&emsp;', $parent->depth + 1) : '' !!}{{ $apartado->nombre }}</td>
    @endif
</tr>

<!-- Recursivamente incluir los subapartados -->
@foreach ($apartado->subapartados as $index => $subapartado)
    @include('partials.apartado_row_pdf', ['apartado' => $subapartado, 'iteration' => $currentIteration, 'parent' => $apartado])
@endforeach
@endif
