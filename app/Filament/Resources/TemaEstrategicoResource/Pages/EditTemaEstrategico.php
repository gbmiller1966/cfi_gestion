<?php

namespace App\Filament\Resources\TemaEstrategicoResource\Pages;

use App\Filament\Resources\TemaEstrategicoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemaEstrategico extends EditRecord
{
    protected static string $resource = TemaEstrategicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
