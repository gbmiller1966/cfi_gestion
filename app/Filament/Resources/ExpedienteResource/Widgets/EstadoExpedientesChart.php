<?php

namespace App\Filament\Resources\ExpedienteResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Expediente;

class EstadoExpedientesChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución por Estado';
    protected static ?string $maxHeight = '250px';

    // ESTO OCULTA EL GRÁFICO PARA QUIEN NO SEA DIRECTOR
    public static function canView(): bool
    {
        return auth()->user()->hasRole('Director');
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $query = Expediente::query();

        // Lógica de filtrado por jerarquía
        if ($user->hasRole('Director')) {
            $query->whereHas('tecnico', fn($q) => $q->where('direccion_id', $user->direccion_id));
        } else {
            // Por seguridad, si no es director, solo vería lo suyo
            $query->where('user_id', $user->id);
        }

        $estados = [
            'Borrador / Sin ingresar',
            'Ingresado al CFI',
            'En análisis',
            'En trámite',
            'En ejecución',
            'Finalizado',
            'Archivado'
        ];

        $data = [];
        foreach ($estados as $estado) {
            $data[] = (clone $query)->where('estado', $estado)->count();
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => [
                        '#94a3b8', '#3b82f6', '#f59e0b', '#6366f1', '#8b5cf6', '#10b981', '#475569',
                    ],
                ],
            ],
            'labels' => $estados,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}