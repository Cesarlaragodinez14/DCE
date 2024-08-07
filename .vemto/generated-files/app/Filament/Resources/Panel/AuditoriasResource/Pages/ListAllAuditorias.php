<?php

namespace App\Filament\Resources\Panel\AuditoriasResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\AuditoriasResource;

class ListAllAuditorias extends ListRecords
{
    protected static string $resource = AuditoriasResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
