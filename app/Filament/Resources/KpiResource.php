<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KpiResource\Pages;
use App\Filament\Resources\KpiResource\RelationManagers;
use App\Models\Kpi;
use App\Models\KpiCategory;
use App\Models\KpiDescription;
use App\Models\Position;
use App\Models\User;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KpiResource extends Resource
{
    protected static ?string $model = Kpi::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationLabel = 'KPI';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        // Check if we're creating a new record or editing an existing one
        $isCreate = !$form->getRecord();

        if ($isCreate) {
            return static::createForm($form);
        } else {
            return static::editForm($form);
        }
    }

    // Form for creating new KPIs (bulk approach)
    public static function createForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('KPI Information')
                    ->schema([
                        Forms\Components\Select::make('position_id')
                            ->label('Job Position')
                            ->options(function () {
                                $user = auth()->user();
                                $query = Position::query();

                                // Filter positions by user's role
                                if ($user->role_id == 5 && $user->divisi_id == 3) {
                                    // BU VEGA
                                    $query->whereHas('user', function ($q) use ($user) {
                                        $q->whereIn('role_id', [4, 5, 3, 2])
                                            ->where('divisi_id', $user->divisi_id);
                                    });
                                } elseif ($user->role_id == 5 && $user->divisi_id != 3) {
                                    // MANAGER
                                    $query->whereHas('user', function ($q) use ($user) {
                                        $q->whereIn('role_id', [4, 5])
                                            ->where('divisi_id', $user->divisi_id);
                                    });
                                } elseif ($user->role_id == 4) {
                                    // COORDINATOR
                                    $query->whereHas('user', function ($q) use ($user) {
                                        $q->whereIn('role_id', [4, 3, 2])
                                            ->where('divisi_id', $user->divisi_id);
                                    });
                                }

                                return $query->get()->mapWithKeys(function ($position) {
                                    $userNames = $position->user->pluck('nama_lengkap')->implode(', ');
                                    return [$position->id => "{$position->name} - {$userNames}"];
                                });
                            })
                            ->searchable()
                            ->required(),

                        Hidden::make('kpi_type_id')
                            ->default(3),

                        Forms\Components\DatePicker::make('date')
                            ->label('Month')
                            ->displayFormat('m/Y')
                            ->format('m/Y')
                            ->native(false)
                            ->required(),
                    ])->columns(2),

                Tabs::make('KPI Categories')
                    ->tabs([
                        Tabs\Tab::make('MAIN JOB')
                            ->schema([
                                Hidden::make('kpi_category_id_main')
                                    ->default(3),

                                TextInput::make('percentageMain')
                                    ->label('Percentage %')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->placeholder('Enter percentage for Main Job'),

                                TableRepeater::make('kpi_details_main')
                                    ->label('KPI Descriptions')
                                    ->headers([
                                        Header::make('Deskripsi')
                                            ->markAsRequired(),
                                        Header::make('start')
                                            ->width('15%'),
                                        Header::make('end')
                                            ->width('15%'),
                                        Header::make('Count Type')
                                            ->width('15%')
                                            ->markAsRequired(),
                                        Header::make('Value Plan')
                                            ->width('10%'),
                                        Header::make('Subtasks')
                                            ->width('10%'),
                                    ])
                                    ->schema([
                                        Select::make('kpi_description_id_main')
                                            ->label('KPI Description')
                                            ->searchable()
                                            ->options(function () {
                                                return KpiDescription::where('kpi_category_id', 3)
                                                    ->pluck('description', 'id');
                                            })
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('description')
                                                    ->required(),
                                                Forms\Components\Hidden::make('kpi_category_id')
                                                    ->default(3)
                                            ])
                                            ->createOptionUsing(function (array $data) {
                                                $description = KpiDescription::create([
                                                    'description' => $data['description'],
                                                    'kpi_category_id' => $data['kpi_category_id'],
                                                ]);

                                                return $description->id;
                                            })
                                            ->required(),

                                        DatePicker::make('startMain')
                                            ->label('Start Date')
                                            ->native(false),

                                        DatePicker::make('endMain')
                                            ->label('End Date')
                                            ->native(false),

                                        Select::make('count_typeMain')
                                            ->label('Count Type')
                                            ->options([
                                                'NON' => 'NON',
                                                'RESULT' => 'RESULT'
                                            ])
                                            ->required()
                                            ->reactive(),

                                        TextInput::make('value_planMain')
                                            ->label('Value Plan')
                                            ->numeric()
                                            ->required(fn(Get $get) => $get('count_typeMain') === 'RESULT')
                                            ->disabled(fn(Get $get) => $get('count_typeMain') !== 'RESULT'),

                                        // Replace Forms\Components\Actions with direct Repeater
                                        Repeater::make('subtasks')
                                            ->label('Subtasks')
                                            ->schema([
                                                TextInput::make('description')
                                                    ->label('Subtask')
                                                    ->required(),
                                            ])
                                            ->columns(1)
                                            ->collapsed()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                $state['description'] ?? 'New Subtask')
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Subtask'),
                                    ])
                                    ->columnSpan('full')
                                    ->defaultItems(0)
                            ]),

                        Tabs\Tab::make('ADMINISTRATION')
                            ->schema([
                                Hidden::make('kpi_category_id_adm')
                                    ->default(1),

                                TextInput::make('percentageAdm')
                                    ->label('Percentage %')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->placeholder('Enter percentage for Administration'),

                                TableRepeater::make('kpi_details_adm')
                                    ->label('KPI Descriptions')
                                    ->headers([
                                        Header::make('Deskripsi')
                                            ->markAsRequired(),
                                        Header::make('start')
                                            ->width('15%'),
                                        Header::make('end')
                                            ->width('15%'),
                                        Header::make('Count Type')
                                            ->width('15%')
                                            ->markAsRequired(),
                                        Header::make('Value Plan')
                                            ->width('10%'),
                                        Header::make('Subtasks')
                                            ->width('10%'),
                                    ])
                                    ->schema([
                                        Select::make('kpi_description_id_adm')
                                            ->label('KPI Description')
                                            ->searchable()
                                            ->options(function () {
                                                return KpiDescription::where('kpi_category_id', 1)
                                                    ->pluck('description', 'id');
                                            })
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('description')
                                                    ->required(),
                                                Forms\Components\Hidden::make('kpi_category_id')
                                                    ->default(1)
                                            ])
                                            ->createOptionUsing(function (array $data) {
                                                $description = KpiDescription::create([
                                                    'description' => $data['description'],
                                                    'kpi_category_id' => $data['kpi_category_id'],
                                                ]);

                                                return $description->id;
                                            })
                                            ->required(),

                                        DatePicker::make('start')
                                            ->label('Start Date')
                                            ->native(false),

                                        DatePicker::make('end')
                                            ->label('End Date')
                                            ->native(false),

                                        Select::make('count_type')
                                            ->label('Count Type')
                                            ->options([
                                                'NON' => 'NON',
                                                'RESULT' => 'RESULT'
                                            ])
                                            ->required()
                                            ->reactive(),

                                        TextInput::make('value_plan')
                                            ->label('Value Plan')
                                            ->numeric()
                                            ->required(fn(Get $get) => $get('count_type') === 'RESULT')
                                            ->disabled(fn(Get $get) => $get('count_type') !== 'RESULT'),

                                        // Replace Forms\Components\Actions with direct Repeater
                                        Repeater::make('subtasks')
                                            ->label('Subtasks')
                                            ->schema([
                                                TextInput::make('description')
                                                    ->label('Subtask')
                                                    ->required(),
                                            ])
                                            ->columns(1)
                                            ->collapsed()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                $state['description'] ?? 'New Subtask')
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Subtask'),
                                    ])
                                    ->columnSpan('full')
                                    ->defaultItems(0)
                            ]),

                        Tabs\Tab::make('REPORTING')
                            ->schema([
                                Hidden::make('kpi_category_id_rep')
                                    ->default(2),

                                TextInput::make('percentageRep')
                                    ->label('Percentage %')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->placeholder('Enter percentage for Reporting'),

                                TableRepeater::make('kpi_details_rep')
                                    ->label('KPI Descriptions')
                                    ->headers([
                                        Header::make('Deskripsi')
                                            ->markAsRequired(),
                                        Header::make('start')
                                            ->width('15%'),
                                        Header::make('end')
                                            ->width('15%'),
                                        Header::make('Count Type')
                                            ->width('15%')
                                            ->markAsRequired(),
                                        Header::make('Value Plan')
                                            ->width('10%'),
                                        Header::make('Subtasks')
                                            ->width('10%'),
                                    ])
                                    ->schema([
                                        Select::make('kpi_description_id_rep')
                                            ->label('KPI Description')
                                            ->searchable()
                                            ->options(function () {
                                                return KpiDescription::where('kpi_category_id', 2)
                                                    ->pluck('description', 'id');
                                            })
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('description')
                                                    ->required(),
                                                Forms\Components\Hidden::make('kpi_category_id')
                                                    ->default(2)
                                            ])
                                            ->createOptionUsing(function (array $data) {
                                                $description = KpiDescription::create([
                                                    'description' => $data['description'],
                                                    'kpi_category_id' => $data['kpi_category_id'],
                                                ]);

                                                return $description->id;
                                            })
                                            ->required(),

                                        DatePicker::make('startRep')
                                            ->label('Start Date')
                                            ->native(false),

                                        DatePicker::make('endRep')
                                            ->label('End Date')
                                            ->native(false),

                                        Select::make('count_typeRep')
                                            ->label('Count Type')
                                            ->options([
                                                'NON' => 'NON',
                                                'RESULT' => 'RESULT'
                                            ])
                                            ->required()
                                            ->reactive(),

                                        TextInput::make('value_planRep')
                                            ->label('Value Plan')
                                            ->numeric()
                                            ->required(fn(Get $get) => $get('count_typeRep') === 'RESULT')
                                            ->disabled(fn(Get $get) => $get('count_typeRep') !== 'RESULT'),

                                        // Replace Forms\Components\Actions with direct Repeater
                                        Repeater::make('subtasks')
                                            ->label('Subtasks')
                                            ->schema([
                                                TextInput::make('description')
                                                    ->label('Subtask')
                                                    ->required(),
                                            ])
                                            ->columns(1)
                                            ->collapsed()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                $state['description'] ?? 'New Subtask')
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Subtask'),
                                    ])
                                    ->columnSpan('full')
                                    ->defaultItems(0)
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    // Form for editing existing KPIs (original form)
    public static function editForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->preload()
                            ->searchable()
                            ->relationship('user', 'nama_lengkap')
                            ->required()
                            ->disabled(),
                        Forms\Components\DatePicker::make('date')
                            ->label('Periode')
                            ->native(false)
                            ->displayFormat('m/Y')
                            ->required()
                            ->disabled(),
                    ])->columns('2'),

                Section::make()
                    ->schema([
                        Select::make('kpi_category_id')
                            ->label('KPI Category')
                            ->options(
                                KpiCategory::pluck('name', 'id')
                            )
                            ->disabled(),
                        TextInput::make('percentage')
                            ->label('Percentage %')
                            ->required()
                            ->numeric()
                            ->placeholder('Enter Percentage'),

                        TableRepeater::make('kpi_detail')
                            ->relationship('kpi_detail')
                            ->headers([
                                Header::make('Deskripsi')
                                    ->markAsRequired(),
                                Header::make('start')
                                    ->width('12%'),
                                Header::make('end')
                                    ->width('12%'),
                                Header::make('Count Type')
                                    ->width('12%')
                                    ->markAsRequired(),
                                Header::make('Value Plan')
                                    ->width('10%'),
                                Header::make('Subtasks')
                                    ->width('10%'),
                            ])
                            ->schema([
                                Select::make('kpi_description_id')
                                    ->label('KPI Description')
                                    ->preload()
                                    ->searchable()
                                    ->relationship('kpi_description', 'description')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('description')
                                            ->required(),
                                        Forms\Components\Select::make('kpi_category_id')
                                            ->preload()
                                            ->searchable()
                                            ->relationship('kpi_category', 'name')
                                            ->required(),
                                    ])
                                    ->required(),
                                DatePicker::make('start')
                                    ->label('Start Date'),
                                DatePicker::make('end')
                                    ->label('End Date'),
                                Select::make('count_type')
                                    ->reactive()
                                    ->searchable()
                                    ->label('Count Type')
                                    ->options([
                                        'NON' => 'NON',
                                        'RESULT' => 'RESULT'
                                    ])
                                    ->required()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('value_plan', null);
                                    }),
                                TextInput::make('value_plan')
                                    ->numeric()
                                    ->label('Value Plan')
                                    ->required(fn(callable $get) => $get('count_type') === 'RESULT')
                                    ->disabled(fn(callable $get) => $get('count_type') !== 'RESULT'),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('manage_subtasks')
                                        ->hiddenLabel()
                                        ->icon('heroicon-o-clipboard-document-list')
                                        ->color('primary')
                                        ->size('sm')
                                        ->modalWidth('lg')
                                        ->modalHeading('Manage Subtasks')
                                        ->form([
                                            Hidden::make('kpi_detail_id'),
                                            Repeater::make('subtasks')
                                                ->schema([
                                                    TextInput::make('description')
                                                        ->label('Subtask')
                                                        ->required()
                                                        ->columnSpanFull(),
                                                ])
                                                ->columnSpanFull()
                                                ->columns(1)
                                                ->addActionLabel('Add Subtask')
                                                ->itemLabel(fn (array $state): ?string =>
                                                    $state['description'] ?? 'New Subtask')
                                                ->defaultItems(0)
                                                ->reorderable()
                                        ])
                                        ->fillForm(function ($record) {
                                            // Ensure subtasks is always an array
                                            $subtasks = [];

                                            if (isset($record->subtasks)) {
                                                if (is_string($record->subtasks)) {
                                                    // If it's a JSON string, decode it
                                                    try {
                                                        $decoded = json_decode($record->subtasks, true);
                                                        if (is_array($decoded)) {
                                                            $subtasks = $decoded;
                                                        }
                                                    } catch (\Exception $e) {
                                                        // If decoding fails, use empty array
                                                    }
                                                } elseif (is_array($record->subtasks)) {
                                                    $subtasks = $record->subtasks;
                                                }
                                            }

                                            return [
                                                'kpi_detail_id' => $record->id,
                                                'subtasks' => $subtasks,
                                            ];
                                        })
                                        ->action(function (array $data, $record) {
                                            $record->subtasks = $data['subtasks'] ?? [];
                                            $record->save();
                                        }),
                                ]),
                            ])
                            ->columnSpan('full'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    // Rest of the code remains the same
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.position.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kpi_category.name')
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('kpi_type.name')
                    ->label('Type'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Periode')
                    ->dateTime('F Y'),
                Tables\Columns\TextColumn::make('percentage')
                    ->label('Persentase')
                    ->alignment(Alignment::Center)
                    ->numeric()
                    ->formatStateUsing(fn($state) => "{$state}%"),
            ])
            ->defaultSort('created_at', 'desc')
            ->deferLoading()
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListKpis::route('/'),
            'create' => Pages\CreateKpi::route('/create'),
            'edit' => Pages\EditKpi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();
        $role = $user->role?->name;

        if ($role === 'ADMIN') {
            return $query;
        } elseif ($role === 'MANAGER') {
            return $query->whereHas('user', function ($query) {
                $query->where('divisi_id', auth()->user()->divisi_id);
            });
        } else {
            return $query->whereHas('user', function ($query) {
                $query->where('approval_id', auth()->id());
            });
        }
    }
}
