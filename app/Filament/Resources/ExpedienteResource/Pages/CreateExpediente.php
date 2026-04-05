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
    
    protected function getRedirectUrl(): string
    {
        // 💡 Al terminar de crear, vuelve al listado (Escritorio)
        return url('/tecnico');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar') // Por si querés cambiar el texto
            ->color('gray')      // Un color neutro para no confundir con "Guardar"
            ->url(url('/tecnico')); // Lo manda al Escritorio
    }

    public function getBreadcrumbs(): array
    {
        return [
            // 💡 Cambiamos el primer enlace para que apunte al Escritorio (Dashboard)
            url('/tecnico') => 'Escritorio',
            
            // El último elemento es la página actual (no lleva link)
            'Ver' => 'Nuevo Expediente', 
        ];
    }
}
