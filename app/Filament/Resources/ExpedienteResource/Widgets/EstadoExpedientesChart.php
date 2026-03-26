<?php

namespace App\Filament\Resources\ExpedienteResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Expediente;

class EstadoExpedientesChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución por Estado';
    protected static ?string $maxHeight = '300px';

    public static function canView(): bool
    {
        return auth()->user()->hasRole('Director');
    }

    protected function getData(): array
    {
        $user = auth()->user();

        // Consulta base: filtra por dirección del Director logueado
        $query = Expediente::query();

        if ($user->hasRole('Director')) {
            $query->where('direccion_id', $user->direccion_id);
        } else {
            // Regla de seguridad
            $query->where('user_id', $user->id);
        }

        // Definimos tus estados y los mapeamos a colores específicos
        // ID => [ 'nombre' => '...', 'color' => '...' ]
        $configEstados = [
            1 => ['nombre' => 'Borrador',   'color' => '#94a3b8'], // Slate 400
            2 => ['nombre' => 'Ingresado',  'color' => '#3b82f6'], // Blue 500
            3 => ['nombre' => 'Análisis',   'color' => '#f59e0b'], // Amber 500
            4 => ['nombre' => 'Trámite',    'color' => '#6366f1'], // Indigo 500
            5 => ['nombre' => 'Ejecución',  'color' => '#8b5cf6'], // Violet 500
            6 => ['nombre' => 'Finalizado', 'color' => '#10b981'], // Emerald 500
            7 => ['nombre' => 'Archivado',  'color' => '#475569'], // Slate 600
            8 => ['nombre' => 'Recisión',   'color' => '#ef4444'], // Red 500
            9 => ['nombre' => 'Baja',       'color' => '#000000'], // Black
        ];

        $data = [];
        $labels = [];
        $backgroundColors = []; // Nuevo array para los colores

        foreach ($configEstados as $id => $config) {
            // 1. Contamos
            $data[] = (clone $query)->where('estado_contrato_id', $id)->count();

            // 2. Agregamos el Label
            $labels[] = $config['nombre'];

            // 3. Agregamos el color al array en el mismo orden
            $backgroundColors[] = $config['color'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad de Expedientes',
                    'data' => $data,
                    // CAMBIO AQUÍ: Le pasamos el array de colores
                    'backgroundColor' => $backgroundColors,
                    // Eliminamos el borderColor único para que se vea más limpio con varios colores
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 50, // Ajustado según el volumen de la imagen (hasta 300)
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
