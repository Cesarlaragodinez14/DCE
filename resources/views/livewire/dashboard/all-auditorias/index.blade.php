<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.link href="/dashboard">
            Dashboard
        </x-ui.breadcrumbs.link>
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link active>
            {{ __('crud.allAuditorias.collectionTitle') }}
        </x-ui.breadcrumbs.link>
    </x-ui.breadcrumbs>

    @if(session()->has('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center py-4">
        <div class="flex space-x-4">
            <x-ui.input
                wire:model.debounce.300ms="search"
                type="text"
                placeholder="Buscar en: {{ __('crud.allAuditorias.collectionTitle') }}..."
            />

            <x-ui.filter-cp-en
                :entregas="$entrega"
                :cuentasPublicas="$cuentaPublica"
                route="dashboard.all-auditorias.index"
                defaultEntregaLabel="Seleccionar Entrega"
                defaultCuentaPublicaLabel="Seleccionar Cuenta Pública"
            />
        </div>

        <div class="flex space-x-4">
            @role('admin')
                <a wire:navigate href="{{ route('dashboard.all-auditorias.create') }}">
                    <x-ui.button>Crear</x-ui.button>
                </a>

            <button wire:click="exportExcel" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" wire:loading.attr="disabled">
                Exportar
            </button>
            @endrole
        </div>
    </div>

    {{-- Delete Modal --}}
    <x-ui.modal.confirm wire:model="confirmingDeletion">
        <x-slot name="title"> {{ __('Borrar') }} </x-slot>

        <x-slot name="content"> {{ __('¿Deseas confirmar esta acción?') }} </x-slot>

        <x-slot name="footer">
            <x-ui.button
                wire:click="$toggle('confirmingDeletion')"
                wire:loading.attr="disabled"
            >
                {{ __('Cancelar') }}
            </x-ui.button>

            <x-ui.button.danger
                class="ml-3"
                wire:click="delete({{ $deletingAuditorias }})"
                wire:loading.attr="disabled"
            >
                {{ __('Borrar') }}
            </x-ui.button.danger>
        </x-slot>
    </x-ui.modal.confirm>

    {{-- Index Table --}}
    <x-ui.container.table>
        <x-ui.table>
            <x-slot name="head">
                <x-ui.table.header
                    >{{ __('Revisión de Expediente')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    >{{ __('Checklist Pdf')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    >{{ __('Estatus de Revisión de Expediente')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('clave_de_accion')"
                    >{{ __('crud.allAuditorias.inputs.clave_de_accion.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('entrega')"
                    >{{ __('crud.allAuditorias.inputs.entrega.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('siglas_auditoria_especial')"
                    >{{
                    __('crud.allAuditorias.inputs.siglas_auditoria_especial.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('uaa')"
                    >{{ __('crud.allAuditorias.inputs.uaa.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('titulo')"
                    >{{ __('crud.allAuditorias.inputs.titulo.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('numero_de_auditoria')"
                    >{{
                    __('crud.allAuditorias.inputs.numero_de_auditoria.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('ente_de_la_accion')"
                    >{{ __('crud.allAuditorias.inputs.ente_de_la_accion.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('dgseg_ef')"
                    >{{ __('crud.allAuditorias.inputs.dgseg_ef.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('sub_direccion_de_area')"
                    >{{
                    __('crud.allAuditorias.inputs.sub_direccion_de_area.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('nombre_sub_director_de_area')"
                    >{{
                    __('crud.allAuditorias.inputs.nombre_sub_director_de_area.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('jefe_de_departamento')"
                    >{{
                    __('crud.allAuditorias.inputs.jefe_de_departamento.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('cuenta_publica')"
                    >{{ __('crud.allAuditorias.inputs.cuenta_publica.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.action-header>Acciones</x-ui.table.action-header>
            </x-slot>

            <x-slot name="body">
                @forelse ($allAuditorias as $auditorias)
                <x-ui.table.row wire:loading.class.delay="opacity-75">
                    <x-ui.table.column>
                        <a href="{{ route('auditorias.apartados', $auditorias->id) }}" style="width: 40px; padding: 10px !important; margin-bottom: 5px; background: #000;" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium tracking-wide text-white transition-colors duration-200 rounded-md bg-indigo-500 hover:bg-indigo-600 focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 focus:shadow-outline focus:outline-none">
                            <ion-icon name="create-outline"></ion-icon>
                        </a>
                    </x-ui.table.column>
                    <x-ui.table.column>
                        @if ($auditorias->estatus_checklist == "Aceptado" && empty($auditorias->archivo_uua))
                            <a href="{{ route('auditorias.pdf', $auditorias->id) }}" style="padding: 10px !important; margin-bottom: 5px; background: #22125e;" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium tracking-wide text-white transition-colors duration-200 rounded-md bg-indigo-500 hover:bg-indigo-600 focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 focus:shadow-outline focus:outline-none">
                                <ion-icon name="cloud-download-outline"></ion-icon> - Con Firma de Seguimiento
                            </a>
                        @elseif($auditorias->estatus_checklist == "Aceptado" && !empty($auditorias->archivo_uua))
                            <a href="{{ route('auditorias.downloadUua', $auditorias->id) }}" style="padding: 10px !important; margin-bottom: 5px; background: #14ae1f;" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium tracking-wide text-white transition-colors duration-200 rounded-md bg-indigo-500 hover:bg-indigo-600 focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 focus:shadow-outline focus:outline-none">
                                <ion-icon name="cloud-download-outline"></ion-icon> - Completado
                            </a>
                        @else
                            Sin pdf generado
                        @endif
                    </x-ui.table.column>
                    <x-ui.table.column>
                        {{ $auditorias->estatus_checklist }}
                    </x-ui.table.column>
                    <x-ui.table.column for-crud
                        >{{ $auditorias->clave_de_accion }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->catEntrega->valor }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->catSiglasAuditoriaEspecial->valor
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        ><b>{{ $auditorias->catUaa->valor }}</b> <br> <small>{{ $auditorias->catUaa->nombre }}</small></x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->titulo }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->numero_de_auditoria
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->catEnteDeLaAccion->valor }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->catDgsegEf->valor }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->sub_direccion_de_area
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->nombre_sub_director_de_area
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->jefe_de_departamento
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->catCuentaPublica->valor }}</x-ui.table.column
                    >
                    <x-ui.table.action-column>
                        @role('admin')
                        @can('update', $auditorias)
                        <x-ui.action
                            wire:navigate
                            href="{{ route('dashboard.all-auditorias.edit', $auditorias) }}"
                            >Editar</x-ui.action
                        >
                        @endcan @can('delete', $auditorias)
                        <x-ui.action.danger
                            wire:click="confirmDeletion({{ $auditorias->id }})"
                            >Borrar</x-ui.action.danger
                        >
                        @endcan
                        @endrole
                    </x-ui.table.action-column>
                </x-ui.table.row>
                @empty
                <x-ui.table.row>
                    <x-ui.table.column colspan="21"
                        >No se encontró: {{ __('crud.allAuditorias.collectionTitle') }}.</x-ui.table.column
                    >
                </x-ui.table.row>
                @endforelse
            </x-slot>
        </x-ui.table>

        <div class="mt-2">{{ $allAuditorias->links() }}</div>
    </x-ui.container.table>
</div>
