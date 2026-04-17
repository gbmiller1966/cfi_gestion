<?php

namespace App\Filament\Widgets;

use App\Models\Expediente;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ComparativoAnualChart extends ChartWidget
{
    protected static ?string $heading = 'Comparativo de Ingresos (Últimos 3 Años)';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $years = [2024, 2025, 2026];
        $datasets = [];
        // Gris para 2024, Azul claro para 2025, Azul CFI para 2026
        $colors = ['#94a3b8', '#3b82f6', '#0055A5']; 

        foreach ($years as $index => $year) {
            $data = Expediente::query()
                ->whereYear('f_ingreso_area', $year)
                ->select(DB::raw('MONTH(f_ingreso_area) as month'), DB::raw('count(*) as count'))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();

            $chartData = [];
            for ($m = 1; $m <= 12; $m++) {
                // Si el mes es mayor al actual y el año es 2026, no ponemos 0 para que la línea se corte
                if ($year == 2026 && $m > now()->month) {
                    $chartData[] = null;
                } else {
                    $chartData[] = $data[$m] ?? 0;
                }
            }

            $datasets[] = [
                'label' => "Año $year",
                'data' => $chartData,
                'borderColor' => $colors[$index],
                'backgroundColor' => $colors[$index],
                'fill' => false,
                'tension' => 0.3, // Le da una leve curvatura para que sea más elegante
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}