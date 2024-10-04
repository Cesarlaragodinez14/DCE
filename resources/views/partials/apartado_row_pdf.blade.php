@php
    // Si es el nivel principal (nivel 1), manejar numeración específica para los primeros 2 apartados
    if (is_null($parent)) {
        // Numerar apartados principales (si están en la posición 1 o 2, serán 0, sino el número normal)
        if ($loop->iteration <= 2) {
            $currentIteration = 0;
        } else {
            $currentIteration = $loop->iteration - 2;
        }
    } else {
        // Para los subapartados, concatenar la numeración del apartado padre
        $currentIteration = "$iteration.$loop->iteration";
    }

    // Verificar si el apartado tiene subapartados
    $hasSubapartados = $apartado->subapartados->isNotEmpty();
@endphp

<tr style="{{ is_null($apartado->parent_id) ? 'background:#d8e1f1;' : 'background:#fff' }}">
    <!-- Numeración del apartado -->
    <td style="text-align: center">{{ $currentIteration }}</td>

    <!-- Mostrar columnas sólo si no tiene subapartados (en caso de tener, se ocultan) -->
    @if (!$hasSubapartados)
        <!-- Indentación según el nivel de profundidad -->
        <td style="width:600px">{!! isset($parent) ? str_repeat('&emsp;', $parent->depth + 1) : '' !!}{{ $apartado->nombre }}</td>

        <!-- Mostrar contenido sólo si el apartado no tiene subapartados -->
        <td style="text-align: center">
            @if (isset($checklist[$apartado->id]) && $checklist[$apartado->id]->aplica)
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
