<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.link href="/dashboard"
            >Dashboard</x-ui.breadcrumbs.link
        >
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link
            href="{{ route('dashboard.all-auditorias.index') }}"
            >{{ __('crud.allAuditorias.collectionTitle')
            }}</x-ui.breadcrumbs.link
        >
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link active
            >Edit {{ __('crud.allAuditorias.itemTitle')
            }}</x-ui.breadcrumbs.link
        >
    </x-ui.breadcrumbs>

    <x-ui.toast on="saved"> Auditorias saved successfully. </x-ui.toast>

    <div class="w-full text-gray-500 text-lg font-semibold py-4 uppercase">
        <h1>Edit {{ __('crud.allAuditorias.itemTitle') }}</h1>
    </div>

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
                        <option value="">Seleccionar</option>
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
                        <option value="">Seleccionar</option>
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
                        <option value="">Seleccionar</option>
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
                        <option value="">Seleccionar</option>
                        @foreach ($catSiglasAuditoriaEspecials as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-ui.input.select>
                    <x-ui.input.error for="form.siglas_auditoria_especial" />
                </div>

                <div class="w-full">
                    <x-ui.label for="siglas_dg_uaa"
                        >{{ __('crud.allAuditorias.inputs.siglas_dg_uaa.label')
                        }}</x-ui.label
                    >
                    <x-ui.input.select
                        wire:model="form.siglas_dg_uaa"
                        name="siglas_dg_uaa"
                        id="siglas_dg_uaa"
                        class="w-full"
                    >
                        <option value="">Seleccionar</option>
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
                        <option value="">Seleccionar</option>
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
                    <x-ui.label for="ente_de_la_accion"
                        >{{
                        __('crud.allAuditorias.inputs.ente_de_la_accion.label')
                        }}</x-ui.label
                    >
                    <x-ui.input.select
                        wire:model="form.ente_de_la_accion"
                        name="ente_de_la_accion"
                        id="ente_de_la_accion"
                        class="w-full"
                    >
                        <option value="">Seleccionar</option>
                        @foreach ($catEnteDeLaAccions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-ui.input.select>
                    <x-ui.input.error for="form.ente_de_la_accion" />
                </div>

                <div class="w-full">
                    <x-ui.label for="clave_accion"
                        >{{ __('crud.allAuditorias.inputs.clave_accion.label')
                        }}</x-ui.label
                    >
                    <x-ui.input.select
                        wire:model="form.clave_accion"
                        name="clave_accion"
                        id="clave_accion"
                        class="w-full"
                    >
                        <option value="">Seleccionar</option>
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
                        <option value="">Seleccionar</option>
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
                        <option value="">Seleccionar</option>
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
                    <x-ui.input.error for="form.nombre_sub_director_de_area" />
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

                <div class="w-full">
                    <x-ui.label for="cuenta_publica"
                        >{{ __('crud.allAuditorias.inputs.cuenta_publica.label')
                        }}</x-ui.label
                    >
                    <x-ui.input.select
                        wire:model="form.cuenta_publica"
                        name="cuenta_publica"
                        id="cuenta_publica"
                        class="w-full"
                    >
                        <option value="">Seleccionar</option>
                        @foreach ($catCuentaPublicas as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-ui.input.select>
                    <x-ui.input.error for="form.cuenta_publica" />
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
</div>
