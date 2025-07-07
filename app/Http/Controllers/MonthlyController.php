<?php

namespace App\Http\Controllers;

use App\Exports\MonthlyExport;
use App\Exports\MonthlyReport;
use App\Exports\TemplateMonthly;
use App\Imports\MonthlyImportUser;
use App\Models\Divisi;
use App\Models\Monthly;
use App\Models\Request as ModelsRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class MonthlyController extends Controller
{

    public function reportAdmin(Request $request)
    {
        try {
            $month = Carbon::parse(strtotime($request->month))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
            return Excel::download(new MonthlyReport($month->format('Y-m-d')), 'monthly_' . $month->format('M_y') . '.xlsx');
        } catch (Exception $e) {
            return redirect('admin/monthly')->with(['error' => $e->getMessage()]);
        }
    }
    public function indexUser(Request $request)
    {
        switch ($request->tasktype) {
            case '1':
                $monthlys = Monthly::with('user')->where('date', now()->startOfDay()->startOfMonth()->format('Y-m-d'))->orderBy('date', 'DESC')->where('user_id', Auth::id())->get();
                break;

            case '2':
                $monthlys = Monthly::with('user')->where('date', now()->startOfDay()->startOfMonth()->subMonth(1)->format('Y-m-d'))->orderBy('date', 'DESC')->where('user_id', Auth::id())->get();
                break;

            default:
                $monthlys = Monthly::with('user')->orderBy('date', 'DESC')->where('user_id', Auth::id())->get();
                break;
        }
        return view('admin.monthly.index')->with([
            'title' => 'Monthly',
            'active' => 'monthly',
            'monthlys' => $monthlys,
        ]);
    }

    public function templateUser(Request $request)
    {
        return Excel::download(new TemplateMonthly, 'monthly_template.xlsx',);
    }

    public function exportAdmin(Request $request)
    {
        try {
            $month = Carbon::parse(strtotime($request->month))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
            return Excel::download(new MonthlyExport($month->format('Y-m-d')), 'monthly_' . $month->format('M_y') . '.xlsx',);
        } catch (Exception $e) {
            return redirect('admin/monthly')->with(['error' => $e->getMessage()]);
        }
    }

    public function importMonthlyUser(Request $request)
    {
        $file = $request->file('file');
        $namaFile = $file->getClientOriginalName();
        $file->move(public_path('import'), $namaFile);
        try {
            Excel::import(new MonthlyImportUser(Auth::user()), public_path('/import/' . $namaFile));
        } catch (Exception $e) {
            return redirect('monthly')->with(['error' => $e->getMessage()]);
        }

        return redirect('monthly')->with(['success' => 'berhasil import daily']);
    }

    public function change(Request $request)
    {
        try {
            $monthly = Monthly::findOrfail($request->id);

            $requesteds = ModelsRequest::where('user_id', Auth::id())->where('jenistodo', 'Monthly')->get();
            foreach ($requesteds as $requested) {
                $idTaskExistings = explode(',', $requested->todo_request);
                foreach ($idTaskExistings as $idTaskExisting) {
                    if ($request->id == $idTaskExisting && $requested->status == 'PENDING') {
                        return redirect('monthly')->with(['error' => "Tidak bisa merubah, task ini ada di pengajuan request task"]);
                    }
                }

                $idTaskReplaces = explode(',', $requested->todo_replace);
                foreach ($idTaskReplaces as $idTaskReplace) {
                    if ($request->id == $idTaskReplace && $requested->status == 'PENDING') {
                        return redirect('monthly')->with(['error' => "Tidak bisa merubah, task ini ada di pengajuan request task"]);
                    }
                }
            }

            if (now() > Carbon::parse($monthly->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(5)->addMonth(1)) {
                return redirect('monthly')->with(['error' => 'Tidak merubah status monthly lebih dari H+5 bulan ' . Carbon::parse($monthly->date / 1000)->format('m')]);
            }

            if (now()->year == $monthly->year && now()->weekOfYear < $monthly->week) {
                return redirect('monthly')->with(['error' => "Tidak bisa merubah status monthly lebih dari week " . now()->weekOfYear]);
            }

            if ($monthly->tipe == 'RESULT') {
                if (!$request->value_actual) {
                    return redirect('monthly')->with(['error' => "Value actual harus di isi"]);
                }
                $monthly['value_actual'] = $request->value_actual;
                $monthly['status_result'] = true;
                $monthly['value'] = $monthly['value_actual'] / $monthly['value_plan'] > 1.2 ? 1.2 : $monthly['value_actual'] / $monthly['value_plan'];
            } else {
                $monthly['status_non'] = !$monthly['status_non'];
                $monthly['value'] = $monthly['status_non'] ? 1 : 0;
            }

            $monthly->save();
            return redirect('monthly?tasktype=' . $request->tasktype)->with(['success' => 'Berhasil merubah status monthly']);
        } catch (Exception $e) {
            return redirect('monthly')->with(['error' => $e->getMessage()]);
        }
    }

    public function showresult(Request $request)
    {
        $monthly = Monthly::find($request->id);
        $requesteds = ModelsRequest::where('user_id', Auth::id())->where('jenistodo', 'Monthly')->get();
        ##CEK TASK PADA REQUEST
        foreach ($requesteds as $requested) {
            $idTaskExistings = explode(',', $requested->todo_request);
            foreach ($idTaskExistings as $idTaskExisting) {
                if ($request->id == $idTaskExisting && $requested->status == 'PENDING') {
                    return redirect('monthly')->with(['error' => 'Tidak bisa merubah, task ini ada di pengajuan request task']);
                }
            }

            $idTaskReplaces = explode(',', $requested->todo_replace);
            foreach ($idTaskReplaces as $idTaskReplace) {
                if ($request->id == $idTaskReplace && $requested->status == 'PENDING') {
                    return redirect('monthly')->with(['error' => 'Tidak bisa merubah, task ini ada di pengajuan request task']);
                }
            }
        }

        if (now() > Carbon::parse($monthly->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(5)->addMonth(1)) {
            return redirect('monthly')->with(['error' => 'Tidak merubah status monthly lebih dari H+5 bulan ' . Carbon::parse($monthly->date / 1000)->format('m')]);
        }
        if (auth()->id() != $monthly->user_id) {
            return back();
        }

        return view('admin.monthly.change')->with([
            'title' => 'Monthly',
            'active' => 'monthly',
            'monthly' => $monthly,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->name && $request->month) {
            $monthlys = Monthly::with('user', 'user.area', 'user.divisi', 'tag', 'add')
                ->whereDate('date', $request->month . '-01')
                ->whereHas('user', function ($q) use ($request) {
                    $q->where('nama_lengkap', "like", '%' . $request->name . '%');
                })
                ->get();
        } else if ($request->divisi_id) {
            $monthlys = Monthly::with('user', 'user.area', 'user.divisi')
                ->whereDate('date', now()->startOfDay()->startOfMonth()->format('Y-m-d'))
                ->orderBy(User::select('nama_lengkap')->whereColumn('users.id', 'monthlies.user_id'))
                ->whereHas('user.divisi', function ($q) use ($request) {
                    $q->where('id', $request->divisi_id);
                })
                ->get();
        } else {
            $monthlys = Monthly::with('user', 'user.area', 'user.divisi')
                ->orderBy('date', 'DESC')
                ->orderBy(User::select('nama_lengkap')->whereColumn('users.id', 'monthlies.user_id'))
                ->simplePaginate(100);
        }
        return view('admin.monthly.index')->with([
            'divisis' => Divisi::all(),
            'title' => 'Monthly',
            'active' => 'monthly',
            'monthlys' => $monthlys,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        function tgl_indo($format)
        {
            $bulan = array(
                1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
            return $bulan[(int)$format];
        }
        try {
            $data = $request->all();
            $date = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
            //HANDLE SUBMIT
            if (now() > $date->addDay(5) && !$request->is_add) {
                return redirect('monthly')->with(['error' => 'Tidak bisa input monthly lebih dari H+5 bulan ' . tgl_indo(now()->format('m'))]);
            }

            //HANDLE TAMBAHAN
            if ($request->is_add) {
                $data['is_add'] = 1;
                if (now() > Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addMonth(1)->addDay(5)) {
                    return redirect('monthly')->with(['error' => 'Tidak bisa input tambahan monthly bulan ' . tgl_indo(now()->subMonth(1)->format('m')) . ' lebih dari H+5 bulan ' . tgl_indo(now()->subMonth(1)->format('m'))]);
                }
            }
            $data['user_id'] = Auth::id();
            unset($data['_token']);

            //HANDLE TASK RESULT ATAU NON
            if ($request->result) {
                if (!$request->value_plan) {
                    return redirect('monthly')->with(['error' => 'Task result harus memasukkan value plan']);
                }
                $data['tipe'] = 'RESULT';
                $data['value_actual'] = 0;
                $data['status_result'] = 0;
            } else {
                $data['tipe'] = 'NON';
                $data['status_non'] = 0;
                unset($data['value_plan']);
            }

            $data['date'] = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->format('Y-m-d');
            unset($data['result']);
            Monthly::create($data);
            return redirect('monthly')->with(['success' => 'Berhasil menambahkan monthly']);
        } catch (Exception $e) {
            return redirect('monthly')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $monthly = Monthly::find($id);
        $requesteds = ModelsRequest::where('user_id', Auth::id())->where('jenistodo', 'Monthly')->get();
        ##CEK TASK PADA REQUEST
        foreach ($requesteds as $requested) {
            $idTaskExistings = explode(',', $requested->todo_request);
            foreach ($idTaskExistings as $idTaskExisting) {
                if ($id == $idTaskExisting && $requested->status == 'PENDING') {
                    return redirect('monthly')->with(['error' => 'Tidak bisa merubah, task ini ada di pengajuan request task']);
                }
            }

            $idTaskReplaces = explode(',', $requested->todo_replace);
            foreach ($idTaskReplaces as $idTaskReplace) {
                if ($id == $idTaskReplace && $requested->status == 'PENDING') {
                    return redirect('monthly')->with(['error' => 'Tidak bisa merubah, task ini ada di pengajuan request task']);
                }
            }
        }
        if (auth()->id() != $monthly->user_id) {
            return back();
        }
        if (now() > Carbon::parse($monthly->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(5)) {
            return redirect('monthly')->with(['error' => 'Tidak bisa delete monthly lebih dari H+5 bulan ' . tglindo(now()->format('m'))]);
        }
        return view('admin.monthly.edit')->with([
            'title' => 'Monthly',
            'active' => 'monthly',
            'monthly' => $monthly,
        ]);
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
        function tglndo($format)
        {
            $bulan = array(
                1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
            return $bulan[(int)$format];
        }
        try {
            $monthly = Monthly::find($id);
            if (now() > Carbon::parse($request->date)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(5)) {

                return redirect('monthly')->with(['error' => 'Tidak bisa mengubah monthly lebih dari H+5 bulan ' . tglndo(now()->format('m'))]);
            }
            if (Carbon::parse($request->date)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta')) < Carbon::parse($monthly->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))) {
                return redirect('monthly')->with(['error' => 'Tidak bisa mengubah monthly kurang dari H+5 bulan ' . tglndo(now()->format('m'))]);
            }
            $monthly['task'] = $request->task;
            $monthly['date'] = Carbon::parse($request->date)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
            if ($monthly->tipe == 'RESULT') {
                if (!$request->value_plan) {
                    return redirect('monthly')->with(['error' => 'Harap isi value plan']);
                }
                $monthly['value_plan'] = (int) $request->value_plan;
            }
            $monthly->save();
            return redirect('monthly')->with(['success' => 'Berhasil merubah monthly']);
        } catch (Exception $e) {
            return redirect('monthly')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        function tglindo($format)
        {
            $bulan = array(
                1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
            return $bulan[(int)$format];
        }
        try {
            $monthly = Monthly::findOrFail($request->id);
            $requesteds = ModelsRequest::where('user_id', Auth::id())->where('jenistodo', 'Monthly')->get();
            foreach ($requesteds as $requested) {
                $idTaskExistings = explode(',', $requested->todo_request);
                foreach ($idTaskExistings as $idTaskExisting) {
                    if ($request->id == $idTaskExisting && $requested->status == 'PENDING') {
                        return redirect('monthly')->with(['error' => "Tidak bisa merubah, task ini ada di pengajuan request task"]);
                    }
                }

                $idTaskReplaces = explode(',', $requested->todo_replace);
                foreach ($idTaskReplaces as $idTaskReplace) {
                    if ($request->id == $idTaskReplace && $requested->status == 'PENDING') {
                        return redirect('monthly')->with(['error' => "Tidak bisa merubah, task ini ada di pengajuan request task"]);
                    }
                }
            }

            if (now() > Carbon::parse($monthly->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(5) && !$monthly->is_add) {
                return redirect('monthly')->with(['error' => 'Tidak bisa delete monthly lebih dari H+5 bulan ' . tglindo(now()->format('m'))]);
            }

            if (now() > Carbon::parse($monthly->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addMonth(1)->addDay(5) && $monthly->is_add ) {
                return redirect('monthly')->with(['error' => 'Tidak bisa delete monthly lebih dari H+5 bulan ' . tglindo(now()->format('m'))]);
            }

            $monthly->delete();
            return redirect('monthly')->with(['success' => "Berhasil menghapus monthly"]);
        } catch (Exception $e) {
            return redirect('monthly')->with(['error' => $e->getMessage()]);
        }
    }

    public function getmonthly(Request $request)
    {
        $monthlys =  Monthly::with('user', 'user.area', 'user.divisi')
            ->whereDate('date', $request->date)
            ->where('user_id', $request->id)
            ->where('is_add', 0)
            ->where('is_update', 0)
            ->get();
        return response()->json($monthlys);
    }
}
