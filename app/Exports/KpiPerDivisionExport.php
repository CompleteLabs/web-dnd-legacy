<?php

namespace App\Exports;

use App\Models\Kpi;
use App\Models\KpiDetail;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KpiPerDivisionExport implements FromArray, WithHeadings, WithMapping
{
    protected String $month;
    protected String $divisi_id;

    function __construct(String $month, String $divisi_id)
    {
        $this->month = $month;
        $this->divisi_id = $divisi_id;
    }

    function array(): array
    {
        $result = [];
        
        if ($this->divisi_id) {
            $users = User::orderBy('nama_lengkap')->where('divisi_id', $this->divisi_id)->get();
        } else {
            $users = User::orderBy('nama_lengkap')->where('divisi_id', auth()->user()->divisi_id)->get();
        }
        
        $date = Carbon::createFromFormat('Y-m', $this->month);
        
        foreach ($users as $user) {
            $user_closed_kpis = 0;
            $user_all_kpis = 0;
            $cumulativeScore = 0;

            $kpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('kpi_type_id', 3)
            ->where('user_id', $user->id)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->get();

            foreach ($kpis as $kpi) {
                $closed_kpis = KpiDetail::where('kpi_id', $kpi->id)
                ->whereNotNull('value_result')
                ->where('value_result', '!=', 0)
                ->count();

                $all_kpis = KpiDetail::where('kpi_id', $kpi->id)
                ->whereNull('deleted_at')
                ->count();

                $kpiDetailWithValue = $kpi->kpi_detail->filter(function ($kpiDetail) {
                    return $kpiDetail->value_result !== null && $kpiDetail->value_result >= 0;
                });

                $actualCount = $kpiDetailWithValue->sum('value_result');
                $score = ($kpi->percentage) * ($actualCount / $all_kpis);

                // Increment the counts within the loop
                $user_closed_kpis += $closed_kpis;
                $user_all_kpis += $all_kpis;
                $cumulativeScore += $score;
            }
            
            array_push($result, [
                'id' => $user->id,
                'name' => $user->nama_lengkap,
                'divisi' => $user->divisi->name,
                'total_task' => $user_closed_kpis . '/' . $user_all_kpis,
                'score' => $cumulativeScore . '%',
            ]);
        }

        return $result;
    }

    public function headings(): array
    {
        $headings = ['Name', 'Divisi', 'Total Task', 'Score'];

        return $headings;
    }

    public function map($row): array
    {
        $result = [
            $row['name'], 
            $row['divisi'],
            $row['total_task'],
            $row['score']
        ];

        return $result;
    }
}
