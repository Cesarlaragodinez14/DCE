<?php

namespace App\Filament\Resources\Panel\CatDgsegEfResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\CatDgsegEfResource;

class ViewCatDgsegEf extends ViewRecord
{
    protected static string $resource = CatDgsegEfResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
