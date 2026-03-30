<?php

namespace App\Filament\Resources\ContraparteResource\Pages;

use App\Filament\Resources\ContraparteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContraparte extends EditRecord
{
    protected static string $resource = ContraparteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
