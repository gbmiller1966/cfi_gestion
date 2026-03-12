<?php

namespace App\Filament\Resources\InformeMaestroResource\Pages;

use App\Filament\Resources\InformeMaestroResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInformeMaestro extends EditRecord
{
    protected static string $resource = InformeMaestroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
