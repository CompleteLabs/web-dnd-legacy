<?php

namespace App\Filament\Resources\KpiResource\Pages;

use App\Filament\Resources\KpiResource;
use App\Models\Divisi;
use App\Models\Position;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\MaxWidth;
use App\Models\Kpi;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use App\Exports\KpiPerDivisionExport;
use Maatwebsite\Excel\Facades\Excel;

class ListKpis extends ListRecords
{
    protected static string $resource = KpiResource::class;
    protected static ?string $title = "KPI";

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ActionGroup::make([
                Action::make('export')
                    ->label('Export KPI')
                    ->icon('heroicon-s-arrow-down-tray')
                    ->form([
                        Select::make('divisi')
                            ->label('Divisi')
                            ->searchable()
                            ->preload()
                            ->options(Divisi::pluck('name', 'id')),
                        TextInput::make('bulan')
                            ->label('Bulan')
                            ->maxLength(7)
                            ->default(fn() => now()->format('Y-m'))
                            ->regex('/^\d{4}-\d{2}$/', 'Format yang valid adalah tahun-bulan (YYYY-MM).')
                            ->extraInputAttributes(['type' => 'month'])
                            ->required(),
                    ])
                    ->modalWidth('md')
                    ->modalHeading('Export KPI')
                    ->modalButton('Export')
                    ->action(function (array $data) {
                        $divisiId = $data['divisi'] ?? auth()->user()->divisi_id;
                        $month = $data['bulan'];
                        $divisi = Divisi::where('id', $divisiId)->first();

                        $filename = 'KPI_' . (auth()->user()->role_id == 1 ? $divisi->name : auth()->user()->divisi->name) . '_' . $month . '.xlsx';

                        return Excel::download(
                            new KpiPerDivisionExport($month, $divisiId),
                            $filename
                        );
                    }),
                Action::make('copy')
                    ->label('Copy KPI')
                    ->icon('heroicon-o-document-duplicate')
                    ->form([
                        Grid::make(['default' => 2,])
                            ->schema([
                                Select::make('position')
                                    ->label('Posisi')
                                    ->searchable()
                                    ->preload()
                                    ->options(function (): array {
                                        $options = [];

                                        // BU VEGA
                                        if (auth()->user()->role_id == 5 && auth()->user()->divisi_id == 3) {
                                            $positions = Position::with('user')->whereHas('user', function ($q) {
                                                $q->whereIn('role_id', [4, 5, 3, 2])
                                                    ->where('divisi_id', auth()->user()->divisi_id);
                                            })->get();
                                            // KALAU ROLE MANAGER BUAT KPI UNTUK ROLE COORDINATOR & MANAGER
                                        } else if (auth()->user()->role_id == 5 && auth()->user()->divisi_id != 3) {
                                            $positions = Position::with('user')->whereHas('user', function ($q) {
                                                $q->whereIn('role_id', [4, 5])
                                                    ->where('divisi_id', auth()->user()->divisi_id);
                                            })->get();
                                            // KALAU ROLE COORDINATOR BUAT KPI UNTUK ROLE COORDINATOR & TEAM LEADER & STAFF
                                        } else if (auth()->user()->role_id == 4) {
                                            $positions = Position::with('user')->whereHas('user', function ($q) {
                                                $q->whereIn('role_id', [4, 3, 2])
                                                    ->where('divisi_id', auth()->user()->divisi_id);
                                            })->get();
                                        } else {
                                            $positions = Position::with('user')->get();
                                        }

                                        foreach ($positions as $position) {
                                            $userNames = collect($position->user)->map(function ($user) {
                                                return $user->nama_lengkap ?? $user->name;
                                            })->implode(', ');

                                            $options[$position->id] = $position->name . ' - ' . $userNames;
                                        }

                                        return $options;
                                    })
                                    ->columnSpanFull(),
                                TextInput::make('tanggal1')
                                    ->label('Dari Bulan')
                                    ->maxLength(7)
                                    ->default(fn() => now()->format('Y-m'))
                                    ->regex('/^\d{4}-\d{2}$/', 'Format yang valid adalah tahun-bulan (YYYY-MM).')
                                    ->extraInputAttributes(['type' => 'month'])
                                    ->required(),
                                TextInput::make('tanggal2')
                                    ->label('Ke Bulan')
                                    ->maxLength(7)
                                    ->default(fn() => now()->addMonth()->format('Y-m'))
                                    ->regex('/^\d{4}-\d{2}$/', 'Format yang valid adalah tahun-bulan (YYYY-MM).')
                                    ->extraInputAttributes(['type' => 'month'])
                                    ->required(),
                            ])
                    ])
                    ->modalWidth('md')
                    ->modalHeading('Copy KPI')
                    ->action(function (array $data) {
                        try {
                            $fromDate = Carbon::createFromFormat('Y-m', $data['tanggal1'])->startOfMonth();
                            $toDate = Carbon::createFromFormat('Y-m', $data['tanggal2'])->startOfMonth();
                            $users = User::where('position_id', $data['position'])->pluck('id');

                            $kpis = Kpi::whereMonth('date', $fromDate->month)
                                ->whereYear('date', $fromDate->year)
                                ->whereIn('user_id', $users)
                                ->get();

                            foreach ($kpis as $kpi) {
                                $newKpi = $kpi->replicate();
                                $newKpi->date = $toDate;
                                $newKpi->save();

                                foreach ($kpi->kpi_detail as $kpiDetail) {
                                    $newKpiDetail = $kpiDetail->replicate();
                                    $newKpiDetail->kpi_id = $newKpi->id;
                                    $newKpiDetail->start = null;
                                    $newKpiDetail->end = null;
                                    $newKpiDetail->value_actual = null;
                                    $newKpiDetail->value_result = 0;
                                    $newKpiDetail->save();
                                }
                            }

                            Notification::make()
                                ->title('KPI copied successfully')
                                ->success()
                                ->send();

                            return redirect()->route('filament.admin.resources.kpis.index');
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error copying KPI')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
                ->label('Lainnya')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->button(),
        ];
    }
}
