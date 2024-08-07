<?php

namespace App\Filament\Resources\Panel\CatEnteDeLaAccionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\CatEnteDeLaAccionResource;

class ViewCatEnteDeLaAccion extends ViewRecord
{
    protected static string $resource = CatEnteDeLaAccionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
