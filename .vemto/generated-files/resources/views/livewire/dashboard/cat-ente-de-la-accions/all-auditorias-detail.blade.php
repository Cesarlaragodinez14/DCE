<div>
    <div class="flex justify-between align-top py-4">
        <x-ui.input
            wire:model.live="detailAllAuditoriasSearch"
            type="text"
            placeholder="Search {{ __('crud.allAuditorias.collectionTitle') }}..."
        />

        @can('create', App\Models\Auditorias::class)
        <a wire:click="newAuditorias()">
            <x-ui.button>New</x-ui.button>
        </a>
        @endcan
    </div>

    {{-- Modal --}}
    <x-ui.modal wire:model="showingModal">
        <div class="overflow-hidden border rounded-lg bg-white">
            <form class="w-full mb-0" wire:submit.prevent="save">
                <div class="p-6 space-y-3">
                    <div class="w-full">
                        <x-ui.label for="clave_de_accion"
                            >{{
                            __('crud.allAuditorias.inputs.clave_de_accion.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.text
                            class="w-full"
                            wire:model="form.clave_de_accion"
                            name="clave_de_accion"
                            id="clave_de_accion"
                            placeholder="{{ __('crud.allAuditorias.inputs.clave_de_accion.placeholder') }}"
                        />
                        <x-ui.input.error for="form.clave_de_accion" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="entrega"
                            >{{ __('crud.allAuditorias.inputs.entrega.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.select
                            wire:model="form.entrega"
                            name="entrega"
                            id="entrega"
                            class="w-full"
                        >
                            <option value="">Select data</option>
                            @foreach ($catEntregas as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-ui.input.select>
                        <x-ui.input.error for="form.entrega" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="auditoria_especial"
                            >{{
                            __('crud.allAuditorias.inputs.auditoria_especial.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.select
                            wire:model="form.auditoria_especial"
                            name="auditoria_especial"
                            id="auditoria_especial"
                            class="w-full"
                        >
                            <option value="">Select data</option>
                            @foreach ($catAuditoriaEspecials as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-ui.input.select>
                        <x-ui.input.error for="form.auditoria_especial" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="tipo_de_auditoria"
                            >{{
                            __('crud.allAuditorias.inputs.tipo_de_auditoria.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.select
                            wire:model="form.tipo_de_auditoria"
                            name="tipo_de_auditoria"
                            id="tipo_de_auditoria"
                            class="w-full"
                        >
                            <option value="">Select data</option>
                            @foreach ($catTipoDeAuditorias as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-ui.input.select>
                        <x-ui.input.error for="form.tipo_de_auditoria" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="siglas_auditoria_especial"
                            >{{
                            __('crud.allAuditorias.inputs.siglas_auditoria_especial.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.select
                            wire:model="form.siglas_auditoria_especial"
                            name="siglas_auditoria_especial"
                            id="siglas_auditoria_especial"
                            class="w-full"
                        >
                            <option value="">Select data</option>
                            @foreach ($catSiglasAuditoriaEspecials as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-ui.input.select>
                        <x-ui.input.error
                            for="form.siglas_auditoria_especial"
                        />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="siglas_dg_uaa"
                            >{{
                            __('crud.allAuditorias.inputs.siglas_dg_uaa.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.select
                            wire:model="form.siglas_dg_uaa"
                            name="siglas_dg_uaa"
                            id="siglas_dg_uaa"
                            class="w-full"
                        >
                            <option value="">Select data</option>
                            @foreach ($catUaas as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-ui.input.select>
                        <x-ui.input.error for="form.siglas_dg_uaa" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="titulo"
                            >{{ __('crud.allAuditorias.inputs.titulo.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.text
                            class="w-full"
                            wire:model="form.titulo"
                            name="titulo"
                            id="titulo"
                            placeholder="{{ __('crud.allAuditorias.inputs.titulo.placeholder') }}"
                        />
                        <x-ui.input.error for="form.titulo" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="ente_fiscalizado"
                            >{{
                            __('crud.allAuditorias.inputs.ente_fiscalizado.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.select
                            wire:model="form.ente_fiscalizado"
                            name="ente_fiscalizado"
                            id="ente_fiscalizado"
                            class="w-full"
                        >
                            <option value="">Select data</option>
                            @foreach ($catEnteFiscalizados as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-ui.input.select>
                        <x-ui.input.error for="form.ente_fiscalizado" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="numero_de_auditoria"
                            >{{
                            __('crud.allAuditorias.inputs.numero_de_auditoria.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.number
                            class="w-full"
                            wire:model="form.numero_de_auditoria"
                            name="numero_de_auditoria"
                            id="numero_de_auditoria"
                            placeholder="{{ __('crud.allAuditorias.inputs.numero_de_auditoria.placeholder') }}"
                            step="1"
                        />
                        <x-ui.input.error for="form.numero_de_auditoria" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="clave_accion"
                            >{{
                            __('crud.allAuditorias.inputs.clave_accion.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.select
                            wire:model="form.clave_accion"
                            name="clave_accion"
                            id="clave_accion"
                            class="w-full"
                        >
                            <option value="">Select data</option>
                            @foreach ($catClaveAccions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-ui.input.select>
                        <x-ui.input.error for="form.clave_accion" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="siglas_tipo_accion"
                            >{{
                            __('crud.allAuditorias.inputs.siglas_tipo_accion.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.select
                            wire:model="form.siglas_tipo_accion"
                            name="siglas_tipo_accion"
                            id="siglas_tipo_accion"
                            class="w-full"
                        >
                            <option value="">Select data</option>
                            @foreach ($catSiglasTipoAcciones as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-ui.input.select>
                        <x-ui.input.error for="form.siglas_tipo_accion" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="dgseg_ef"
                            >{{ __('crud.allAuditorias.inputs.dgseg_ef.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.select
                            wire:model="form.dgseg_ef"
                            name="dgseg_ef"
                            id="dgseg_ef"
                            class="w-full"
                        >
                            <option value="">Select data</option>
                            @foreach ($catDgsegEfs as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-ui.input.select>
                        <x-ui.input.error for="form.dgseg_ef" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="nombre_director_general"
                            >{{
                            __('crud.allAuditorias.inputs.nombre_director_general.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.text
                            class="w-full"
                            wire:model="form.nombre_director_general"
                            name="nombre_director_general"
                            id="nombre_director_general"
                            placeholder="{{ __('crud.allAuditorias.inputs.nombre_director_general.placeholder') }}"
                        />
                        <x-ui.input.error for="form.nombre_director_general" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="direccion_de_area"
                            >{{
                            __('crud.allAuditorias.inputs.direccion_de_area.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.text
                            class="w-full"
                            wire:model="form.direccion_de_area"
                            name="direccion_de_area"
                            id="direccion_de_area"
                            placeholder="{{ __('crud.allAuditorias.inputs.direccion_de_area.placeholder') }}"
                        />
                        <x-ui.input.error for="form.direccion_de_area" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="nombre_director_de_area"
                            >{{
                            __('crud.allAuditorias.inputs.nombre_director_de_area.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.text
                            class="w-full"
                            wire:model="form.nombre_director_de_area"
                            name="nombre_director_de_area"
                            id="nombre_director_de_area"
                            placeholder="{{ __('crud.allAuditorias.inputs.nombre_director_de_area.placeholder') }}"
                        />
                        <x-ui.input.error for="form.nombre_director_de_area" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="sub_direccion_de_area"
                            >{{
                            __('crud.allAuditorias.inputs.sub_direccion_de_area.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.text
                            class="w-full"
                            wire:model="form.sub_direccion_de_area"
                            name="sub_direccion_de_area"
                            id="sub_direccion_de_area"
                            placeholder="{{ __('crud.allAuditorias.inputs.sub_direccion_de_area.placeholder') }}"
                        />
                        <x-ui.input.error for="form.sub_direccion_de_area" />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="nombre_sub_director_de_area"
                            >{{
                            __('crud.allAuditorias.inputs.nombre_sub_director_de_area.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.text
                            class="w-full"
                            wire:model="form.nombre_sub_director_de_area"
                            name="nombre_sub_director_de_area"
                            id="nombre_sub_director_de_area"
                            placeholder="{{ __('crud.allAuditorias.inputs.nombre_sub_director_de_area.placeholder') }}"
                        />
                        <x-ui.input.error
                            for="form.nombre_sub_director_de_area"
                        />
                    </div>

                    <div class="w-full">
                        <x-ui.label for="jefe_de_departamento"
                            >{{
                            __('crud.allAuditorias.inputs.jefe_de_departamento.label')
                            }}</x-ui.label
                        >
                        <x-ui.input.text
                            class="w-full"
                            wire:model="form.jefe_de_departamento"
                            name="jefe_de_departamento"
                            id="jefe_de_departamento"
                            placeholder="{{ __('crud.allAuditorias.inputs.jefe_de_departamento.placeholder') }}"
                        />
                        <x-ui.input.error for="form.jefe_de_departamento" />
                    </div>
                </div>

                <div
                    class="flex justify-between mt-4 border-t border-gray-50 bg-gray-50 p-4"
                >
                    <div>
                        <!-- Other buttons here -->
                    </div>
                    <div>
                        <x-ui.button type="submit">Save</x-ui.button>
                    </div>
                </div>
            </form>
        </div>
    </x-ui.modal>

    {{-- Delete Modal --}}
    <x-ui.modal.confirm wire:model="confirmingAuditoriasDeletion">
        <x-slot name="title"> {{ __('Delete') }} </x-slot>

        <x-slot name="content"> {{ __('Are you sure?') }} </x-slot>

        <x-slot name="footer">
            <x-ui.button
                wire:click="$toggle('confirmingAuditoriasDeletion')"
                wire:loading.attr="disabled"
            >
                {{ __('Cancel') }}
            </x-ui.button>

            <x-ui.button.danger
                class="ml-3"
                wire:click="deleteAuditorias({{ $deletingAuditorias }})"
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
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('clave_de_accion')"
                    >{{ __('crud.allAuditorias.inputs.clave_de_accion.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-detailCrud wire:click="sortBy('entrega')"
                    >{{ __('crud.allAuditorias.inputs.entrega.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('auditoria_especial')"
                    >{{ __('crud.allAuditorias.inputs.auditoria_especial.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('tipo_de_auditoria')"
                    >{{ __('crud.allAuditorias.inputs.tipo_de_auditoria.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('siglas_auditoria_especial')"
                    >{{
                    __('crud.allAuditorias.inputs.siglas_auditoria_especial.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('siglas_dg_uaa')"
                    >{{ __('crud.allAuditorias.inputs.siglas_dg_uaa.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header for-detailCrud wire:click="sortBy('titulo')"
                    >{{ __('crud.allAuditorias.inputs.titulo.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('ente_fiscalizado')"
                    >{{ __('crud.allAuditorias.inputs.ente_fiscalizado.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('numero_de_auditoria')"
                    >{{
                    __('crud.allAuditorias.inputs.numero_de_auditoria.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('clave_accion')"
                    >{{ __('crud.allAuditorias.inputs.clave_accion.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('siglas_tipo_accion')"
                    >{{ __('crud.allAuditorias.inputs.siglas_tipo_accion.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('dgseg_ef')"
                    >{{ __('crud.allAuditorias.inputs.dgseg_ef.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('nombre_director_general')"
                    >{{
                    __('crud.allAuditorias.inputs.nombre_director_general.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('direccion_de_area')"
                    >{{ __('crud.allAuditorias.inputs.direccion_de_area.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('nombre_director_de_area')"
                    >{{
                    __('crud.allAuditorias.inputs.nombre_director_de_area.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('sub_direccion_de_area')"
                    >{{
                    __('crud.allAuditorias.inputs.sub_direccion_de_area.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('nombre_sub_director_de_area')"
                    >{{
                    __('crud.allAuditorias.inputs.nombre_sub_director_de_area.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.header
                    for-detailCrud
                    wire:click="sortBy('jefe_de_departamento')"
                    >{{
                    __('crud.allAuditorias.inputs.jefe_de_departamento.label')
                    }}</x-ui.table.header
                >
                <x-ui.table.action-header>Actions</x-ui.table.action-header>
            </x-slot>

            <x-slot name="body">
                @forelse ($detailAllAuditorias as $auditorias)
                <x-ui.table.row wire:loading.class.delay="opacity-75">
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->clave_de_accion }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->entrega }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->auditoria_especial
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->tipo_de_auditoria }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->siglas_auditoria_especial
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->siglas_dg_uaa }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->titulo }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->ente_fiscalizado }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->numero_de_auditoria
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->clave_accion }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->siglas_tipo_accion
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->dgseg_ef }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->nombre_director_general
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->direccion_de_area }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->nombre_director_de_area
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->sub_direccion_de_area
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->nombre_sub_director_de_area
                        }}</x-ui.table.column
                    >
                    <x-ui.table.column for-detailCrud
                        >{{ $auditorias->jefe_de_departamento
                        }}</x-ui.table.column
                    >
                    <x-ui.table.action-column>
                        @can('update', $auditorias)
                        <x-ui.action
                            wire:click="editAuditorias({{ $auditorias->id }})"
                            >Edit</x-ui.action
                        >
                        @endcan @can('delete', $auditorias)
                        <x-ui.action.danger
                            wire:click="confirmAuditoriasDeletion({{ $auditorias->id }})"
                            >Delete</x-ui.action.danger
                        >
                        @endcan
                    </x-ui.table.action-column>
                </x-ui.table.row>
                @empty
                <x-ui.table.row>
                    <x-ui.table.column colspan="19"
                        >No {{ __('crud.allAuditorias.collectionTitle') }} found.</x-ui.table.column
                    >
                </x-ui.table.row>
                @endforelse
            </x-slot>
        </x-ui.table>

        <div class="mt-2">{{ $detailAllAuditorias->links() }}</div>
    </x-ui.container.table>
</div>
