<?php

namespace App\Filament\Resources\Panel\CatCuentaPublicaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\CatCuentaPublicaResource;

class ListCatCuentaPublicas extends ListRecords
{
    protected static string $resource = CatCuentaPublicaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
