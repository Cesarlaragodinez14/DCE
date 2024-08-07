<?php

namespace App\Filament\Resources\Panel\CatTipoDeAuditoriaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\CatTipoDeAuditoriaResource;

class ListCatTipoDeAuditorias extends ListRecords
{
    protected static string $resource = CatTipoDeAuditoriaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
