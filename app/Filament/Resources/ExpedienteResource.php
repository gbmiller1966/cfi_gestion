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
use Doctrine\DBAL\Query\Limit;

class ExpedienteResource extends Resource
{
    protected static ?string $model = Expediente::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Expedientes';
    protected static ?string $modelLabel = 'Expediente';


    public static function shouldRegisterNavigation(): bool
    {
        // Si el usuario es Técnico, NO registrar en el menú lateral
        if (auth()->user()?->hasRole('Técnico')) {
            return false;
        }

        return true;
    }

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
                                // 1. EL SELECT (Para Crear y Editar)
                                Forms\Components\Select::make('localidad_id')
                                    ->label('Localidad')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->relationship(
                                        name: 'localidad',
                                        titleAttribute: 'localidad', // Asegurate que la columna en la tabla localidades se llame 'localidad'
                                        modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query, Forms\Get $get) =>
                                            $query->where('provincia_id', $get('provincia_id'))
                                    )
                                    ->live()
                                    ->hidden(fn ($operation) => $operation === 'view'), // Oculto en modo visualización

                                // 2. EL PLACEHOLDER (Para Visualización)
                                Forms\Components\Placeholder::make('localidad_visual')
                                    ->label('Localidad')
                                    ->visible(fn ($operation) => $operation === 'view') // Solo visible al "Ver"
                                    ->content(function ($record) {
                                        if (!$record || !$record->localidad_id) return '-';

                                        // Buscamos el nombre de la localidad manualmente para que no muestre el ID
                                        return \App\Models\Localidad::find($record->localidad_id)?->localidad ?? 'No definida';
                                    }),
                                Forms\Components\Textarea::make('objeto')
                                    ->required()
                                    ->columnSpanFull(),
                                    ])->columns(2),

                                Forms\Components\Section::make('Clasificación y Estado')
                                    ->schema([
                                        Forms\Components\Select::make('asignacion_id')
                                            ->relationship('asignacion', 'asignacion')
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('tema_id')
                                            ->relationship('tema', 'tema')
                                            ->searchable()
                                            ->preload(),
                                        // 1. EL SELECT (Para Crear y Editar)
                                        Forms\Components\Select::make('tipo_id')
                                            ->relationship('tipo', 'tipo')
                                            ->label('Tipo')
                                            ->searchable()
                                            ->preload()
                                            ->hidden(fn ($operation) => $operation === 'view'), // Se oculta al visualizar

                                        // 2. EL PLACEHOLDER (Para Visualización)
                                        Forms\Components\Placeholder::make('tipo_visual')
                                            ->label('Tipo')
                                            ->visible(fn ($operation) => $operation === 'view') // Solo se ve al visualizar
                                            ->content(function ($record) {
                                                if (!$record || !$record->tipo_id) return '-';

                                                // Buscamos el nombre del tipo manualmente en la tabla 'tipos'
                                                // Usamos la columna 'tipo' que es donde guardas el texto
                                                return \App\Models\Tipo::find($record->tipo_id)?->tipo ?? 'Tipo no definido';
                                            }),

                                        // Muestra el estado actual calculado (Solo lectura para que el técnico lo vea)
                                        Forms\Components\Placeholder::make('estado_actual_visual')
                                            ->label('Estado del Contrato')
                                            ->content(function ($record) {
                                                if (!$record) return 'Borrador / Sin Ingresar';

                                                // 1. Si el record tiene un estado_id (que es ese "3" que ves)
                                                if (isset($record->estado_id)) {
                                                    // Buscamos el nombre del estado directamente en su tabla
                                                    $nombreEstado = \App\Models\Estado::find($record->estado_id)?->estado;

                                                    return $nombreEstado ?? 'Estado no definido';
                                                }

                                                return 'Borrador / Sin Ingresar';
                                            }),

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
                                        Forms\Components\DatePicker::make('f_ingreso_cfi')
                                            ->label('Ingreso a Coord. CFI')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\DatePicker::make('f_ingreso_area')
                                            ->label('Derivación al Área')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\DatePicker::make('f_derivacion_tecnico')
                                            ->label('Derivación al Técnico')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                    ])->columns(3),

                                Forms\Components\Section::make('Actores y Montos')
                                    ->schema([
                                        Forms\Components\Select::make('proveedores')
                                            ->multiple()
                                            ->relationship('proveedores', 'razon_social')
                                            ->preload(),
                                Forms\Components\Select::make('contraparte_id')
                                    ->label('Contraparte Provincial')
                                    ->relationship('contraparte', 'apellido')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->apellido}, {$record->nombre} ({$record->provincia?->provincia})")
                                    ->searchable(['apellido', 'nombre'])
                                    ->preload()
                                    ->getSearchResultsUsing(fn (string $search): array => \App\Models\Contraparte::where('apellido', 'like', "%{$search}%")
                                        ->orWhere('nombre', 'like', "%{$search}%")
                                        ->limit(50) // Trae los mejores 50 resultados según lo que escribió
                                        ->get()
                                        ->mapWithKeys(fn ($record) => [$record->id => "{$record->apellido}, {$record->nombre} ({$record->provincia?->provincia})"])
                                        ->toArray()
                                    )
                                    // Importante: para que cuando edites cargue el valor actual aunque no esté pre-cargado
                                    ->getOptionLabelUsing(fn ($value): ?string => ($record = \App\Models\Contraparte::find($value))
                                        ? "{$record->apellido}, {$record->nombre} ({$record->provincia?->provincia})"
                                        : null
                                    ),
                                        Forms\Components\Select::make('colaboradores')
                                            ->multiple()
                                            ->relationship('colaboradores', 'apellido')
                                            ->label('Técnicos Colaboradores')
                                            ->preload()
                                            ->searchable(),

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
                                        Forms\Components\TextInput::make('monto_imputado')
                                            ->label('Monto Imputado Total')
                                            ->numeric()
                                            ->prefix('$')
                                            ->readOnly() // Se calcula solo, el usuario no lo toca
                                            ->placeholder('Se calculará automáticamente al guardar'),
                                    ])->columns(3),
                            ]),

                        // ---------------------------------------------------------
                        // PESTAÑA 2: TDR, CONTRATO E INFORMES
                        // ---------------------------------------------------------
                        Forms\Components\Tabs\Tab::make('2. TDR y Contrato')
                            ->schema([
                                Forms\Components\Section::make('Términos de Referencia (TDR)')
                                    ->schema([
                                        Forms\Components\DatePicker::make('f_elevacion_tdr')
                                            ->label('Elevación TDR (Jefe)')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\DatePicker::make('f_firma_jefe_tdr')
                                            ->label('Firma Jefe TDR')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\DatePicker::make('f_firma_director_tdr')
                                            ->label('Firma Director TDR')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\TextInput::make('gde_tdr')
                                            ->label('N° GDE TDR'),
                                    ])->columns(2),

                                Forms\Components\Section::make('Gestión del Contrato')
                                    ->schema([
                                        Forms\Components\DatePicker::make('f_derivacion_compras')
                                            ->label('Pase a Compras')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\DatePicker::make('f_inicio_contrato')
                                            ->label('Inicio de Contrato')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d')
                                            ->live(),
                                        Forms\Components\DatePicker::make('f_fin_contrato')
                                            ->label('Fin de Contrato')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d')
                                            ->live(),
                                    ])->columns(3),

                                Forms\Components\Section::make('Planificación de Informes')
                                    ->schema([
                                        Forms\Components\Repeater::make('expediente_informes') // Usamos el nombre real de la relación HasMany
                                            ->label('Planificación de Informes')
                                            ->relationship('expediente_informes')
                                            ->defaultItems(0)
                                            ->schema([
                                                Forms\Components\Select::make('informe_id') // El campo en la BD
                                                    ->label('Tipo de Informe')
                                                    ->relationship('informe', 'informe') // Relación 'informe' en modelo ExpedienteInforme, columna 'informe' en tabla informes
                                                    ->required()
                                                    ->preload()
                                                    ->searchable(),

                                                Forms\Components\TextInput::make('meses_pactados')
                                                    ->label('Meses')
                                                    ->numeric()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                                        $inicio = $get('../../f_inicio_contrato');
                                                        if ($inicio && $state) {
                                                            $set('fecha_limite', \Carbon\Carbon::parse($inicio)->addMonths((int) $state)->format('Y-m-d'));
                                                        }
                                                    }),

                                                Forms\Components\DatePicker::make('fecha_limite')
                                                    ->label('Fecha Pactada')
                                                    ->native(false)
                                                    ->displayFormat('d/m/Y')
                                                    ->format('Y-m-d'),
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
                                        Forms\Components\DatePicker::make('f_ingreso_informe_final')
                                            ->label('Ingreso Informe Final')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\DatePicker::make('f_aprobacion_contraparte')
                                            ->label('Aprob. Contraparte')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\DatePicker::make('f_aprobacion_jefe_tecnico')
                                            ->label('Aprob. Jefe/Técnico')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\DatePicker::make('f_aprobacion_director')
                                            ->label('Aprob. Director')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\TextInput::make('gde_aprobacion_dir')
                                            ->label('GDE Aprob. Director'),
                                    ])->columns(2),

                                Forms\Components\Section::make('Resolución Final')
                                    ->schema([
                                        Forms\Components\DatePicker::make('f_pase_gestion')
                                            ->label('Pase a Gestión')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\DatePicker::make('f_aprobacion_sec_gen')
                                            ->label('Aprob. Sec. General')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\TextInput::make('gde_sec_gen')
                                            ->label('GDE Sec. General'),
                                    ])->columns(3),

                                Forms\Components\Section::make('Cierre y Archivo')
                                    ->schema([
                                        Forms\Components\DatePicker::make('f_envio_biblioteca')
                                            ->label('Envío a Biblioteca')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\TextInput::make('gde_biblioteca')
                                            ->label('GDE Biblioteca'),
                                        Forms\Components\DatePicker::make('f_envio_archivo')
                                            ->label('Envío a Archivo')
                                            ->native(false) // Esto hace que el picker sea más amigable
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d'),
                                        Forms\Components\TextInput::make('gde_archivo')
                                            ->label('GDE Archivo'),
                                    ])->columns(2),
                            ]),

                    // ---------------------------------------------------------
                    // PESTAÑA 4: Hitos
                    // ---------------------------------------------------------
                    Forms\Components\Tabs\Tab::make('4. Hitos y Novedades')
                        ->schema([
                            // --- 1. BLOQUE DE VISUALIZACIÓN (Agrupado por Fecha) ---
                            // Este bloque solo se ve cuando entrás a "Ver" el expediente
                            Forms\Components\Placeholder::make('bitacora_agrupada')
                                ->label('Bitácora de Gestión')
                                ->visible(fn ($operation) => $operation === 'view')
                                ->content(fn ($record) => view('filament.components.bitacora-agrupada', [
                                    'hitos' => $record->hitos()
                                                    ->orderBy('fecha', 'desc')
                                                    ->get()
                                                    ->groupBy(fn($item) => \Carbon\Carbon::parse($item->fecha)->format('d/m/Y'))
                                ])),

                            // --- 2. BLOQUE DE EDICIÓN (Tu Repeater original) ---
                            // Este bloque se ve en "Crear" y "Editar", permitiendo al técnico cargar datos
                            Forms\Components\Repeater::make('hitos')
                                ->label('Carga de Novedades')
                                ->relationship('hitos')
                                ->hidden(fn ($operation) => $operation === 'view') // Se oculta en modo vista
                                ->schema([
                                    Forms\Components\DatePicker::make('fecha')
                                        ->label('Fecha')
                                        ->default(now())
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('d/m/Y'),

                                    Forms\Components\Textarea::make('descripcion')
                                        ->label('Registro / Novedad')
                                        ->required()
                                        ->columnSpanFull(),
                                ])
                                ->itemLabel(fn (array $state): ?string => 
                                    !empty($state['fecha']) ? \Carbon\Carbon::parse($state['fecha'])->format('d/m/Y') : 'Nueva Novedad'
                                )
                                ->collapsible()
                                ->collapsed()
                                ->defaultItems(0),
                        ])
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
/*         ->headerActions(
            auth()->user()->hasRole('Director') ? [] : [Tables\Actions\CreateAction::make()]
        ) */
        ->columns([
            // --- COLUMNAS BÁSICAS (Visibles para todos) ---
            Tables\Columns\TextColumn::make('gde_numero')
                ->label('GDE')
                ->searchable()
                ->sortable()
                ->limit(15),

            Tables\Columns\TextColumn::make('titulo')
                ->label('Título')
                ->searchable()
                ->wrap()
                // ->words(5)
                ->limit(20),

            Tables\Columns\TextColumn::make('provincia.provincia')
                ->label('Provincia')
                ->sortable(),

            // --- COLUMNA TÉCNICO (Oculta para el técnico, visible para Director/Admin) ---
            Tables\Columns\TextColumn::make('tecnico.apellido')
                ->label('Técnico a cargo')
                ->hidden(fn () => auth()->user()->hasRole('Técnico'))
                ->searchable(),

/*             Tables\Columns\TextColumn::make('monto_convenido')
                ->label('Monto Convenido')
                ->money('ARS')
                ->sortable() */
            Tables\Columns\TextColumn::make('monto_imputado')
                ->label('Monto Imputado')
                //->money('ARS')  O tu moneda local
                ->sortable()
                ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Imputado'))
                ->visible(fn () => !auth()->user()->hasRole('Técnico')),

            Tables\Columns\TextColumn::make('estado.estado')
                ->label('Estado')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'En ejecución' => 'success',
                    'Finalizado' => 'info',
                    'Archivado' => 'gray',
                    default => 'warning',
                }),

            // --- SECCIÓN DE FECHAS (Solo Director/Admin) ---
            // Usamos toggleable() para que puedan ocultarlas si la tabla queda muy ancha
            Tables\Columns\TextColumn::make('f_ingreso_cfi')
                ->label('Ingreso CFI')
                ->date('d/m/Y')
                ->toggleable(isToggledHiddenByDefault: true)
                ->visible(fn () => auth()->user()->hasAnyRole(['Admin', 'Director'])),

            Tables\Columns\TextColumn::make('f_ingreso_area')
                ->label('Ingreso Área')
                ->date('d/m/Y')
                ->toggleable(isToggledHiddenByDefault: true)
                ->visible(fn () => auth()->user()->hasAnyRole(['Admin', 'Director'])),

            Tables\Columns\TextColumn::make('f_firma_director_tdr')
                ->label('Firma Dirección')
                ->date('d/m/Y')
                ->toggleable(isToggledHiddenByDefault: true)
                ->visible(fn () => auth()->user()->hasAnyRole(['Admin', 'Director'])),

            Tables\Columns\TextColumn::make('f_inicio_contrato')
                ->label('Inicio Contrato')
                ->date('d/m/Y')
                ->toggleable(isToggledHiddenByDefault: true)
                ->visible(fn () => auth()->user()->hasAnyRole(['Admin', 'Director'])),

            // --- CÁLCULOS DE TIEMPOS DE GESTIÓN (Días) ---
            // 1. Desde ingreso CFI hasta ingreso al Área
            Tables\Columns\TextColumn::make('dias_cfi_area')
                ->label('Días (CFI-Área)')
                ->state(function ($record) {
                    if (!$record->f_ingreso_cfi || !$record->f_ingreso_area) return '-';
                    $dias = Carbon::parse($record->f_ingreso_cfi)->diffInDays(Carbon::parse($record->f_ingreso_area));
                return round($dias) . ' d.';
                })
                ->visible(fn () => auth()->user()->hasAnyRole(['Admin', 'Director'])),

            // 2. Desde ingreso Área hasta Firma Dirección
            Tables\Columns\TextColumn::make('dias_area_firma')
                ->label('Días (Área-Dir)')
                ->state(function ($record) {
                    if (!$record->f_ingreso_area || !$record->f_firma_director_tdr) return '-';
                    $dias = Carbon::parse($record->f_ingreso_area)->diffInDays(Carbon::parse($record->f_firma_director_tdr));
                    return round($dias) . ' d.';
                })
                ->visible(fn () => auth()->user()->hasAnyRole(['Admin', 'Director'])),

            // 3. Desde Firma Dirección hasta Inicio Contrato
            Tables\Columns\TextColumn::make('dias_firma_contrato')
                ->label('Días (Firma-Contrato)')
                ->state(function ($record) {
                    if (!$record->f_firma_director_tdr || !$record->f_inicio_contrato) return '-';
                    $dias = Carbon::parse($record->f_firma_director_tdr)->diffInDays(Carbon::parse($record->f_inicio_contrato));
                    return round($dias) . ' d.';
                })
                ->visible(fn () => auth()->user()->hasAnyRole(['Admin', 'Director'])),
        ])
        ->paginated([10, 25, 50, 100, 'all' => 'Todos'])
        ->defaultPaginationPageOption(10)
        //->extremePaginationLinks()

        ->filters([
            // Filtro por Estado
            Tables\Filters\SelectFilter::make('estado_id')
                ->label('Estado')
                ->relationship('estado', 'estado')
                ->multiple()
                ->preload(),

            // Filtro por Provincia
            Tables\Filters\SelectFilter::make('provincia_id')
                ->label('Provincia')
                ->relationship('provincia', 'provincia')
                ->searchable()
                ->preload(),

            // Filtro por Técnico (Súper útil para el Director)
            Tables\Filters\SelectFilter::make('user_id')
                ->label('Técnico')
                ->relationship('tecnico', 'apellido')
                ->searchable()
                ->preload()
                ->visible(fn () => auth()->user()->hasAnyRole(['Admin', 'Director'])),
        ])
        ->actions([
            // 💡 Acción de Ver: Misma pestaña, visible para todos
            Tables\Actions\ViewAction::make()
                ->label('Ver')
                ->icon('heroicon-m-eye')
                ->openUrlInNewTab(false), // 👈 Aseguramos misma pestaña

            // 💡 Acción de Editar: Misma pestaña, visible para Admin y Técnico
            Tables\Actions\EditAction::make()
                ->label('Editar')
                ->icon('heroicon-m-pencil-square')
                ->openUrlInNewTab(false) // 👈 Aseguramos misma pestaña
                ->visible(fn() => auth()->user()->hasAnyRole(['Admin', 'Técnico'])),

            // 💡 Acción de Borrar: Solo para el Admin
            Tables\Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()->hasRole('Admin')),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                // Solo el Admin puede borrar muchos expedientes de una
            Tables\Actions\DeleteBulkAction::make()
                ->visible(fn () => auth()->user()->hasRole('Admin')),
            ]),
        ]);
    }

    // EL FILTRO MÁGICO PARA QUE EL TÉCNICO VEA SOLO LO SUYO
public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // 1. Admin: Ve TODO el CFI
        if ($user->hasRole('Admin')) {
            return $query;
        }

        // 2. Director: Ve los expedientes de todos los técnicos que pertenezcan a su DIRECCIÓN
        if ($user->hasRole('Director')) {
            return $query->whereHas('tecnico', function ($q) use ($user) {
                $q->where('direccion_id', $user->direccion_id);
            });
        }

        // 3. Jefe de Área: Ve los expedientes de todos los técnicos que pertenezcan a su ÁREA
        if ($user->hasRole('Jefe de Área')) {
            return $query->whereHas('tecnico', function ($q) use ($user) {
                $q->where('area_id', $user->area_id);
            });
        }

        // 4. Técnico (el rol por defecto): Solo ve los suyos
        return $query->where('user_id', $user->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpedientes::route('/'),
            'create' => Pages\CreateExpediente::route('/create'),
            'view' => Pages\ViewExpediente::route('/{record}'),
            'edit' => Pages\EditExpediente::route('/{record}/edit'),
        ];
    }

    protected static function isTablePaginationSimple(): bool
    {
        return false;
    }
}
