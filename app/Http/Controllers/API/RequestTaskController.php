<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Models\Daily;
use App\Models\Weekly;
use App\Models\Monthly;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Helpers\SendNotif;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as ModelsRequest;

class RequestTaskController extends Controller
{
    public function fetchuser(Request $request)
    {
        try {
            $requestings = ModelsRequest::with(
                'user',
                'user.role',
                'user.area',
                'user.divisi',
                'approveId',
                'approveId.role',
                'approveId.area',
                'approveId.divisi',
                'approvedBy',
                'approvedBy.role',
                'approvedBy.divisi',
                'approvedBy.area'
            )->where('user_id', Auth::id())
                ->orderBy('created_at', 'DESC')
                ->get();

            $result = array();
            foreach ($requestings as $requested) {
                $taskExisting = array();
                $taskReplace = array();
                switch ($requested->jenistodo) {
                    case 'Daily':
                        ##TASK EXISTING
                        $existIds = explode(',', $requested->todo_request);
                        foreach ($existIds as $existId) {
                            $daily = Daily::withTrashed()->find($existId);
                            array_push($taskExisting, $daily);
                        }
                        #TASK REPLACE
                        $replaceIds =
                            explode(',', $requested->todo_replace);
                        foreach ($replaceIds as $replaceId) {
                            $daily = Daily::withTrashed()->find($replaceId);
                            array_push($taskReplace, $daily);
                        }
                        break;
                    case 'Weekly':
                        ##TASK EXISTING
                        $existIds = explode(',', $requested->todo_request);
                        foreach ($existIds as $existId) {
                            $weekly = Weekly::withTrashed()->find($existId);
                            array_push($taskExisting, $weekly);
                        }
                        #TASK REPLACE
                        $replaceIds =
                            explode(',', $requested->todo_replace);
                        foreach ($replaceIds as $replaceId) {
                            $weekly = Weekly::withTrashed()->find($replaceId);
                            array_push($taskReplace, $weekly);
                        }
                        break;
                    default:
                        ##TASK EXISTING
                        $existIds = explode(',', $requested->todo_request);
                        foreach ($existIds as $existId) {
                            $monthly = Monthly::withTrashed()->find($existId);
                            array_push($taskExisting, $monthly);
                        }
                        #TASK REPLACE
                        $replaceIds =
                            explode(',', $requested->todo_replace);
                        foreach ($replaceIds as $replaceId) {
                            $monthly = Monthly::withTrashed()->find($replaceId);
                            array_push($taskReplace, $monthly);
                        }
                        break;
                }
                array_push(
                    $result,
                    [
                        'request' => $requested,
                        'existing' => $taskExisting,
                        'replace' => $taskReplace,
                    ],
                );
                unset($taskExisting);
                unset($taskReplace);
            }


            return ResponseFormatter::success($result, 'berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function fetchapprove(Request $request)
    {
        try {
            $requestings = ModelsRequest::with(
                'user',
                'user.role',
                'user.area',
                'user.divisi',
                'approveId',
                'approveId.role',
                'approveId.area',
                'approveId.divisi',
                'approvedBy',
                'approvedBy.role',
                'approvedBy.divisi',
                'approvedBy.area'
            )->where('approval_id', Auth::id())
                ->withTrashed()
                ->orderBy('created_at', 'DESC')
                ->get();

            $result = array();
            foreach ($requestings as $requested) {
                $taskExisting = array();
                $taskReplace = array();
                switch ($requested->jenistodo) {
                    case 'Daily':
                        ##TASK EXISTING
                        $existIds = explode(',', $requested->todo_request);
                        foreach ($existIds as $existId) {
                            $daily = Daily::withTrashed()->find($existId);
                            array_push($taskExisting, $daily);
                        }
                        #TASK REPLACE
                        $replaceIds =
                            explode(',', $requested->todo_replace);
                        foreach ($replaceIds as $replaceId) {
                            $daily = Daily::withTrashed()->find($replaceId);
                            array_push($taskReplace, $daily);
                        }
                        break;
                    case 'Weekly':
                        ##TASK EXISTING
                        $existIds = explode(',', $requested->todo_request);
                        foreach ($existIds as $existId) {
                            $weekly = Weekly::withTrashed()->find($existId);
                            array_push($taskExisting, $weekly);
                        }
                        #TASK REPLACE
                        $replaceIds =
                            explode(',', $requested->todo_replace);
                        foreach ($replaceIds as $replaceId) {
                            $weekly = Weekly::withTrashed()->find($replaceId);
                            array_push($taskReplace, $weekly);
                        }
                        break;
                    default:
                        ##TASK EXISTING
                        $existIds = explode(',', $requested->todo_request);
                        foreach ($existIds as $existId) {
                            $monthly = Monthly::withTrashed()->find($existId);
                            array_push($taskExisting, $monthly);
                        }
                        #TASK REPLACE
                        $replaceIds =
                            explode(',', $requested->todo_replace);
                        foreach ($replaceIds as $replaceId) {
                            $monthly = Monthly::withTrashed()->find($replaceId);
                            array_push($taskReplace, $monthly);
                        }
                        break;
                }
                array_push(
                    $result,
                    [
                        'request' => $requested,
                        'existing' => $taskExisting,
                        'replace' => $taskReplace,
                    ],
                );
                unset($taskExisting);
                unset($taskReplace);
            }


            return ResponseFormatter::success($result, 'berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function submit(Request $request)
    {
        try {
            $userId = Auth::id();
            $requested = ModelsRequest::where('user_id', $userId)->get();
            $taskExistingId = array();
            $taskReplacingId = array();
            switch ($request->type) {
                case 'Daily':
                    foreach ($request->dailye as $daily) {
                        if ($requested) {
                            foreach ($requested->where('jenistodo', 'Daily') as $req) {
                                $taskIds = explode(',', $req->todo_request);
                                foreach ($taskIds as $taskId) {
                                    if ($daily == $taskId && ($req->status == 'PENDING' || $req->status == 'APPROVED')) {
                                        return ResponseFormatter::error(null, 'Task existing sudah pernah di buat request');
                                    }
                                }
                            }
                        }
                        array_push($taskExistingId, $daily);
                    }
                    foreach ($request->dailyr as $daily) {
                        $daily['user_id'] = $userId;
                        $date = Carbon::parse(strtotime($daily['date']))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
                        if ($date < now()->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfDay()->subDay(1)) {
                            return ResponseFormatter::error(null, 'Task replace tidak boleh kurang dari ' . now()->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfDay()->subDay(1)->format('d M Y'));
                        }
                        $daily['time'] = date('H:i', strtotime($daily['time']));

                        $insert = Daily::create($daily);
                        array_push($taskReplacingId, $insert->id);
                    }
                    break;
                case 'Weekly':
                    foreach ($request->weeklye as $weekly) {
                        if ($requested) {
                            foreach ($requested->where('jenistodo', 'Weekly') as $req) {
                                $taskIds = explode(',', $req->todo_request);
                                foreach ($taskIds as $taskId) {
                                    if ($weekly == $taskId && ($req->status == 'PENDING' || $req->status == 'APPROVED')) {
                                        return ResponseFormatter::error(null, 'Task existing sudah pernah di buat request');
                                    }
                                }
                            }
                        }
                        array_push($taskExistingId, $weekly);
                    }
                    foreach ($request->weeklyr as $weekly) {
                        $weekly['user_id'] = $userId;
                        if ($weekly['week'] < now()->weekOfYear) {
                            return ResponseFormatter::error(null, 'Task replace tidak boleh kurang dari week ' . now()->weekOfYear);
                        }
                        $insert = Weekly::create($weekly);
                        array_push($taskReplacingId, $insert->id);
                    }
                    break;
                default:
                    foreach ($request->monthlye as $monthly) {
                        if ($requested) {
                            foreach ($requested->where('jenistodo', 'Monthly') as $req) {
                                $taskIds = explode(',', $req->todo_request);
                                foreach ($taskIds as $taskId) {
                                    if ($monthly == $taskId && ($req->status == 'PENDING' || $req->status == 'APPROVED')) {
                                        return ResponseFormatter::error(null, 'Task existing sudah pernah di buat request');
                                    }
                                }
                            }
                        }
                        array_push($taskExistingId, $monthly);
                    }
                    foreach ($request->monthlyr as $monthly) {
                        $monthly['user_id'] = $userId;
                        $monthRequest = Carbon::parse($monthly['date'])->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
                        if ($monthRequest < now()->startOfMonth()->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))) {
                            return ResponseFormatter::error(null, 'Task replace tidak boleh kurang dari bulan ' . now()->format('M Y'));
                        }
                        $insert = Monthly::create($monthly);
                        array_push($taskReplacingId, $insert->id);
                    }
                    break;
            }
            ModelsRequest::create([
                'user_id' => $userId,
                'jenistodo' => $request->type,
                'todo_request' => implode(',', $taskExistingId),
                'todo_replace' => implode(',', $taskReplacingId),
                'approval_id' => Auth::user()->approval_id ?? 1,
                'status' => 'PENDING',
            ]);

            if (Auth::user()->approval->id_notif) {
                SendNotif::sendMessage('Request task ' . $request->type . ' dari ' . Auth::user()->nama_lengkap, array(Auth::user()->approval->id_notif));
            }

            return ResponseFormatter::success(null, 'Berhasil menambahkan request');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function approve(Request $request)
    {
        try {
            $requested = ModelsRequest::find($request->id);
            if ($requested->status == 'CANCELED') {
                return ResponseFormatter::error(null, 'Request ini di cancel oleh yang bersangkutan');
            }
            switch ($requested->jenistodo) {
                case 'Daily':
                    //TASK EXISTING
                    $idTaskExistings = explode(',', $requested->todo_request);
                    foreach ($idTaskExistings as $idTaskExisting) {
                        $dailyExisting = Daily::find($idTaskExisting);
                        // if (now() > Carbon::parse($dailyExisting->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfWeek()->addWeek(1)->addDay(2)) {
                        //     return ResponseFormatter::error(null, 'Tidak bisa approved task yang lebih Task request tanggal ' . Carbon::parse($dailyExisting->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->format('d M Y'));
                        // }
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
                        // if (now()->weekOfYear > $dailyExisting->week) {
                        //     return ResponseFormatter::error(null, 'Tidak bisa approved task yang lebih dari 1 week, Task request week ' . $dailyExisting->week);
                        // }
                        $dailyExisting->delete();
                    }
                    break;

                default:
                    //TASK EXISTING
                    $idTaskExistings = explode(',', $requested->todo_request);
                    foreach ($idTaskExistings as $idTaskExisting) {
                        $dailyExisting = Monthly::find($idTaskExisting);
                        // if (now() > Carbon::parse($dailyExisting->date)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addMonth(1)->addDay(4)) {
                        //     return ResponseFormatter::error(null, 'Tidak bisa approved task yang lebih dari 1 bulan, Task request bulan ' . Carbon::parse($dailyExisting->date)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta')));
                        // }
                        $dailyExisting->delete();
                    }
                    break;
            }
            $requested->status = 'APPROVED';
            $requested->approved_by = Auth::id();
            $requested->approved_at = now();
            $requested->save();
            if ($requested->user->id_notif) {
                SendNotif::sendMessage('Request task ' . $requested->jenistodo . ' sudah di setujui oleh ' . Auth::user()->nama_lengkap, array($requested->user->id_notif));
            }
            return ResponseFormatter::success(null, 'Berhasil menyetujui request');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
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
            $requested->approved_by = Auth::id();
            $requested->approved_at = now();
            $requested->save();
            if ($requested->user->id_notif) {
                SendNotif::sendMessage('Request task ' . $requested->jenistodo . ' di tolak oleh ' . Auth::user()->nama_lengkap, array($requested->user->id_notif));
            }
            return ResponseFormatter::success(null, 'Berhasil menolak request');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function cancel(Request $request)
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
            $requested->status = 'CANCELED';
            $requested->save();
            return ResponseFormatter::success(null, 'Berhasil membatalkan request');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }
}
