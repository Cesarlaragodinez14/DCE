<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Histórico de observaciones en la revisión de los Expediente de acción') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <x-ui.breadcrumbs>
                <x-ui.breadcrumbs.link href="/dashboard">Dashboard</x-ui.breadcrumbs.link> 
                <x-ui.breadcrumbs.separator />
                <x-ui.breadcrumbs.link active>{{ __('Histórico de observaciones en la revisión de los Expediente de acción') }}</x-ui.breadcrumbs.link>
            </x-ui.breadcrumbs>
            @livewire('auditorias-historico')
        </div>
    </div>
</x-app-layout>
