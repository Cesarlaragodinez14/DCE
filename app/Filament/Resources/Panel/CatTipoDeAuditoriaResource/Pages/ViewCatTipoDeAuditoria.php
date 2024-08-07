<?php

namespace App\Filament\Resources\Panel\CatTipoDeAuditoriaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\CatTipoDeAuditoriaResource;

class ViewCatTipoDeAuditoria extends ViewRecord
{
    protected static string $resource = CatTipoDeAuditoriaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
