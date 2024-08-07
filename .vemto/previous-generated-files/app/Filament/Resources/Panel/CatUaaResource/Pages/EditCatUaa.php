<?php

namespace App\Filament\Resources\Panel\CatUaaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\CatUaaResource;

class EditCatUaa extends EditRecord
{
    protected static string $resource = CatUaaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
