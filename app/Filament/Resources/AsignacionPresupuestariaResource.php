<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsignacionPresupuestariaResource\Pages;
use App\Filament\Resources\AsignacionPresupuestariaResource\RelationManagers;
use App\Models\AsignacionPresupuestaria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AsignacionPresupuestariaResource extends Resource
{
    protected static ?string $model = AsignacionPresupuestaria::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Asignación Presupuestaria';
    protected static ?string $pluralModelLabel = 'Asignaciones Presupuestarias';
    protected static ?string $navigationLabel = 'Asignaciones Presupuestarias';
    protected static ?string $navigationGroup = 'Tipificaciones'; // Agrupa el menú

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('asignacion_presupuestaria') // <-- Cambiar según la tabla
                ->label('Asignación Presupuestaria') // Etiqueta amigable
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('asignacion_presupuestaria')->searchable()->sortable(), // <-- Cambiar según la tabla
        ])->actions([ Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make() ]);
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
            'index' => Pages\ListAsignacionPresupuestarias::route('/'),
            'create' => Pages\CreateAsignacionPresupuestaria::route('/create'),
            'edit' => Pages\EditAsignacionPresupuestaria::route('/{record}/edit'),
        ];
    }
}
