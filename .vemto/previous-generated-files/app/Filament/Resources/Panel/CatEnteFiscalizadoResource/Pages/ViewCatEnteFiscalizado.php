<?php

namespace App\Filament\Resources\Panel\CatEnteFiscalizadoResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\CatEnteFiscalizadoResource;

class ViewCatEnteFiscalizado extends ViewRecord
{
    protected static string $resource = CatEnteFiscalizadoResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
