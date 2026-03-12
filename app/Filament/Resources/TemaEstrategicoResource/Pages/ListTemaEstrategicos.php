<?php

namespace App\Filament\Resources\TemaEstrategicoResource\Pages;

use App\Filament\Resources\TemaEstrategicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemaEstrategicos extends ListRecords
{
    protected static string $resource = TemaEstrategicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
