<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use App\Models\Provincia;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    /**
     * Registramos los widgets que deben aparecer en este Dashboard.
     */
    public function getWidgets(): array
    {
        return [
            \App\Filament\Resources\ExpedienteResource\Widgets\ExpedientesStats::class,
            \App\Filament\Resources\ExpedienteResource\Widgets\TiemposPromedioChart::class,
            \App\Filament\Resources\ExpedienteResource\Widgets\ExpedienteTableWidget::class,
        ];
    }

    /**
     * Definimos cuántas columnas de ancho tiene el dashboard (por defecto es 2).
     */
    public function getColumns(): int | string | array
    {
        return 2;
    }

    /**
     * Formulario de filtros superior.
     */
    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filtros de Gestión')
                    ->description('Seleccione una provincia para actualizar las estadísticas y promedios.')
                    ->schema([
                        Select::make('provincia_id')
                            ->label('Provincia')
                            ->options(Provincia::pluck('provincia', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable() 
                            ->placeholder('Todas las provincias (CFI Total)'),
                    ])
                    ->columns(1)
                    ->visible(fn () => auth()->user()->hasRole('Director')),
            ]);
    }

    /**
     * Título dinámico de la página.
     */
    public function getTitle(): string
    {
        return auth()->user()->hasRole('Director') 
            ? 'Panel de Control de Gestión' 
            : 'Escritorio';
    }
}