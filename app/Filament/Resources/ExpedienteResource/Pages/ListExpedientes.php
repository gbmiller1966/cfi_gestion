<?php

namespace App\Filament\Resources\ExpedienteResource\Pages;

use App\Filament\Resources\ExpedienteResource;
use Filament\Resources\Pages\ListRecords;

class ListExpedientes extends ListRecords
{
    protected static string $resource = ExpedienteResource::class;

    /**
     * Ancho de pantalla:
     * Si es Director -> 'full' (pantalla completa)
     * Si es otro -> null (el estándar de Filament)
     */
    public function getMaxContentWidth(): ?string
    {
        return auth()->user()->hasRole('Director') ? 'full' : null;
    }

    /**
     * Botones de arriba (Header):
     * Si es Director -> nada.
     * Si no -> lo que venga por defecto (Botón Crear).
     */
    protected function getHeaderActions(): array
    {
        if (auth()->user()->hasRole('Director')) {
            return [];
        }

        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }

    public function getHeaderWidgets(): array
{
    // Si es Director, inyectamos un poquito de CSS para esconder el menú
    if (auth()->user()->hasRole('Director')) {
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            'panels::head.end',
            fn () => new \Illuminate\Support\HtmlString("
                <style>
                    .fi-sidebar { display: none !important; }
                    .fi-main-ctn { margin-left: 0 !important; }
                    .fi-topbar-start { display: none !important; } /* Oculta el botón de hamburguesa */
                </style>
            ")
        );
    }

    return [];
}
}
