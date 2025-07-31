<?php

namespace App\Http\Controllers;

use App\Helpers\SendNotif;
use App\Models\Daily;
use App\Models\Monthly;
use App\Models\Request as ModelsRequest;
use App\Models\Weekly;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        //SETUP DAILY
        $totalDaily = 0;
        $dailyClosed = 0;
        $dailysLength = [];
        $totalPointDaily = 0;
        $dailyOntime = 0;
        $totalPointOnTimeDaily = 0;
        //SETUP WEEKLY
        $closedWeekly = 0;
        $totalWeekly = 0;
        $totalPointWeekly = 0;
        //SETUP WEEKLY
        $closedMonthly = 0;
        $totalMonthly = 0;
        $totalPointMonthly = 0;

        for ($i = 0; $i < 7; $i++) {
            if ($i == 0) {
                $monday = now()->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfDay()->startOfWeek();
                $daily = Daily::where('date', $monday)->where('user_id', auth()->id())->orderBy('time')->get();
            } else {
                $monday1 = now()->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfDay()->startOfWeek();
                $daily = Daily::where('date', $monday1->addDay($i))->where('user_id', auth()->id())->orderBy('time')->get();
            }

            if (count($daily) > 0) {
                array_push($dailysLength, $daily);
            }
        }
        foreach ($dailysLength as $dailys) {
            $totalDaily += count($dailys->where('isplan', 1));
            foreach ($dailys as $daily) {
                if ($daily->status) {
                    $dailyClosed++;
                }
                if ($daily->ontime) {
                    $dailyOntime += $daily->ontime;
                }
            }
        }
        $dailySubmited = count($dailysLength);

        #SUMMARY DAILY
        if ($dailyClosed) {
            if (
                !auth()->user()->wr && !auth()->user()->wn
            ) {
                if ($dailySubmited) {

                    $totalPointDaily += ($dailySubmited / 6) * ($dailyClosed / $totalDaily) * 80 > 80 ? 80 : ($dailySubmited / 6) * ($dailyClosed / $totalDaily) * 80;
                }
            } else {
                if ($totalDaily) {
                    $totalPointDaily += ($dailySubmited / 6) * ($dailyClosed / $totalDaily) * 40 > 40 ? 40 : ($dailySubmited / 6) * ($dailyClosed / $totalDaily) * 40;
                }
            }
            if ($totalDaily) {
                $totalPointOnTimeDaily += ($dailySubmited / 6) * ($dailyOntime / $totalDaily) * 20 > 20 ? 20 : ($dailySubmited / 6) * ($dailyOntime / $totalDaily) * 20;
            }
        }

        if (auth()->user()->wr || auth()->user()->wn) {
            $weeklys = Weekly::where('week', now()->weekOfYear)->where('year', now()->year)->where('user_id', auth()->id())->get();
            $totalWeekly = count($weeklys);
            if ($weeklys) {
                foreach ($weeklys as $weekly) {
                    if ($weekly->value) {
                        $closedWeekly += $weekly->value;
                    }
                }
                if ($closedWeekly) {
                    $totalPointWeekly = ($closedWeekly / $totalWeekly) > 1 ? 40 : ($closedWeekly / $totalWeekly) * 40;
                }
            }
        }

        if (auth()->user()->mr || auth()->user()->mn) {
            ##MONTHLY
            $startMonth = now()->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfDay()->startOfMonth();
            $monthlys = Monthly::whereDate('date', $startMonth)->where('user_id', auth()->id())->get();
            $totalMonthly = count($monthlys);
            if ($monthlys) {
                foreach ($monthlys as $monthly) {
                    if ($monthly->value) {
                        $closedMonthly += $monthly->value;
                    }
                }
                if ($closedMonthly) {
                    $totalPointMonthly = ($closedMonthly / $totalMonthly) > 1 ? 20 : ($closedMonthly / $totalMonthly) * 20;
                }
            }
        }
        $totalKpi = $totalPointDaily + $totalPointOnTimeDaily + $totalPointWeekly;
        return view('dashboard.index', [
            'title' => 'DASHBOARD',
            'active' => 'dashboard',
            'data' => [
                'totalTaskDaily' => $totalDaily,
                'closedTaskDaily' => $dailyClosed,
                'submitedDaily' => $dailySubmited,
                'pointDaily' => $totalPointDaily,
                'pointOntime' => $totalPointOnTimeDaily,
                'totalTaskWeekly' => $totalWeekly,
                'closedTaskWeekly' => $closedWeekly,
                'pointWeekly' => $totalPointWeekly,
                'totalTaskMonthly' => $totalMonthly,
                'closedTaskMonthly' => $closedMonthly,
                'pointMonthly' => $totalPointMonthly,
                'totalKpi' => $totalKpi,
            ],
        ]);
    }

    public function broadcast(Request $request)
    {
        if ($request->content) {
            return SendNotif::sendBroadcast($request->content);
        }
    }

    public function request(Request $request)
    {
        return view('request.index')->with([
            'title' => 'Request',
            'active' => 'request',
            'requests' => ModelsRequest::with('user', 'approveId', 'approvedBy')->where('user_id', auth()->id())->get(),
        ]);
    }

    public function submit(Request $request)
    {
        try {
            if (!$request->get('selectedExisting')) {
                return redirect('request')->with(['error' => "Task existing harus di pilih"]);
            }
            $requesteds = ModelsRequest::where('user_id', auth()->id())->where('jenistodo', $request->jenistodo)->get();
            ##CEK TASK DI REQUEST ATAU TIDAK
            foreach ($requesteds as $requested) {
                $idTaskExistings = explode(',', $requested->todo_request);
                foreach ($idTaskExistings as $idTaskExisting) {
                    foreach ($request->get('selectedExisting') as $exist) {
                        if ($exist == $idTaskExisting && $requested->status == 'PENDING') {
                            return redirect('request')
                                ->with(['error' => 'Tidak bisa menambahkan request, task sudah ada di pengajuan request task']);
                        }
                    }
                }
            }
            $idReplace = array();
            switch ($request->jenistodo) {
                case 'Daily':
                    if (
                        (Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->weekOfYear < now()->weekOfYear)
                        &&
                        (Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->year <= now()->year)
                    ) {
                        return redirect('request')->with(['error' => 'Tidak bisa membuat request kurang dari tanggal ' . now()->startOfDay()->startOfWeek()->format('d M y')]);
                    }
                    for ($i = 0; $i < count($request->get('taskdaily')); $i++) {
                        $temp = array();
                        $temp['user_id'] = auth()->id();
                        $temp['date'] = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
                        $temp['task'] = $request->get('taskdaily')[$i];
                        $temp['isupdate'] = 1;
                        $temp['isplan'] = 1;
                        $temp['time'] = date('H:i', strtotime($request->get('timedaily')[$i]));
                        $insertDaily = Daily::create($temp);
                        array_push($idReplace, $insertDaily->id);
                    }
                    ModelsRequest::create([
                        'user_id' => auth()->id(),
                        'jenistodo' => $request->jenistodo,
                        'todo_request' => implode(',', $request->get('selectedExisting')),
                        'todo_replace' => implode(',', $idReplace),
                        'approval_id' => auth()->user()->approval_id,
                        'status' => 'PENDING',
                    ]);
                    if (auth()->user()->approval->id_notif) {
                        SendNotif::sendMessage(
                            'Request task ' . $request->jenistodo . ' dari ' .
                                auth()->user()->nama_lengkap,
                            array(auth()->user()->approval->id_notif)
                        );
                    }
                    break;
                case 'Weekly':
                    if (
                        ($request->week < now()->weekOfYear)
                        &&
                        ($request->year <= now()->year)
                    ) {
                        return redirect('request')->with(['error' => 'Tidak bisa membuat request kurang dari week ' . now()->weekOfYear]);
                    }
                    if (count($request->get('selectedExisting')) > count($request->get('taskweekly'))) {
                        return redirect('request')->with(['error' => 'task replace jumlahnya harus sama atau lebih dengan task existing']);
                    }
                    for ($i = 0; $i < count($request->get('taskweekly')); $i++) {
                        $temp = array();
                        $temp['user_id'] = auth()->id();
                        $temp['year'] = $request->year;
                        $temp['week'] = $request->week;
                        $temp['task'] = $request->get('taskweekly')[$i];
                        $temp['is_update'] = 1;
                        if (auth()->user()->wr) {
                            if ($request->get('tipe')[$i] == 'RESULT') {
                                if (!$request->get('value_plan')[$i]) {
                                    return redirect('request')->with(['error' => 'jika task result harus di isi value nya']);
                                }
                                $temp['tipe'] = 'RESULT';
                                $temp['value_plan'] = $request->get('value_plan')[$i];
                                $temp['value_actual'] = 0;
                                $temp['status_result'] = 0;
                            } else {
                                $temp['tipe'] = 'NON';
                                $temp['status_non'] = 0;
                            }
                        } else {
                            $temp['tipe'] = 'NON';
                            $temp['status_non'] = 0;
                        }
                        $insertWeekly = Weekly::create($temp);
                        array_push($idReplace, $insertWeekly->id);
                    }
                    ModelsRequest::create([
                        'user_id' => auth()->id(),
                        'jenistodo' => $request->jenistodo,
                        'todo_request' => implode(',', $request->get('selectedExisting')),
                        'todo_replace' => implode(',', $idReplace),
                        'approval_id' => auth()->user()->approval_id,
                        'status' => 'PENDING',
                    ]);
                    if (auth()->user()->approval->id_notif) {
                        SendNotif::sendMessage(
                            'Request task ' . $request->jenistodo . ' dari ' .
                                auth()->user()->nama_lengkap,
                            array(auth()->user()->approval->id_notif)
                        );
                    }
                    break;
                default:
                    if (
                        (now() > Carbon::parse(strtotime($request->month))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addMonth(1)->addDay(5))
                    ) {
                        return redirect('request')->with(['error' => 'Tidak bisa membuat request bulan ' . Carbon::parse(strtotime($request->month))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->format('M') . ' kurang dari tanggal ' . now()->startOfDay()->startOfMonth()->addDay(4)->format('d M y')]);
                    }
                    if (count($request->get('selectedExisting')) > count($request->get('taskmonthly'))) {
                        return redirect('request')->with(['error' => 'task replace jumlahnya harus sama atau lebih dengan task existing']);
                    }

                    for ($i = 0; $i < count($request->get('taskmonthly')); $i++) {
                        $temp = array();
                        $temp['user_id'] = auth()->id();
                        $temp['date'] = Carbon::parse(strtotime($request->month))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
                        $temp['task'] = $request->get('taskmonthly')[$i];
                        $temp['is_update'] = 1;
                        if (auth()->user()->mr) {
                            if ($request->get('tipe')[$i] == 'RESULT') {
                                if (!$request->get('value_plan')[$i]) {
                                    return redirect('request')->with(['error' => 'jika task result harus di isi value nya']);
                                }
                                $temp['tipe'] = 'RESULT';
                                $temp['value_plan'] = $request->get('value_plan')[$i];
                                $temp['value_actual'] = 0;
                                $temp['status_result'] = 0;
                            } else {
                                $temp['tipe'] = 'NON';
                                $temp['status_non'] = 0;
                            }
                        } else {
                            $temp['tipe'] = 'NON';
                            $temp['status_non'] = 0;
                        }
                        $insertMonthly = Monthly::create($temp);
                        array_push($idReplace, $insertMonthly->id);
                    }
                    ModelsRequest::create([
                        'user_id' => auth()->id(),
                        'jenistodo' => $request->jenistodo,
                        'todo_request' => implode(',', $request->get('selectedExisting')),
                        'todo_replace' => implode(',', $idReplace),
                        'approval_id' => auth()->user()->approval_id,
                        'status' => 'PENDING',
                    ]);
                    if (auth()->user()->approval->id_notif) {
                        SendNotif::sendMessage(
                            'Request task ' . $request->jenistodo . ' dari ' .
                                auth()->user()->nama_lengkap,
                            array(auth()->user()->approval->id_notif)
                        );
                    }
                    break;
            }

            return redirect('request')->with(['success' => "Berhasil menambahkan request change task"]);
        } catch (Exception $e) {
            return redirect('request')->with(['error' => $e->getMessage()]);
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $requested = ModelsRequest::find($id);
            if (auth()->id() != $requested->user_id) {
                return redirect('request')->with(['error' => 'Tidak bisa merubah request yang bukan milik sendiri']);
            }
            foreach (explode(',', $requested->todo_replace) as $replace) {
                $dailyReplace = Daily::find($replace);
                $dailyReplace->delete();
            }
            $requested->status = 'CANCELED';
            $requested->save();
            return redirect('request')->with(['success' => 'Berhasil membatalkan pengajuan']);
        } catch (Exception $e) {
            return redirect('request')->with(['error' => $e->getMessage()]);
        }
    }

    public function requestcreate(Request $request)
    {
        return view('request.create')->with([
            'title' => 'Request',
            'active' => 'request',
        ]);
    }

    public function reqindex(Request $request)
    {
        return view('approval.index')->with([
            'title' => 'APPROVAL',
            'active' => 'req',
            'requests' => auth()->user()->role_id == 1 ? ModelsRequest::orderBy('created_at','DESC')->simplePaginate(100) : ModelsRequest::where('approval_id', auth()->id())->simplePaginate(100),
        ]);
    }

    public function approve(Request $request)
    {
        try {
            $requested = ModelsRequest::find($request->id);
            if ($requested->status == 'CANCELED') {
                return redirect('req')->with(['error' => 'Request ini di cancel oleh yang bersangkutan']);
            }
            switch ($requested->jenistodo) {
                case 'Daily':
                    //TASK EXISTING
                    $idTaskExistings = explode(',', $requested->todo_request);
                    foreach ($idTaskExistings as $idTaskExisting) {
                        $dailyExisting = Daily::find($idTaskExisting);
                        if (now() > Carbon::parse($dailyExisting->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfWeek()->addWeek(1)->addHour(10)) {
                            return redirect('req')->with(['error' => 'Tidak bisa approved task yang lebih Task request tanggal ' . Carbon::parse($dailyExisting->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->format('d M Y')]);
                        }
                        $dailyExisting->delete();
                    }
                    //TASK REPLACE
                    $idTaskReplaces = explode(',', $requested->todo_replace);
                    foreach ($idTaskReplaces as $idTaskReplace) {
                        $dailyReplace = Daily::find($idTaskReplace);
                        if (Carbon::parse($dailyReplace->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->diffInDays(now())) {
                            $dailyReplace->status = 1;
                            $dailyReplace->ontime = 1;
                            $dailyReplace->save();
                        }
                    }
                    break;
                case 'Weekly':
                    //TASK EXISTING
                    $idTaskExistings = explode(',', $requested->todo_request);
                    foreach ($idTaskExistings as $idTaskExisting) {
                        $dailyExisting = Weekly::find($idTaskExisting);
                        if (now()->weekOfYear > $dailyExisting->week) {
                            return redirect('req')->with(['error' => 'Tidak bisa approved task yang lebih dari 1 week, Task request week ' . $dailyExisting->week]);
                        }
                        $dailyExisting->delete();
                    }
                    break;

                default:
                    //TASK EXISTING
                    $idTaskExistings = explode(',', $requested->todo_request);
                    foreach ($idTaskExistings as $idTaskExisting) {
                        $dailyExisting = Monthly::find($idTaskExisting);
                        // if (now() > Carbon::parse($dailyExisting->date)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addMonth(1)->addDay(4)) {
                        //     return redirect('req')->with(['error' => 'Tidak bisa approve task yang melebihi 1 bulan']);
                        // }
                        $dailyExisting->delete();
                    }
                    break;
            }
            $requested->status = 'APPROVED';
            $requested->approved_by = auth()->id();
            $requested->approved_at = now();
            $requested->save();
            if ($requested->user->id_notif) {
                SendNotif::sendMessage('Request task ' . $requested->jenistodo . ' sudah di setujui oleh ' . auth()->user()->nama_lengkap, array($requested->user->approval->id_notif));
            }
            return redirect('req')->with(['success' => 'Berhasil menyetujui request']);
        } catch (Exception $e) {
            return redirect('req')->with(['error' => $e->getMessage()]);
        }
    }

    public function reject(Request $request)
    {
        try {
            $requested = ModelsRequest::find($request->id);
            $idTaskReplaces = explode(',', $requested->todo_replace);
            switch ($requested->jenistodo) {
                case 'Daily':
                    foreach ($idTaskReplaces as $idTaskReplace) {
                        $daily = Daily::find($idTaskReplace);
                        $daily->delete();
                    }
                    break;
                case 'Weekly':
                    foreach ($idTaskReplaces as $idTaskReplace) {
                        $daily = Weekly::find($idTaskReplace);
                        $daily->delete();
                    }
                    break;

                default:
                    foreach ($idTaskReplaces as $idTaskReplace) {
                        $daily = Monthly::find($idTaskReplace);
                        $daily->delete();
                    }
                    break;
            }
            $requested->status = 'REJECTED';
            $requested->approved_by = auth()->id();
            $requested->approved_at = now();
            $requested->save();
            if ($requested->user->id_notif) {
                SendNotif::sendMessage('Request task ' . $requested->jenistodo . ' di tolak oleh ' . auth()->user()->nama_lengkap, array($requested->user->id_notif));
            }
            return redirect('req')->with(['success' => 'Berhasil menolak request']);
        } catch (Exception $e) {
            return redirect('req')->with(['error' => $e->getMessage()]);
        }
    }

    public function exist(Request $request)
    {
        switch ($request->jenistodo) {
            case 'Daily':
                $existData = array();
                $listExist = explode(',', $request->id);
                foreach ($listExist as $le) {
                    $dailyExist = Daily::withTrashed()->find($le);
                    array_push($existData, $dailyExist);
                }
                return view('request.daily')->with([
                    'exist' => true,
                    'active' => '',
                    'title' => 'Detail Task',
                    'dailys' => $existData,
                ]);
                break;

            case 'Weekly':
                $existData = array();
                $listExist = explode(',', $request->id);
                foreach ($listExist as $le) {
                    $weeklyExist = Weekly::withTrashed()->find($le);
                    array_push($existData, $weeklyExist);
                }
                return view('request.weekly')->with([
                    'exist' => true,
                    'active' => '',
                    'title' => 'Detail Task',
                    'weeklys' => $existData,
                ]);
                break;

            default:
                $existData = array();
                $listExist = explode(',', $request->id);
                foreach ($listExist as $le) {
                    $monthlyExist = Monthly::withTrashed()->find($le);
                    array_push($existData, $monthlyExist);
                }
                return view('request.monthly')->with([
                    'exist' => true,
                    'active' => '',
                    'title' => 'Detail Task',
                    'monthlys' => $existData,
                ]);
                break;
        }
    }

    public function replace(Request $request)
    {
        switch ($request->jenistodo) {
            case 'Daily':
                $replaceData = array();
                $listReplace = explode(',', $request->id);
                foreach ($listReplace as $le) {
                    $dailyReplace = Daily::withTrashed()->find($le);
                    array_push($replaceData, $dailyReplace);
                }
                return view('request.daily')->with([
                    'exist' => false,
                    'active' => '',
                    'title' => 'Detail Task',
                    'dailys' => $replaceData,
                ]);
                break;

            case 'Weekly':
                $existData = array();
                $listExist = explode(',', $request->id);
                foreach ($listExist as $le) {
                    $weeklyExist = Weekly::withTrashed()->find($le);
                    array_push($existData, $weeklyExist);
                }
                return view('request.weekly')->with([
                    'exist' => false,
                    'active' => '',
                    'title' => 'Detail Task',
                    'weeklys' => $existData,
                ]);
                break;

            default:
                $existData = array();
                $listExist = explode(',', $request->id);
                foreach ($listExist as $le) {
                    $monthlyExist = Monthly::withTrashed()->find($le);
                    array_push($existData, $monthlyExist);
                }
                return view('request.monthly')->with([
                    'exist' => false,
                    'active' => '',
                    'title' => 'Detail Task',
                    'monthlys' => $existData,
                ]);
                break;
        }
    }
}
