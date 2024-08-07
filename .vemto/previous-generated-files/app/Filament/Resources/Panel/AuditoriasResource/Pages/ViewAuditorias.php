<?php

namespace App\Filament\Resources\Panel\AuditoriasResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\AuditoriasResource;

class ViewAuditorias extends ViewRecord
{
    protected static string $resource = AuditoriasResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
