<?php

namespace App\Filament\Resources\Panel\CatClaveAccionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\CatClaveAccionResource;

class EditCatClaveAccion extends EditRecord
{
    protected static string $resource = CatClaveAccionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
