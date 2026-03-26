<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProveedorResource\Pages;
use App\Models\Proveedor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $modelLabel = 'Proveedor';
    protected static ?string $pluralModelLabel = 'Proveedores';
    protected static ?string $navigationLabel = 'Proveedores';
    protected static ?string $navigationGroup = 'Actores Externos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Datos de la Empresa (Estructurales)
                Forms\Components\Section::make('Datos de la Empresa')
                    ->description('Información legal y ubicación física.')
                    ->schema([
                        Forms\Components\TextInput::make('numero_proveedor')
                            ->label('N° de Proveedor')
                            ->placeholder('Ej: 1234/24')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('razon_social')
                            ->label('Razón Social / Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('cuit')
                            ->label('CUIT')
                            ->required()
                            ->placeholder('30-XXXXXXXX-X')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Institucional')
                            ->email(),
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono Empresa')
                            ->tel(),
                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección')
                            ->placeholder('Calle, Número, Piso/Depto'),
                        Forms\Components\Select::make('provincia_id')
                            ->label('Provincia')
                            ->relationship('provincia', 'provincia')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                // 2. Datos del Contacto Directo (Referente)
                Forms\Components\Section::make('Referente de Contacto')
                    ->description('Persona con la que el técnico mantiene comunicación directa.')
                    ->schema([
                        Forms\Components\TextInput::make('contacto_nombre')
                            ->label('Nombre del Contacto')
                            ->placeholder('Ej: Juan Pérez')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contacto_email')
                            ->label('Email del Contacto')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contacto_celular')
                            ->label('Celular del Contacto')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(3), // Tres columnas para que entren en una sola fila

                // 3. Notas y Documentos
                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Textarea::make('doc_proveedor')
                            ->label('Documentación del Proveedor')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('doc_dotacion')
                            ->label('Documentación de la Dotación')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones Generales')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_proveedor')
                    ->label('N° Prov.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('razon_social')
                    ->label('Razón Social')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cuit')
                    ->label('CUIT')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contacto_nombre')
                    ->label('Contacto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provincia.provincia')
                    ->label('Provincia')
                    ->badge(),
            ])
            ->filters([])
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
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        // Tanto el Admin como el Técnico pueden ver y gestionar esto
        return auth()->user()->hasAnyRole(['Admin', 'Técnico']);
    }
}
