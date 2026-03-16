<?php

namespace App\Filament\Resources\EstadoContratoResource\Pages;

use App\Filament\Resources\EstadoContratoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstadoContratos extends ListRecords
{
    protected static string $resource = EstadoContratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
