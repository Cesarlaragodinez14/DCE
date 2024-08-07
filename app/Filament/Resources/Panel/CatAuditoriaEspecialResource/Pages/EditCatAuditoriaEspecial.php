<?php

namespace App\Filament\Resources\Panel\CatAuditoriaEspecialResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\CatAuditoriaEspecialResource;

class EditCatAuditoriaEspecial extends EditRecord
{
    protected static string $resource = CatAuditoriaEspecialResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
