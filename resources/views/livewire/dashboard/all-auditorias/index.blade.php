<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.link href="/dashboard"
            >Dashboard</x-ui.breadcrumbs.link
        >
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link active
            >{{ __('crud.allAuditorias.collectionTitle')
            }}</x-ui.breadcrumbs.link
        >
    </x-ui.breadcrumbs>

    <div class="flex justify-between align-top py-4">
        <x-ui.input
            wire:model.live="search"
            type="text"
            placeholder="Buscar en: {{ __('crud.allAuditorias.collectionTitle') }}..."
        />

        @can('create', App\Models\Auditorias::class)
        <a wire:navigate href="{{ route('dashboard.all-auditorias.create') }}">
            <x-ui.button>Crear</x-ui.button>
        </a>
        @endcan
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
                    wire:click="sortBy('auditoria_especial')"
                    >{{ __('crud.allAuditorias.inputs.auditoria_especial.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('tipo_de_auditoria')"
                    >{{ __('crud.allAuditorias.inputs.tipo_de_auditoria.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('siglas_auditoria_especial')"
                    >{{
                    __('crud.allAuditorias.inputs.siglas_auditoria_especial.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('siglas_dg_uaa')"
                    >{{ __('crud.allAuditorias.inputs.siglas_dg_uaa.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('titulo')"
                    >{{ __('crud.allAuditorias.inputs.titulo.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('ente_fiscalizado')"
                    >{{ __('crud.allAuditorias.inputs.ente_fiscalizado.label')
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
                <x-ui.table.header for-crud wire:click="sortBy('clave_accion')"
                    >{{ __('crud.allAuditorias.inputs.clave_accion.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('siglas_tipo_accion')"
                    >{{ __('crud.allAuditorias.inputs.siglas_tipo_accion.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('dgseg_ef')"
                    >{{ __('crud.allAuditorias.inputs.dgseg_ef.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('nombre_director_general')"
                    >{{
                    __('crud.allAuditorias.inputs.nombre_director_general.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('direccion_de_area')"
                    >{{ __('crud.allAuditorias.inputs.direccion_de_area.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-crud
                    wire:click="sortBy('nombre_director_de_area')"
                    >{{
                    __('crud.allAuditorias.inputs.nombre_director_de_area.label')
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
                    <x-ui.table.column for-crud
                        >{{ $auditorias->clave_de_accion }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->entrega }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->auditoria_especial
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->tipo_de_auditoria }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->siglas_auditoria_especial
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->siglas_dg_uaa }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->titulo }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->ente_fiscalizado }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->numero_de_auditoria
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->ente_de_la_accion }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->clave_accion }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->siglas_tipo_accion
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->dgseg_ef }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->nombre_director_general
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->direccion_de_area }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $auditorias->nombre_director_de_area
                        }}</x-ui.table.column
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
                        >{{ $auditorias->cuenta_publica }}</x-ui.table.column
                    >
                    <x-ui.table.action-column>
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
