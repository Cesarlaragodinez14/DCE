<?php

namespace App\Filament\Resources\Panel\CatClaveAccionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\CatClaveAccionResource;

class ListCatClaveAccions extends ListRecords
{
    protected static string $resource = CatClaveAccionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
