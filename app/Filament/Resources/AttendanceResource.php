<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $navigationLabel = 'Kehadiran';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('user_id')
                            ->preload()
                            ->searchable()
                            ->relationship('user', 'nama_lengkap')
                            ->label('Pengguna')
                            ->required()
                            ->columnSpan(2),

                        TextInput::make('periode')
                            ->label('Periode')
                            ->required()
                            ->maxLength(7)
                            ->default(fn() => now()->format('Y-m'))
                            ->regex('/^\d{4}-\d{2}$/', 'Format yang valid adalah tahun-bulan (YYYY-MM).')
                            ->extraInputAttributes(['type' => 'month'])
                            ->columnSpan(2),

                        TextInput::make('work_days')
                            ->label('Jumlah Hari Kerja')
                            ->required()
                            ->maxLength(2)
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Masukkan jumlah hari kerja'),

                        TextInput::make('late_less_30')
                            ->label('Terlambat < 30 Menit')
                            ->required()
                            ->maxLength(2)
                            ->numeric()
                            ->default(0)
                            ->placeholder('Masukkan jumlah terlambat < 30 menit'),

                        TextInput::make('late_more_30')
                            ->label('Terlambat > 30 Menit')
                            ->required()
                            ->maxLength(2)
                            ->numeric()
                            ->default(0)
                            ->placeholder('Masukkan jumlah terlambat > 30 menit'),

                        TextInput::make('sick_days')
                            ->label('Jumlah Hari Sakit')
                            ->required()
                            ->maxLength(2)
                            ->numeric()
                            ->default(0)
                            ->placeholder('Masukkan jumlah hari sakit'),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.employee_id')
                    ->label('ID Karyawan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.area.name')
                    ->label('Area')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.divisi.name')
                    ->label('Divisi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.position.name')
                    ->label('Posisi')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('periode')
                    ->width('10%')
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('work_days')
                    ->width('10%')
                    ->alignment(Alignment::Center)
                    ->label('Hari Kerja')
                    ->numeric(),
                Tables\Columns\TextColumn::make('late_less_30')
                    ->width('10%')
                    ->wrapHeader()
                    ->alignment(Alignment::Center)
                    ->label(' Keterlambatan < 30 Menit')
                    ->numeric(),
                Tables\Columns\TextColumn::make('late_more_30')
                    ->width('10%')
                    ->wrapHeader()
                    ->alignment(Alignment::Center)
                    ->label('Keterlambatan > 30 Menit')
                    ->numeric(),
                Tables\Columns\TextColumn::make('sick_days')
                    ->width('10%')
                    ->alignment(Alignment::Center)
                    ->label('Sakit/Izin')
                    ->numeric(),
            ])
            ->defaultSort('created_at', 'desc')
            ->deferLoading()
            ->filters([
                SelectFilter::make('periode')
                    ->preload()
                    ->searchable()
                    ->options(function () {
                        return Attendance::distinct('periode')
                            ->pluck('periode', 'periode')
                            ->toArray();
                    })
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role?->name == 'MANAGER') {
            $query->whereHas('user', function ($query) {
                $query->where('divisi_id', auth()->user()->divisi_id);
            });
        } elseif (auth()->user()->role?->name !== 'ADMIN') {
            $query->whereHas('user', function ($query) {
                $query->where('approval_id', auth()->id());
            });
        }

        return $query;
    }
}
