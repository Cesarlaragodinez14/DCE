<?php

namespace App\Filament\Resources\Panel\CatCuentaPublicaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\CatCuentaPublicaResource;

class ViewCatCuentaPublica extends ViewRecord
{
    protected static string $resource = CatCuentaPublicaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
