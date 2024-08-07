<?php

namespace App\Filament\Resources\Panel\CatTipoDeAuditoriaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\CatTipoDeAuditoriaResource;

class EditCatTipoDeAuditoria extends EditRecord
{
    protected static string $resource = CatTipoDeAuditoriaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
