<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Divisi;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Pengguna')
                    ->schema([
                        Forms\Components\TextInput::make('employee_id')
                            ->label('ID Karyawan')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('area_id')
                            ->preload()
                            ->searchable()
                            ->relationship('area', 'name')
                            ->reactive()
                            ->label('Area')
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('divisi_id', null);
                                $set('approval_id', null);
                            }),
                        Forms\Components\Select::make('divisi_id')
                            ->preload()
                            ->searchable()
                            ->reactive()
                            ->options(function (callable $get) {
                                $area_id = $get('area_id');
                                if (!$area_id) {
                                    return [];
                                }
                                return Divisi::where('area_id', $area_id)
                                    ->pluck('name', 'id');
                            })
                            ->label('Divisi')
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('approval_id', null);
                            }),
                        Forms\Components\Select::make('role_id')
                            ->preload()
                            ->searchable()
                            ->relationship('role', 'name')
                            ->label('Jabatan')
                            ->required(),
                        Forms\Components\Select::make('position_id')
                            ->preload()
                            ->searchable()
                            ->relationship('position', 'name')
                            ->label('Posisi')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                            ]),
                        Forms\Components\Select::make('approval_id')
                            ->preload()
                            ->searchable()
                            ->options(function (callable $get) {
                                $area_id = $get('area_id');
                                $divisi_id = $get('divisi_id');
                                if (!$area_id) {
                                    return [];
                                }
                                if (!$divisi_id) {
                                    return [];
                                }

                                return User::where('role_id', 6)
                                    ->where('area_id', $area_id)
                                    ->orWhereIn('role_id', [3, 4, 5])
                                    ->where('divisi_id', $divisi_id)
                                    ->orderBy('nama_lengkap')
                                    ->pluck('nama_lengkap', 'id');
                            })
                            ->label('Approval')
                            ->columnSpan(2),
                    ])
                    ->collapsible()
                    ->columnSpan(2)
                    ->columns(2),

                Forms\Components\Split::make([
                    Forms\Components\Group::make([
                        Forms\Components\Section::make('Login')
                            ->schema([
                                Forms\Components\TextInput::make('username')
                                    ->label('Username')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->dehydrateStateUsing(fn($state) => strtolower($state))
                                    ->regex('/^[\S]+$/', 'Username tidak boleh mengandung spasi')
                                    ->helperText('Username tidak boleh mengandung spasi'),
                                Forms\Components\TextInput::make('password')
                                    ->label('Kata Sandi')
                                    ->password()
                                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                    ->dehydrated(fn($state) => filled($state))
                                    ->maxLength(255)
                                    ->label('Password')
                                    ->placeholder('Masukkan password')
                                    ->required(fn(string $context): bool => $context === 'create')
                                    ->revealable(),
                            ])
                            ->collapsible()
                            ->columns(1)
                            ->columnSpan(2),

                        Forms\Components\Section::make()
                            ->schema([
                                Checkbox::make('dr')
                                    ->label('Hasil Harian'),

                                Checkbox::make('wn')
                                    ->label('Non Mingguan')
                                    ->required(),

                                Checkbox::make('wr')
                                    ->label('Hasil Mingguan')
                                    ->required(),

                                Checkbox::make('mn')
                                    ->label('Non Bulanan')
                                    ->required(),

                                Checkbox::make('mr')
                                    ->label('Hasil Bulanan')
                                    ->required(),
                            ])
                            ->columns(1)
                            ->columnSpan(2),
                    ])
                        ->columns(1)
                        ->columnSpan(2),
                ])
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('ID Karyawan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('area.name'),
                Tables\Columns\TextColumn::make('divisi.name'),
                Tables\Columns\TextColumn::make('position.name')
                    ->label('Posisi'),
                Tables\Columns\TextColumn::make('role.name')
                    ->label('Jabatan'),
                // Tables\Columns\IconColumn::make('d')
                //     ->boolean(),
                // Tables\Columns\IconColumn::make('dr')
                //     ->boolean(),
                // Tables\Columns\IconColumn::make('wn')
                //     ->boolean(),
                // Tables\Columns\IconColumn::make('wr')
                //     ->boolean(),
                // Tables\Columns\IconColumn::make('mn')
                //     ->boolean(),
                // Tables\Columns\IconColumn::make('mr')
                //     ->boolean(),
                Tables\Columns\TextColumn::make('approval.nama_lengkap'),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $query->whereNull('deleted_at');

        if (auth()->user()->role?->name === 'ADMIN') {
            return $query;
        } elseif (auth()->user()->role?->name === 'MANAGER') {
            return $query->where('divisi_id', auth()->user()->divisi_id);
        } else {
            return $query->where('approval_id', auth()->id());
        }
    }

    public static function getNavigationLabel(): string
    {
        if (auth()->user()->role?->name === 'ADMIN') {
            return 'Karyawan';
        }

        return 'Tim Saya';
    }
}
