<?php

namespace App\Http\Controllers;

use App\Exports\DailyExport;
use App\Models\Daily;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TamplateDaily;
use App\Helpers\ConvertDate;
use App\Imports\DailyImportUser;
use App\Models\DailyLog;
use App\Models\Divisi;
use App\Models\Request as ModelsRequest;
use App\Models\TaskCategory;
use App\Models\TaskStatus;
use App\Models\User;
use Carbon\Carbon;
use Exception;

class DailyController extends Controller
{
    ##FUNCTION USER
    public function indexUser(Request $request)
    {

        switch ($request->tasktype) {
            case '1':
                $dailys = Daily::with('tag')->where('date', now()->format('Y-m-d'))->orderBy('date', 'DESC')->orderBy('time', 'DESC')->where('user_id', auth()->id())->simplePaginate(100);
                break;

            case '2':
                $dailys = Daily::with('tag')->where('date', now()->subDay(1)->format('Y-m-d'))->orderBy('date', 'DESC')->orderBy('time', 'DESC')->where('user_id', auth()->id())->simplePaginate(100);
                break;

            case '3':
                $dailys = Daily::with('tag')->whereBetween('date', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')])->orderBy('date', 'DESC')->orderBy('time', 'DESC')->where('user_id', auth()->id())->simplePaginate(100);
                break;

            default:
                $dailys = Daily::with('tag')->orderBy('date', 'DESC')->orderBy('time', 'DESC')->where('user_id', auth()->id())->simplePaginate(100);
                break;
        }
        return view('admin.daily.index')->with([
            'title' => 'Daily',
            'active' => 'daily',
            'dailys' => $dailys,
        ]);
    }

    public function indexTeamsDaily(Request $request)
    {
        switch ($request->tasktype) {
            case '1':
                $dailys = Daily::with(['tag', 'user'])
                ->whereHas('user', function ($query) {
                        $query->where('divisi_id', auth()->user()->divisi_id);
                    })
                ->where('date', now()->format('Y-m-d'))
                ->orderBy('date', 'DESC')
                ->orderBy('time', 'DESC')
                ->simplePaginate(30);
                break;

            case '2':
                $dailys = Daily::with(['tag', 'user'])
                ->whereHas('user', function ($query) {
                        $query->where('divisi_id', auth()->user()->divisi_id);
                    })
                ->where('date', now()->subDay(1)->format('Y-m-d'))
                ->orderBy('date', 'DESC')
                ->orderBy('time', 'DESC')
                ->simplePaginate(30);
                break;

            case '3':
                $dailys = Daily::with(['tag', 'user'])
                ->whereHas('user', function ($query) {
                        $query->where('divisi_id', auth()->user()->divisi_id);
                    })
                ->whereBetween('date', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')])
                ->orderBy('date', 'DESC')
                ->orderBy('time', 'DESC')
                ->simplePaginate(30);
                break;

            default:
                $dailys = Daily::with(['tag', 'user'])
                    ->whereHas('user', function ($query) {
                            $query->where('divisi_id', auth()->user()->divisi_id);
                        })
                    ->orderBy('date', 'DESC')
                    ->orderBy('time', 'DESC')
                    ->simplePaginate(30);
                break;
        }

        if ($request->user) {
            $dailys = Daily::with(['tag', 'user'])
                ->where('user_id', $request->user)
                ->orderBy('date', 'DESC')
                ->orderBy('time', 'DESC')
                ->simplePaginate(30);
        }

        $logs = DailyLog::with(['user' => function ($query) {
                $query->select('id', 'nama_lengkap', 'divisi_id');
            }])
                ->whereHas('user', function ($query) {
                    $query->where('divisi_id', auth()->user()->divisi_id);
                })
                ->limit(30)
                ->orderBy('created_at', 'DESC')
                ->simplePaginate(30);

        if ($request->user_log) {
            $logs = DailyLog::with('user')
                ->where('user_id', $request->user_log)
                ->orderBy('created_at', 'DESC')
                ->simplePaginate(30);
        }

        return view('teams.daily.index')->with([
            'title' => 'Daily',
            'active' => 'teams-daily',
            'dailys' => $dailys,
            'logs' => $logs,
            'users' => User::where('divisi_id', auth()->user()->divisi_id)->get(),
        ]);
    }

    public function teamsDailyEdit(Daily $daily)
    {
        ##CEK TASK PUNYA SENDIRI ATAU BUKAN
        if ($daily->user_id != auth()->id()) {
            return redirect('/teams/daily')->with(['error' => 'Task ini bukan milik anda !']);
        }

        return view('teams.daily.edit')->with([
            'title' => 'Edit Daily',
            'active' => 'teams-daily',
            'daily' => $daily,
            'task_categories' => TaskCategory::all(),
            'task_status' => TaskStatus::all(),
        ]);
    }

    public function teamsDailyUpdate(Request $request, Daily $daily)
    {
        try {
            ##BELUM ADA VALIDASI BATAS WAKTU GANTI STATUS

            ##PASS ALL VALIDATION
            $daily->task_category_id = $request->task_category_id ?? '';
            $daily->task_status_id = $request->task_status_id ?? '';
    
            if ($request->task_status_id == 1) {
                $daily->status = 1;
            } else {
                $daily->status = 0;
            }
            $daily->save();

            $task = Daily::find($daily->id);
            $category = TaskCategory::find($request->task_category_id);
            $status = TaskStatus::find($request->task_status_id);

            $dailyLog = DailyLog::create([
                'user_id' => auth()->user()->id,
                'task_id' => $daily->id,
                'activity' => 'Merubah kategori task ' . $task->task . ' menjadi ' . $category->task_category . ' dan status task menjadi ' . $status->task_status,
            ]);
            $dailyLog->save();
            
            return redirect('/teams/daily')->with(['success' => 'berhasil update status task']);
        } catch (Exception $e) {
            return redirect('/teams/daily')->with(['error' => $e->getMessage()]);
        }
    }

    public function sendDaily(Request $request)
    {
        try {
            $data = $request->all();
            $data['date'] = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
            $date = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));

            ##EXTRA TASK / TAMBAHAN
            if ($request->isplan) {
                // if (!Daily::whereDate('date', Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->format('Y-m-d'))->where('user_id', auth()->id())->get()) {
                //     return redirect('daily')->with(['error' => "Tidak bisa menambahkan daily extra karena hari ini ".Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->format('d M Y')." anda tidak ada plan"]);
                // }
                $data['isplan'] = false;
                $data['ontime'] = true;
                ##TAMBAHAN LEBIH DARI H+1 JAM 10:00
                if (
                    $date->diffInDays(now()) > 0
                    &&
                    now()->subDay(1) > $date->addHour(10)
                ) {
                    $data['status'] = true;
                    $data['ontime'] = 0.5;
                }

                ##TAMBAHAN LEBIH DARI H+2
                // if (
                //     $date->diffInDays(now()) > 0
                //     &&
                //     now() > $date->addDay(2)
                // ) {
                //     return redirect('daily')->with(['error' => 'Tidak bisa menambahkan daily extra, sudah lebih dari H+2']);
                // }
            }

            ##CONVERT KE 24 JAM
            if (!$request->isplan) {
                $data['time'] = date('H:i', strtotime($request->time));
            }

            $selectedUserIds = $request->input('user_id');

            foreach ($selectedUserIds as $userId) {
                $data['user_id'] = $userId;
                $data['add_id'] = auth()->user()->id;

                // Create the daily task
                $daily = Daily::create($data);

                // Create the daily log
                $dailyLog = DailyLog::create([
                    'user_id' => auth()->user()->id,
                    'task_id' => $daily->id,
                    'activity' => 'Mengirim task ' . $daily->task . ' ke ' . $daily->user->nama_lengkap,
                ]);
                $dailyLog->save();
            }
            
            return redirect('/teams/daily')->with(['success' => "Berhasil mengirim daily !"]);
        } catch (Exception $e) {
            return redirect('/teams/daily')->with(['error' => $e->getMessage()]);
        }
    }

