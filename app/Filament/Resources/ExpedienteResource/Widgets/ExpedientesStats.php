<?php

namespace App\Filament\Resources\ExpedienteResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Expediente;

class ExpedientesStats extends BaseWidget
{
    // ESTO ES LO QUE OCULTA EL WIDGET PARA EL TÉCNICO
    public static function canView(): bool
    {
        return auth()->user()->hasRole('Director');
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $query = Expediente::query();

        if ($user->hasRole('Director')) {
            $query->whereHas('tecnico', fn($q) => $q->where('direccion_id', $user->direccion_id));
        }

        return [
            // Total
            Stat::make('Total Gestión', (clone $query)->count())
                ->description('Expedientes en la dirección')
                ->color('info'),

            // Críticos: Borradores + Ingresados
            Stat::make('Pendientes / Ingresados', 
                (clone $query)->whereIn('estado', ['Borrador / Sin ingresar', 'Ingresado al CFI'])->count())
                ->description('Pendientes de inicio')
                ->color('danger'),

            // Activos: Análisis + Trámite + Ejecución
            Stat::make('En Gestión Activa', 
                (clone $query)->whereIn('estado', ['En análisis', 'En trámite', 'En ejecución'])->count())
                ->description('En proceso actual')
                ->color('warning'),
        ];
    }
}