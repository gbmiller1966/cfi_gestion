<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionResource\Pages;
use App\Filament\Resources\RegionResource\RelationManagers;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Región';
    protected static ?string $pluralModelLabel = 'Regiones';
    protected static ?string $navigationLabel = 'Regiones';
    protected static ?string $navigationGroup = 'Geografía'; // Agrupa el menú!

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('region')
                ->label('Nombre de la Región')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('region')->label('Región')->searchable()->sortable(),
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
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        // Solo el Admin verá este botón en el menú izquierdo
        return auth()->user()->hasRole('Admin');
    }
}
