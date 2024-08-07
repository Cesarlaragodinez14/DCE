<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.link href="/dashboard"
            >Dashboard</x-ui.breadcrumbs.link
        >
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link active
            >{{ __('crud.catAuditoriaEspecials.collectionTitle')
            }}</x-ui.breadcrumbs.link
        >
    </x-ui.breadcrumbs>

    <div class="flex justify-between align-top py-4">
        <x-ui.input
            wire:model.live="search"
            type="text"
            placeholder="Search {{ __('crud.catAuditoriaEspecials.collectionTitle') }}..."
        />

        @can('create', App\Models\CatAuditoriaEspecial::class)
        <a
            wire:navigate
            href="{{ route('dashboard.cat-auditoria-especials.create') }}"
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
                wire:click="delete({{ $deletingCatAuditoriaEspecial }})"
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
                    >{{ __('crud.catAuditoriaEspecials.inputs.valor.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('descripcion')"
                    >{{
                    __('crud.catAuditoriaEspecials.inputs.descripcion.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('activo')"
                    >{{ __('crud.catAuditoriaEspecials.inputs.activo.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.action-header>Actions</x-ui.table.action-header>
            </x-slot>

            <x-slot name="body">
                @forelse ($catAuditoriaEspecials as $catAuditoriaEspecial)
                <x-ui.table.row wire:loading.class.delay="opacity-75">
                    <x-ui.table.column for-crud
                        >{{ $catAuditoriaEspecial->valor }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $catAuditoriaEspecial->descripcion
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-crud
                        >{{ $catAuditoriaEspecial->activo }}</x-ui.table.column
                    >
                    <x-ui.table.action-column>
                        @can('update', $catAuditoriaEspecial)
                        <x-ui.action
                            wire:navigate
                            href="{{ route('dashboard.cat-auditoria-especials.edit', $catAuditoriaEspecial) }}"
                            >Edit</x-ui.action
                        >
                        @endcan @can('delete', $catAuditoriaEspecial)
                        <x-ui.action.danger
                            wire:click="confirmDeletion({{ $catAuditoriaEspecial->id }})"
                            >Delete</x-ui.action.danger
                        >
                        @endcan
                    </x-ui.table.action-column>
                </x-ui.table.row>
                @empty
                <x-ui.table.row>
                    <x-ui.table.column colspan="4"
                        >No {{ __('crud.catAuditoriaEspecials.collectionTitle') }} found.</x-ui.table.column
                    >
                </x-ui.table.row>
                @endforelse
            </x-slot>
        </x-ui.table>

        <div class="mt-2">{{ $catAuditoriaEspecials->links() }}</div>
    </x-ui.container.table>
</div>
