<form id="filterForm" method="GET" action="{{ route($route) }}" class="flex space-x-2 items-center">
    <!-- Selector de Entrega -->
    <label for="entrega" class="sr-only">Entrega: </label>
    <select name="entrega" onchange="this.form.submit()" class="form-select rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <option value="">{{ $defaultEntregaLabel }}</option>
        @foreach($entregas as $entrega)
            <option value="{{ $entrega->id }}" {{ request('entrega') == $entrega->id ? 'selected' : '' }}>
                {{ $entrega->valor }}
            </option>
        @endforeach
    </select>
    &nbsp;
    <!-- Selector de Cuenta Pública -->
    <label for="cuenta_publica" class="sr-only">Cuenta Pública: </label>
    <select name="cuenta_publica" onchange="this.form.submit()" class="form-select rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <option value="">{{ $defaultCuentaPublicaLabel }}</option>
        @foreach($cuentasPublicas as $cuenta)
            <option value="{{ $cuenta->id }}" {{ request('cuenta_publica') == $cuenta->id ? 'selected' : '' }}>
                {{ $cuenta->valor }}
            </option>
        @endforeach
    </select>

    <!-- Botón de Filtrar -->
    <x-ui.button type="submit" class="rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        Filtrar
    </x-ui.button>
</form>
