<?php

namespace App\Http\Controllers\API;

use App\Helpers\ConvertDate;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Daily;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SendNotif;
use App\Models\Request as ModelsRequest;

class DailyController extends Controller
{
    public function fetch(Request $request)
    {
        try {
            $request->validate([
                'date' => ['required'],
            ]);
            $raw = Daily::with('tag.area', 'tag.role', 'tag.divisi', 'add.area', 'add.role', 'add.divisi')
                ->whereDate('date', $request->date)
                ->where('user_id', Auth::id())
                ->orderBy('time')
                ->get();

            $dailys = $raw->sortBy('time')->values()->all();
            return ResponseFormatter::success($dailys, 'berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function insert(Request $request)
    {
        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();
            $data['date'] = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
            $date = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
            if (!$request->isplan) {
                // if (!Daily::whereDate('date', Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->format('Y-m-d'))->where('user_id', auth()->id())->get()) {
                //     return ResponseFormatter::error(null, "Tidak bisa menambahkan daily extra karena hari ini anda tidak ada plan");
                // }
                $data['isplan'] = false;
                $data['ontime'] = true;
                if (
                    $date->diffInDays(now()) > 0
                    &&
                    now()->subDay(1) > $date->addHour(10)
                ) {
                    $data['status'] = true;
                    $data['ontime'] = 0.5;
                }
                // if (
                //     $date->diffInDays(now()) > 0
                //     &&
                //     now() > $date->addDay(2)
                // ) {
                //     return ResponseFormatter::error(null, "Tidak bisa menambahkan daily extra, sudah lebih dari H+2");
                // }
            } else {
                // if (
                //     Auth::user()->area_id == 2
                //     && now() > $date->startOfWeek()->addDay(1)->addHour(10)
                // ) {
                    //     return ResponseFormatter::error(null, "Tidak bisa menambahkan daily, sudah lebih dari hari hari selasa Jam 10:00");
                // }

                // $date2 = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));

                // if (
                //     Auth::user()->area_id != 2
                //     && now() > $date2->startOfWeek()->addHour(17)
                // ) {
                    //     return ResponseFormatter::error(null, "Tidak bisa menambahkan daily, sudah lebih dari hari hari senin Jam 17:00");
                // }
            }


            if ($request->isplan) {
                $data['time'] = date('H:i', strtotime($request->time));
            }

            // if ($data['tipe'] == 'RESULT') {
            //     $data['value_actual'] = 0;
            //     $data['status_result'] = 0;
            // }

            if (!$request->add_id) {
                Daily::create([
                    'task' => $data['task'],
                    'date' => $data['date'],
                    'time' => $data['time'],
                    'isplan' => $data['isplan'],
                    'isupdate' => $data['isupdate'] ?? false,
                    'value_plan' => $data['value_plan'] ?? 0,
                    'value_actual' => $data['value_actual'] ?? 0,
                    'tipe' => $data['tipe'] ?? 'NON',
                    'user_id' => $data['user_id'],
                    'status' => $data['status'] ?? 0,
                    'ontime' => $data['ontime'] ?? 1,
                ]);
            }

            if ($request->tag) {
                $users = array();
                foreach ($request->tag as $tag) {
                    $data['user_id'] = $tag;
                    $data['tag_id'] = Auth::id();
                    Daily::create([
                        'task' => $data['task'],
                        'date' => $data['date'],
                        'time' => $data['time'],
                        'isplan' => $data['isplan'],
                        'isupdate' => $data['isupdate'],
                        'value_plan' => $data['value_plan'],
                        'value_actual' => $data['value_actual'] ?? 0,
                        'tipe' => $data['tipe'],
                        'user_id' => $data['user_id'],
                        'status' => $data['status'] ?? 0,
                        'ontime' => $data['ontime'] ?? 1,
                        'tag_id' => $data['tag_id'],
                    ]);
                    $user = User::find($tag);
                    if ($user->id_notif) {
                        array_push($users, $user->id_notif);
                    }
                }
                if (count($users) > 0) {
                    SendNotif::sendMessage('Anda menerima daily tag dari ' . Auth::user()->nama_lengkap . ' pada tanggal ' . date('d M Y', strtotime($request->date)) . ' dengan task ' . $request->task, $users);
                }
            }

            if ($request->add_id) {
                $users = array();
                foreach ($request->add_id as $add_id) {
                    $data['user_id'] = $add_id;
                    $data['add_id'] = Auth::id();
                    Daily::create([
                        'task' => $data['task'],
                        'date' => $data['date'],
                        'time' => $data['time'],
                        'isplan' => $data['isplan'],
                        'isupdate' => $data['isupdate'],
                        'value_plan' => $data['value_plan'],
                        'value_actual' => $data['value_actual'] ?? 0,
                        'tipe' => $data['tipe'],
                        'user_id' => $data['user_id'],
                        'status' => $data['status'] ?? 0,
                        'ontime' => $data['ontime'] ?? 1,
                        'add_id' => $data['add_id'],
                    ]);
                    $user = User::find($add_id);
                    if ($user->id_notif) {
                        array_push($users, $user->id_notif);
                    }
                }
                if (count($users) > 0) {
                    SendNotif::sendMessage('Anda menerima daily dari ' . Auth::user()->nama_lengkap . ' pada tanggal ' . date('d M Y', strtotime($request->date)) . ' dengan task ' . $request->task, $users);
                }
            }
            return ResponseFormatter::success(null, 'Berhasil menambahkan daily');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $daily = Daily::findOrFail($id);
            $requesteds = ModelsRequest::where('user_id', Auth::id())->where('jenistodo', 'Daily')->get();
            foreach ($requesteds as $requested) {
                $idTaskExistings = explode(',', $requested->todo_request);
                foreach ($idTaskExistings as $idTaskExisting) {
                    if ($id == $idTaskExisting && $requested->status == 'PENDING') {
                        return ResponseFormatter::error(null, "Tidak bisa merubah, task ini ada di pengajuan request task");
                    }
                }

                $idTaskReplaces = explode(',', $requested->todo_replace);
                foreach ($idTaskReplaces as $idTaskReplace) {
                    if ($id == $idTaskReplace && $requested->status == 'PENDING') {
                        return ResponseFormatter::error(null, "Tidak bisa merubah, task ini ada di pengajuan request task");
                    }
                }
            }
            $data = $request->all();
            $data['date'] = Carbon::parse(strtotime($request->date))->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
            $data['time'] = date('H:i', strtotime($request->time));
            $daily = Daily::findOrFail($id);

            if ($daily->add_id) {
                return ResponseFormatter::error(null, "Tidak bisa merubah, task ini kiriman dari manager/coor/leader");
            }

            if ($daily->tag_id) {
                return ResponseFormatter::error(null, "Tidak bisa merubah daily tag");
            }

            if (!$daily->isplan) {
                return ResponseFormatter::error(null, "Extra task tidak bisa di rubah");
            }
            // if (Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->weekOfYear <= now()->weekOfYear) {
            //     if (Auth::user()->area_id == 2 && now() > Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfWeek()->addDay(1)->addHour(10)) {
            //         return ResponseFormatter::error(null, "Tidak bisa merubah daily di week yang sudah berjalan dan lebih dari hari selasa " . now()->startOfDay()->startOfWeek()->addDay(1)->addHour(10)->format('d M y') . " jam 10.00");
            //     } else if (Auth::user()->area_id != 2 && now() > Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfWeek()->addHour(17)) {
            //         return ResponseFormatter::error(null, "Tidak bisa merubah daily di week yang sudah berjalan dan lebih dari hari senin " . now()->startOfDay()->startOfWeek()->addHour(10)->format('d M y') . " jam 17.00");
            //     }
            // }
            $changes = Daily::where('task', $daily->task)->where('tag_id', Auth::id())->whereDate('date', date('y-m-d', $daily->date / 1000))->get();
            if ($changes) {
                foreach ($changes as $change) {
                    $change->update([
                        'task' => $data['task'],
                        'date' => $data['date'],
                        'time' => $data['time'],
                        'isplan' => $data['isplan'],
                        'isupdate' => $data['isupdate'] ?? false,
                        'value_plan' => $data['value_plan'] ?? null,
                        'tipe' => $data['tipe'] ?? 'NON',
                    ]);
                }
            }
            $daily->update([
                'task' => $data['task'],
                'date' => $data['date'],
                'time' => $data['time'],
                'isplan' => $data['isplan'],
                'isupdate' => $data['isupdate'] ?? false,
                'value_plan' => $data['value_plan'] ?? null,
                'tipe' => $data['tipe'] ?? 'NON',
            ]);
            return ResponseFormatter::success(null, 'Berhasil merubah daily');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $daily = Daily::findOrFail($id);
            $requesteds = ModelsRequest::where('user_id', Auth::id())->where('jenistodo', 'Daily')->get();
            foreach ($requesteds as $requested) {
                $idTaskExistings = explode(',', $requested->todo_request);
                foreach ($idTaskExistings as $idTaskExisting) {
                    if ($id == $idTaskExisting && $requested->status == 'PENDING') {
                        return ResponseFormatter::error(null, "Tidak bisa menghapus, task ini ada di pengajuan request task");
                    }
                }

                $idTaskReplaces = explode(',', $requested->todo_replace);
                foreach ($idTaskReplaces as $idTaskReplace) {
                    if ($id == $idTaskReplace && $requested->status == 'PENDING') {
                        return ResponseFormatter::error(null, "Tidak bisa menghapus, task ini ada di pengajuan request task");
                    }
                }
            }

            if ($daily->add_id) {
                return ResponseFormatter::error(null, "Tidak bisa dihapus, task ini kiriman dari manager/coor/leader");
            }

            if ($daily->tag_id) {
                return ResponseFormatter::error(null, "Tidak bisa menghapus tag daily, tag daily hanya bisa di hapus oleh pembuatan tag");
            }

            // if (!$daily->isplan && now() > Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(2)) {
            //     return ResponseFormatter::error(null, "Extra task tidak bisa di hapus lebih dari H+2");
            // }
            // if ($daily->isplan && Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->weekOfYear <= now()->weekOfYear && !$daily->tag_id) {
            //     if (Auth::user()->area_id == 2 && now() > Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfWeek()->addDay(1)->addHour(10)) {
            //         return ResponseFormatter::error(null, "Tidak bisa menghapus daily di week yang sudah berjalan dan lebih dari selasa jam 10.00");
            //     } else if (Auth::user()->area_id != 2 && now() > Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfWeek()->addHour(17)) {
            //         return ResponseFormatter::error(null, "Tidak bisa menghapus daily di week yang sudah berjalan dan lebih dari senin jam 17.00");
            //     }
            // }

            $deletes = Daily::where('task', $daily->task)->where('tag_id', Auth::id())->whereDate('date', date('y-m-d', $daily->date / 1000))->get();
            if ($deletes) {
                foreach ($deletes as $delete) {
                    $delete->delete();
                }
            }
            $daily->delete();
            return ResponseFormatter::success(null, 'Berhasil menghapus daily');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    // KALAU PAKE DND VER BARU GANTI PARAMETER JADI Request $request
    public function change($id)
    {
        try {
            $daily = Daily::findOrFail($id);
            $requesteds = ModelsRequest::where('user_id', Auth::id())->where('jenistodo', 'Daily')->get();
            foreach ($requesteds as $requested) {
                $idTaskExistings = explode(',', $requested->todo_request);
                foreach ($idTaskExistings as $idTaskExisting) {
                    if ($id == $idTaskExisting && $requested->status == 'PENDING') {
                        return ResponseFormatter::error(null, "Tidak bisa merubah status, task ini ada di pengajuan request task");
                    }
                }

                $idTaskReplaces = explode(',', $requested->todo_replace);
                foreach ($idTaskReplaces as $idTaskReplace) {
                    if ($id == $idTaskReplace && $requested->status == 'PENDING') {
                        return ResponseFormatter::error(null, "Tidak bisa merubah status, task ini ada di pengajuan request task");
                    }
                }
            }
            // if (
            //     Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->weekOfYear
            //     <=
            //     now()->weekOfYear
            //     &&
            //     now()
            //     >
            //     Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(3)
            // ) {
            //     return ResponseFormatter::error(null, "Tidak bisa merubah status sudah lebih dari H+2 dari daily");
            // }
            // $H = Carbon::parse($daily->date / 1000);
            // if ($H > now()) {
            //     return ResponseFormatter::error(null, "Tidak bisa merubah status yang lebih dari hari ini tanggal " . now()->format('d M y'));
            // }

            $daily['status'] ? $daily['ontime'] = 0  : $daily['ontime'] = 1.0;
            if (
                now()
                >
                Carbon::parse($daily->date / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(1)->addHour(10)
            ) {
                $daily['status'] ? $daily['ontime'] = 0 : $daily['ontime'] = 0.5;
            }
            Carbon::now()->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->subWeek(2);

            // if ($daily->tipe == 'RESULT') {
            //     $daily['value_actual'] = $request->value;
            //     $daily['status_result'] = true;
            //     $daily['value'] = $daily['value_actual'] / $daily['value_plan'] > 1.2 ? 1.2 : $daily['value_actual'] / $daily['value_plan'];
            // } else {
                $daily['status'] = !$daily['status'];
            //     $daily['value'] = $daily['status'] ? 1 : 0;
            // }

            $daily->save();
            return ResponseFormatter::success(null, 'Berhasil merubah status daily');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function getTagUser(Request $request)
    {
        try {
            $users = User::whereHas('daily', function ($query) use ($request) {
                $query->where('task', $request->task)
                    ->where('tag_id', Auth::id());
            })->get();
            return ResponseFormatter::success($users, 'Berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function copy(Request $request)
    {
        try {
            $monday = ConvertDate::getMondayOrSaturday($request->year, $request->week, true);
            $sunday = ConvertDate::getMondayOrSaturday($request->year, $request->week, false);
            // $mondayTo = ConvertDate::getMondayOrSaturday($request->year, $request->week, true);
            // if (Auth::user()->area_id == 2 && now() > $mondayTo->addWeek($request->addweek)->addDay(1)->addHour(10)) {
            //     return ResponseFormatter::error(null, 'Tidak bisa menduplikat daily week ' . $request->week . ' sudah lebih dari week ' . now()->weekOfYear . ' hari selasa jam 10:00');
            // } else if (Auth::user()->area_id != 2 && now() > $mondayTo->addWeek($request->addweek)->addHour(17)) {
            //     return ResponseFormatter::error(null, 'Tidak bisa menduplikat daily week ' . $request->week . ' sudah lebih dari week ' . now()->weekOfYear . ' hari senin jam 17:00');
            // }
            $dailys = Daily::where('user_id', Auth::id())
                ->where('isplan', 1)
                // ->where('tag_id', null)
                // ->where('add_id', null)
                ->where('isupdate', 0)
                ->whereBetween('date', [$monday->format('y-m-d'), $sunday->format('y-m-d')])
                ->get()
                ->toArray();
            foreach ($dailys as $daily) {
                $taged = Daily::where('task', $daily['task'])
                    ->whereDate('date', date('y-m-d', $daily['date'] / 1000))
                    ->where('tag_id', Auth::id())
                    ->get()
                    ->toArray();
                if ($taged && auth()->id() != 2) {
                    foreach ($taged as $tag) {
                        $user = User::find($tag['user_id']);
                        if ($user) {
                            $tag['date'] = Carbon::parse($tag['date'] / 1000)->addWeek($request->addweek)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
                            $tag['time'] = date('H:i', strtotime($tag['time']));
                            $tag['ontime'] = 0;
                            $tag['status'] = 0;
                            $tag['created_at'] = now();
                            $tag['updated_at'] = now();
                            unset($tag['id']);
                            Daily::create($tag);
                            if ($user->id_notif) {
                                SendNotif::sendMessage('Anda menerima daily tag dari ' . Auth::user()->nama_lengkap . ' pada tanggal ' . date('d M Y', strtotime($tag['date'])) . ' dengan task ' . $tag['task'], array(User::find($tag['user_id'])->id_notif));
                            }
                        }
                    }
                }

                $daily['date'] = Carbon::parse($daily['date'] / 1000)->addWeek($request->addweek)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));
                $daily['time'] = date('H:i', strtotime($daily['time']));
                $daily['ontime'] = 0;
                $daily['status'] = 0;
                $daily['created_at'] = now();
                $daily['updated_at'] = now();
                unset($daily['id']);
                Daily::create($daily);
            }
            return ResponseFormatter::success(null, 'Berhasil menduplikat daily');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function fetchweek(Request $request)
    {
        try {
            $userId = Auth::id();
            $week = $request->week;
            $year = $request->year;

            //GET ALL DAILY MONDAY TO SUNDAY
            $dailys = array();
            for ($i = 0; $i < 7; $i++) {
                $monday = ConvertDate::getMondayOrSaturday($year, $week, true);
                $i == 0 ?
                    $daily = Daily::where('date', $monday)
                    ->where('user_id', $userId)
                    ->where('isplan', 1)
                    // ->where('tag_id', null)
                    // ->where('add_id', null)
                    ->where('isupdate', 0)
                    ->orderBy('time')
                    ->get() :
                    $daily = Daily::where('date', $monday->addDay($i))
                    ->where('user_id', $userId)
                    ->where('isplan', 1)
                    // ->where('tag_id', null)
                    // ->where('add_id', null)
                    ->where('isupdate', 0)
                    ->orderBy('time')
                    ->get();

                if (count($daily) > 0) {
                    array_push($dailys, $daily);
                }
            }
            return ResponseFormatter::success($dailys, 'Berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }
}
