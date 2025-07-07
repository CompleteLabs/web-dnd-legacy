<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeReviewResource\Pages;
use App\Filament\Resources\EmployeeReviewResource\RelationManagers;
use App\Models\EmployeeReview;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
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

class EmployeeReviewResource extends Resource
{
    protected static ?string $model = EmployeeReview::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Penilaian';
    protected static ?int $navigationSort = 2;


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

                        TextInput::make('responsiveness')
                            ->label('Responsivitas')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->default(0)
                            ->placeholder('Masukkan angka responsivitas (1-5)')
                            ->helperText('Masukkan angka antara 0 dan 5'),

                        TextInput::make('problem_solver')
                            ->label('Pemecah Masalah')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->default(0)
                            ->placeholder('Masukkan angka pemecah masalah (1-5)')
                            ->helperText('Masukkan angka antara 0 dan 5'),

                        TextInput::make('helpfulness')
                            ->label('Kepedulian')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->default(0)
                            ->placeholder('Masukkan angka kepedulian (1-5)')
                            ->helperText('Masukkan angka antara 0 dan 5'),

                        TextInput::make('initiative')
                            ->label('Inisiatif')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->default(0)
                            ->placeholder('Masukkan angka inisiatif (1-5)')
                            ->helperText('Masukkan angka antara 0 dan 5'),
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
                Tables\Columns\TextColumn::make('responsiveness')
                    ->width('10%')
                    ->wrapHeader()
                    ->alignment(Alignment::Center)
                    ->label('Responsivitas')
                    ->numeric(),
                Tables\Columns\TextColumn::make('problem_solver')
                    ->width('10%')
                    ->wrapHeader()
                    ->alignment(Alignment::Center)
                    ->label('Pemecahan Masalah')
                    ->numeric(),
                Tables\Columns\TextColumn::make('helpfulness')
                    ->width('10%')
                    ->wrapHeader()
                    ->alignment(Alignment::Center)
                    ->label('Kesediaan Membantu')
                    ->numeric(),
                Tables\Columns\TextColumn::make('initiative')
                    ->width('10%')
                    ->wrapHeader()
                    ->alignment(Alignment::Center)
                    ->label('Inisiatif')
                    ->numeric(),
            ])
            ->defaultSort('created_at', 'desc')
            ->deferLoading()
            ->filters([
                SelectFilter::make('periode')
                    ->preload()
                    ->searchable()
                    ->options(function () {
                        return EmployeeReview::distinct('periode')
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
            'index' => Pages\ListEmployeeReviews::route('/'),
            'create' => Pages\CreateEmployeeReview::route('/create'),
            'edit' => Pages\EditEmployeeReview::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role?->name !== 'ADMIN') {
            $query->whereHas('user', function ($query) {
                $query->where('approval_id', auth()->id());
            })->whereNull('deleted_at');
        }

        return $query;
    }
}
