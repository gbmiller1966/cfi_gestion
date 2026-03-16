<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemaEstrategicoResource\Pages;
use App\Filament\Resources\TemaEstrategicoResource\RelationManagers;
use App\Models\TemaEstrategico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemaEstrategicoResource extends Resource
{
    protected static ?string $model = TemaEstrategico::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Tema Estratégico';
    protected static ?string $pluralModelLabel = 'Temas Estratégicos';
    protected static ?string $navigationLabel = 'Temas Estratégicos';
    protected static ?string $navigationGroup = 'Tipificaciones'; // Agrupa el menú

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('tema_estrategico') // <-- Cambiar según la tabla
                ->label('Nombre')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('tema_estrategico')->searchable()->sortable(), // <-- Cambiar según la tabla
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
            'index' => Pages\ListTemaEstrategicos::route('/'),
            'create' => Pages\CreateTemaEstrategico::route('/create'),
            'edit' => Pages\EditTemaEstrategico::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        // Solo el Admin verá este botón en el menú izquierdo
        return auth()->user()->hasRole('Admin');
    }
}
