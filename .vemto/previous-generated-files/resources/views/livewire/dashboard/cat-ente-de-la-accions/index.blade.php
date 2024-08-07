<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.link href="/dashboard"
            >Dashboard</x-ui.breadcrumbs.link
        >
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link active
            >{{ __('crud.catEnteDeLaAccions.collectionTitle')
            }}</x-ui.breadcrumbs.link
        >
    </x-ui.breadcrumbs>

    <div class="flex justify-between align-top py-4">
        <x-ui.input
            wire:model.live="search"
            type="text"
            placeholder="Search {{ __('crud.catEnteDeLaAccions.collectionTitle') }}..."
        />

        @can('create', App\Models\CatEnteDeLaAccion::class)
        <a
            wire:navigate
            href="{{ route('dashboard.cat-ente-de-la-accions.create') }}"
        >
            <x-ui.button>New</x-ui.button>
        </a>
        @endcan
    </div>

    {{-- Delete Modal --}}
    <x-ui.modal.confirm wire:model="confirmingDeletion">
        <x-slot name="title"> {{ __('Delete') }} </x-slot>

        <x-slot name="content"> {{ __('Are you sure?') }} </x-slot>

        <x-slot name="footer">
            <x-ui.button
                wire:click="$toggle('confirmingDeletion')"
                wire:loading.attr="disabled"
            >
                {{ __('Cancel') }}
            </x-ui.button>

            <x-ui.button.danger
                class="ml-3"
                wire:click="delete({{ $deletingCatEnteDeLaAccion }})"
                wire:loading.attr="disabled"
            >
                {{ __('Delete') }}
            </x-ui.button.danger>
        </x-slot>
    </x-ui.modal.confirm>

    {{-- Index Table --}}
    <x-ui.container.table>
        <x-ui.table>
            <x-slot name="head">
                <x-ui.table.header for-crud wire:click="sortBy('valor')"
                    >{{ __('crud.catEnteDeLaAccions.inputs.valor.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('descripcion')"
                    >{{ __('crud.catEnteDeLaAccions.inputs.descripcion.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('activo')"
                    >{{ __('crud.catEnteDeLaAccions.inputs.activo.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.action-header>Actions</x-ui.table.action-header>
            </x-slot>

            <x-slot name="body">
                @forelse ($catEnteDeLaAccions as $catEnteDeLaAccion)
                <x-ui.table.row wire:loading.class.delay="opacity-75">
                    <x-ui.table.column for-crud
                        >{{ $catEnteDeLaAccion->valor }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $catEnteDeLaAccion->descripcion
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $catEnteDeLaAccion->activo }}</x-ui.table.column
                    >
                    <x-ui.table.action-column>
                        @can('update', $catEnteDeLaAccion)
                        <x-ui.action
                            wire:navigate
                            href="{{ route('dashboard.cat-ente-de-la-accions.edit', $catEnteDeLaAccion) }}"
                            >Edit</x-ui.action
                        >
                        @endcan @can('delete', $catEnteDeLaAccion)
                        <x-ui.action.danger
                            wire:click="confirmDeletion({{ $catEnteDeLaAccion->id }})"
                            >Delete</x-ui.action.danger
                        >
                        @endcan
                    </x-ui.table.action-column>
                </x-ui.table.row>
                @empty
                <x-ui.table.row>
                    <x-ui.table.column colspan="4"
                        >No {{ __('crud.catEnteDeLaAccions.collectionTitle') }} found.</x-ui.table.column
                    >
                </x-ui.table.row>
                @endforelse
            </x-slot>
        </x-ui.table>

        <div class="mt-2">{{ $catEnteDeLaAccions->links() }}</div>
    </x-ui.container.table>
</div>
