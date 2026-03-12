<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoContratoResource\Pages;
use App\Filament\Resources\TipoContratoResource\RelationManagers;
use App\Models\TipoContrato;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TipoContratoResource extends Resource
{
    protected static ?string $model = TipoContrato::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Tipo de Contrato';
    protected static ?string $pluralModelLabel = 'Tipos de Contratos';
    protected static ?string $navigationLabel = 'Tipos de Contratos';
    protected static ?string $navigationGroup = 'Tipificaciones'; // Agrupa el menú

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('tipo_contrato') // <-- Cambiar según la tabla
                ->label('Nombre')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('tipo_contrato')->searchable()->sortable(), // <-- Cambiar según la tabla
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
            'index' => Pages\ListTipoContratos::route('/'),
            'create' => Pages\CreateTipoContrato::route('/create'),
            'edit' => Pages\EditTipoContrato::route('/{record}/edit'),
        ];
    }
}
