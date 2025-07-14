<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AttendanceImport implements ToModel
{
    private $usersCache = [];
    private $importedCount = 0;
    private $skippedCount = 0;
    private $skippedDetails = []; // Array untuk menyimpan detail data yang dilewati

    public function __construct()
    {
        // Cache semua pengguna berdasarkan 'nama_lengkap' dan 'employee_id' untuk menghindari query database berulang
        $this->usersCache = User::pluck('id', 'nama_lengkap')->toArray();
        $employeeCache = User::pluck('id', 'employee_id')->toArray();

        // Gabungkan cache berdasarkan 'nama_lengkap' dan 'employee_id'
        $this->usersCache = array_merge($this->usersCache, $employeeCache);
    }

    public function model(array $row)
    {
        Log::info('Data baris: ', $row);

        // Cek apakah ada employee_id, jika ada gunakan employee_id untuk mencari userId, jika tidak, gunakan nama_lengkap
        $userId = null;

        // Prioritaskan pencarian berdasarkan employee_id terlebih dahulu
        if (!empty($row[0]) && isset($this->usersCache[$row[0]])) {
            // Jika id_karyawan ada (kolom 0), cari berdasarkan employee_id
            $userId = $this->usersCache[$row[0]];
        }

        // Jika tidak ditemukan berdasarkan id_karyawan, coba cari berdasarkan nama_lengkap
        if (is_null($userId) && !empty($row[1]) && isset($this->usersCache[$row[1]])) {
            // Jika nama_lengkap ada (kolom 1), cari berdasarkan nama_lengkap
            $userId = $this->usersCache[$row[1]];
        }

        // Proses periode
        $periode = null;

        try {
            if (is_numeric($row[2])) {
                $periode = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[2])->format('Y-m');
            } elseif (\DateTime::createFromFormat('Y-m', $row[2]) !== false) {
                $periode = \DateTime::createFromFormat('Y-m', $row[2])->format('Y-m');
            } elseif (\DateTime::createFromFormat('d/m/y', $row[2]) !== false) {
                $periode = \DateTime::createFromFormat('d/m/y', $row[2])->format('Y-m');
            } else {
                Log::error("Format Periode tidak dikenali: " . $row[2]);
            }
        } catch (\Exception $e) {
            Log::error("Kesalahan saat parsing Periode: " . $e->getMessage());
        }

        // Validasi userId dan periode sebelum memproses lebih lanjut
        if ($userId && $periode) {
            // Cek apakah absensi untuk userId dan periode sudah ada
            $existingAttendance = Attendance::where('user_id', $userId)
                ->where('periode', $periode)
                ->exists();

            if ($existingAttendance) {
                Log::info('Melewati absensi yang sudah ada untuk user_id ' . $userId . ' dan periode ' . $periode);

                // Simpan detail data yang dilewati
                $this->skippedDetails[] = [
                    'nama_lengkap' => $row[1],
                    'employee_id' => $row[0],
                    'periode' => $periode,
                ];

                $this->skippedCount++;
                return null;
            }

            $this->importedCount++;
            return new Attendance([
                'user_id' => $userId,
                'periode' => $periode,
                'work_days' => $row[3] ?? 0,
                'late_less_30' => $row[4] ?? 0,
                'late_more_30' => $row[5] ?? 0,
                'sick_days' => $row[6] ?? 0,
            ]);
        }

        Log::error('Import Attendance: Pengguna tidak ditemukan atau gagal parsing Periode untuk nama_lengkap ' . $row[1] . ' atau id_karyawan ' . $row[0]);

        // Simpan detail data yang dilewati jika pengguna tidak ditemukan atau periode tidak valid
        $this->skippedDetails[] = [
            'nama_lengkap' => $row[1],
            'employee_id' => $row[0],
            'periode' => $row[2] ?? 'Tidak diketahui',
        ];

        $this->skippedCount++;
        return null;
    }

    public function getImportSummary()
    {
        return [
            'importedCount' => $this->importedCount,
            'skippedCount' => $this->skippedCount,
            'skippedDetails' => $this->skippedDetails,
        ];
    }
}
