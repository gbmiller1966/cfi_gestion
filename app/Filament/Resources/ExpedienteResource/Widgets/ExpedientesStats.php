<?php

namespace App\Filament\Resources\ExpedienteResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Expediente;
use Carbon\Carbon;

class ExpedientesStats extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->hasRole('Director');
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $query = Expediente::query();

        if ($user->hasRole('Director')) {
            $query->where('direccion_id', $user->direccion_id);
        }

        $hace15Dias = Carbon::now()->subDays(15);

        // 1. Ingreso CFI -> Derivación al Área
        $demoraDerivacion = (clone $query)
            ->whereNotNull('f_ingreso_cfi')
            ->whereNull('f_ingreso_area')
            ->where('f_ingreso_cfi', '<=', $hace15Dias)
            ->count();

        // 2. Ingreso Área -> Elevación TDRs
        $demoraTdr = (clone $query)
            ->whereNotNull('f_ingreso_area')
            ->whereNull('f_elevacion_tdr')
            ->where('f_ingreso_area', '<=', $hace15Dias)
            ->count();

        // 3. Firma Directora TDR -> Inicio Contrato (Firma Contrato)
        $demoraContrato = (clone $query)
            ->whereNotNull('f_firma_director_tdr')
            ->whereNull('f_inicio_contrato')
            ->where('f_firma_director_tdr', '<=', $hace15Dias)
            ->count();

        // --- Contadores que ya tenías ---
        $totalGestion = (clone $query)->count();
        $pendientes = (clone $query)->whereIn('estado_contrato_id', [1, 2])->count();
        $gestionActiva = (clone $query)->whereIn('estado_contrato_id', [3, 4, 5])->count();

        return [
            // Fila 1: Resumen General
            Stat::make('Total Gestión', $totalGestion)->color('info'),
            Stat::make('Pendientes / Ingresados', $pendientes)->color('gray'),
            Stat::make('En Gestión Activa', $gestionActiva)->color('primary'),

            // Fila 2: KPIs de Tiempo (+15 días)
            Stat::make('Demora Derivación', $demoraDerivacion)
                ->description('CFI -> Área (+15 días)')
                ->descriptionIcon('heroicon-m-clock')
                ->color($demoraDerivacion > 0 ? 'danger' : 'success'),

            Stat::make('Demora TDRs', $demoraTdr)
                ->description('En Área -> TDR (+15 días)')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($demoraTdr > 0 ? 'danger' : 'success'),

            Stat::make('Demora Contrato', $demoraContrato)
                ->description('Dir -> Contrato (+15 días)')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color($demoraContrato > 0 ? 'danger' : 'success'),
        ];
    }
}
