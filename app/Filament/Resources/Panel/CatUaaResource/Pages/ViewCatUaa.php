<?php

namespace App\Filament\Resources\Panel\CatUaaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\CatUaaResource;

class ViewCatUaa extends ViewRecord
{
    protected static string $resource = CatUaaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
