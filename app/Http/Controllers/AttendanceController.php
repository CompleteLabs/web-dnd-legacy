<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
use App\Exports\AttendanceImportTemplateExport;
use App\Imports\AttendanceImport;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attendances = Attendance::all();
        $title = 'Absensi';
        $active = 'attendance';
        return view('attendance.index', compact('attendances', 'title', 'active'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $title = 'Tambah Absensi';
        $active = 'attendance';
        return view('attendance.create', compact('users', 'title', 'active'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'periode' => 'required|date_format:Y-m',
            'work_days' => 'required|integer|min:0',
            'late_less_30' => 'nullable|integer|min:0',
            'late_more_30' => 'nullable|integer|min:0',
            'sick_days' => 'nullable|integer|min:0',
        ]);

        // Check if an attendance record already exists for the given user and period
        $existingAttendance = Attendance::where('user_id', $request->user_id)
            ->where('periode', $request->periode)
            ->first();

        if ($existingAttendance) {
            // Redirect back with an error message in Bahasa Indonesia
            return redirect()->back()->with('error', 'Data absensi untuk karyawan dan periode ini sudah ada.');
        }

        // Create a new attendance record if no duplicate is found
        Attendance::create($request->all());

        return redirect()->route('attendance.index')->with('success', 'Data absensi berhasil ditambahkan.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $attendance = Attendance::findOrFail($id);
        $title = 'Detail Absensi';
        $active = 'attendance';
        return view('attendance.show', compact('attendance', 'title', 'active'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);
        $users = User::all();
        $title = 'Edit Absensi';
        $active = 'attendance';
        return view('attendance.edit', compact('attendance', 'title', 'active', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'periode' => 'required|string',
            'work_days' => 'required|integer|min:0',
            'late_less_30' => 'nullable|integer|min:0',
            'late_more_30' => 'nullable|integer|min:0',
            'sick_days' => 'nullable|integer|min:0',
        ]);

        $attendance = Attendance::findOrFail($id);
        $attendance->update($validated);

        return redirect()->route('attendance.index')->with('success', 'Data absensi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->route('attendance.index')->with('success', 'Data absensi berhasil dihapus.');
    }


    public function import(Request $request)
    {
        $import = new AttendanceImport();
        Excel::import($import, $request->file('file'));

        $summary = $import->getImportSummary();

        if ($summary['importedCount'] > 0) {
            $message = "Data Kehadiran berhasil diimport. {$summary['importedCount']} data ditambahkan, {$summary['skippedCount']} data dilewati.";
        } else {
            $message = "Tidak ada data yang diimport. Semua data ({$summary['skippedCount']}) sudah ada atau tidak valid.";
        }

        return redirect()->back()->with([
            'importMessage' => $message,
            'skippedDetails' => $summary['skippedDetails']
        ]);
    }

    public function download()
    {
        // Get the current period in 'YYYY-MM' format
        $currentPeriod = Carbon::now()->format('Y-m');

        // Set the filename with the current period
        $fileName = "attendance_import_template_{$currentPeriod}.xlsx";

        return Excel::download(new AttendanceImportTemplateExport, $fileName);
    }

    public function exportAttendance()
    {
        return Excel::download(new AttendanceExport(), 'attendance_data.xlsx');
    }
}
