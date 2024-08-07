<?php

namespace App\Filament\Resources\Panel\CatSiglasTipoAccionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\CatSiglasTipoAccionResource;

class ListCatSiglasTipoAcciones extends ListRecords
{
    protected static string $resource = CatSiglasTipoAccionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
