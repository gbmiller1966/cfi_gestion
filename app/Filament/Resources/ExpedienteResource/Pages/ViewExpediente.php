<?php

namespace App\Filament\Resources\ExpedienteResource\Pages;

use App\Filament\Resources\ExpedienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewExpediente extends ViewRecord
{
    protected static string $resource = ExpedienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 💡 Agregamos el botón de Editar en la parte superior derecha
            Actions\EditAction::make()
                ->label('Editar Expediente')
                ->icon('heroicon-m-pencil-square')
                ->color('primary')
                ->visible(fn () => auth()->user()->hasRole('Técnico')),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 💡 Cargamos las relaciones para que el Placeholder pueda leer el texto y no el ID
        $this->record->load(['estado', 'tipo', 'provincia', 'localidad', 'tema', 'asignacion']);

        return $data;
    }

    public function getBreadcrumbs(): array
    {
        $user = auth()->user();
        
        // 💡 Definimos la ruta base según el rol
        // Si es Director, va al /admin (donde está su Dashboard)
        // Si no, va al /tecnico
        $dashboardUrl = $user->hasRole('Director') ? url('/admin') : url('/tecnico');

        return [
            $dashboardUrl => 'Escritorio',
            'Ver' => 'Ver Expediente', 
        ];
    }
}
