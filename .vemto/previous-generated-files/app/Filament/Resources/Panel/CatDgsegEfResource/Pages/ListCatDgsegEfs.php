<?php

namespace App\Filament\Resources\Panel\CatDgsegEfResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\CatDgsegEfResource;

class ListCatDgsegEfs extends ListRecords
{
    protected static string $resource = CatDgsegEfResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
