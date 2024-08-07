<?php

namespace App\Filament\Resources\Panel\CatSiglasAuditoriaEspecialResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\CatSiglasAuditoriaEspecialResource;

class ViewCatSiglasAuditoriaEspecial extends ViewRecord
{
    protected static string $resource = CatSiglasAuditoriaEspecialResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
