<?php

namespace App\Filament\Resources\DireccionResource\Pages;

use App\Filament\Resources\DireccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDireccion extends EditRecord
{
    protected static string $resource = DireccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
