<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.link href="/dashboard"
            >Dashboard</x-ui.breadcrumbs.link
        >
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link active
            >{{ __('crud.catSiglasTipoAcciones.collectionTitle')
            }}</x-ui.breadcrumbs.link
        >
    </x-ui.breadcrumbs>

    <div class="flex justify-between align-top py-4">
        <x-ui.input
            wire:model.live="search"
            type="text"
            placeholder="Buscar en: {{ __('crud.catSiglasTipoAcciones.collectionTitle') }}..."
        />

        @can('create', App\Models\CatSiglasTipoAccion::class)
        <a
            wire:navigate
            href="{{ route('dashboard.cat-siglas-tipo-acciones.create') }}"
        >
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
                wire:click="delete({{ $deletingCatSiglasTipoAccion }})"
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
                <x-ui.table.header for-crud wire:click="sortBy('valor')"
                    >{{ __('crud.catSiglasTipoAcciones.inputs.valor.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('description')"
                    >{{
                    __('crud.catSiglasTipoAcciones.inputs.description.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('activo')"
                    >{{ __('crud.catSiglasTipoAcciones.inputs.activo.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.action-header>Acciones</x-ui.table.action-header>
            </x-slot>

            <x-slot name="body">
                @forelse ($catSiglasTipoAcciones as $catSiglasTipoAccion)
                <x-ui.table.row wire:loading.class.delay="opacity-75">
                    <x-ui.table.column for-crud
                        >{{ $catSiglasTipoAccion->valor }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $catSiglasTipoAccion->description
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $catSiglasTipoAccion->activo }}</x-ui.table.column
                    >
                    <x-ui.table.action-column>
                        @can('update', $catSiglasTipoAccion)
                        <x-ui.action
                            wire:navigate
                            href="{{ route('dashboard.cat-siglas-tipo-acciones.edit', $catSiglasTipoAccion) }}"
                            >Editar</x-ui.action
                        >
                        @endcan @can('delete', $catSiglasTipoAccion)
                        <x-ui.action.danger
                            wire:click="confirmDeletion({{ $catSiglasTipoAccion->id }})"
                            >Borrar</x-ui.action.danger
                        >
                        @endcan
                    </x-ui.table.action-column>
                </x-ui.table.row>
                @empty
                <x-ui.table.row>
                    <x-ui.table.column colspan="4"
                        >No se encontró: {{ __('crud.catSiglasTipoAcciones.collectionTitle') }}.</x-ui.table.column
                    >
                </x-ui.table.row>
                @endforelse
            </x-slot>
        </x-ui.table>

        <div class="mt-2">{{ $catSiglasTipoAcciones->links() }}</div>
    </x-ui.container.table>
</div>
