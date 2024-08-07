<?php

namespace App\Filament\Resources\Panel\CatDgsegEfResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\CatDgsegEfResource;

class EditCatDgsegEf extends EditRecord
{
    protected static string $resource = CatDgsegEfResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