    public function templateUser(Request $request)
    {
        return Excel::download(new TamplateDaily, 'daily_template.xlsx',);
    }

    public function importDailyUser(Request $request)
    {
        $file = $request->file('file');
        $namaFile = $file->getClientOriginalName();
        $file->move(public_path('import'), $namaFile);
        try {
            $userIds = $request->input('userid', []);

            if ($request->page == 'teams') {
                Excel::import(new DailyImportUser(auth()->user()->role->name != 'STAFF' ? $userIds :  [auth()->id()], $request->page ?? ''), public_path('/import/' . $namaFile));
            } else {
                Excel::import(new DailyImportUser(auth()->user()->role_id == 1 ? $userIds :  [auth()->id()], $request->page ?? ''), public_path('/import/' . $namaFile));
            }
        } catch (Exception $e) {
            if ($request->page == 'teams') {
                return redirect(auth()->user()->role->name != 'STAFF' ? '/teams/daily' : 'daily')->with(['error' => $e->getMessage()]);
            }
            return redirect(auth()->user()->role_id == 1 ? 'admin/daily' : 'daily')->with(['error' => $e->getMessage()]);
        }
        if ($request->page == 'teams') {
            return redirect(auth()->user()->role->name != 'STAFF' ? '/teams/daily' : 'daily')->with(['success' => 'berhasil import daily']);
        }
        return redirect(auth()->user()->role_id == 1 ? 'admin/daily' : 'daily')->with(['success' => 'berhasil import daily']);
    }

