<?php

namespace App\Filament\Resources\Panel\CatEnteFiscalizadoResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\CatEnteFiscalizadoResource;

class EditCatEnteFiscalizado extends EditRecord
{
    protected static string $resource = CatEnteFiscalizadoResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
