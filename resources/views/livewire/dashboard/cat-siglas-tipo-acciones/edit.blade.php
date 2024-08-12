<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.link href="/dashboard"
            >Dashboard</x-ui.breadcrumbs.link
        >
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link
            href="{{ route('dashboard.cat-siglas-tipo-acciones.index') }}"
            >{{ __('crud.catSiglasTipoAcciones.collectionTitle')
            }}</x-ui.breadcrumbs.link
        >
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link active
            >Editar: {{ __('crud.catSiglasTipoAcciones.itemTitle')
            }}</x-ui.breadcrumbs.link
        >
    </x-ui.breadcrumbs>

    <x-ui.toast on="saved">
        CatSiglasTipoAccion saved successfully.
    </x-ui.toast>

    <div class="w-full text-gray-500 text-lg font-semibold py-4 uppercase">
        <h1>Editar: {{ __('crud.catSiglasTipoAcciones.itemTitle') }}</h1>
    </div>

    <div class="overflow-hidden border rounded-lg bg-white">
        <form class="w-full mb-0" wire:submit.prevent="save">
            <div class="p-6 space-y-3">
                <div class="w-full">
                    <x-ui.label for="valor"
                        >{{ __('crud.catSiglasTipoAcciones.inputs.valor.label')
                        }}</x-ui.label
                    >
                    <x-ui.input.text
                        class="w-full"
                        wire:model="form.valor"
                        name="valor"
                        id="valor"
                        placeholder="{{ __('crud.catSiglasTipoAcciones.inputs.valor.placeholder') }}"
                    />
                    <x-ui.input.error for="form.valor" />
                </div>

                <div class="w-full">
                    <x-ui.label for="description"
                        >{{
                        __('crud.catSiglasTipoAcciones.inputs.description.label')
                        }}</x-ui.label
                    >
                    <x-ui.input.textarea
                        class="w-full"
                        wire:model="form.description"
                        rows="6"
                        name="description"
                        id="description"
                        placeholder="{{ __('crud.catSiglasTipoAcciones.inputs.description.placeholder') }}"
                    />
                    <x-ui.input.error for="form.description" />
                </div>

                <div class="w-full">
                    <x-ui.input.checkbox
                        class=""
                        wire:model="form.activo"
                        name="activo"
                        id="activo"
                    />
                    <x-ui.label for="activo"
                        >{{ __('crud.catSiglasTipoAcciones.inputs.activo.label')
                        }}</x-ui.label
                    >
                    <x-ui.input.error for="form.activo" />
                </div>
            </div>

            <div class="flex justify-between mt-4 border-t border-gray-50 p-4">
                <div>
                    <!-- Other buttons here -->
                </div>
                <div>
                    <x-ui.button type="submit">Guardar</x-ui.button>
                </div>
            </div>
        </form>
    </div>

    @can('view-any', App\Models\Auditorias::class)
    <div class="overflow-hidden border rounded-lg bg-white">
        <div class="w-full mb-0">
            <div class="p-6 space-y-3">
                <div
                    class="w-full text-gray-500 text-lg font-semibold py-4 uppercase"
                >
                    <h2>{{ __('crud.allAuditorias.collectionTitle') }}</h2>
                </div>

                <livewire:dashboard.cat-siglas-tipo-accion-all-auditorias-detail
                    :cat-siglas-tipo-accion="$catSiglasTipoAccion"
                />
            </div>
        </div>
    </div>
    @endcan
</div>
