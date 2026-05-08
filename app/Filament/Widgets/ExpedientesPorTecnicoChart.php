<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class ExpedientesPorTecnicoChart extends ChartWidget
{
    protected static ?string $heading = 'Carga de Trabajo por Técnico';
    protected static string $color = 'primary';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $user = auth()->user();

        // Iniciamos la consulta de usuarios con rol Técnico
        $query = User::role('Técnico')->withCount('expedientes');

        // Lógica de Filtrado Jerárquico
        if ($user->hasRole('Director')) {
            // El Director ve a todos los técnicos de su Dirección
            $query->where('direccion_id', $user->direccion_id);
        } elseif ($user->hasRole('Jefe de Área')) {
            // Hernán ve solo a los técnicos de su Área
            $query->where('area_id', $user->area_id);
        }

        $data = $query->get();

        return [
            'datasets' => [
                [
                    'label' => 'Expedientes asignados',
                    'data' => $data->pluck('expedientes_count')->toArray(),
                    'backgroundColor' => '#0055A5',
                    'borderColor' => '#0055A5',
                ],
            ],
            'labels' => $data->map(fn($u) => $u->apellido ?? $u->name)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
