<?php

namespace App\Filament\Resources\Panel\CatSiglasTipoAccionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\CatSiglasTipoAccionResource;

class ViewCatSiglasTipoAccion extends ViewRecord
{
    protected static string $resource = CatSiglasTipoAccionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
