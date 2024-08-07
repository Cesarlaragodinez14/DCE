<?php

namespace App\Filament\Resources\Panel\CatUaaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\CatUaaResource;

class ListCatUaas extends ListRecords
{
    protected static string $resource = CatUaaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
