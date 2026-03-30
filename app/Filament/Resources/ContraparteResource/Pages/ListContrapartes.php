<?php

namespace App\Filament\Resources\ContraparteResource\Pages;

use App\Filament\Resources\ContraparteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContrapartes extends ListRecords
{
    protected static string $resource = ContraparteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
