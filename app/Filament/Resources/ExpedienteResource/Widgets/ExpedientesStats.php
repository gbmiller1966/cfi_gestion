<?php

namespace App\Filament\Resources\ExpedienteResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Expediente;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ExpedientesStats extends BaseWidget
{
    use InteractsWithPageFilters;

    public static function canView(): bool
    {
        return auth()->user()->hasRole('Director');
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $query = Expediente::query();

        // 1. Filtro por Rol (Seguridad)
        if ($user->hasRole('Director')) {
            $query->where('direccion_id', $user->direccion_id);
        }

        // 2. Filtro por Provincia desde el Dashboard
        // En StatsWidget usamos $this->filters directamente
        $filters = $this->filters;
        $provinciaId = $filters['provincia_id'] ?? null;

        if (!empty($provinciaId)) {
            $query->where('provincia_id', $provinciaId);
        }

        // --- A partir de aquí, las consultas usan la base filtrada ---

        // 3. KPI: Demora Derivación (CFI -> Área)
        $demoraDerivacion = (clone $query)
            ->whereNotNull('f_ingreso_cfi')
            ->whereNotNull('f_ingreso_area')
            ->whereRaw('DATEDIFF(f_ingreso_area, f_ingreso_cfi) > 15')
            ->count();

        // 4. KPI: Demora TDRs (Área -> TDR)
        $demoraTdr = (clone $query)
            ->whereNotNull('f_ingreso_area')
            ->whereNotNull('f_elevacion_tdr')
            ->whereRaw('DATEDIFF(f_elevacion_tdr, f_ingreso_area) > 15')
            ->count();

        // 5. KPI: Demora Contrato (Dir -> Contrato)
        $demoraContrato = (clone $query)
            ->whereNotNull('f_firma_director_tdr')
            ->whereNotNull('f_inicio_contrato')
            ->whereRaw('DATEDIFF(f_inicio_contrato, f_firma_director_tdr) > 15')
            ->count();

        // --- Contadores de estado ---
        $totalGestion = (clone $query)->count();
        $pendientes = (clone $query)->whereIn('estado_id', [1, 2])->count();
        $gestionActiva = (clone $query)->whereIn('estado_id', [3, 4, 5])->count();

        return [
            Stat::make('Total Gestión', $totalGestion)->color('info'),
            Stat::make('Pendientes / Ingresados', $pendientes)->color('gray'),
            Stat::make('En Gestión Activa', $gestionActiva)->color('primary'),

            Stat::make('Demora Derivación', $demoraDerivacion)
                ->description('CFI -> Área (+15 días)')
                ->descriptionIcon('heroicon-m-clock')
                ->color($demoraDerivacion > 0 ? 'danger' : 'success') // Cambié warning por danger para que resalte más el problema
                ->url(route('filament.admin.pages.dashboard', [
                    'filtro_demora' => 'derivacion',
                    'provincia_id' => $this->filters['provincia_id'] ?? null, // Mantenemos la provincia si está seleccionada
                ])),

            Stat::make('Demora TDRs', $demoraTdr)
                ->description('En Área -> TDR (+15 días)')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($demoraTdr > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.pages.dashboard', [
                    'filtro_demora' => 'tdr',
                    'provincia_id' => $this->filters['provincia_id'] ?? null,
                ])),

            Stat::make('Demora Contrato', $demoraContrato)
                ->description('Dir -> Contrato (+15 días)')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color($demoraContrato > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.pages.dashboard', [
                    'filtro_demora' => 'contrato',
                    'provincia_id' => $this->filters['provincia_id'] ?? null,
                ])),
        ];
    }
}