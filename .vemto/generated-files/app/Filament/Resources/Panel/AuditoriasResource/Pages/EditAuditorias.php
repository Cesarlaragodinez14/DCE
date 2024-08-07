<?php

namespace App\Filament\Resources\Panel\AuditoriasResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\AuditoriasResource;

class EditAuditorias extends EditRecord
{
    protected static string $resource = AuditoriasResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
