<?php

namespace App\Filament\Resources\Panel\CatEntregaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\CatEntregaResource;

class ViewCatEntrega extends ViewRecord
{
    protected static string $resource = CatEntregaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
