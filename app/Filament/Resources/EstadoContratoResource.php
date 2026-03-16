<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstadoContratoResource\Pages;
use App\Filament\Resources\EstadoContratoResource\RelationManagers;
use App\Models\EstadoContrato;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EstadoContratoResource extends Resource
{
    protected static ?string $model = EstadoContrato::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

protected static ?string $modelLabel = 'Estado del Contrato';
    protected static ?string $pluralModelLabel = 'Estados del Contrato';
    protected static ?string $navigationLabel = 'Estados Contrato';
    protected static ?string $navigationGroup = 'Tipificaciones';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('estado')
                ->label('Nombre del Estado')
                ->required()
                ->unique(ignoreRecord: true)
                ->placeholder('Ej: En ejecución'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('estado')
                ->label('Estado')
                ->badge() // Esto hará que se vea como una etiqueta de color
                ->color(fn (string $state): string => match ($state) {
                    'Finalizado' => 'success',
                    'En ejecución' => 'info',
                    'Firma del contrato' => 'warning',
                    'En análisis' => 'gray',
                    default => 'primary',
                })
                ->searchable(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEstadoContratos::route('/'),
            'create' => Pages\CreateEstadoContrato::route('/create'),
            'edit' => Pages\EditEstadoContrato::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        // Solo el Admin verá este botón en el menú izquierdo
        return auth()->user()->hasRole('Admin');
    }
}
