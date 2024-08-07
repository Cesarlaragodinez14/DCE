<?php

namespace App\Filament\Resources\Panel\CatEntregaResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\CatEntregaResource;

class EditCatEntrega extends EditRecord
{
    protected static string $resource = CatEntregaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
