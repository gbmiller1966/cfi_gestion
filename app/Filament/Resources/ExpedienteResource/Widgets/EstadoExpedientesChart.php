<?php

namespace App\Filament\Resources\ExpedienteResource\Widgets;

use Filament\Widgets\ChartWidget;

class EstadoExpedientesChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución por Estado';
    protected static ?string $maxHeight = '250px'; // Para que no sea un círculo gigante

protected function getData(): array
{
    $user = auth()->user();
    $query = \App\Models\Expediente::query();

    if ($user->hasRole('Director')) {
        $query->whereHas('tecnico', fn($q) => $q->where('direccion_id', $user->direccion_id));
    }

    // Definimos los estados exactos de tu lista
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
                    '#94a3b8', // Borrador (Gris)
                    '#3b82f6', // Ingresado (Azul)
                    '#f59e0b', // Análisis (Naranja)
                    '#6366f1', // Trámite (Indigo)
                    '#8b5cf6', // Ejecución (Violeta)
                    '#10b981', // Finalizado (Verde)
                    '#475569', // Archivado (Gris oscuro)
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


