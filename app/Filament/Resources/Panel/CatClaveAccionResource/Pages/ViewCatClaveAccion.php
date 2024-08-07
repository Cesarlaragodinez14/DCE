<?php

namespace App\Filament\Resources\Panel\CatClaveAccionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\CatClaveAccionResource;

class ViewCatClaveAccion extends ViewRecord
{
    protected static string $resource = CatClaveAccionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
