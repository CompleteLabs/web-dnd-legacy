<?php

namespace App\Exports;

use App\Models\Kpi;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KpiMonthlyExport implements FromArray, WithHeadings, WithMapping
{
    protected String $year;
    protected String $divisi_id;
    protected $usersMonthYear;
    protected $usersMonthlyKpiUsers;
    protected $usersAverageYearlyKpi;

    function __construct(String $year, String $divisi_id)
    {
        $this->year = $year;
        $this->divisi_id = $divisi_id;
    }

    public function array(): array
    {
        $result = [];
        $this->usersMonthYear = [];

        if ($this->divisi_id) {
            $users = User::orderBy('nama_lengkap')->where('divisi_id', $this->divisi_id)->get();
        } else {
            $users = User::orderBy('nama_lengkap')->where('divisi_id', auth()->user()->divisi_id)->get();
        }

        foreach ($users as $user) {
            $score = [];

            $usersYearlyKpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
                ->where('kpi_type_id', 3)
                ->whereHas('user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                })
                ->orderBy('date', 'DESC')->get();

            // Group the KPIs by year
            $groupedUsersKpisByYear = $usersYearlyKpis->groupBy(function ($kpi) {
                return CarbonImmutable::parse($kpi->date)->format('Y-m');
            });

            // Calculate the average KPI for each user and month
            $usersAverageKpiMonthlyByYear = [];
            $sumOfScores = 0;
            $numberOfMonths = 0;

            for ($month = 1; $month <= 12; $month++) {
                $yearMonth = $this->year . '-' . sprintf("%02d", $month);
                $yearlyGroupedKpis = $groupedUsersKpisByYear[$yearMonth] ?? collect();
                $cumulativeScore = 0;
                $userKpis = $yearlyGroupedKpis;

                foreach ($userKpis as $kpi) {
                    $kpiDetailWithValue = $kpi->kpi_detail->filter(function ($kpiDetail) {
                        return $kpiDetail->value_result !== null && $kpiDetail->value_result >= 0;
                    });

                    if ($kpiDetailWithValue->isNotEmpty()) {
                        $actualCount = $kpiDetailWithValue->sum('value_result');
                        $score = ($kpi->percentage / 100) * ($actualCount / $kpiDetailWithValue->count());

                        $cumulativeScore += $score;
                    }
                }
                // Calculate the average KPI for the month
                $usersAverageKpiMonthly = $userKpis->count() > 0 ? ($cumulativeScore * 100) : 0;

                // Store the average KPI in the array
                $usersAverageKpiMonthlyByYear[$yearMonth] = $usersAverageKpiMonthly;

                // Add the score to the sum if it has a value
                if ($usersAverageKpiMonthly > 0) {
                    $sumOfScores += $usersAverageKpiMonthly;
                    $numberOfMonths++;
                }
            }

            // Calculate the overall average score
            $overallAverageScore = $numberOfMonths > 0 ? ($sumOfScores / $numberOfMonths) : 0;

            array_push($result, [
                'id' => $user->id,
                'name' => $user->nama_lengkap,
                'score' => $usersAverageKpiMonthlyByYear,
                'average' => $overallAverageScore,
            ]);
        }

        // Generate month names for headings
        $this->usersMonthYear = []; // Reset usersMonthYear
        if (!empty($usersAverageKpiMonthlyByYear)) { // Pastikan array tidak kosong
            foreach (array_keys($usersAverageKpiMonthlyByYear) as $yearMonth) {
                $carbonDate = Carbon::createFromFormat('Y-m', $yearMonth);
                $monthName = $carbonDate->isoFormat('MMM');
                $this->usersMonthYear[] = $monthName;
            }
        }


        return $result;
    }

    public function headings(): array
    {
        $headings = ['Name'];

        $usersYearlyKpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('kpi_type_id', 3)
            ->orderBy('date', 'DESC')->get();

        // Group the KPIs by year
        $groupedUsersKpisByYear = $usersYearlyKpis->groupBy(function ($kpi) {
            return CarbonImmutable::parse($kpi->date)->format('Y-m');
        });

        // Calculate the average KPI for each user and month
        $usersAverageKpiMonthlyByYear = [];

        for ($month = 1; $month <= 12; $month++) {
            $yearMonth = $this->year . '-' . sprintf("%02d", $month);
            $yearlyGroupedKpis = $groupedUsersKpisByYear[$yearMonth] ?? collect();
            $cumulativeScore = 0;

            foreach ($yearlyGroupedKpis as $kpi) {
                $kpiDetailWithValue = $kpi->kpi_detail->filter(function ($kpiDetail) {
                    return $kpiDetail->value_result !== null && $kpiDetail->value_result >= 0;
                });

                if ($kpiDetailWithValue->isNotEmpty()) {
                    $actualCount = $kpiDetailWithValue->sum('value_result');
                    $score = ($kpi->percentage / 100) * ($actualCount / $kpiDetailWithValue->count());

                    $cumulativeScore += $score;
                }
            }
            // Calculate the average KPI for the month
            $usersAverageKpiMonthly = $yearlyGroupedKpis->count() > 0 ? ($cumulativeScore * 100) : 0;

            // Store the average KPI in the array
            $usersAverageKpiMonthlyByYear[$yearMonth] = $usersAverageKpiMonthly;
        }

        // Populate the month names array outside the foreach loop
        foreach ($usersAverageKpiMonthlyByYear as $yearMonth => $averageKpi) {
            $carbonDate = Carbon::createFromFormat('Y-m', $yearMonth);
            $monthName = $carbonDate->isoFormat('MMM');
            $this->usersMonthYear[] = $monthName;
        }

        $endHeadings = ['Average'];

        $headings = array_merge($headings, $this->usersMonthYear, $endHeadings);

        return $headings;
    }

    public function map($row): array
    {
        $result = [$row['name']];
        $average = [$row['average']];
        $result = array_merge($result, $row['score'], $average);
        return $result;
    }
}
