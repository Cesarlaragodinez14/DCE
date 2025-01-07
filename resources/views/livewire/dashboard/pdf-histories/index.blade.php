<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-4">
    <x-ui.breadcrumbs>
        <x-ui.breadcrumbs.link href="/dashboard">
            Dashboard
        </x-ui.breadcrumbs.link>
        <x-ui.breadcrumbs.separator />
        <x-ui.breadcrumbs.link active>
            Histórico de PDFs
        </x-ui.breadcrumbs.link>
    </x-ui.breadcrumbs> 

    @if(session()->has('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center py-4">
        <div class="flex space-x-4">
            <x-ui.input
                wire:model.debounce.300ms="search"
                type="text"
                placeholder="Buscar por Clave de Acción, Cuenta Pública o Entrega..."
            />
        </div>

        <div class="flex space-x-4">
            <a href="{{ route('dashboard.all-auditorias.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Volver a Auditorías
            </a>
        </div>
    </div>

    {{-- Index Table --}}
    <x-ui.container.table>
        <x-ui.table>
            <x-slot name="head">
                <x-ui.table.header wire:click="sortBy('clave_de_accion')" class="cursor-pointer">
                    Clave de Acción
                    @if($sortField === 'clave_de_accion')
                        @if($sortDirection === 'asc')
                            &uarr;
                        @else
                            &darr;
                        @endif
                    @endif
                </x-ui.table.header>
            
                <x-ui.table.header wire:click="sortBy('cat_entregas.valor')" class="cursor-pointer">
                    Entrega
                    @if($sortField === 'cat_entregas.valor')
                        @if($sortDirection === 'asc')
                            &uarr;
                        @else
                            &darr;
                        @endif
                    @endif
                </x-ui.table.header>
            
                <x-ui.table.header wire:click="sortBy('auditorias.estatus_checklist')" class="cursor-pointer">
                    Estado del Checklist
                    @if($sortField === 'auditorias.estatus_checklist')
                        @if($sortDirection === 'asc')
                            &uarr; 
                        @else
                            &darr;
                        @endif
                    @endif
                </x-ui.table.header>
            
                <x-ui.table.header wire:click="sortBy('cat_uaa.valor')" class="cursor-pointer">
                    Siglas DG UAA
                    @if($sortField === 'cat_uaa.valor')
                        @if($sortDirection === 'asc')
                            &uarr;
                        @else
                            &darr;
                        @endif
                    @endif
                </x-ui.table.header>
            
                <x-ui.table.header wire:click="sortBy('cat_dgseg_ef.valor')" class="cursor-pointer">
                    DG DGSEGxEF
                    @if($sortField === 'cat_dgseg_ef.valor')
                        @if($sortDirection === 'asc')
                            &uarr;
                        @else
                            &darr;
                        @endif
                    @endif
                </x-ui.table.header>
            
                <x-ui.table.header wire:click="sortBy('auditorias.titulo')" class="cursor-pointer">
                    Título
                    @if($sortField === 'auditorias.titulo')
                        @if($sortDirection === 'asc')
                            &uarr;
                        @else
                            &darr;
                        @endif
                    @endif
                </x-ui.table.header>

                <x-ui.table.header wire:click="sortBy('generated_by')" class="cursor-pointer">
                    Generado Por
                    @if($sortField === 'generated_by')
                        @if($sortDirection === 'asc')
                            &uarr;
                        @else
                            &darr;
                        @endif
                    @endif
                </x-ui.table.header>
                <x-ui.table.header wire:click="sortBy('created_at')" class="cursor-pointer">
                    Fecha de Generación
                    @if($sortField === 'created_at')
                        @if($sortDirection === 'asc')
                            &uarr;
                        @else
                            &darr;
                        @endif
                    @endif
                </x-ui.table.header>
            
                <x-ui.table.header wire:click="sortBy('pdf_path')" class="cursor-pointer">
                    PDF
                    @if($sortField === 'pdf_path')
                        @if($sortDirection === 'asc')
                            &uarr;
                        @else
                            &darr;
                        @endif
                    @endif
                </x-ui.table.header>
            </x-slot>
            
            
            <x-slot name="body">
                @forelse ($pdfHistories as $history)
                    <x-ui.table.row wire:loading.class.delay="opacity-75">
                        <!-- Clave de Acción -->
                        <x-ui.table.column>
                            {{ $history->clave_de_accion }}
                        </x-ui.table.column>
            
                        <!-- Entrega -->
                        <x-ui.table.column>
                            {{ $history->auditoria->catEntrega->valor ?? 'N/A' }}
                        </x-ui.table.column>
            
                        <!-- Estado del Checklist -->
                        <x-ui.table.column>
                            {{ $history->auditoria->estatus_checklist ?? 'N/A' }}
                        </x-ui.table.column>
            
                        <!-- Siglas DG UAA -->
                        <x-ui.table.column>
                            {{ $history->auditoria->catUaa->valor ?? 'N/A' }}
                        </x-ui.table.column>
            
                        <!-- DG DGSEGxEF -->
                        <x-ui.table.column>
                            {{ $history->auditoria->catDgsegEf->valor ?? 'N/A' }}
                        </x-ui.table.column>
            
                        <!-- Título -->
                        <x-ui.table.column>
                            {{ $history->auditoria->titulo ?? 'N/A' }}
                        </x-ui.table.column>

                        <x-ui.table.column>
                            {{ $history->user->name ?? 'N/A' }}
                        </x-ui.table.column>
                        <x-ui.table.column>
                            {{ $history->created_at->format('Y-m-d H:i:s') }}
                        </x-ui.table.column>
            
                        <!-- PDF -->
                        <x-ui.table.column>
                            <a href="{{ Storage::disk('public')->url($history->pdf_path) }}" target="_blank" class="text-blue-500 hover:underline">
                                Ver PDF
                            </a>
                        </x-ui.table.column>
                    </x-ui.table.row>
                @empty
                    <x-ui.table.row>
                        <x-ui.table.column colspan="7">
                            No se encontraron PDFs generados.
                        </x-ui.table.column>
                    </x-ui.table.row>
                @endforelse
            </x-slot>
            
        </x-ui.table>

        <div class="mt-2">{{ $pdfHistories->links() }}</div>
    </x-ui.container.table>
</div>
