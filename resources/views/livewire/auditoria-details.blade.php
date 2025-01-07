<div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Detalle de Auditoría y Historial de Cambios</h3>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campo</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apartado</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Actual</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Cambio</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Antes</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Después</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($dataForTable as $data)
                    @php
                        $rowspan = max(1, count($data['histories']));
                        $firstHistory = true;
                    @endphp
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-900" rowspan="{{ $rowspan }}">
                            {{ ucfirst($data['type']) }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-900" rowspan="{{ $rowspan }}">
                            {{ $data['field'] }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-900" rowspan="{{ $rowspan }}">
                            {{ $data['apartado_nombre'] ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-900" rowspan="{{ $rowspan }}">
                            {{ is_bool($data['current_value']) ? ($data['current_value'] ? 'Sí' : 'No') : $data['current_value'] }}
                        </td>
                        @if (count($data['histories']) > 0)
                            @foreach ($data['histories'] as $history)
                                @if (!$firstHistory)
                                    <tr>
                                @endif
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    {{ $history['date'] }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    {{ $history['user'] }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    {{ is_bool($history['before']) ? ($history['before'] ? 'Sí' : 'No') : $history['before'] }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    {{ is_bool($history['after']) ? ($history['after'] ? 'Sí' : 'No') : $history['after'] }}
                                </td>
                                @if (!$firstHistory)
                                    </tr>
                                @endif
                                @php $firstHistory = false; @endphp
                            @endforeach
                        @else
                            <td class="px-4 py-2 text-sm text-gray-900 text-center" colspan="4">
                                Sin cambios
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>