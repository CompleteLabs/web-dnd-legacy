<?php

namespace App\Filament\Widgets;

use App\Models\Kpi;
use App\Models\Divisi;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ChecklistKPI extends Widget
{
    protected static string $view = 'filament.widgets.checklist-kpi';
    protected int | string | array $columnSpan = 'full';

    public $month;
    public $user_id;
    public $parent_id;
    public $description;
    public $count_type = 'NON';
    public $value_actual;
    public $kpiDetail;

    public function mount($user_id = null, $month = null): void
    {
        $this->user_id = $user_id ?? null;
        $this->month = $month ?? now()->format('m/Y');
    }

    protected function getKpis()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $kpisQuery = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('user_id', Auth::id())
            ->where('kpi_type_id', 3)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear);

        if ($this->month) {
            try {
                $date = Carbon::createFromFormat('m/Y', $this->month);
            } catch (\Exception $e) {
                try {
                    $date = Carbon::parse($this->month);
                } catch (\Exception $e2) {
                    $date = Carbon::now();
                }
            }

            $kpisQuery = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
                ->where('kpi_type_id', 3)
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year);

            if ($this->user_id) {
                $kpisQuery->where('user_id', $this->user_id);
            } else {
                $kpisQuery->where('user_id', Auth::id());
            }
        }

        $kpis = $kpisQuery->orderBy('date', 'DESC')->get();

        // Group the KPIs by yearly month
        $groupedKpisByYear = $kpis->groupBy(function ($kpi) {
            return CarbonImmutable::parse($kpi->date)->format('Y-m');
        });

        // Group the KPIs by KPI category within each month group
        $groupedKpisByYearAndCategory = [];
        $totalScore = 0;

        foreach ($groupedKpisByYear as $yearMonth => $groupedKpi) {
            $groupedKpiByCategory = $groupedKpi->groupBy('kpi_category.name');

            // Sort the grouped data by category name
            $groupedKpiByCategory = $groupedKpiByCategory->sortBy(function ($kpis, $categoryName) {
                $categoryOrder = ['MAIN JOB', 'ADMINISTRATION', 'REPORTING'];
                $categoryIndex = array_search($categoryName, $categoryOrder);
                return $categoryIndex !== false ? $categoryIndex : count($categoryOrder);
            });

            $groupedKpisByYearAndCategory[$yearMonth] = $groupedKpiByCategory;

            foreach ($groupedKpiByCategory as $categoryName => $kpis) {
                foreach ($kpis as $kpi) {
                    $kpiDetailWithValue = $kpi->kpi_detail->filter(function ($kpiDetail) {
                        return $kpiDetail->value_result !== null && $kpiDetail->value_result >= 0;
                    });

                    $actualCount = $kpiDetailWithValue->sum('value_result');
                    $count = $kpiDetailWithValue->count();

                    $score = 0;
                    if ($count > 0) {
                        $score = ($kpi->percentage / 100) * ($actualCount / $count);
                    }

                    $kpi->actualCount = $actualCount;
                    $kpi->score = $score;

                    $totalScore += $score;
                }
            }
        }

        return [
            'groupedKpis' => $groupedKpisByYearAndCategory,
            'totalScore' => $totalScore,
            'users' => $this->getUsers(),
            'divisions' => Divisi::all(),
        ];
    }

    protected function getUsers()
    {
        if (auth()->user()->role_id == 1) {
            // If user is admin (role_id 1), show all users except user with id 1
            return \App\Models\User::where('id', '<>', 1)
                ->orderBy('nama_lengkap')
                ->get();
        } else {
            // For other roles, show users in the same division with role_id 2 or 3, plus current user
            return \App\Models\User::where(function($query) {
                    $query->where('divisi_id', auth()->user()->divisi_id)
                          ->whereIn('role_id', [2, 3]);
                })
                ->orWhere('id', auth()->user()->id)
                ->orderBy('nama_lengkap')
                ->get();
        }
    }

    public function openUpdateModal($kpiDetailId)
    {
        $this->kpiDetail = \App\Models\KpiDetail::findOrFail($kpiDetailId);
        $this->value_actual = $this->kpiDetail->value_actual ?? 0;

        $this->dispatch('open-modal', id: 'updateKpiModal');
    }

    public function openExtraTaskModal($kpiDetailId)
    {
        $this->parent_id = $kpiDetailId;
        $this->description = '';
        $this->count_type = 'NON';
        $this->value_actual = 0;

        $this->dispatch('open-modal', id: 'extraTaskModal');
    }

    public function changeKpiStatus($kpiDetailId, $type)
    {
        $kpiDetail = \App\Models\KpiDetail::findOrFail($kpiDetailId);

        if ($kpiDetail->count_type === 'NON') {
            // Toggle between completed (1) and not completed (0)
            $kpiDetail->value_result = $kpiDetail->value_result ? 0 : 1;
            $kpiDetail->save();
        }
    }

    public function submitUpdateKpi()
    {
        $this->validate([
            'value_actual' => 'required|numeric',
        ]);

        $this->kpiDetail->value_actual = $this->value_actual;

        // Hitung value_result berdasarkan value_plan dan value_actual
        if ($this->kpiDetail->count_type === 'RESULT') {
            $this->kpiDetail->value_result = $this->kpiDetail->value_actual / $this->kpiDetail->value_plan;
        }

        $this->kpiDetail->save();

        $this->dispatch('close-modal', id: 'updateKpiModal');
    }

    public function updatedCountType($value)
    {
        $this->dispatch('count-type-changed', $value);
    }

    public function submitExtraTask()
    {
        $this->validate([
            'parent_id' => 'required|exists:kpi_details,id',
            'description' => 'required|string|max:255',
            'count_type' => 'required|in:NON,RESULT',
            'value_actual' => 'required_if:count_type,RESULT|nullable|numeric|min:0',
        ]);

        try {
            // Ambil detail parent KPI
            $parentKpiDetail = \App\Models\KpiDetail::findOrFail($this->parent_id);

            // Buat deskripsi baru untuk ekstra task
            $kpiDescription = \App\Models\KpiDescription::create([
                'description' => $this->description,
                'kpi_category_id' => $parentKpiDetail->kpi->kpi_category_id,
                'created_by' => auth()->id(), // Maintaining created_by from original code
            ]);

            // Buat detail KPI untuk ekstra task
            $extraTask = \App\Models\KpiDetail::create([
                'parent_id' => $parentKpiDetail->id,
                'is_extra_task' => 1,
                'value_actual' => $this->count_type === 'RESULT' ? $this->value_actual : null,
                'count_type' => $this->count_type,
                'kpi_description_id' => $kpiDescription->id,
                'kpi_id' => $parentKpiDetail->kpi_id,
                'created_by' => auth()->id(), // Maintaining created_by from original code
            ]);

            // Update nilai Value_Result pada parent
            if ($this->count_type === 'NON') {
                // Jika NON, langsung set Value_Result parent menjadi 1
                $parentKpiDetail->update([
                    'value_result' => 1,
                ]);
            } elseif ($this->count_type === 'RESULT') {
                // Jika RESULT, hitung Value_Result baru berdasarkan nilai actual
                $parentPlanValue = $parentKpiDetail->value_plan ?? 1; // Hindari pembagian dengan 0
                $newActualValue = $parentKpiDetail->value_actual + $this->value_actual;

                $newValueResult = min($newActualValue / $parentPlanValue, 1); // Maksimum 1
                $parentKpiDetail->update([
                    'value_actual' => $newActualValue,
                    'value_result' => $newValueResult,
                ]);
            }

            // Flash message ke session
            session()->flash('success', 'Ekstra task berhasil ditambahkan dan nilai parent diperbarui.');

            // Close the modal (keeping this from the original code)
            $this->dispatch('close-modal', id: 'extraTaskModal');

        } catch (\Exception $e) {
            // Flash error ke session
            session()->flash('error', 'Terjadi kesalahan saat menyimpan ekstra task: ' . $e->getMessage());
        }
    }

    public function deleteExtraTask($extraTaskId)
    {
        try {
            // Cari data extra task berdasarkan ID
            $extraTask = \App\Models\KpiDetail::findOrFail($extraTaskId);

            // Pastikan data yang dihapus adalah ekstra task
            if ($extraTask->is_extra_task == 1) {
                // Ambil parent dari extra task
                $parentKpiDetail = \App\Models\KpiDetail::findOrFail($extraTask->parent_id);

                // Kurangi nilai actual parent jika tipe extra task adalah RESULT
                if ($extraTask->count_type === 'RESULT') {
                    $parentActualValue = $parentKpiDetail->value_actual - ($extraTask->value_actual ?? 0);
                    $parentActualValue = max($parentActualValue, 0); // Pastikan tidak negatif
                } else {
                    $parentActualValue = $parentKpiDetail->value_actual; // Tidak ada perubahan untuk NON
                }

                // Hitung ulang value_result untuk parent
                $parentPlanValue = $parentKpiDetail->value_plan ?? 1; // Hindari pembagian dengan 0
                $newValueResult = min($parentActualValue / $parentPlanValue, 1); // Maksimum 1

                // Update parent KPI detail
                $parentKpiDetail->update([
                    'value_actual' => $parentActualValue,
                    'value_result' => $newValueResult,
                ]);

                // Hapus kpi_description terkait, jika ada
                if ($extraTask->kpi_description_id) {
                    $extraTask->kpi_description->delete(); // Hapus kpi_description
                }

                // Hapus data extra task
                $extraTask->delete();

                // Flash message ke session
                session()->flash('success', 'Ekstra task berhasil dihapus dan nilai parent diperbarui.');

                // Optional: For Livewire, you might want to emit an event to refresh the UI
                $this->dispatch('extraTaskDeleted');
            } else {
                session()->flash('error', 'Data bukan ekstra task.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menghapus ekstra task: ' . $e->getMessage());
        }
    }
}