    public function exportAdmin(Request $request)
    {
        return Excel::download(new DailyExport($request->week, $request->year), 'daily_week_' . $request->week . '_year_' . $request->year . '.xlsx',);
    }

    ##FUNCITION ADMIN
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $monday = ConvertDate::getMondayOrSaturday(now()->year, now()->weekOfYear, true);
        if ($request->name && $request->date) {
            $data = explode('-', preg_replace('/\s+/', '', $request->date));
            $date1 = Carbon::parse($data[0])->format('Y-m-d');
            $date2 = Carbon::parse($data[1])->format('Y-m-d');
            $dailys = Daily::with('tag', 'user', 'user.area', 'user.divisi')
                ->whereBetween('date', [$date1, $date2])
                ->orderBy('date')
                ->orderBy('time')
                ->whereHas('user', function ($q) use ($request) {
                    $q->where('nama_lengkap', "like", '%' . $request->name . '%');
                })
                ->get();
        } else if ($request->divisi_id) {
            $dailys = Daily::with('tag', 'user', 'user.area', 'user.divisi')
                ->whereBetween('date', [$monday->format('y-m-d'), $monday->addDay(6)->format('y-m-d')])
                ->orderBy(User::select('nama_lengkap')->whereColumn('users.id', 'dailies.user_id'))
                ->orderBy('date')
                ->orderBy('time')
                ->whereHas('user.divisi', function ($q) use ($request) {
                    $q->where('id', $request->divisi_id);
                })
                ->get();
        } else {
            $dailys = Daily::with('tag', 'user', 'user.area', 'user.divisi')
                ->whereBetween('date', [$monday->format('y-m-d'), $monday->addDay(6)->format('y-m-d')])
                ->orderBy(User::select('nama_lengkap')->whereColumn('users.id', 'dailies.user_id'), 'ASC')
                ->orderBy('date')
                ->orderBy('time')
                ->simplePaginate(100);
        }
        return view('admin.daily.index')->with([
            'title' => 'Daily',
            'active' => 'daily',
            'divisis' => Divisi::all()->except(17),
            'dailys' => $dailys,
            'users' => User::orderBy('nama_lengkap')->get()->except(1),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['user_id'] = auth()->id();
            $data['date'] = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
            $date = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));

            ##EXTRA TASK / TAMBAHAN
            if ($request->isplan) {
                if (!Daily::whereDate('date', Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->format('Y-m-d'))->where('user_id', auth()->id())->get()) {
                    if ($request->page == 'teams') {
                        return redirect('/teams/daily')->with(['error' => "Tidak bisa menambahkan daily extra karena hari ini ".Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->format('d M Y')." anda tidak ada plan"]);
                    }
                    return redirect('daily')->with(['error' => "Tidak bisa menambahkan daily extra karena hari ini ".Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->format('d M Y')." anda tidak ada plan"]);
                }
                $data['isplan'] = false;
                $data['ontime'] = true;
                ##TAMBAHAN LEBIH DARI H+1 JAM 10:00
                if (
                    $date->diffInDays(now()) > 0
                    &&
                    now()->subDay(1) > $date->addHour(10)
                ) {
                    $data['status'] = true;
                    $data['ontime'] = 0.5;
                }

                ##TAMBAHAN LEBIH DARI H+2
                // if (
                //     $date->diffInDays(now()) > 0
                //     &&
                //     now() > $date->addDay(2)
                // ) {
                //     if ($request->page == 'teams') {
                //         return redirect('/teams/daily')->with(['error' => 'Tidak bisa menambahkan daily extra, sudah lebih dari H+2']);
                //     }
                //     return redirect('daily')->with(['error' => 'Tidak bisa menambahkan daily extra, sudah lebih dari H+2']);
                // }
            }

