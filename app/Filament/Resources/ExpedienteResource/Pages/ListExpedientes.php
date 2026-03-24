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
/*         if (auth()->user()->hasRole('Director')) {
            // 1. Inyectamos el Logo (CORREGIDO)
            \Filament\Support\Facades\FilamentView::registerRenderHook(
                'panels::topbar.start',
                fn () => new \Illuminate\Support\HtmlString("
                    <div class='flex items-center h-full ps-4'>
                        <img src='" . asset('images/logo-cfi.png') . "' alt='Logo' style='height: 2.5rem; width: auto;'>
                    </div>
                ") // Cerramos el HtmlString
            ); // Cerramos el registerRenderHook

            // 2. Ajustamos el CSS
            \Filament\Support\Facades\FilamentView::registerRenderHook(
                'panels::head.end',
                fn () => new \Illuminate\Support\HtmlString("
                    <style>
                        .fi-sidebar, .fi-topbar-start button { display: none !important; }
                        .fi-main-ctn { margin-left: 0 !important; }
                        .fi-topbar nav {
                            display: flex !important;
                            justify-content: space-between !important;
                            width: 100% !important;
                            max-width: 100% !important;
                            padding: 0 1rem !important;
                        }
                        .fi-topbar-start, .fi-topbar-end {
                            display: flex !important;
                            align-items: center !important;
                        }
                        .fi-topbar {
                            display: flex !important;
                            height: 4rem !important;
                            background-color: white !important;
                            border-bottom: 1px solid #e5e7eb !important;
                        }
                    </style>
                ")
            );
        } */

        return [
            \App\Filament\Resources\ExpedienteResource\Widgets\ExpedientesStats::class,
            \App\Filament\Resources\ExpedienteResource\Widgets\EstadoExpedientesChart::class,
        ];
    }
}
