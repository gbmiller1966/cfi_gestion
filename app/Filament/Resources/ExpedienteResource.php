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
                // Campo oculto para el técnico logueado
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),

                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        // ---------------------------------------------------------
                        // PESTAÑA 1: DATOS GENERALES E INGRESO
                        // ---------------------------------------------------------
                        Forms\Components\Tabs\Tab::make('1. General e Ingreso')
                            ->schema([
                                Forms\Components\Section::make('Datos del Proyecto')
                                    ->schema([
                                        Forms\Components\TextInput::make('titulo')
                                            ->label('Título del Proyecto')
                                            ->required()
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('gde_numero')
                                            ->label('N° GDE Principal'),
                                        Forms\Components\Select::make('provincia_id')
                                            ->relationship('provincia', 'provincia')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live() // ¡CLAVE 1! Le avisa al sistema que el valor cambió en tiempo real
                                            ->afterStateUpdated(fn (Forms\Set $set) => $set('localidad_id', null)), // Si cambia la provincia, le vaciamos la localidad que había elegido antes
                                        Forms\Components\Select::make('localidad_id')
                                            ->label('Localidad')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            // ¡CLAVE 2! Filtramos las localidades según la provincia seleccionada arriba
                                            ->relationship(
                                                name: 'localidad',
                                                titleAttribute: 'localidad',
                                                modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query, Forms\Get $get) =>
                                                    $query->where('provincia_id', $get('provincia_id'))),
                                Forms\Components\Textarea::make('objeto')
                                    ->required()
                                    ->columnSpanFull(),
                                    ])->columns(2),

                                Forms\Components\Section::make('Clasificación y Estado')
                                    ->schema([
                                        Forms\Components\Select::make('asignacion_id')
                                            ->relationship('asignacion', 'asignacion_presupuestaria') // Ajustá 'nombre' a la columna real de tu tabla
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('tema_id')
                                            ->relationship('tema', 'tema_estrategico') // Ajustá 'nombre' a la columna real de tu tabla
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('tipo_contrato_id')
                                            ->relationship('tipoContrato', 'tipo_contrato') // Ajustá 'nombre' a la columna real de tu tabla
                                            ->searchable()
                                            ->preload(),

                                        // Muestra el estado actual calculado (Solo lectura para que el técnico lo vea)
                                        Forms\Components\Placeholder::make('estado_actual_visual')
                                            ->label('Estado del Contrato')
                                            ->content(fn ($record) => $record ? $record->estado : 'Borrador / Sin Ingresar'),

                                        // El "Botón de Pánico" para bajas o rescisiones
                                        Forms\Components\Select::make('estado_excepcion')
                                            ->label('Excepción (Baja/Rescisión)')
                                            ->options([
                                                'Dado de baja' => 'Dado de baja',
                                                'Rescindido' => 'Rescindido',
                                            ])
                                            ->placeholder('Ninguna (Cálculo automático)')
                                            ->helperText('Solo completar si el contrato se interrumpe de forma anormal.'),
                                    ])->columns(3),

                                Forms\Components\Section::make('Ingreso y Derivación')
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('f_ingreso_cfi')
                                            ->label('Ingreso a Coord. CFI'),
                                        Forms\Components\DateTimePicker::make('f_ingreso_area')
                                            ->label('Derivación al Área'),
                                        Forms\Components\DateTimePicker::make('f_derivacion_tecnico')
                                            ->label('Derivación al Técnico'),
                                    ])->columns(3),

                                Forms\Components\Section::make('Actores y Montos')
                                    ->schema([
                                        Forms\Components\Select::make('proveedores')
                                            ->multiple()
                                            ->relationship('proveedores', 'razon_social')
                                            ->preload(),
                                        Forms\Components\Select::make('contraparte_id')
                                            ->relationship('contraparte', 'apellido')
                                            ->label('Contraparte Provincial'),
                                        Forms\Components\Select::make('colaboradores')
                                            ->multiple()
                                            ->relationship('colaboradores', 'nombre')
                                            ->label('Técnicos Colaboradores')
                                            ->preload(),

                                        Forms\Components\TextInput::make('monto_solicitud_provincial')
                                            ->label('Monto Solicitud')
                                            ->numeric()
                                            ->prefix('$'),
                                        Forms\Components\TextInput::make('monto_cfi')
                                            ->label('Monto CFI')
                                            ->numeric()
                                            ->prefix('$'),
                                        Forms\Components\TextInput::make('monto_convenido')
                                            ->label('Monto Convenido')
                                            ->numeric()
                                            ->prefix('$'),
                                    ])->columns(3),
                            ]),

                        // ---------------------------------------------------------
                        // PESTAÑA 2: TDR, CONTRATO E INFORMES
                        // ---------------------------------------------------------
                        Forms\Components\Tabs\Tab::make('2. TDR y Contrato')
                            ->schema([
                                Forms\Components\Section::make('Términos de Referencia (TDR)')
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('f_elevacion_tdr')
                                            ->label('Elevación TDR (Jefe)'),
                                        Forms\Components\DateTimePicker::make('f_firma_jefe_tdr')
                                            ->label('Firma Jefe TDR'),
                                        Forms\Components\DateTimePicker::make('f_firma_director_tdr')
                                            ->label('Firma Director TDR'),
                                        Forms\Components\TextInput::make('gde_tdr')
                                            ->label('N° GDE TDR'),
                                    ])->columns(2),

                                Forms\Components\Section::make('Gestión del Contrato')
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('f_derivacion_compras')
                                            ->label('Pase a Compras'),
                                        Forms\Components\DatePicker::make('f_inicio_contrato')
                                            ->label('Inicio de Contrato')
                                            ->live(),
                                        Forms\Components\DatePicker::make('f_fin_contrato')
                                            ->label('Fin de Contrato')
                                            ->live(),
                                    ])->columns(3),

                                Forms\Components\Section::make('Planificación de Informes')
                                    ->schema([
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

                                                        // Le agregamos (int) a $state para que Carbon no explote
                                                        if ($inicio && $state) {
                                                            $set('fecha_limite', \Carbon\Carbon::parse($inicio)->addMonths((int) $state)->format('Y-m-d'));
                                                        }
                                                    }),
                                                Forms\Components\DatePicker::make('fecha_limite')
                                                    ->label('Fecha Pactada'),
                                                    //->readOnly()
                                                    //->dehydrated(),
                                            ])->columns(3)->columnSpanFull(),
                                    ]),
                            ]),

                        // ---------------------------------------------------------
                        // PESTAÑA 3: APROBACIONES Y CIERRE
                        // ---------------------------------------------------------
                        Forms\Components\Tabs\Tab::make('3. Aprobaciones y Cierre')
                            ->schema([
                                Forms\Components\Section::make('Ingreso y Evaluaciones')
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('f_ingreso_informe_final')
                                            ->label('Ingreso Informe Final'),
                                        Forms\Components\DateTimePicker::make('f_aprobacion_contraparte')
                                            ->label('Aprob. Contraparte'),
                                        Forms\Components\DateTimePicker::make('f_aprobacion_jefe_tecnico')
                                            ->label('Aprob. Jefe/Técnico'),
                                        Forms\Components\DateTimePicker::make('f_aprobacion_director')
                                            ->label('Aprob. Director'),
                                        Forms\Components\TextInput::make('gde_aprobacion_dir')
                                            ->label('GDE Aprob. Director'),
                                    ])->columns(2),

                                Forms\Components\Section::make('Resolución Final')
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('f_pase_gestion')
                                            ->label('Pase a Gestión'),
                                        Forms\Components\DateTimePicker::make('f_aprobacion_sec_gen')
                                            ->label('Aprob. Sec. General'),
                                        Forms\Components\TextInput::make('gde_sec_gen')
                                            ->label('GDE Sec. General'),
                                    ])->columns(3),

                                Forms\Components\Section::make('Cierre y Archivo')
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('f_envio_biblioteca')
                                            ->label('Envío a Biblioteca'),
                                        Forms\Components\TextInput::make('gde_biblioteca')
                                            ->label('GDE Biblioteca'),
                                        Forms\Components\DateTimePicker::make('f_envio_archivo')
                                            ->label('Envío a Archivo'),
                                        Forms\Components\TextInput::make('gde_archivo')
                                            ->label('GDE Archivo'),
                                    ])->columns(2),
                            ]),

                        // ---------------------------------------------------------
                        // PESTAÑA 4: HITOS Y NOVEDADES
                        // ---------------------------------------------------------
                        Forms\Components\Tabs\Tab::make('4. Hitos y Novedades')
                            ->schema([
                                Forms\Components\Repeater::make('hitos')
                                    ->label('Bitácora del Expediente')
                                    ->relationship('hitos')
                                    ->schema([
                                        Forms\Components\DatePicker::make('fecha')
                                            ->label('Fecha')
                                            ->default(now())
                                            ->required(),
                                        Forms\Components\Textarea::make('descripcion')
                                            ->label('Registro / Novedad')
                                            ->required()
                                            ->columnSpan(2),
                                    ])
                                    ->columns(3)
                                    ->addActionLabel('Agregar novedad')
                                    ->defaultItems(0)
                                    ->reorderableWithButtons(), // Permite ordenar si se olvidó de cargar uno viejo
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gde_numero')->label('GDE')->searchable(),
                Tables\Columns\TextColumn::make('titulo')->label('Título')->limit(50)->searchable(),

                Tables\Columns\TextColumn::make('estadoContrato.estado') // Asumiendo que la columna de tu tabla maestra se llama 'nombre'
                    ->label('Estado')
                    ->badge()
                    // Si querés mantener los colores, podés hacer un match con el ID o el nombre:
                    ->color(fn (string $state): string => match ($state) {
                        'Borrador / Sin ingresar' => 'gray',
                        'Ingresado al CFI' => 'blue',
                        'En análisis' => 'info',
                        'En trámite' => 'warning',
                        'En ejecución' => 'success',
                        'Finalizado' => 'info',
                        'Archivado' => 'danger',
                        default => 'warning',
                }),
/*                 Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ingresado al CFI' => 'gray',
                        'En análisis' => 'info', // Azul
                        'En trámite' => 'warning', // Amarillo/Naranja
                        'En ejecución' => 'primary', // Color principal de Filament
                        'Finalizado' => 'success', // Verde
                        'Archivado' => 'gray', // Gris
                        'Dado de baja' => 'danger', // Rojo
                        'Rescindido' => 'danger', // Rojo
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(), */

                // Como en tu tabla users tenés 'nombre' y 'apellido', lo mostramos así
                Tables\Columns\TextColumn::make('tecnico.usuario')->label('Técnico')
                ->hidden(fn () => auth()->user()->hasRole('Técnico')),
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
