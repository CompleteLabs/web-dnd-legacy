<?php

namespace App\Imports;

use App\Models\EmployeeReview;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class EmployeeReviewImport implements ToModel, WithHeadingRow
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

        // Cek apakah ada id_karyawan, jika ada gunakan id_karyawan untuk mencari userId, jika tidak, gunakan nama_lengkap
        $userId = null;

        // Prioritaskan pencarian berdasarkan id_karyawan
        if (!empty($row['id_karyawan']) && isset($this->usersCache[$row['id_karyawan']])) {
            $userId = $this->usersCache[$row['id_karyawan']];
        }

        // Jika tidak ditemukan berdasarkan id_karyawan, coba cari berdasarkan nama_lengkap
        if (is_null($userId) && !empty($row['nama_lengkap']) && isset($this->usersCache[$row['nama_lengkap']])) {
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

        // Jika userId dan periode valid, lakukan pengecekan dan simpan data
        if ($userId && $periode) {
            $existingReview = EmployeeReview::where('user_id', $userId)
                                            ->where('periode', $periode)
                                            ->exists();

            if ($existingReview) {
                Log::info('Melewati review yang sudah ada untuk user_id ' . $userId . ' dan periode ' . $periode);

                // Simpan detail data yang dilewati
                $this->skippedDetails[] = [
                    'nama_lengkap' => $row['nama_lengkap'],
                    'id_karyawan' => $row['id_karyawan'],
                    'periode' => $periode,
                ];

                $this->skippedCount++;
                return null;
            }

            $this->importedCount++;
            return new EmployeeReview([
                'user_id' => $userId,
                'periode' => $periode,
                'responsiveness' => $row['responsiveness'] ?? 0,
                'problem_solver' => $row['problem_solver'] ?? 0,
                'helpfulness' => $row['helpfulness'] ?? 0,
                'initiative' => $row['initiative'] ?? 0,
            ]);
        }

        Log::error('Import EmployeeReview: Pengguna tidak ditemukan atau gagal parsing Periode untuk nama_lengkap ' . $row['nama_lengkap']);

        // Simpan detail data yang dilewati jika pengguna tidak ditemukan atau periode tidak valid
        $this->skippedDetails[] = [
            'nama_lengkap' => $row['nama_lengkap'],
            'id_karyawan' => $row['id_karyawan'],
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
            'skippedDetails' => $this->skippedDetails, // Menyertakan detail data yang dilewati
        ];
    }
}
