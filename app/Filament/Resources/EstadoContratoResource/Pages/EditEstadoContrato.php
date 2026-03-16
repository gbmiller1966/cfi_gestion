<?php

namespace App\Filament\Resources\EstadoContratoResource\Pages;

use App\Filament\Resources\EstadoContratoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstadoContrato extends EditRecord
{
    protected static string $resource = EstadoContratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