            ##VALIDASI WAKTU INPUT
            // if (
            //     auth()->user()->area_id == 2
            //     && now() > $date->startOfWeek()->addDay(1)->addHour(10)
            //     && !$request->isplan
            // ) {
            //     if ($request->page == 'teams') {
            //         return redirect('/teams/daily')->with(['error' => 'Tidak bisa menambahkan daily, sudah lebih dari hari hari selasa Jam 10:00']);
            //     }
            //     return redirect('daily')->with(['error' => 'Tidak bisa menambahkan daily, sudah lebih dari hari hari selasa Jam 10:00']);
            // }

            // $date2 = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));

            // if (
            //     auth()->user()->area_id != 2
            //     && now() > $date2->startOfWeek()->addHour(17)
            //     && !$request->isplan
            // ) {
            //     if ($request->page == 'teams') {
            //         return redirect('/teams/daily')->with(['error' => 'Tidak bisa menambahkan daily, sudah lebih dari hari hari senin Jam 17:00']);
            //     }
            //     return redirect('daily')->with(['error' => 'Tidak bisa menambahkan daily, sudah lebih dari hari hari senin Jam 17:00']);
            // }

            ##CONVERT KE 24 JAM
            if (!$request->isplan) {
                $data['time'] = date('H:i', strtotime($request->time));
            }
            Daily::create($data);

