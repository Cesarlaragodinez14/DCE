<?php

namespace App\Filament\Resources\Panel\CatEnteDeLaAccionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\CatEnteDeLaAccionResource;

class EditCatEnteDeLaAccion extends EditRecord
{
    protected static string $resource = CatEnteDeLaAccionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
