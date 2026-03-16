<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DireccionResource\Pages;
use App\Filament\Resources\DireccionResource\RelationManagers;
use App\Models\Direccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DireccionResource extends Resource
{
    protected static ?string $model = Direccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Dirección'; // Singular (para el botón "Crear Dirección")
    protected static ?string $pluralModelLabel = 'Direcciones'; // Plural (para los títulos)
    protected static ?string $navigationLabel = 'Direcciones'; // El texto exacto del menú lateral superior
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('direccion')
                    ->label('Nombre de la Dirección')
                    ->placeholder('Ej: Dirección de Coordinación')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(), // Hace que el campo ocupe todo el ancho del formulario
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('direccion')
                    ->label('Dirección')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Lo oculta por defecto, pero puedes verlo desde el menú de columnas
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Agregamos el botón para poder eliminar
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListDireccions::route('/'),
            'create' => Pages\CreateDireccion::route('/create'),
            'edit' => Pages\EditDireccion::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        // Solo el Admin verá este botón en el menú izquierdo
        return auth()->user()->hasRole('Admin');
    }
}
