<?php

namespace App\Filament\Resources\ExpedienteResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpedientesStats extends BaseWidget
{
protected function getStats(): array
{
    $user = auth()->user();
    $query = \App\Models\Expediente::query();

    if ($user->hasRole('Director')) {
        $query->whereHas('tecnico', fn($q) => $q->where('direccion_id', $user->direccion_id));
    }

    return [
        // Total
        \Filament\Widgets\StatsOverviewWidget\Stat::make('Total Gestión', (clone $query)->count())
            ->description('Expedientes en la dirección')
            ->color('info'),

        // Críticos: Borradores + Ingresados (Lo que está arrancando)
        \Filament\Widgets\StatsOverviewWidget\Stat::make('Pendientes / Ingresados',
            (clone $query)->whereIn('estado', ['Borrador / Sin ingresar', 'Ingresado al CFI'])->count())
            ->description('Pendientes de inicio')
            ->color('danger'),

        // Activos: Análisis + Trámite + Ejecución (Lo que se está trabajando)
        \Filament\Widgets\StatsOverviewWidget\Stat::make('En Gestión Activa',
            (clone $query)->whereIn('estado', ['En análisis', 'En trámite', 'En ejecución'])->count())
            ->description('En proceso actual')
            ->color('warning'),
    ];
}
}
