<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CutpointResource\Pages;
use App\Filament\Resources\CutpointResource\RelationManagers;
use App\Models\Cutpoint;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CutpointResource extends Resource
{
    protected static ?string $model = Cutpoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi User')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->options(User::orderBy('nama_lengkap')->pluck('nama_lengkap', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $user = User::with(['position', 'divisi'])->find($state);
                                $set('user_position', $user?->position?->name ?? '-');
                                $set('user_divisi', $user?->divisi?->name ?? '-');
                            })
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Placeholder::make('user_position')
                                    ->label('Posisi')
                                    ->content(fn ($get) => $get('user_position') ?? '-')
                                    ->columnSpan(1),
                                Forms\Components\Placeholder::make('user_divisi')
                                    ->label('Divisi')
                                    ->content(fn ($get) => $get('user_divisi') ?? '-')
                                    ->columnSpan(1),
                            ]),
                    ]),
                Forms\Components\Section::make('Input Cutpoint')
                    ->schema([
                        Forms\Components\TextInput::make('periode')
                            ->label('Periode')
                            ->required()
                            ->maxLength(7)
                            ->default(fn() => now()->format('Y-m'))
                            ->regex('/^\d{4}-\d{2}$/', 'Format yang valid adalah tahun-bulan (YYYY-MM).')
                            ->extraInputAttributes(['type' => 'month'])
                            ->rule('date_format:Y-m'),
                        Forms\Components\TextInput::make('point')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('keterangan')
                            ->maxLength(255),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('point')
                    ->numeric(),
                Tables\Columns\TextColumn::make('periode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->options(User::orderBy('nama_lengkap')->pluck('nama_lengkap', 'id'))
                    ->visible(fn () => auth()->user()?->can('create', \App\Models\Cutpoint::class)),
                Tables\Filters\Filter::make('periode')
                    ->form([
                        Forms\Components\TextInput::make('periode')
                            ->label('Periode')
                            ->required()
                            ->maxLength(7)
                            ->default(fn() => now()->format('Y-m'))
                            ->regex('/^\d{4}-\d{2}$/', 'Format yang valid adalah tahun-bulan (YYYY-MM).')
                            ->extraInputAttributes(['type' => 'month'])
                            ->rule('date_format:Y-m'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['periode'] ?? null)) {
                            $query->where('periode', 'like', "%{$data['periode']}%");
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCutpoints::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        // Jika user tidak bisa create (berdasarkan policy), hanya tampilkan data miliknya
        if ($user && !($user->can('create', \App\Models\Cutpoint::class))) {
            $query->where('user_id', $user->id);
        }
        return $query;
    }
}
