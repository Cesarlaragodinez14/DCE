<?php

namespace App\Filament\Resources\Panel\CatSiglasAuditoriaEspecialResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\CatSiglasAuditoriaEspecialResource;

class EditCatSiglasAuditoriaEspecial extends EditRecord
{
    protected static string $resource = CatSiglasAuditoriaEspecialResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
