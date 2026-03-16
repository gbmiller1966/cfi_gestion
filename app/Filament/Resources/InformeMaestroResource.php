<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InformeMaestroResource\Pages;
use App\Filament\Resources\InformeMaestroResource\RelationManagers;
use App\Models\InformeMaestro;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InformeMaestroResource extends Resource
{
    protected static ?string $model = InformeMaestro::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Informe Maestro';
    protected static ?string $pluralModelLabel = 'Informes Maestros';
    protected static ?string $navigationLabel = 'Informes Maestros';
    protected static ?string $navigationGroup = 'Tipificaciones'; // Agrupa el menú

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('informe_maestro') // <-- Cambiar según la tabla
                ->label('Nombre')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('informe_maestro')->searchable()->sortable(), // <-- Cambiar según la tabla
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
            'index' => Pages\ListInformeMaestros::route('/'),
            'create' => Pages\CreateInformeMaestro::route('/create'),
            'edit' => Pages\EditInformeMaestro::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        // Solo el Admin verá este botón en el menú izquierdo
        return auth()->user()->hasRole('Admin');
    }
}
