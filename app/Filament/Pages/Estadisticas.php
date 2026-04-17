<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use App\Models\Provincia;
use App\Filament\Resources\ExpedienteResource\Widgets\ExpedientesStats;
use App\Filament\Resources\ExpedienteResource\Widgets\TiemposPromedioChart;
use App\Filament\Resources\ExpedienteResource\Widgets\EstadoExpedientesChart;

class Estadisticas extends Page
{
    use HasFiltersForm; // Mantenemos los filtros aquí para los gráficos

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'Estadísticas';
    protected static ?string $title = 'Tablero de Control Estadístico';
    protected static string $view = 'filament.pages.estadisticas'; // Crearemos esta vista simple
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('Director');
    }

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Section::make('Filtros')
                ->schema([
                    Select::make('provincia_id')
                        ->label('Provincia')
                        ->options(Provincia::pluck('provincia', 'id'))
                        ->searchable()
                        ->live()
                        ->placeholder('Todas las provincias'),
                ])
        ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\ComparativoAnualChart::class,
            ExpedientesStats::class,
            EstadoExpedientesChart::class,
            TiemposPromedioChart::class,
        ];
    }
}