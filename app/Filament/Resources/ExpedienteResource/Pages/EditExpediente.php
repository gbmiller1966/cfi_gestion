<?php

namespace App\Filament\Resources\ExpedienteResource\Pages;

use App\Filament\Resources\ExpedienteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpediente extends EditRecord
{
    protected static string $resource = ExpedienteResource::class;

    protected function getRedirectUrl(): string
    {
        // 💡 Al terminar de editar, vuelve al listado (Escritorio)
        return url('/tecnico');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar') // Por si querés cambiar el texto
            ->color('gray')      // Un color neutro para no confundir con "Guardar"
            ->url(url('/tecnico')); // Lo manda al Escritorio
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->visible(fn() => auth()->user()->hasRole('Admin')),
        ];
    }

    public function getBreadcrumbs(): array
{
    return [
        // 💡 Cambiamos el primer enlace para que apunte al Escritorio (Dashboard)
        url('/tecnico') => 'Escritorio',
        
        // El último elemento es la página actual (no lleva link)
        'Ver' => 'Editar Expediente', 
    ];
}
}
