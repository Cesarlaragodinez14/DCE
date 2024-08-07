<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.link href="/dashboard"
            >Dashboard</x-ui.breadcrumbs.link
        >
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link active
            >{{ __('crud.catEntregas.collectionTitle') }}</x-ui.breadcrumbs.link
        >
    </x-ui.breadcrumbs>

    <div class="flex justify-between align-top py-4">
        <x-ui.input
            wire:model.live="search"
            type="text"
            placeholder="Search {{ __('crud.catEntregas.collectionTitle') }}..."
        />

        @can('create', App\Models\CatEntrega::class)
        <a wire:navigate href="{{ route('dashboard.cat-entregas.create') }}">
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
                wire:click="delete({{ $deletingCatEntrega }})"
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
                    >{{ __('crud.catEntregas.inputs.valor.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('descripcion')"
                    >{{ __('crud.catEntregas.inputs.descripcion.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('activo')"
                    >{{ __('crud.catEntregas.inputs.activo.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.action-header>Actions</x-ui.table.action-header>
            </x-slot>

            <x-slot name="body">
                @forelse ($catEntregas as $catEntrega)
                <x-ui.table.row wire:loading.class.delay="opacity-75">
                    <x-ui.table.column for-crud
                        >{{ $catEntrega->valor }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $catEntrega->descripcion }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $catEntrega->activo }}</x-ui.table.column
                    >
                    <x-ui.table.action-column>
                        @can('update', $catEntrega)
                        <x-ui.action
                            wire:navigate
                            href="{{ route('dashboard.cat-entregas.edit', $catEntrega) }}"
                            >Edit</x-ui.action
                        >
                        @endcan @can('delete', $catEntrega)
                        <x-ui.action.danger
                            wire:click="confirmDeletion({{ $catEntrega->id }})"
                            >Delete</x-ui.action.danger
                        >
                        @endcan
                    </x-ui.table.action-column>
                </x-ui.table.row>
                @empty
                <x-ui.table.row>
                    <x-ui.table.column colspan="4"
                        >No {{ __('crud.catEntregas.collectionTitle') }} found.</x-ui.table.column
                    >
                </x-ui.table.row>
                @endforelse
            </x-slot>
        </x-ui.table>

        <div class="mt-2">{{ $catEntregas->links() }}</div>
    </x-ui.container.table>
</div>
