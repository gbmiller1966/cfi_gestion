<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocalidadResource\Pages;
use App\Filament\Resources\LocalidadResource\RelationManagers;
use App\Models\Localidad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocalidadResource extends Resource
{
    protected static ?string $model = Localidad::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

protected static ?string $modelLabel = 'Localidad';
    protected static ?string $pluralModelLabel = 'Localidades';
    protected static ?string $navigationLabel = 'Localidades';
    protected static ?string $navigationGroup = 'Geografía';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('localidad')
                ->label('Nombre de la Localidad')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('provincia_id')
                ->label('Provincia')
                ->relationship('provincia', 'provincia')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('latitud')
                ->label('Latitud (Opcional)')
                ->maxLength(255),
            Forms\Components\TextInput::make('longitud')
                ->label('Longitud (Opcional)')
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('localidad')->label('Localidad')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('provincia.provincia')->label('Provincia')->badge()->sortable(),
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
            'index' => Pages\ListLocalidads::route('/'),
            'create' => Pages\CreateLocalidad::route('/create'),
            'edit' => Pages\EditLocalidad::route('/{record}/edit'),
        ];
    }
}
