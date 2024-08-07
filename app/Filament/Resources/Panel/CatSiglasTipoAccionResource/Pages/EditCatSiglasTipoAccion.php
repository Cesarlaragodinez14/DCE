<?php

namespace App\Filament\Resources\Panel\CatSiglasTipoAccionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\CatSiglasTipoAccionResource;

class EditCatSiglasTipoAccion extends EditRecord
{
    protected static string $resource = CatSiglasTipoAccionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
