<?php

namespace App\Filament\Resources\Panel\CatEnteDeLaAccionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\CatEnteDeLaAccionResource;

class ListCatEnteDeLaAccions extends ListRecords
{
    protected static string $resource = CatEnteDeLaAccionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