            if ($request->page == 'teams') {
                return redirect('/teams/daily')->with(['success' => "Berhasil menambahkan daily"]);
            }
            return redirect('daily')->with(['success' => "Berhasil menambahkan daily"]);
        } catch (Exception $e) {
            if ($request->page == 'teams') {
                return redirect('/teams/daily')->with(['error' => $e->getMessage()]);
            }
            return redirect('daily')->with(['error' => $e->getMessage()]);
        }
    }

    public function change(Request $request)
    {
        try {
            $daily = Daily::findOrFail($request->id);
            $requesteds = ModelsRequest::where('user_id', auth()->id())->where('jenistodo', 'Daily')->get();

            ##CEK TASK PUNYA SENDIRI ATAU BUKAN
            if ($daily->user_id != auth()->id()) {
                if ($request->page == 'teams') {
                    return redirect('/teams/daily')->with(['error' => 'Bukan task milik anda !']);
                }
                return redirect('daily')->with(['error' => 'Tidak bisa merubah status, task ini bukan milik anda']);
            }

            ##CEK TASK DI REQUEST ATAU TIDAK
            foreach ($requesteds as $requested) {
                $idTaskExistings = explode(',', $requested->todo_request);
                foreach ($idTaskExistings as $idTaskExisting) {
                    if ($request->id == $idTaskExisting && $requested->status == 'PENDING') {
                        if ($request->page == 'teams') {
                            return redirect('/teams/daily')->with(['error' => 'Tidak bisa merubah status, task ini ada di pengajuan request task']);
                        }
                        return redirect('daily')->with(['error' => 'Tidak bisa merubah status, task ini ada di pengajuan request task']);
                    }
                }

                $idTaskReplaces = explode(',', $requested->todo_replace);
                foreach ($idTaskReplaces as $idTaskReplace) {
                    if ($request->id == $idTaskReplace && $requested->status == 'PENDING') {
                        if ($request->page == 'teams') {
                            return redirect('/teams/daily')->with(['error' => 'Tidak bisa merubah status, task ini ada di pengajuan request task dan belum di approve']);
                        }
                        return redirect('daily')->with(['error' => 'Tidak bisa merubah status, task ini ada di pengajuan request task dan belum di approve']);
                    }
                }
            }

            ##VALIDASI JIKA TASK LEBIH DARI 2 HARI
            // if (
            //     Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->weekOfYear
            //     <=
            //     now()->weekOfYear
            //     &&
            //     now()
            //     >
            //     Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(2)
            // ) {
            //     if ($request->page == 'teams') {
            //         return redirect('/teams/daily')->with(['error' => 'Tidak bisa merubah status sudah lebih dari H+2 dari daily']);
            //     }
            //     return redirect('daily')->with(['error' => 'Tidak bisa merubah status sudah lebih dari H+2 dari daily']);
            // }

            ##VALIDASI TIDAK BISA RUBAH STATUS H+1
            // $H = Carbon::parse($daily->date / 1000);
            // if ($H > now()) {
            //     if ($request->page == 'teams') {
            //         return redirect('/teams/daily')->with(['error' => 'Tidak bisa merubah status yang lebih dari ' . now()->format('d M Y')]);
            //     }
            //     return redirect('daily')->with(['error' => 'Tidak bisa merubah status yang lebih dari ' . now()->format('d M Y')]);
            // }

            // ##EXTRA TASK TIDAK BISA DI RUBAH
            // if (!$daily->isplan) {
            //     return redirect('daily')->with(['error' => 'Extra task tidak bisa dirubah']);
            // }

            ##KONVERSI DARI OPEN <=> CLOSE UNTUK ONTIME POINT
            $daily['status'] ? $daily['ontime'] = 0  : $daily['ontime'] = 1.0;

            ##UBAH NILAI ONTIME KETIKA LEBIH H+1 JAM 10:00
            if (
                now()
                >
                Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(1)->addHour(10)
            ) {
                $daily['status'] ? $daily['ontime'] = 0 : $daily['ontime'] = 0.5;
            }

            ##KALAU GANTI CEKLIS DONE, STATUS = DONE, GANTI CEKLIS UNDONE, STATUS = PROGRESS
            if ($request->page == 'teams') {
                if ($daily->status == 1) {
                    $daily->task_status_id = 2;
                } 
                if ($daily->status == 0) {
                    $daily->task_status_id = 1;
                }
                $daily->save();

            }

            ##KONVERSI DARI OPEN <=> CLOSE UNTUK STATUS
            $daily['status'] = !$daily['status'];
            $daily->save();

            $dailyLog = DailyLog::create([
                'user_id' => auth()->user()->id,
                'task_id' => $daily->id,
                'activity' => auth()->user()->nama_lengkap . ' merubah status task ' . $daily->task . ' menjadi ' . ($daily->status == 0 ? 'UNDONE' : 'DONE'),
            ]);
            $dailyLog->save();

            ##REDIRECT TEAMS DAILY
            if ($request->page == 'teams') {
                return redirect('/teams/daily/')->with(['success' => 'Berhasil merubah status daily']);
            }

            return redirect('daily/?tasktype=' . $request->tasktype)->with(['success' => 'Berhasil merubah status daily']);
        } catch (Exception $e) {
            if ($request->page == 'teams') {
                return redirect('/teams/daily')->with(['error' => $e->getMessage()]);
            }

            return redirect('daily')->with(['error' => $e->getMessage()]);
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
    public function edituser($id, Request $request)
    {
        $daily = Daily::find($id);
        $requesteds = ModelsRequest::where('user_id', auth()->id())->where('jenistodo', 'Daily')->get();
        ##CEK TASK PADA REQUEST
        foreach ($requesteds as $requested) {
            $idTaskExistings = explode(',', $requested->todo_request);
            foreach ($idTaskExistings as $idTaskExisting) {
                if ($id == $idTaskExisting && $requested->status == 'PENDING') {
                    return redirect('daily')->with(['error' => 'Tidak bisa merubah, task ini ada di pengajuan request task']);
                }
            }

            $idTaskReplaces = explode(',', $requested->todo_replace);
            foreach ($idTaskReplaces as $idTaskReplace) {
                if ($id == $idTaskReplace && $requested->status == 'PENDING') {
                    return redirect('daily')->with(['error' => 'Tidak bisa merubah, task ini ada di pengajuan request task']);
                }
            }
        }
        ##VALIDASI TASK TAG TIDAK BISA DI RUBAH SELAIN PEMBUAT TAG
        if ($daily->tag_id) {
            return redirect('daily')->with(['error' => 'Tidak bisa merubah, task tagging hanya bisa di rubah oleh pembuat tag']);
        }
        ##VALIDASI TASK ADDED TIDAK BISA DI RUBAH SELAIN PEMBUAT TASK
        if ($daily->add_id != null) {
            if ($daily->add_id != auth()->user()->id) {
                if ($request->page == 'teams') {
                    return redirect('/teams/daily')->with(['error' => 'Tidak bisa merubah, task bukan ditambahkan oleh anda']);
                }
                return redirect('daily')->with(['error' => 'Tidak bisa merubah, task bukan ditambahkan oleh anda']);
            }
        }

        if ($daily->add_id == null) {
            if ($daily->user_id != auth()->user()->id) {
                if ($request->page == 'teams') {
                    return redirect('/teams/daily')->with(['error' => 'Tidak bisa merubah, task bukan ditambahkan oleh anda']);
                }
                return redirect('daily')->with(['error' => 'Tidak bisa merubah, task bukan ditambahkan oleh anda']);
            }
        }
        ##VALIDASI TIDAK BISA EDIT PADA WEEK YANG SEDANG BERJALAN SETELAH MAKSIMAL WAKTU INPUT DAN USER BUKAN PEMBERI TASK/PEMILIK TASK
        ##CEK LAGI DI HARI SELASA
        if (Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->weekOfYear <= now()->weekOfYear && (auth()->user()->id != ($daily->add_id ?? $daily->user_id))) {
            if (auth()->user()->area_id == 2 && now() > Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfWeek()->addDay(1)->addHour(10)) {
                return redirect('daily')->with(['error' => 'Tidak bisa merubah daily di week yang sudah berjalan dan lebih dari hari selasa jam 10.00']);
            } else if (auth()->user()->area_id != 2 && now() > Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfWeek()->addHour(17)) {
                return redirect('daily')->with(['error' => 'Tidak bisa merubah daily di week yang sudah berjalan dan lebih dari hari senin jam 17.00']);
            }
        }
        return view('admin.daily.edit')->with([
            'title' => 'Daily',
            'active' => 'daily',
            'daily' => Daily::find($id),
            'page' => $request->page,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $daily = Daily::find($request->id);
            ##VALIDASI JIKA MERUBAH TANGGAL KURANG DARI SEBELUMNYA
            if (Carbon::parse($request->date)->weekOfYear < Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->weekOfYear) {
                return back()->with(['error' => 'Tidak bisa merubah daily dengan tanggal kurang dari daily yang di edit']);
            }

            $data = $request->all();
            $data['date'] = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
            $data['time'] = date('H:i', strtotime($request->time));
            $daily->update($data);

            if ($request->page == 'teams') {
                return redirect('/teams/daily')->with(['success' => 'Berhasil merubah daily']);
            }
            return redirect('daily')->with(['success' => 'Berhasil merubah daily']);
        } catch (Exception $e) {
            return redirect('daily')->with(['error' => $e->getMessage()]);
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
        try {
            $daily = Daily::findOrFail($request->id);
            $requesteds = ModelsRequest::where('user_id', auth()->id())->where('jenistodo', 'Daily')->get();

            ##VALIDASI TASK ADDED TIDAK BISA DI DELETE SELAIN PEMBUAT TASK
            if ($daily->add_id != null) {
                if ($daily->add_id != auth()->user()->id) {
                    if ($request->page == 'teams') {
                        return redirect('/teams/daily')->with(['error' => 'Tidak bisa menghapus, task bukan ditambahkan oleh anda']);
                    }
                    return redirect('daily')->with(['error' => 'Tidak bisa menghapus, task bukan ditambahkan oleh anda']);
                }
            }

            if ($daily->add_id == null) {
                if ($daily->user_id != auth()->user()->id) {
                    if ($request->page == 'teams') {
                        return redirect('/teams/daily')->with(['error' => 'Tidak bisa menghapus, task bukan ditambahkan oleh anda']);
                    }
                    return redirect('daily')->with(['error' => 'Tidak bisa menghapus, task bukan ditambahkan oleh anda']);
                }
            }
            
            ##CEK TASK DI REQUEST
            foreach ($requesteds as $requested) {
                $idTaskExistings = explode(',', $requested->todo_request);
                foreach ($idTaskExistings as $idTaskExisting) {
                    if ($request->id == $idTaskExisting && $requested->status == 'PENDING') {
                        if ($request->page == 'teams') {
                            return redirect('/teams/daily')->with(['error' => "Tidak bisa menghapus, task ini ada di pengajuan request task"]);
                        }
                        return redirect('daily')->with(['error' => "Tidak bisa menghapus, task ini ada di pengajuan request task"]);
                    }
                }

                $idTaskReplaces = explode(',', $requested->todo_replace);
                foreach ($idTaskReplaces as $idTaskReplace) {
                    if ($request->id == $idTaskReplace && $requested->status == 'PENDING') {
                        if ($request->page == 'teams') {
                            return redirect('/teams/daily')->with(['error' => "Tidak bisa menghapus, task ini ada di pengajuan request task"]);
                        }
                        return redirect('daily')->with(['error' => "Tidak bisa menghapus, task ini ada di pengajuan request task"]);
                    }
                }
            }
            ##VALIDASI TIDAK BISA DETELET TASK TAG KECUALI OLEH PEMBUAT TAG
            if ($daily->tag_id) {
                if ($request->page == 'teams') {
                    return redirect('/teams/daily')->with(['error' => "Tidak bisa menghapus tag daily, tag daily hanya bisa di hapus oleh pembuatan tag"]);
                }
                return redirect('daily')->with(['error' => "Tidak bisa menghapus tag daily, tag daily hanya bisa di hapus oleh pembuatan tag"]);
            }
            ##VALIDASI JIKA TASK PLAN
            if ($daily->isplan) {
                if (Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->weekOfYear <= now()->weekOfYear && !$daily->tag_id && $daily->add_id == null) {
                    if (auth()->user()->area_id == 2 && now() > Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfWeek()->addDay(1)->addHour(10)) {
                        if ($request->page == 'teams') {
                            return redirect('/teams/daily')->with(['error' => "Tidak bisa menghapus daily di week yang sudah berjalan dan lebih dari selasa jam 10.00"]);
                        }
                        return redirect('daily')->with(['error' => "Tidak bisa menghapus daily di week yang sudah berjalan dan lebih dari selasa jam 10.00"]);
                    } else if (auth()->user()->area_id != 2 && now() > Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfWeek()->addHour(17)) {
                        if ($request->page == 'teams') {
                            return redirect('/teams/daily')->with(['error' => "Tidak bisa menghapus daily di week yang sudah berjalan dan lebih dari senin jam 17.00"]);
                        }
                        return redirect('daily')->with(['error' => "Tidak bisa menghapus daily di week yang sudah berjalan dan lebih dari senin jam 17.00"]);
                    }
                }
            } else {
                $date = Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
                ##VALIDASI EXTRA TASK TIDAK BISA DI HAPUS H+2
                if (
                    $date->diffInDays(now()) > 0
                    &&
                    now() > $date->addDay(2)
                ) {
                    if ($request->page == 'teams') {
                        return redirect('/teams/daily')->with(['error' => 'Tidak bisa menghapus daily extra, sudah lebih dari H+2']);
                    }
                    return redirect('daily')->with(['error' => 'Tidak bisa menghapus daily extra, sudah lebih dari H+2']);
                }
            }
            ##MENDAPATKAN TAG DAILY
            $deletes = Daily::where('task', $daily->task)->where('tag_id', auth()->id())->whereDate('date', date('y-m-d', $daily->date / 1000))->get();
            ##MENGHAPUS DAILY TAG YANG LAIN
            if ($deletes) {
                foreach ($deletes as $delete) {
                    $delete->delete();
                }
            }
            $daily->delete();

            if ($request->page == 'teams') {
                return redirect('/teams/daily')->with(['success' => "Berhasil menghapus daily"]);
            }
            return redirect('daily')->with(['success' => "Berhasil menghapus daily"]);
        } catch (Exception $e) {
            if ($request->page == 'teams') {
                return redirect('/teams/daily')->with(['error' => $e->getMessage()]);
            }
            return redirect('daily')->with(['error' => $e->getMessage()]);
        }
    }

    public function getdaily(Request $request)
    {
        $dailys =  Daily::with('user', 'user.area', 'user.divisi')
            ->whereDate('date', $request->date)
            ->where('user_id', $request->id)
            ->where('isplan', 1)
            ->where('isupdate', 0)
            ->orderBy('time')
            ->get();
        return response()->json($dailys);
    }
}
