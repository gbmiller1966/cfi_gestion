<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContraparteProvincialResource\Pages;
use App\Models\ContraparteProvincial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContraparteProvincialResource extends Resource
{
    protected static ?string $model = ContraparteProvincial::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $modelLabel = 'Contraparte Provincial';
    protected static ?string $pluralModelLabel = 'Contrapartes Provinciales';
    protected static ?string $navigationLabel = 'Contrapartes Prov.';
    protected static ?string $navigationGroup = 'Actores Externos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Datos Personales
                Forms\Components\Section::make('Datos del Referente')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('apellido')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Institucional')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email_particular')
                            ->label('Email Particular (Opcional)')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('celular')
                            ->label('Celular / WhatsApp')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(2),

                // 2. Ubicación Institucional
                Forms\Components\Section::make('Información del Cargo')
                    ->schema([
                        Forms\Components\Select::make('provincia_id')
                            ->label('Provincia')
                            ->relationship('provincia', 'provincia')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('dependencia')
                            ->label('Dependencia / Ministerio')
                            ->placeholder('Ej: Ministerio de Producción')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('cargo')
                            ->label('Cargo Exacto')
                            ->placeholder('Ej: Director Provincial de Estadística')
                            ->maxLength(255),
                    ])->columns(2),

                // 3. Notas
                Forms\Components\Section::make('Adicionales')
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('apellido')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provincia.provincia')
                    ->label('Provincia')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dependencia')
                    ->label('Dependencia')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provincia_id')
                    ->label('Filtrar por Provincia')
                    ->relationship('provincia', 'provincia'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContraparteProvincials::route('/'),
            'create' => Pages\CreateContraparteProvincial::route('/create'),
            'edit' => Pages\EditContraparteProvincial::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        // Tanto el Admin como el Técnico pueden ver y gestionar esto
        return auth()->user()->hasAnyRole(['Admin', 'Técnico']);
    }
}
