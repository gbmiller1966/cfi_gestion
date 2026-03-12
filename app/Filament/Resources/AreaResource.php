<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaResource\Pages;
use App\Models\Area;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    // Etiquetas en español (con tilde)
    protected static ?string $modelLabel = 'Área';
    protected static ?string $pluralModelLabel = 'Áreas';
    protected static ?string $navigationLabel = 'Áreas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Campo de texto para el nombre del área
                Forms\Components\TextInput::make('area')
                    ->label('Nombre del Área')
                    ->placeholder('Ej: Gestión de Gobierno')
                    ->required()
                    ->maxLength(255),

                // 2. Menú desplegable mágico que trae las direcciones de la base de datos
                Forms\Components\Select::make('direccion_id')
                    ->label('Dirección a la que pertenece')
                    ->relationship('direccion', 'direccion') // Busca la relación 'direccion' y muestra la columna 'direccion'
                    ->searchable() // Permite buscar escribiendo
                    ->preload() // Carga las opciones al abrir la página
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('area')
                    ->label('Área')
                    ->searchable()
                    ->sortable(),

                // Mostramos a qué dirección pertenece cruzando las tablas
                Tables\Columns\TextColumn::make('direccion.direccion')
                    ->label('Dirección')
                    ->searchable()
                    ->sortable()
                    ->badge(), // Le da un diseño de "etiqueta" para que resalte

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAreas::route('/'),
            'create' => Pages\CreateArea::route('/create'),
            'edit' => Pages\EditArea::route('/{record}/edit'),
        ];
    }
}
