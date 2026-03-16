<?php

namespace App\Filament\Resources\ExpedienteResource\Pages;

use App\Filament\Resources\ExpedienteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpediente extends CreateRecord
{
    protected static string $resource = ExpedienteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Le inyectamos el ID del usuario logueado antes de guardar en la base de datos
        $data['user_id'] = auth()->id();
        
        // De paso, ya le clavamos su área y dirección para los reportes futuros
        $data['area_id'] = auth()->user()->area_id;
        $data['direccion_id'] = auth()->user()->direccion_id;
    
        return $data;
    }
}
