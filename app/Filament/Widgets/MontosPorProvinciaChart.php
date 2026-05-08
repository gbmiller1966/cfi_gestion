<?php

namespace App\Filament\Widgets;

use App\Models\Expediente;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Support\RawJs;

class MontosPorProvinciaChart extends ChartWidget
{
    protected static ?string $heading = 'Inversión en Ejecución por Provincia';
    protected static string $color = 'success';
    protected int | string | array $columnSpan = 'full'; // Lo ponemos ancho para que se lean bien las provincias

    protected function getData(): array
    {
        $user = auth()->user();

        // Consulta para obtener montos por provincia de expedientes en ejecución (estado 5)
        $results = DB::table('expedientes')
            ->join('provincias', 'expedientes.provincia_id', '=', 'provincias.id')
            ->select('provincias.provincia', DB::raw('SUM(monto_imputado) as total_monto'))
            ->where('expedientes.estado_id', 5) // Solo En Ejecución
            ->whereExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereRaw('users.id = expedientes.user_id')
                    ->where('users.area_id', $user->area_id);
            })
            ->groupBy('provincias.provincia')
            ->orderByDesc('total_monto')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Monto Imputado ($)',
                    'data' => $results->pluck('total_monto')->toArray(),
                    'backgroundColor' => '#22C55E', // Color verde para ejecución
                ],
            ],
            'labels' => $results->pluck('provincia')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
        {
            return [
                'indexAxis' => 'y',
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                ],
                'scales' => [
                    'x' => [
                        'display' => true,
                        'beginAtZero' => true,
                        'ticks' => [
                            'display' => true,
                        ],
                    ],
                ],
            ];
        }
}
