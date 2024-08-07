@can('view-any', App\Models\CatDgsegEf::class)
<x-responsive-nav-link
    href="{{ route('dashboard.cat-dgseg-efs.index') }}"
    :active="request()->routeIs('dashboard.cat-dgseg-efs.index')"
>
    {{ __('navigation.cat_dgseg_efs') }}
</x-responsive-nav-link>
@endcan @can('view-any', App\Models\CatClaveAccion::class)
<x-responsive-nav-link
    href="{{ route('dashboard.cat-clave-accions.index') }}"
    :active="request()->routeIs('dashboard.cat-clave-accions.index')"
>
    {{ __('navigation.cat_clave_accions') }}
</x-responsive-nav-link>
@endcan @can('view-any', App\Models\CatEnteDeLaAccion::class)
<x-responsive-nav-link
    href="{{ route('dashboard.cat-ente-de-la-accions.index') }}"
    :active="request()->routeIs('dashboard.cat-ente-de-la-accions.index')"
>
    {{ __('navigation.cat_ente_de_la_accions') }}
</x-responsive-nav-link>
@endcan @can('view-any', App\Models\CatEnteFiscalizado::class)
<x-responsive-nav-link
    href="{{ route('dashboard.cat-ente-fiscalizados.index') }}"
    :active="request()->routeIs('dashboard.cat-ente-fiscalizados.index')"
>
    {{ __('navigation.cat_ente_fiscalizados') }}
</x-responsive-nav-link>
@endcan @can('view-any', App\Models\CatSiglasAuditoriaEspecial::class)
<x-responsive-nav-link
    href="{{ route('dashboard.cat-siglas-auditoria-especials.index') }}"
    :active="request()->routeIs('dashboard.cat-siglas-auditoria-especials.index')"
>
    {{ __('navigation.cat_siglas_auditoria_especials') }}
</x-responsive-nav-link>
@endcan @can('view-any', App\Models\CatTipoDeAuditoria::class)
<x-responsive-nav-link
    href="{{ route('dashboard.cat-tipo-de-auditorias.index') }}"
    :active="request()->routeIs('dashboard.cat-tipo-de-auditorias.index')"
>
    {{ __('navigation.cat_tipo_de_auditorias') }}
</x-responsive-nav-link>
@endcan @can('view-any', App\Models\CatUaa::class)
<x-responsive-nav-link
    href="{{ route('dashboard.cat-uaas.index') }}"
    :active="request()->routeIs('dashboard.cat-uaas.index')"
>
    {{ __('navigation.cat_uaas') }}
</x-responsive-nav-link>
@endcan @can('view-any', App\Models\CatAuditoriaEspecial::class)
<x-responsive-nav-link
    href="{{ route('dashboard.cat-auditoria-especials.index') }}"
    :active="request()->routeIs('dashboard.cat-auditoria-especials.index')"
>
    {{ __('navigation.cat_auditoria_especials') }}
</x-responsive-nav-link>
@endcan @can('view-any', App\Models\CatEntrega::class)
<x-responsive-nav-link
    href="{{ route('dashboard.cat-entregas.index') }}"
    :active="request()->routeIs('dashboard.cat-entregas.index')"
>
    {{ __('navigation.cat_entregas') }}
</x-responsive-nav-link>
@endcan @can('view-any', App\Models\CatCuentaPublica::class)
<x-responsive-nav-link
    href="{{ route('dashboard.cat-cuenta-publicas.index') }}"
    :active="request()->routeIs('dashboard.cat-cuenta-publicas.index')"
>
    {{ __('navigation.cat_cuenta_publicas') }}
</x-responsive-nav-link>
@endcan @can('view-any', App\Models\CatSiglasTipoAccion::class)
<x-responsive-nav-link
    href="{{ route('dashboard.cat-siglas-tipo-acciones.index') }}"
    :active="request()->routeIs('dashboard.cat-siglas-tipo-acciones.index')"
>
    {{ __('navigation.cat_siglas_tipo_acciones') }}
</x-responsive-nav-link>
@endcan
