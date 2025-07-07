<?php

namespace App\Imports;

use App\Models\Area;
use App\Models\Divisi;
use App\Models\User;
use App\Models\Role;
use Exception;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    protected $errors = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Normalize username
            $username = strtolower(str_replace(".", "", preg_replace('/\s+/', '', $row['username'])));
            $user = User::where('username', $username)->first();

            // Retrieve related IDs with proper error handling
            $role = Role::where('name', $row['role'])->first();
            if (!$role) {
                throw new Exception('Role ' . $row['role'] . ' tidak ditemukan');
            }

            $area = Area::where('name', preg_replace('/\s+/', '', $row['area']))->first();
            if (!$area) {
                throw new Exception('Area ' . $row['area'] . ' tidak ditemukan');
            }

            $divisi = Divisi::where('name', preg_replace('/\s+/', '', $row['divisi']))->first();
            if (!$divisi) {
                throw new Exception('Divisi ' . $row['divisi'] . ' tidak ditemukan');
            }

            $approval = User::where('nama_lengkap', strtoupper($row['approval']))->first();

            if ($user) {
                // Update existing user
                $user->update([
                    'employee_id' => $row['id_karyawan'],
                    'role_id' => $role->id,
                    'area_id' => $area->id,
                    'divisi_id' => $divisi->id,
                    'dr' => strtoupper($row['dr']) == 'YES',
                    'wn' => strtoupper($row['wn']) == 'YES',
                    'wr' => strtoupper($row['wr']) == 'YES',
                    'mn' => strtoupper($row['mn']) == 'YES',
                    'mr' => strtoupper($row['mr']) == 'YES',
                    'approval_id' => $approval ? $approval->id : null,
                    'position_id' => $row['position_id'],
                ]);
            } else {
                // Create new user
                return new User([
                    'nama_lengkap' => strtoupper($row['nama_lengkap']),
                    'username' => $username,
                    'employee_id' => $row['id_karyawan'],
                    'role_id' => $role->id,
                    'area_id' => $area->id,
                    'divisi_id' => $divisi->id,
                    'dr' => strtoupper($row['dr']) == 'YES',
                    'wn' => strtoupper($row['wn']) == 'YES',
                    'wr' => strtoupper($row['wr']) == 'YES',
                    'mn' => strtoupper($row['mn']) == 'YES',
                    'mr' => strtoupper($row['mr']) == 'YES',
                    'approval_id' => $approval ? $approval->id : null,
                    'position_id' => $row['position_id'],
                    'password' => $row['password'] ? bcrypt($row['password']) : bcrypt('complete123'),
                ]);
            }
        } catch (Exception $e) {
            // Store the error message with the row number
            $this->errors[] = 'Error in row ' . ($row['row_number'] ?? 'unknown') . ': ' . $e->getMessage();
        } catch (QueryException $e) {
            // Store the SQL error message with the row number
            $this->errors[] = 'SQL Error in row ' . ($row['row_number'] ?? 'unknown') . ': ' . $e->getMessage();
        }

        return null; // Return null to skip this row if there's an error
    }

    /**
     * Get the errors after the import process
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
