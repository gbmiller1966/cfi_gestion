<?php

namespace App\Filament\Resources\InformeMaestroResource\Pages;

use App\Filament\Resources\InformeMaestroResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInformeMaestros extends ListRecords
{
    protected static string $resource = InformeMaestroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
