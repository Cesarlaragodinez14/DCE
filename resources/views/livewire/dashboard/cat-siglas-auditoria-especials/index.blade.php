<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.link href="/dashboard"
            >Dashboard</x-ui.breadcrumbs.link
        >
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link active
            >{{ __('crud.catSiglasAuditoriaEspecials.collectionTitle')
            }}</x-ui.breadcrumbs.link
        >
    </x-ui.breadcrumbs>

    <div class="flex justify-between align-top py-4">
        <x-ui.input
            wire:model.live="search"
            type="text"
            placeholder="Buscar en: {{ __('crud.catSiglasAuditoriaEspecials.collectionTitle') }}..."
        />

        @can('create', App\Models\CatSiglasAuditoriaEspecial::class)
        <a
            wire:navigate
            href="{{ route('dashboard.cat-siglas-auditoria-especials.create') }}"
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
                wire:click="delete({{ $deletingCatSiglasAuditoriaEspecial }})"
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
                    >{{
                    __('crud.catSiglasAuditoriaEspecials.inputs.valor.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('descripcion')"
                    >{{
                    __('crud.catSiglasAuditoriaEspecials.inputs.descripcion.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-crud wire:click="sortBy('activo')"
                    >{{
                    __('crud.catSiglasAuditoriaEspecials.inputs.activo.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.action-header>Acciones</x-ui.table.action-header>
            </x-slot>

            <x-slot name="body">
                @forelse ($catSiglasAuditoriaEspecials as $catSiglasAuditoriaEspecial)
                    <x-ui.table.row wire:loading.class.delay="opacity-75">
                        <x-ui.table.column for-crud>
                            {{ $catSiglasAuditoriaEspecial->valor }}
                        </x-ui.table.column>
                        <x-ui.table.column for-crud>
                            {{ $catSiglasAuditoriaEspecial->descripcion }}
                        </x-ui.table.column>
                        <x-ui.table.column for-crud>
                            {{ $catSiglasAuditoriaEspecial->activo }}
                        </x-ui.table.column>
                        <x-ui.table.action-column>
                            @can('update', $catSiglasAuditoriaEspecial)
                                <x-ui.action
                                    wire:navigate
                                    href="{{ route('dashboard.cat-siglas-auditoria-especials.edit', $catSiglasAuditoriaEspecial) }}"
                                >
                                    Editar
                                </x-ui.action>
                            @endcan
                            @can('delete', $catSiglasAuditoriaEspecial)
                                <x-ui.action.danger
                                    wire:click="confirmDeletion({{ $catSiglasAuditoriaEspecial->id }})"
                                >
                                    Borrar
                                </x-ui.action.danger>
                            @endcan
                        </x-ui.table.action-column>
                    </x-ui.table.row>
                @empty
                    <x-ui.table.row>
                        <x-ui.table.column colspan="4">
                            No {{ __('crud.catSiglasAuditoriaEspecials.collectionTitle') }} found.
                        </x-ui.table.column>
                    </x-ui.table.row>
                @endforelse
            </x-slot>
        </x-ui.table>

        <div class="mt-2">{{ $catSiglasAuditoriaEspecials->links() }}</div>
    </x-ui.container.table>
</div>
