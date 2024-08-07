<?php

namespace App\Filament\Resources\Panel\CatEnteFiscalizadoResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\CatEnteFiscalizadoResource;

class ListCatEnteFiscalizados extends ListRecords
{
    protected static string $resource = CatEnteFiscalizadoResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
