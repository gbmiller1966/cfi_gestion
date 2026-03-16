<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpedienteResource\Pages;
use App\Models\Expediente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ExpedienteResource extends Resource
{
    protected static ?string $model = Expediente::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Expedientes';
    protected static ?string $modelLabel = 'Expediente';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Campo oculto: El usuario no lo ve, pero viaja en el formulario
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),

                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        // PESTAÑA 1: DATOS GENERALES
                        Forms\Components\Tabs\Tab::make('General y Ubicación')
                            ->schema([
                                Forms\Components\TextInput::make('titulo')
                                    ->label('Título del Proyecto')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('gde_numero')
                                    ->label('N° GDE'),
                                Forms\Components\Textarea::make('objeto')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('provincia_id')
                                    ->relationship('provincia', 'provincia')
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('localidad_id')
                                    ->relationship('localidad', 'localidad')
                                    ->required()
                                    ->searchable(),
                            ])->columns(2),

                        // PESTAÑA 2: CONTRATO E INFORMES
                        Forms\Components\Tabs\Tab::make('Contrato e Informes')
                            ->schema([
                                Forms\Components\DatePicker::make('f_inicio_contrato')
                                    ->label('Inicio de Contrato')
                                    ->live(),
                                Forms\Components\DatePicker::make('f_fin_contrato')
                                    ->label('Fin de Contrato')
                                    ->live(),
                                
                                Forms\Components\Repeater::make('informes')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Select::make('informe_id')
                                            ->label('Tipo de Informe')
                                            ->relationship('informeMaestro', 'informe_maestro')
                                            ->required(),
                                        Forms\Components\TextInput::make('meses_pactados')
                                            ->label('Meses')
                                            ->numeric()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                                $inicio = $get('../../f_inicio_contrato');
                                                if ($inicio && $state) {
                                                    $set('fecha_limite', Carbon::parse($inicio)->addMonths($state)->format('Y-m-d'));
                                                }
                                            }),
                                        Forms\Components\DatePicker::make('fecha_limite')
                                            ->label('Fecha Pactada')
                                            ->readOnly(),
                                    ])->columns(3)->columnSpanFull(),
                            ])->columns(2),

                        // PESTAÑA 3: ACTORES Y MONTOS
                        Forms\Components\Tabs\Tab::make('Actores y Montos')
                            ->schema([
                                Forms\Components\Select::make('proveedores')
                                    ->multiple()
                                    ->relationship('proveedores', 'razon_social')
                                    ->preload(),
                                Forms\Components\Select::make('contraparte_id')
                                    ->relationship('contraparte', 'apellido')
                                    ->label('Contraparte Provincial'),
                                Forms\Components\TextInput::make('monto_cfi')
                                    ->numeric()
                                    ->prefix('$'),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gde_numero')->label('GDE')->searchable(),
                Tables\Columns\TextColumn::make('titulo')->label('Título')->limit(50)->searchable(),
                // Como en tu tabla users tenés 'nombre' y 'apellido', lo mostramos así
                Tables\Columns\TextColumn::make('user.nombre')->label('Técnico'), 
                Tables\Columns\TextColumn::make('provincia.provincia')->label('Provincia'),
            ])
            ->filters([
                // Acá agregaremos filtros más adelante
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

    // EL FILTRO MÁGICO PARA QUE EL TÉCNICO VEA SOLO LO SUYO
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Si es Admin, ve todo sin restricciones
        if ($user->hasRole('Admin')) {
            return $query; 
        }

        // Si es Técnico, forzamos a que solo traiga sus expedientes
        return $query->where('user_id', $user->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpedientes::route('/'),
            'create' => Pages\CreateExpediente::route('/create'),
            'edit' => Pages\EditExpediente::route('/{record}/edit'),
        ];
    }
}