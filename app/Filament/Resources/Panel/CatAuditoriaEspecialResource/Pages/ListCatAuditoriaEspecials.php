<?php

namespace App\Filament\Resources\Panel\CatAuditoriaEspecialResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\CatAuditoriaEspecialResource;

class ListCatAuditoriaEspecials extends ListRecords
{
    protected static string $resource = CatAuditoriaEspecialResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
