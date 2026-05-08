<?php

namespace App\Filament\Widgets;

use App\Models\Expediente;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use NumberFormatter;

class StatsGestionArea extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user = auth()->user();
        $query = Expediente::query();

        // Filtro por Área (Hernán) o Dirección (Director)
        if ($user->hasRole('Jefe de Área')) {
            $query->whereHas('tecnico', fn($q) => $q->where('area_id', $user->area_id));
        } elseif ($user->hasRole('Director')) {
            $query->where('direccion_id', $user->direccion_id);
        }

        // Formateador para moneda argentina
        $formateador = new NumberFormatter('es_AR', NumberFormatter::CURRENCY);
        $formateador->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        // Cálculos de montos
        $montoTotal = (clone $query)->sum('monto_imputado');
        // Monto de los que están estrictamente "En Ejecución" (Estado 5)
        $montoEjecucion = (clone $query)->where('estado_id', 5)->sum('monto_imputado');

        return [
            // 1. TOTAL
            Stat::make('Total Expedientes', (clone $query)->count())
                ->description('Total de la unidad')
                ->descriptionIcon('heroicon-m-folder')
                ->color('info'),

            // 2. EN ANÁLISIS (Estado 3) - Agregamos la lupita y el texto
            Stat::make('En Análisis', (clone $query)->where('estado_id', 3)->count())
                ->description('Evaluación inicial')
                ->descriptionIcon('heroicon-m-magnifying-glass') // La lupita
                ->color('gray'),

            // 3. EN TRÁMITE (Estado 4) - Restauramos texto e icono
            Stat::make('En Trámite', (clone $query)->where('estado_id', 4)->count())
                ->description('Firma de TDR / Compras')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            // 4. EN EJECUCIÓN (Estado 5) - Restauramos texto e icono
            Stat::make('En Ejecución', (clone $query)->where('estado_id', 5)->count())
                ->description('Proyectos con contrato')
                ->descriptionIcon('heroicon-m-play-circle')
                ->color('success'),

            // --- Fila de Montos (Mantenemos como estaba) ---
            Stat::make('Inversión Total Proyectada', $formateador->formatCurrency($montoTotal, 'ARS'))
                ->description('Suma de todos los expedientes')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Monto en Ejecución', $formateador->formatCurrency($montoEjecucion, 'ARS'))
                ->description('Proyectos con contrato activo')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
