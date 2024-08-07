<?php

namespace App\Filament\Resources\Panel\CatSiglasAuditoriaEspecialResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\CatSiglasAuditoriaEspecialResource;

class ListCatSiglasAuditoriaEspecials extends ListRecords
{
    protected static string $resource = CatSiglasAuditoriaEspecialResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
