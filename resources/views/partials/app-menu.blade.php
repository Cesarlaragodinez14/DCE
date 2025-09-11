<div class="hidden sm:flex sm:items-center sm:ml-6">
    @role('admin')
    <div class="ml-3 relative">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                    >
                        {{ __('navigation.home') }}

                        <svg
                            class="ml-2 -mr-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link> Sin Secciones </x-dropdown-link>
            </x-slot>
        </x-dropdown>
    </div>
    <div class="ml-3 relative">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                    >
                        {{ __('navigation.apps') }}

                        <svg
                            class="ml-2 -mr-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                @can('view-any', App\Models\CatDgsegEf::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('dashboard.cat-dgseg-efs.index') }}"
                >
                    {{ __('navigation.cat_dgseg_efs') }}
                </x-dropdown-link>
                @endcan @can('view-any', App\Models\CatClaveAccion::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('dashboard.cat-clave-accions.index') }}"
                >
                    {{ __('navigation.cat_clave_accions') }}
                </x-dropdown-link>
                @endcan @can('view-any', App\Models\CatEnteDeLaAccion::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('dashboard.cat-ente-de-la-accions.index') }}"
                >
                    {{ __('navigation.cat_ente_de_la_accions') }}
                </x-dropdown-link>
                @endcan @can('view-any', App\Models\CatEnteFiscalizado::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('dashboard.cat-ente-fiscalizados.index') }}"
                >
                    {{ __('navigation.cat_ente_fiscalizados') }}
                </x-dropdown-link>
                @endcan @can('view-any',
                App\Models\CatSiglasAuditoriaEspecial::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('dashboard.cat-siglas-auditoria-especials.index') }}"
                >
                    {{ __('navigation.cat_siglas_auditoria_especials') }}
                </x-dropdown-link>
                @endcan @can('view-any', App\Models\CatTipoDeAuditoria::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('dashboard.cat-tipo-de-auditorias.index') }}"
                >
                    {{ __('navigation.cat_tipo_de_auditorias') }}
                </x-dropdown-link>
                @endcan @can('view-any', App\Models\CatUaa::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('dashboard.cat-uaas.index') }}"
                >
                    {{ __('navigation.cat_uaas') }}
                </x-dropdown-link>
                @endcan @can('view-any', App\Models\CatAuditoriaEspecial::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('dashboard.cat-auditoria-especials.index') }}"
                >
                    {{ __('navigation.cat_auditoria_especials') }}
                </x-dropdown-link>
                @endcan @can('view-any', App\Models\CatEntrega::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('dashboard.cat-entregas.index') }}"
                >
                    {{ __('navigation.cat_entregas') }}
                </x-dropdown-link>
                @endcan @can('view-any', App\Models\CatCuentaPublica::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('dashboard.cat-cuenta-publicas.index') }}"
                >
                    {{ __('navigation.cat_cuenta_publicas') }}
                </x-dropdown-link>
                @endcan @can('view-any', App\Models\CatSiglasTipoAccion::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('dashboard.cat-siglas-tipo-acciones.index') }}"
                >
                    {{ __('navigation.cat_siglas_tipo_acciones') }}
                </x-dropdown-link>
                @endcan
            </x-slot>
        </x-dropdown>
    </div>
    @endrole
    <div class="ml-3 relative">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                    >
                        Expedientes de Acción

                        <svg
                            class="ml-2 -mr-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                @can('view-any', App\Models\Auditorias::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('dashboard.all-auditorias.index') }}"
                >
                    {{ __('Expedientes') }}
                </x-dropdown-link>
                @endcan
                @can('view-any', App\Models\Auditorias::class)
                <x-dropdown-link
                    wire:navigate
                    href="{{ route('auditorias.resumen') }}"
                >
                    Resumen de Auditorías
                </x-dropdown-link>
                @endcan 
            </x-slot>
        </x-dropdown>
    </div>
</div>
