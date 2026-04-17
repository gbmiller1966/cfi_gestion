<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InformeResource\Pages;
use App\Filament\Resources\InformeResource\RelationManagers;
use App\Models\Informe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InformeResource extends Resource
{
    protected static ?string $model = Informe::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Tablas Maestras';
    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Informe'; // Singular (para el botón "Crear Dirección")
    protected static ?string $pluralModelLabel = 'Informes'; // Plural (para los títulos)
    protected static ?string $navigationLabel = 'Informes'; // El texto exacto del menú lateral superior
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('direccion')
                    ->label('Nombre del Informe')
                    ->placeholder('Ej: Informe Parcial')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(), // Hace que el campo ocupe todo el ancho del formulario
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('informe')
                    ->label('Informe')
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
            'index' => Pages\ListInformes::route('/'),
            'create' => Pages\CreateInforme::route('/create'),
            'edit' => Pages\EditInforme::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        // Solo el Admin verá este botón en el menú izquierdo
        return auth()->user()->hasRole('Admin');
    }
}
