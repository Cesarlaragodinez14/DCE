<?php

namespace App\Filament\Resources\Panel\CatEntregaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\CatEntregaResource;

class ListCatEntregas extends ListRecords
{
    protected static string $resource = CatEntregaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
