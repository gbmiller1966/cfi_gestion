<?php

namespace App\Filament\Widgets;

use App\Models\Expediente;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsGestionArea extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $query = Expediente::query();

        // Seguridad: Filtro por Área (Jefe) o Dirección (Director)

        if ($user->hasRole('Director')) {
            $query->where('direccion_id', $user->direccion_id);
        } elseif ($user->hasRole('Jefe de Área')) {
            $query->whereHas('tecnico', fn($q) => $q->where('area_id', $user->area_id));
        }

        return [
            Stat::make('Total Expedientes', (clone $query)->count())
                ->description('Total de la unidad')
                ->descriptionIcon('heroicon-m-folder')
                ->color('info'),

            Stat::make('En Ejecución', (clone $query)->where('estado_id', 5)->count())
                ->description('Proyectos con contrato')
                ->descriptionIcon('heroicon-m-play-circle')
                ->color('success'),

            Stat::make('En Trámite', (clone $query)->where('estado_id', 4)->count())
                ->description('Firma de TDR / Compras')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
