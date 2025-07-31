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
use Filament\Forms\Components\Radio;
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
                        Grid::make(['default' => 1,])
                            ->schema([
                                Radio::make('copy_mode')
                                    ->label('Pilih Mode Copy')
                                    ->options([
                                        'position' => 'Berdasarkan Posisi',
                                        'individual' => 'Berdasarkan User Individual',
                                    ])
                                    ->default('position')
                                    ->inline()
                                    ->required()
                                    ->reactive(),
                                
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
                                                return $user->nama_lengkap;
                                            })->implode(', ');

                                            $options[$position->id] = $position->name . ' - ' . $userNames;
                                        }

                                        return $options;
                                    })
                                    ->visible(fn ($get) => $get('copy_mode') === 'position')
                                    ->required(fn ($get) => $get('copy_mode') === 'position'),

                                Select::make('source_users')
                                    ->label('Copy dari User')
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->options(function (): array {
                                        $options = [];

                                        // BU VEGA
                                        if (auth()->user()->role_id == 5 && auth()->user()->divisi_id == 3) {
                                            $users = User::whereIn('role_id', [4, 5, 3, 2])
                                                ->where('divisi_id', auth()->user()->divisi_id)
                                                ->get();
                                            // KALAU ROLE MANAGER BUAT KPI UNTUK ROLE COORDINATOR & MANAGER
                                        } else if (auth()->user()->role_id == 5 && auth()->user()->divisi_id != 3) {
                                            $users = User::whereIn('role_id', [4, 5])
                                                ->where('divisi_id', auth()->user()->divisi_id)
                                                ->get();
                                            // KALAU ROLE COORDINATOR BUAT KPI UNTUK ROLE COORDINATOR & TEAM LEADER & STAFF
                                        } else if (auth()->user()->role_id == 4) {
                                            $users = User::whereIn('role_id', [4, 3, 2])
                                                ->where('divisi_id', auth()->user()->divisi_id)
                                                ->get();
                                        } else {
                                            $users = User::all();
                                        }

                                        foreach ($users as $user) {
                                            $positionName = $user->position ? $user->position->name : 'No Position';
                                            $options[$user->id] = $user->nama_lengkap . ' (' . $positionName . ')';
                                        }

                                        return $options;
                                    })
                                    ->visible(fn ($get) => $get('copy_mode') === 'individual')
                                    ->required(fn ($get) => $get('copy_mode') === 'individual'),

                                Select::make('target_users')
                                    ->label('Copy ke User')
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->options(function (): array {
                                        $options = [];

                                        // BU VEGA
                                        if (auth()->user()->role_id == 5 && auth()->user()->divisi_id == 3) {
                                            $users = User::whereIn('role_id', [4, 5, 3, 2])
                                                ->where('divisi_id', auth()->user()->divisi_id)
                                                ->get();
                                            // KALAU ROLE MANAGER BUAT KPI UNTUK ROLE COORDINATOR & MANAGER
                                        } else if (auth()->user()->role_id == 5 && auth()->user()->divisi_id != 3) {
                                            $users = User::whereIn('role_id', [4, 5])
                                                ->where('divisi_id', auth()->user()->divisi_id)
                                                ->get();
                                            // KALAU ROLE COORDINATOR BUAT KPI UNTUK ROLE COORDINATOR & TEAM LEADER & STAFF
                                        } else if (auth()->user()->role_id == 4) {
                                            $users = User::whereIn('role_id', [4, 3, 2])
                                                ->where('divisi_id', auth()->user()->divisi_id)
                                                ->get();
                                        } else {
                                            $users = User::all();
                                        }

                                        foreach ($users as $user) {
                                            $positionName = $user->position ? $user->position->name : 'No Position';
                                            $options[$user->id] = $user->nama_lengkap . ' (' . $positionName . ')';
                                        }

                                        return $options;
                                    })
                                    ->visible(fn ($get) => $get('copy_mode') === 'individual')
                                    ->required(fn ($get) => $get('copy_mode') === 'individual'),

                                Grid::make(['default' => 2,])
                                    ->schema([
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
                    ])
                    ->modalWidth('md')
                    ->modalHeading('Copy KPI')
                    ->action(function (array $data) {
                        try {
                            $fromDate = Carbon::createFromFormat('Y-m', $data['tanggal1'])->startOfMonth();
                            $toDate = Carbon::createFromFormat('Y-m', $data['tanggal2'])->startOfMonth();
                            $copyMode = $data['copy_mode'];

                            $copiedCount = 0;
                            $skippedCount = 0;

                            if ($copyMode === 'position') {
                                // Copy berdasarkan posisi (logika lama)
                                $users = User::where('position_id', $data['position'])->pluck('id');

                                $kpis = Kpi::whereMonth('date', $fromDate->month)
                                    ->whereYear('date', $fromDate->year)
                                    ->whereIn('user_id', $users)
                                    ->get();

                                // Get existing KPIs for the target month to avoid duplicates
                                $existingKpis = Kpi::whereMonth('date', $toDate->month)
                                    ->whereYear('date', $toDate->year)
                                    ->whereIn('user_id', $users)
                                    ->pluck('user_id')
                                    ->toArray();

                                foreach ($kpis as $kpi) {
                                    // Skip if KPI already exists for this user in target month
                                    if (in_array($kpi->user_id, $existingKpis)) {
                                        $skippedCount++;
                                        continue;
                                    }

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

                                    $copiedCount++;
                                }
                            } else {
                                // Copy berdasarkan user individual (logika baru)
                                $sourceUsers = $data['source_users'];
                                $targetUsers = $data['target_users'];

                                // Get KPIs from source users
                                $kpis = Kpi::whereMonth('date', $fromDate->month)
                                    ->whereYear('date', $fromDate->year)
                                    ->whereIn('user_id', $sourceUsers)
                                    ->get();

                                // Get existing KPIs for target users in target month to avoid duplicates
                                $existingKpis = Kpi::whereMonth('date', $toDate->month)
                                    ->whereYear('date', $toDate->year)
                                    ->whereIn('user_id', $targetUsers)
                                    ->pluck('user_id')
                                    ->toArray();

                                foreach ($kpis as $kpi) {
                                    foreach ($targetUsers as $targetUserId) {
                                        // Skip if KPI already exists for this target user in target month
                                        if (in_array($targetUserId, $existingKpis)) {
                                            $skippedCount++;
                                            continue;
                                        }

                                        $newKpi = $kpi->replicate();
                                        $newKpi->user_id = $targetUserId;
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

                                        $copiedCount++;
                                    }
                                }
                            }

                            $message = "KPI copied successfully. {$copiedCount} KPI(s) copied";
                            if ($skippedCount > 0) {
                                $message .= ", {$skippedCount} KPI(s) skipped (already exists)";
                            }

                            Notification::make()
                                ->title($message)
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
