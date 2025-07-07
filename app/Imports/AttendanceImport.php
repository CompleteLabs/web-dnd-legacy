<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AttendanceImport implements ToModel, WithHeadingRow
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
        if (!empty($row['id_karyawan']) && isset($this->usersCache[$row['id_karyawan']])) {
            // Jika id_karyawan ada, cari berdasarkan employee_id
            $userId = $this->usersCache[$row['id_karyawan']];
        }

        // Jika tidak ditemukan berdasarkan id_karyawan, coba cari berdasarkan nama_lengkap
        if (is_null($userId) && !empty($row['nama_lengkap']) && isset($this->usersCache[$row['nama_lengkap']])) {
            // Jika nama_lengkap ada, cari berdasarkan nama_lengkap
            $userId = $this->usersCache[$row['nama_lengkap']];
        }

        // Proses periode
        $periode = null;

        try {
            if (is_numeric($row['periode'])) {
                $periode = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['periode'])->format('Y-m');
            } elseif (\DateTime::createFromFormat('Y-m', $row['periode']) !== false) {
                $periode = \DateTime::createFromFormat('Y-m', $row['periode'])->format('Y-m');
            } elseif (\DateTime::createFromFormat('d/m/y', $row['periode']) !== false) {
                $periode = \DateTime::createFromFormat('d/m/y', $row['periode'])->format('Y-m');
            } else {
                Log::error("Format Periode tidak dikenali: " . $row['periode']);
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
                    'nama_lengkap' => $row['nama_lengkap'],
                    'employee_id' => $row['id_karyawan'],
                    'periode' => $periode,
                ];

                $this->skippedCount++;
                return null;
            }

            $this->importedCount++;
            return new Attendance([
                'user_id' => $userId,
                'periode' => $periode,
                'work_days' => $row['hari_kerja'] ?? 0,
                'late_less_30' => $row['late_less_30_min'] ?? 0,
                'late_more_30' => $row['late_more_30_min'] ?? 0,
                'sick_days' => $row['sakit_or_izin'] ?? 0,
            ]);
        }

        Log::error('Import Attendance: Pengguna tidak ditemukan atau gagal parsing Periode untuk nama_lengkap ' . $row['nama_lengkap'] . ' atau id_karyawan ' . $row['id_karyawan']);

        // Simpan detail data yang dilewati jika pengguna tidak ditemukan atau periode tidak valid
        $this->skippedDetails[] = [
            'nama_lengkap' => $row['nama_lengkap'],
            'employee_id' => $row['id_karyawan'],
            'periode' => $row['periode'] ?? 'Tidak diketahui',
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
