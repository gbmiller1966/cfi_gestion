<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Resources\ExpedienteResource\Widgets\ExpedienteTableWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Expedientes';
    protected static ?string $title = 'Gestión de Expedientes';
    protected static ?int $navigationSort = 1;
    // Eliminamos los filtros y los otros widgets para que solo quede la tabla
    public function getWidgets(): array
    {
        return [
            ExpedienteTableWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1; // Una sola columna para que la tabla use todo el ancho
    }

    public function getTitle(): string
    {
        return auth()->user()->hasRole('Director') 
            ? 'Gestión de Expedientes' 
            : 'Escritorio';
    }
}