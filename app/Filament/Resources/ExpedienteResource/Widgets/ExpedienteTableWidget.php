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
    protected static ?string $heading = 'Listado de Gestión de Expedientes';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = Expediente::query()->with(['estado', 'provincia']);
                $user = auth()->user();

                // 1. Filtro de Seguridad por Rol
                if ($user->hasRole('Admin')) {
                    // No filtramos nada
                } elseif ($user->hasRole('Director')) {
                    $query->where('direccion_id', $user->direccion_id);
                } elseif ($user->hasRole('Jefe de Área')) {
                    $query->whereHas('tecnico', fn ($q) => $q->where('area_id', $user->area_id));
                } else {
                    // Rol Técnico o cualquier otro: solo lo propio
                    $query->where('user_id', $user->id);
                }

                // 2. Filtro dinámico del Dashboard (Provincia)
                $provinciaId = $this->filters['provincia_id'] ?? null;

                if ($provinciaId) {
                    $query->where('provincia_id', $provinciaId);
                }

                $query->orderByRaw("FIELD(
                    (SELECT estado FROM estados WHERE estados.id = expedientes.estado_id),
                    'En análisis',
                    'En trámite',
                    'En ejecución',
                    'Finalizado',
                    'Archivado',
                    'Borrador / Sin Ingresar al Área'
                ) ASC");

                return $query;
            })
            ->persistSortInSession()
            ->columns([
                Tables\Columns\TextColumn::make('gde_numero')
                    ->label('GDE')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->limit(40)
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
                    ->sortable(),

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
                    ->label('Días (CFI-Área)')
                    ->getStateUsing(fn ($record) => $record->f_ingreso_cfi && $record->f_ingreso_area
                        ? \Carbon\Carbon::parse($record->f_ingreso_cfi)->diffInDays($record->f_ingreso_area) . ' d.'
                        : '-'),

                Tables\Columns\TextColumn::make('dias_area_dir')
                    ->label('Días (Área-Dir)')
                    ->getStateUsing(fn ($record) => $record->f_ingreso_area && $record->f_elevacion_tdr
                        ? \Carbon\Carbon::parse($record->f_ingreso_area)->diffInDays($record->f_elevacion_tdr) . ' d.'
                        : '-'),
            ])
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50])
            ->filters([
                Tables\Filters\SelectFilter::make('estado_id')
                    ->label('Estado')
                    ->relationship('estado', 'estado'),
            ])
            ->actions([
                // Acción VER: Disponible para todos, abre en pestaña nueva
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->url(fn (Expediente $record): string => \App\Filament\Resources\ExpedienteResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),

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
