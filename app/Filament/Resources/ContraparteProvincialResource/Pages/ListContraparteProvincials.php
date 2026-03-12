<?php

namespace App\Filament\Resources\ContraparteProvincialResource\Pages;

use App\Filament\Resources\ContraparteProvincialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContraparteProvincials extends ListRecords
{
    protected static string $resource = ContraparteProvincialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
