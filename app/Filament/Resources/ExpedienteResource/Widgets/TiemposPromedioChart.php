<?php

namespace App\Filament\Resources\ExpedienteResource\Widgets;

use App\Models\Expediente;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class TiemposPromedioChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Promedio de Días por Etapa';
    protected static string $color = 'info';

    public static function canView(): bool
    {
        return auth()->user()->hasRole('Director');
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $query = Expediente::query();

        // 1. Filtro por Rol (Seguridad)
        if ($user->hasRole('Director')) {
            $query->where('direccion_id', $user->direccion_id);
        }

        // 2. Filtro por Provincia desde el Dashboard
        $provinciaId = $this->filters['provincia_id'] ?? null;

        if (!empty($provinciaId)) {
            $query->where('provincia_id', $provinciaId);
        }

        // 3. Cálculo de promedios en SQL
        // Usamos COALESCE para que si no hay datos devuelva 0 y no rompa el gráfico
        $stats = (clone $query)
            ->select(
                DB::raw('AVG(DATEDIFF(f_ingreso_area, f_ingreso_cfi)) as avg_derivacion'),
                DB::raw('AVG(DATEDIFF(f_elevacion_tdr, f_ingreso_area)) as avg_tdr'),
                DB::raw('AVG(DATEDIFF(f_inicio_contrato, f_firma_director_tdr)) as avg_contrato')
            )
            ->first();

        return [
            'datasets' => [
                [
                    'label' => 'Días Promedio',
                    'data' => [
                        round($stats->avg_derivacion ?? 0, 1),
                        round($stats->avg_tdr ?? 0, 1),
                        round($stats->avg_contrato ?? 0, 1),
                    ],
                    'backgroundColor' => [
                        '#3490dc', // Azul para Derivación
                        '#f6993f', // Naranja para TDR
                        '#38c172', // Verde para Contrato
                    ],
                ],
            ],
            'labels' => [
                'CFI a Área', 
                'Área a TDR', 
                'Firma a Contrato'
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}