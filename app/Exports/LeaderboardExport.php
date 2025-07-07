<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeaderboardExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $leaderboardData;

    public function __construct($leaderboardData)
    {
        $this->leaderboardData = $leaderboardData;
    }

    // Mendapatkan koleksi data yang akan diekspor
    public function collection()
    {
        return collect($this->leaderboardData);
    }

    // Menentukan heading (judul kolom) di Excel
    public function headings(): array
    {
        return [
            'ID Karyawan',
            'Nama Lengkap',
            // 'Posisi',
            'Division',
            'Area',
            'KPI Score (40%)',
            'Attendance Score (40%)',
            'Activity Score (20%)',
            'Total Score'
        ];
    }

    // Memetakan data yang akan dimasukkan ke dalam file Excel
    public function map($data): array
    {
        return [
            $data['user']->employee_id,
            $data['user']->nama_lengkap,
            // $data['user']->position->name,
            $data['user']->divisi->name ?? 'N/A',
            $data['user']->area->name ?? 'N/A',
            number_format($data['kpiScore'], 2),
            number_format($data['attendanceScore'], 2),
            number_format($data['activityScore'], 2),
            number_format($data['totalScore'], 2)
        ];
    }
}
