<?php

namespace App\Filament\Widgets;

use App\Models\Expediente;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ExpedientesPorTemaChart extends ChartWidget
{
    protected static ?string $heading = 'Temáticas de Proyectos (Top 5)';
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $user = auth()->user();

        // Buscamos los temas y contamos cuántos expedientes tiene cada uno
        // Solo para el área del Jefe actual
        $results = DB::table('expedientes')
            ->join('temas', 'expedientes.tema_id', '=', 'temas.id')
            ->select('temas.tema', DB::raw('count(*) as total'))
            ->whereExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->whereRaw('users.id = expedientes.user_id')
                    ->where('users.area_id', $user->area_id);
            })
            ->groupBy('temas.tema')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad',
                    'data' => $results->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#0055A5', '#22C55E', '#EAB308', '#EF4444', '#8B5CF6'
                    ],
                ],
            ],
            'labels' => $results->pluck('tema')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // Estilo circular como el de "Estado" en tu Power BI
    }
}
