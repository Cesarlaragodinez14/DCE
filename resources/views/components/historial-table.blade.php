<!-- resources/views/components/historial-table.blade.php -->
<div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg">
        <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                <th class="px-6 py-4 border-b border-gray-300 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    <div class="flex items-center">
                        <ion-icon name="calendar-outline" class="text-base text-blue-500 mr-2"></ion-icon>
                        Fecha
                    </div>
                </th>
                <th class="px-6 py-4 border-b border-gray-300 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    <div class="flex items-center">
                        <ion-icon name="person-outline" class="text-base text-green-500 mr-2"></ion-icon>
                        Usuario
                    </div>
                </th>
                <th class="px-6 py-4 border-b border-gray-300 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    <div class="flex items-center">
                        <ion-icon name="document-outline" class="text-base text-amber-500 mr-2"></ion-icon>
                        Campo
                    </div>
                </th>
                <th class="px-6 py-4 border-b border-gray-300 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    <div class="flex items-center">
                        <ion-icon name="time-outline" class="text-base text-red-500 mr-2"></ion-icon>
                        Antes
                    </div>
                </th>
                <th class="px-6 py-4 border-b border-gray-300 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    <div class="flex items-center">
                        <ion-icon name="checkmark-circle-outline" class="text-base text-emerald-500 mr-2"></ion-icon>
                        Después
                    </div>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse ($historiales as $historial)
                <tr class="hover:bg-gray-50 transition duration-200 ease-in-out">
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $historial['date'] }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-green-400 to-blue-500 flex items-center justify-center">
                                    <span class="text-xs font-medium text-white">
                                        {{ strtoupper(substr($historial['user'], 0, 2)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $historial['user'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-amber-100 text-amber-800">
                            {{ $historial['field'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="max-w-xs">
                            @if(strlen($historial['before']) > 50)
                                <div class="relative group">
                                    <div class="comentario-observacionmb-2 shadow-lg max-w-sm">
                                        {{ $historial['before'] }}
                                        <div class="absolute top-full left-4 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-800">
                                    {{ $historial['before'] }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="max-w-xs">
                            @if(strlen($historial['after']) > 50)
                                <div class="relative group">
                                    <div class="comentario-observacionmb-2 shadow-lg max-w-sm">
                                        {{ $historial['after'] }}
                                        <div class="absolute top-full left-4 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-emerald-100 text-emerald-800">
                                    {{ $historial['after'] }}
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <ion-icon name="document-text-outline" class="text-5xl text-gray-400 mb-4"></ion-icon>
                            <p class="text-lg font-medium text-gray-500">Sin cambios registrados</p>
                            <p class="text-sm text-gray-400">No se encontraron modificaciones</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
