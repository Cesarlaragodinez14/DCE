<?php

namespace App\Filament\Resources\Panel\CatAuditoriaEspecialResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\CatAuditoriaEspecialResource;

class ViewCatAuditoriaEspecial extends ViewRecord
{
    protected static string $resource = CatAuditoriaEspecialResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
