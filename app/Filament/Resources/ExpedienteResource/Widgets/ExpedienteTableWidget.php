<?php

namespace App\Filament\Resources\ExpedienteResource\Widgets;

use App\Models\Expediente;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class ExpedienteTableWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';
    protected bool $isSimplePagination = false;
    // protected static ?string $heading = 'Listado de Gestión de Expedientes';
// app/Filament/Resources/ExpedienteResource/Widgets/ExpedienteTableWidget.php

    protected function getTableHeading(): string | null
    {
        $filtro = request()->query('filtro_demora');
        $provinciaId = $this->filters['provincia_id'] ?? null;

        // Si no hay filtro de demora, mostramos el título estándar
        if (!$filtro) {
            return 'Listado General de Gestión';
        }

        // 💡 Preparamos la consulta para contar cuántos hay en esta situación
        $query = \App\Models\Expediente::query();

        // Aplicamos seguridad de Director
        if (auth()->user()->hasRole('Director')) {
            $query->where('direccion_id', auth()->user()->direccion_id);
        }

        // Aplicamos filtro de provincia si existe
        if ($provinciaId) {
            $query->where('provincia_id', $provinciaId);
        }

        // Aplicamos la lógica de la demora específica para el conteo
        $count = match($filtro) {
            'derivacion' => $query->whereNotNull('f_ingreso_cfi')
                                ->whereNotNull('f_ingreso_area')
                                ->whereRaw('DATEDIFF(f_ingreso_area, f_ingreso_cfi) > 15')
                                ->count(),
            'tdr' => $query->whereNotNull('f_ingreso_area')
                            ->whereNotNull('f_elevacion_tdr')
                            ->whereRaw('DATEDIFF(f_elevacion_tdr, f_ingreso_area) > 15')
                            ->count(),
            'contrato' => $query->whereNotNull('f_firma_director_tdr')
                                ->whereNotNull('f_inicio_contrato')
                                ->whereRaw('DATEDIFF(f_inicio_contrato, f_firma_director_tdr) > 15')
                                ->count(),
            default => null,
        };

        // 💡 Retornamos el título con el número (ej: 455)
        return match($filtro) {
            'derivacion' => "⚠️ Expedientes con Demora en Derivación: {$count}",
            'tdr' => "⚠️ Expedientes con Demora en TDR: {$count}",
            'contrato' => "⚠️ Expedientes con Demora en Contrato: {$count}",
            default => 'Listado General de Gestión',
        };
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = Expediente::query()->with(['estado', 'provincia']);
                $user = auth()->user();

                // 1. Filtro de Seguridad por Rol (Lo que ya tenías)
                if ($user->hasRole('Director')) {
                    $query->where('direccion_id', $user->direccion_id);
                } elseif ($user->hasRole('Jefe de Área')) {
                    $query->whereHas('tecnico', fn ($q) => $q->where('area_id', $user->area_id));
                } elseif (!$user->hasRole('Admin')) {
                    $query->where('user_id', $user->id);
                }

                // 2. Filtro dinámico de Provincia (Dashboard)
                $provinciaId = $this->filters['provincia_id'] ?? null;
                if (!empty($provinciaId)) {
                    $query->where('provincia_id', $provinciaId);
                }

                // 💡 3. CAPTURAR CLIC DE LAS TARJETAS (Nuevo)
                $filtroDemora = request()->query('filtro_demora');

                if ($filtroDemora === 'derivacion') {
                    $query->whereNotNull('f_ingreso_cfi')
                        ->whereNotNull('f_ingreso_area')
                        ->whereRaw('DATEDIFF(f_ingreso_area, f_ingreso_cfi) > 15');
                } elseif ($filtroDemora === 'tdr') {
                    $query->whereNotNull('f_ingreso_area')
                        ->whereNotNull('f_elevacion_tdr')
                        ->whereRaw('DATEDIFF(f_elevacion_tdr, f_ingreso_area) > 15');
                } elseif ($filtroDemora === 'contrato') {
                    $query->whereNotNull('f_firma_director_tdr')
                        ->whereNotNull('f_inicio_contrato')
                        ->whereRaw('DATEDIFF(f_inicio_contrato, f_firma_director_tdr) > 15');
                }

                // Mantener el orden que ya tenías
                $query->orderByRaw("FIELD(
                    (SELECT estado FROM estados WHERE estados.id = expedientes.estado_id),
                    'En análisis', 'En trámite', 'En ejecución', 'Finalizado', 'Archivado', 'Borrador / Sin Ingresar al Área', 'Dado de baja'
                ) ASC");

                return $query;
            })

            ->persistSortInSession()
            // 💡 CAMBIO 1: Usamos el array simple (como en los Resources que funcionan)
            // Eliminamos el 'all' => 'Todos' por ahora para asegurar compatibilidad total
            ->paginated([5, 10, 25, 50, 100])
            ->defaultPaginationPageOption(25)

            // 💡 CAMBIO 2: Reforzamos la visibilidad de los links
            ->extremePaginationLinks()

            ->actionsColumnLabel('Acciones') 
            ->actionsPosition(Tables\Enums\ActionsPosition::AfterColumns)

            ->columns([
                Tables\Columns\TextColumn::make('gde_numero')
                    ->label('GDE')
                    ->limit(16)
                    ->tooltip(fn (Expediente $record): string => $record->gde_numero)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->limit(35)
                    ->tooltip(fn (Expediente $record): string => $record->titulo)
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('provincia.provincia')
                    ->label('Provincia')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tecnico.apellido')
                    ->label('Técnico a cargo')
                    ->sortable()
                    ->visible(fn () => ! auth()->user()->hasRole('Técnico')),

                Tables\Columns\TextColumn::make('monto_imputado')
                    ->label('Monto Imputado')
                    ->money('ARS')
                    ->sortable()
                    ->visible(fn () => ! auth()->user()->hasRole('Técnico')),

                Tables\Columns\TextColumn::make('estado.estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Borrador / Sin Ingresar al Área' => 'gray',
                        'Ingresado al CFI'                => 'info',
                        'En análisis'                     => 'warning',
                        'En trámite'                      => 'warning',
                        'En ejecución'                    => 'primary',
                        'Finalizado'                      => 'success',
                        'Archivado'                       => 'gray',
                        'Recisión'                        => 'danger',
                        default                           => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('dias_cfi_area')
                    ->label('CFI - Área')
                    ->alignment('center')
                    ->getStateUsing(fn ($record) => $record->f_ingreso_cfi && $record->f_ingreso_area
                        ? \Carbon\Carbon::parse($record->f_ingreso_cfi)->diffInDays($record->f_ingreso_area) . ' d.'
                        : '-')
                    ->visible(fn () => ! auth()->user()->hasRole('Técnico')),

                Tables\Columns\TextColumn::make('dias_area_dir')
                    ->label('Área - Dir')
                    ->alignment('center')
                    ->getStateUsing(fn ($record) => $record->f_ingreso_area && $record->f_elevacion_tdr
                        ? \Carbon\Carbon::parse($record->f_ingreso_area)->diffInDays($record->f_elevacion_tdr) . ' d.'
                        : '-')
                    ->visible(fn () => ! auth()->user()->hasRole('Técnico')),
            ])
            ->filters([
                // 1. Filtro por Estado (Ya lo tenías, lo dejamos impecable)
                Tables\Filters\SelectFilter::make('estado_id')
                    ->label('Estado')
                    ->relationship('estado', 'estado')
                    ->preload(),

                // 2. Filtro por Provincia
                Tables\Filters\SelectFilter::make('provincia_id')
                    ->label('Provincia')
                    ->relationship('provincia', 'provincia')
                    ->searchable() // Permite escribir para buscar la provincia
                    ->preload(),

                // 3. Filtro por Técnico a cargo
                Tables\Filters\SelectFilter::make('tecnico_id')
                    ->label('Técnico a cargo')
                    ->relationship('tecnico', 'apellido') // Usamos el apellido para el listado
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->apellido} {$record->nombre}"), // Muestra Apellido y Nombre
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                        ->label('Ver')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        // Forzamos la URL al recurso principal
                        ->url(fn (Expediente $record): string => \App\Filament\Resources\ExpedienteResource::getUrl('view', ['record' => $record]))
                        ->openUrlInNewTab(false), // Para que Miller no se pierda en mil pestañas

                // Acción EDITAR: Solo visible para Técnicos
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (Expediente $record): string => \App\Filament\Resources\ExpedienteResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn () => auth()->user()->hasRole('Técnico')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nuevo Expediente')
                    ->icon('heroicon-m-plus')
                    // Solo lo ve el Técnico
                    ->visible(fn () => auth()->user()->hasRole('Técnico'))
                    // Forzamos que la URL sea la del panel 'tecnico' para evitar errores de permisos
                    ->url(fn (): string => \App\Filament\Resources\ExpedienteResource::getUrl('create', panel: 'tecnico')),
            ]);
    }
}
