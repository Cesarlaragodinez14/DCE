<!-- resources/views/components/historial-table.blade.php -->
<div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 border-b border-gray-300 bg-gray-100 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    Fecha
                </th>
                <th class="px-6 py-3 border-b border-gray-300 bg-gray-100 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    Usuario
                </th>
                <th class="px-6 py-3 border-b border-gray-300 bg-gray-100 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    Campo
                </th>
                <th class="px-6 py-3 border-b border-gray-300 bg-gray-100 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    Antes
                </th>
                <th class="px-6 py-3 border-b border-gray-300 bg-gray-100 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    Después
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($historiales as $historial)
                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                    <td class="px-6 py-4 border-b border-gray-200">{{ $historial['date'] }}</td>
                    <td class="px-6 py-4 border-b border-gray-200">{{ $historial['user'] }}</td>
                    <td class="px-6 py-4 border-b border-gray-200">{{ $historial['field'] }}</td>
                    <td class="px-6 py-4 border-b border-gray-200">{{ $historial['before'] }}</td>
                    <td class="px-6 py-4 border-b border-gray-200">{{ $historial['after'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Sin cambios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
