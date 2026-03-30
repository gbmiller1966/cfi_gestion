<?php

namespace App\Filament\Resources\InformeResource\Pages;

use App\Filament\Resources\InformeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInforme extends EditRecord
{
    protected static string $resource = InformeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
