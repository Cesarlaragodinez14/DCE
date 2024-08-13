<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Carga de Acciones - Archivo Importado') }}
        </h2>
    </x-slot>

    <div class="container">
        <h1>Datos Importados para: {{ $import->name }}</h1>
    
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <!-- Define aquí las cabeceras de la tabla -->
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <!-- Agrega más columnas según sea necesario -->
                </tr>
            </thead>
            <tbody>
                @foreach ($importedData as $data)
                <tr>
                    <td>{{ $data->id }}</td>
                    <td>{{ $data->name }}</td>
                    <td>{{ $data->description }}</td>
                    <!-- Agrega más columnas según sea necesario -->
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
