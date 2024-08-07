<?php

namespace App\Filament\Resources\Panel\CatCuentaPublicaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\CatCuentaPublicaResource;

class EditCatCuentaPublica extends EditRecord
{
    protected static string $resource = CatCuentaPublicaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
