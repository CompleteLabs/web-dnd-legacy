<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeReviewImportTemplateExport;
use App\Imports\EmployeeReviewImport;
use Illuminate\Http\Request;
use App\Models\EmployeeReview;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employeeReviews = EmployeeReview::with('user')->get();
        $title = 'Employee Reviews';
        $active = 'employee_review';

        return view('employee_reviews.index', compact('employeeReviews', 'title', 'active'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        $title = 'Create Employee Review';
        $active = 'employee_review';

        return view('employee_reviews.create', compact('users', 'title', 'active'));
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
            'responsiveness' => 'required|integer|min:0|max:5',
            'problem_solver' => 'required|integer|min:0|max:5',
            'helpfulness' => 'required|integer|min:0|max:5',
            'initiative' => 'required|integer|min:0|max:5',
        ]);

        // Cek jika review untuk user dan periode sudah ada
        $existingReview = EmployeeReview::where('user_id', $request->user_id)
            ->where('periode', $request->periode)
            ->first();

        if ($existingReview) {
            // Kembali dengan pesan error dalam bahasa Indonesia
            return redirect()->back()->with('error', 'Review untuk karyawan dan periode ini sudah ada.');
        }

        // Buat review baru jika tidak ada duplikat
        EmployeeReview::create($request->all());

        return redirect()->route('employee_reviews.index')->with('success', 'Review karyawan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employeeReview = EmployeeReview::with('user')->findOrFail($id);
        $title = 'View Employee Review';
        $active = 'employee_review';

        return view('employee_reviews.show', compact('employeeReview', 'title', 'active'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeeReview = EmployeeReview::findOrFail($id);
        $users = User::all();
        $title = 'Edit Employee Review';
        $active = 'employee_review';

        return view('employee_reviews.edit', compact('employeeReview', 'users', 'title', 'active'));
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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'periode' => 'required|date_format:Y-m',
            'responsiveness' => 'required|integer|min:1|max:5',
            'problem_solver' => 'required|integer|min:1|max:5',
            'helpfulness' => 'required|integer|min:1|max:5',
            'initiative' => 'required|integer|min:1|max:5',
        ]);

        $employeeReview = EmployeeReview::findOrFail($id);
        $employeeReview->update($request->all());

        return redirect()->route('employee_reviews.index')->with('success', 'Review karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employeeReview = EmployeeReview::findOrFail($id);
        $employeeReview->delete();

        return redirect()->route('employee_reviews.index')->with('success', 'Review karyawan berhasil dihapus.');
    }

    public function myTeam()
    {
        $title = 'My Team';
        $active = 'my_team';

        // Cek tanggal sekarang untuk menentukan periode
        $currentDate = \Carbon\Carbon::now();
        $currentPeriod = $currentDate->month;

        // Jika tanggal kurang dari 5, set periode ke bulan sebelumnya
        if ($currentDate->day < 5) {
            $currentPeriod = $currentDate->subMonth()->format('Y-m');  // Mengambil bulan sebelumnya
        } else {
            $currentPeriod = $currentDate->format('Y-m');  // Mengambil bulan saat ini
        }

        // Get employees under the current user's approval with no soft-deletes
        $employees = User::with('position')
            ->where('approval_id', auth()->id())
            ->whereNull('deleted_at')
            ->get();

        // Retrieve existing reviews for the current period
        $existingReviews = EmployeeReview::where('periode', $currentPeriod)
            ->pluck('user_id')
            ->toArray();

        return view('myteams.index', compact('employees', 'existingReviews', 'currentPeriod', 'title', 'active'));
    }

    public function import(Request $request)
    {
        $import = new EmployeeReviewImport();
        Excel::import($import, $request->file('file'));

        $summary = $import->getImportSummary();

        if ($summary['importedCount'] > 0) {
            $message = "Data Penilaian Tim berhasil diimport. {$summary['importedCount']} data ditambahkan, {$summary['skippedCount']} data dilewati.";
        } else {
            $message = "Tidak ada data yang diimport. Semua data ({$summary['skippedCount']}) sudah ada atau tidak valid.";
        }

        // Kirim data yang dilewati ke sesi
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
        $fileName = "employee_review_import_template_{$currentPeriod}.xlsx";

        return Excel::download(new EmployeeReviewImportTemplateExport, $fileName);
    }
}
