<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use App\Models\Provincia;

// Widgets originales del Director
use App\Filament\Resources\ExpedienteResource\Widgets\ExpedientesStats;
use App\Filament\Resources\ExpedienteResource\Widgets\TiemposPromedioChart;
use App\Filament\Resources\ExpedienteResource\Widgets\EstadoExpedientesChart;

// Widgets nuevos
use App\Filament\Widgets\StatsGestionArea;
use App\Filament\Widgets\ExpedientesPorTecnicoChart;
use App\Filament\Widgets\ExpedientesPorTemaChart;
use App\Filament\Widgets\ComparativoAnualChart;
use App\Filament\Widgets\MontosPorProvinciaChart; // <--- Importación nueva

class Estadisticas extends Page
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'Estadísticas';
    protected static ?string $title = 'Tablero de Control Estadístico';
    protected static string $view = 'filament.pages.estadisticas';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['Director', 'Jefe de Área']);
    }

    public function getColumns(): int | string | array
    {
        return 2;
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
                ->hidden(fn() => auth()->user()->hasRole('Jefe de Área'))
        ]);
    }

    protected function getHeaderWidgets(): array
    {
        $user = auth()->user();

        // Vista para el Jefe de Área (Hernán)
        if ($user->hasRole('Jefe de Área')) {
            return [
                StatsGestionArea::class,
                ExpedientesPorTecnicoChart::class,
                ExpedientesPorTemaChart::class,
                MontosPorProvinciaChart::class, // <--- Agregado aquí
            ];
        }

        // Vista para el Director
        return [
            StatsGestionArea::class,
            ComparativoAnualChart::class,
            ExpedientesStats::class,
            EstadoExpedientesChart::class,
            TiemposPromedioChart::class,
            ExpedientesPorTecnicoChart::class,
            MontosPorProvinciaChart::class, // El Director también podrá verlo
        ];
    }
}
