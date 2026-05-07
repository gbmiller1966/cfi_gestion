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
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'Estadísticas';
    protected static ?string $title = 'Tablero de Control Estadístico';
    protected static string $view = 'filament.pages.estadisticas';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        // 💡 AHORA: Ambos roles pueden ver la pestaña
        return auth()->user()->hasAnyRole(['Director', 'Jefe de Área']);
    }

    public function filtersForm(Form $form): Form
    {
        // 💡 Filtros: Los mantenemos, pero podrías ocultarlos para el Jefe si no los necesita
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
                ->hidden(fn() => auth()->user()->hasRole('Jefe de Área')) // Opcional: ocultar filtros si el jefe no los usa
        ]);
    }

    protected function getHeaderWidgets(): array
    {
        $user = auth()->user();

        // 💡 Lógica de visualización de Widgets por Rol
        if ($user->hasRole('Jefe de Área')) {
            return [
                \App\Filament\Widgets\StatsGestionArea::class,          // Los stats simples
                \App\Filament\Widgets\ExpedientesPorTecnicoChart::class, // El gráfico de su equipo
            ];
        }

        // Si es Director, mantiene sus widgets originales + los nuevos si querés
        return [
            \App\Filament\Widgets\StatsGestionArea::class, // También le sirve al Director (verá totales de Dir)
            \App\Filament\Widgets\ComparativoAnualChart::class,
            ExpedientesStats::class,
            EstadoExpedientesChart::class,
            TiemposPromedioChart::class,
            \App\Filament\Widgets\ExpedientesPorTecnicoChart::class, // El director verá a TODOS sus técnicos
        ];
    }
}
