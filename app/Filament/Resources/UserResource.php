<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    // Dejamos el ícono una sola vez aquí arriba
    protected static ?string $navigationIcon = 'heroicon-o-users';

    // Etiquetas para el panel
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static ?string $navigationLabel = 'Personal';
    protected static ?string $navigationGroup = 'Administración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos Personales')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('apellido')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('usuario')
                            ->label('Nombre de Usuario')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('celular')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Datos de Acceso')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->revealable(),
                        Forms\Components\Select::make('roles')
                            ->label('Rol en el Sistema')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Ubicación en el CFI')
                    ->schema([
                        Forms\Components\Select::make('direccion_id')
                            ->label('Dirección')
                            ->relationship('direccion', 'direccion')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('area_id')
                            ->label('Área')
                            ->relationship('area', 'area')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('apellido')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion.direccion')
                    ->label('Dirección')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('area.area')
                    ->label('Área')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
