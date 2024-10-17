<!-- resources/views/apartados/partials/audit_info_grid.blade.php -->

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
    <!-- Área que entrega -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Área que entrega</h3>
        <ul class="space-y-2 text-gray-700">
            <li><strong>Auditoría Especial:</strong> {{ $auditoria->catSiglasAuditoriaEspecial->descripcion ?? '-' }}</li>
            <li><strong>Dirección General de la UAA:</strong> {{ $auditoria->catUaa->nombre ?? '-' }}</li>
            <li><strong>Título de la Auditoría:</strong> {{ $auditoria->titulo }}</li>
            <li><strong>Número de Auditoría:</strong> {{ $auditoria->catAuditoriaEspecial->valor ?? '-' }}</li>
            <li><strong>Clave de la Acción:</strong> {{ $auditoria->catClaveAccion->valor ?? '-' }}</li>
            <li><strong>Nombre del Ente de la Acción o Recomendación:</strong> {{ $auditoria->catEnteDeLaAccion->valor ?? '-' }}</li>
        </ul>
    </div>
    <!-- Área que recibe y revisa -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Área que recibe y revisa</h3>
        <ul class="space-y-2 text-gray-700">
            <li><strong>Dirección General:</strong> {{ $auditoria->catDgsegEf->valor ?? '-' }}</li>
            <li><strong>Dirección de Área:</strong> {{ $auditoria->direccion_de_area ?? '-' }}</li>
            <li><strong>Subdirección:</strong> {{ $auditoria->sub_direccion_de_area ?? '-' }}</li>
            <li><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($auditoria->fecha)->format('d/m/Y') }}</li>
        </ul>
    </div>
</div>
