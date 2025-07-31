<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Models\Monthly;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Helpers\SendNotif;
use App\Http\Controllers\Controller;
use App\Models\Request as ModelsRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;

class MonthlyController extends Controller
{

    public function getmonthly(Request $request)
    {
        try {
            $request->validate([
                'date' => ['required']
            ]);

            $request['date'] = Carbon::parse(strtotime($request->date))
                ->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))
                ->startOfMonth();
            $monthly = Monthly::with('tag.area', 'tag.role', 'tag.divisi', 'add.area', 'add.role', 'add.divisi')
                ->whereDate('date', $request->date)
                ->where('user_id', Auth::id())
                ->orderBy('task')
                ->get();

            return ResponseFormatter::success($monthly, 'berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function change(Request $request)
    {
        try {
            $cekrequest = ModelsRequest::where('user_id', Auth::id())->get();
            if ($cekrequest) {
                foreach ($cekrequest as $requested) {
                    $taskrequest = explode(',', $requested->todo_replace);
                    foreach ($taskrequest as $task) {
                        if ($task == $request->id && $requested->status != 'APPROVED') {
                            return ResponseFormatter::error(null, 'Tidak bisa merubah status monthly yang belum approved oleh atasan');
                        }
                    }
                }
            }
            $monthly = Monthly::findOrfail($request->id);
            $tanggal = $monthly->date;
            // $max = Carbon::parse($tanggal / 1000)
            //     ->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))
            //     ->addMonth(1)->addDay(5)->subSecond(1);
            // if (
            //     now()
            //     >
            //     $max
            // ) {
            //     return ResponseFormatter::error(null, 'Tidak bisa merubah status monthly sudah lebih dari H+5 atau tanggal ' . $max->format('d M Y'));
            // }

            if ($monthly->tipe == 'RESULT') {
                $monthly['value_actual'] = $request->value;
                $monthly['status_result'] = true;
                $monthly['value'] = $monthly['value_actual'] / $monthly['value_plan'] > 1.2 ? 1.2 :  $monthly['value_actual'] / $monthly['value_plan'];
            } else {
                $monthly['status_non'] = !$monthly['status_non'];
                $monthly['value'] = $monthly['status_non'] ? 1 : 0;
            }
            $monthly['date'] = Carbon::parse($tanggal / 1000)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));

            $monthly->save();
            return ResponseFormatter::success(null, 'Berhasil merubah status monthly');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function insert(Request $request)
    {
        try {
            // if (
            //     now()
            //     >
            //     Carbon::parse(strtotime($request->date))
            //     ->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))
            //     ->addDay(5)->subSecond(1) && !$request->is_add
            // ) {
            //     return ResponseFormatter::error(null, 'Tidak bisa menambahkan monthly sudah lebih dari H+5 atau tanggal ' . Carbon::parse(strtotime($request->date))
            //         ->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))
            //         ->addDay(5)->subSecond(1)->format('d M Y'));
            // }

            // if (
            //     now()
            //     >
            //     Carbon::parse(strtotime($request->date))
            //     ->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))
            //     ->addMonth(1)
            //     ->addDay(5)->subSecond(1)
            //     &&
            //     $request->is_add
            // ) {
            //     return ResponseFormatter::error(null, 'Tidak bisa menambahkan monthly sudah lebih dari H+5 atau tanggal ' . Carbon::parse(strtotime($request->date))
            //         ->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))
            //         ->addMonth(1)
            //         ->addDay(5)
            //         ->subSecond(1)
            //         ->format('d M Y'));
            // }

            if ($request->tipe == 'RESULT' && $request->value_plan == null) {
                return ResponseFormatter::error(null, 'Tipe task result harus isi value plan resultnya');
            }

            $data = $request->all();
            $data['user_id'] = Auth::id();
            if ($request->is_add) {
                $data['is_add'] = 1;
            }
            $data['date'] = Carbon::parse(strtotime($request->date))
                ->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));

            if (!$request->add_id) {
                Monthly::create([
                    'user_id' => $data['user_id'],
                    'task' => $data['task'],
                    'date' => $data['date'],
                    'tipe' => $data['tipe'],
                    'value_plan' => $data['value_plan'],
                    'value_actual' => $data['value_actual'] ?? 0,
                    'status_non' => $data['status_non'] ?? 0,
                    'status_result' => $data['status_result'] ?? 0,
                    'value' => $data['value'] ?? 0,
                    'is_add' => $data['is_add'],
                    'is_update' => $data['is_update'],
                ]);
            }

            if ($request->tag) {
                $users = array();
                foreach ($request->tag as $tag) {
                    $data['user_id'] = $tag;
                    $data['tag_id'] = Auth::id();
                    Monthly::create([
                        'user_id' => $data['user_id'],
                        'task' => $data['task'],
                        'date' => $data['date'],
                        'tipe' => $data['tipe'],
                        'value_plan' => $data['value_plan'],
                        'value_actual' => $data['value_actual'] ?? 0,
                        'status_non' => $data['status_non'] ?? 0,
                        'status_result' => $data['status_result'] ?? 0,
                        'value' => $data['value'] ?? 0,
                        'is_add' => $data['is_add'],
                        'is_update' => $data['is_update'],
                        'tag_id' => $data['tag_id'],
                    ]);
                    $user = User::find($tag);
                    if ($user->id_notif) {
                        array_push($users, $user->id_notif);
                    }
                }
                if (count($users) > 0) {
                    SendNotif::sendMessage('Anda menerima monthly tag dari ' . Auth::user()->nama_lengkap . ' pada tanggal ' . date('d M Y', strtotime($request->date)) . ' dengan task ' . $request->task, $users);
                }
            }

            if ($request->add_id) {
                $users = array();
                foreach ($request->add_id as $add_id) {
                    $data['user_id'] = $add_id;
                    $data['add_id'] = Auth::id();
                    Monthly::create([
                        'user_id' => $data['user_id'],
                        'task' => $data['task'],
                        'date' => $data['date'],
                        'tipe' => $data['tipe'],
                        'value_plan' => $data['value_plan'],
                        'value_actual' => $data['value_actual'] ?? 0,
                        'status_non' => $data['status_non'] ?? 0,
                        'status_result' => $data['status_result'] ?? 0,
                        'value' => $data['value'] ?? 0,
                        'is_add' => $data['is_add'],
                        'is_update' => $data['is_update'],
                        'add_id' => $data['add_id'],
                    ]);
                    $user = User::find($add_id);
                    if ($user->id_notif) {
                        array_push($users, $user->id_notif);
                    }
                }
                if (count($users) > 0) {
                    SendNotif::sendMessage('Anda menerima monthly dari ' . Auth::user()->nama_lengkap . ' pada tanggal ' . date('d M Y', strtotime($request->date)) . ' dengan task ' . $request->task, $users);
                }
            }

            return ResponseFormatter::success(null, 'Berhasil menambahkan monthly');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $monthly = Monthly::findOrFail($id);
            $requesteds = ModelsRequest::where('user_id', Auth::id())->get();
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

            // if (
            //     now()
            //     >
            //     Carbon::parse($monthly->date / 1000)
            //     ->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))
            //     ->addDay(5)->subSecond(1)
            // ) {
            //     return ResponseFormatter::error(
            //         null,
            //         'Tidak bisa menghapus monthly sudah lebih dari hari H+5 atau tanggal ' . Carbon::parse($monthly->date / 1000)
            //             ->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))
            //             ->startOfMonth()
            //             ->addDay(5)
            //             ->subSecond(1)
            //             ->format('d M Y')
            //     );
            // }

            if ($monthly->add_id) {
                return ResponseFormatter::error(null, "Tidak bisa dihapus, task ini kiriman dari manager/coor/leader");
            }

            if ($monthly->tag_id) {
                return ResponseFormatter::error(null, "Tidak bisa menghapus tag daily, tag daily hanya bisa di hapus oleh pembuatan tag");
            }

            // $deletes = Monthly::where('task', $monthly->task)->where('tag_id', Auth::id())->whereDate('date', date('y-m-d', $monthly->date / 1000))->get();
            // if ($deletes) {
            //     foreach ($deletes as $delete) {
            //         $delete->delete();
            //     }
            // }

            $monthly->delete();
            return ResponseFormatter::success(null, 'Berhasil menghapus monthly');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $data = $request->all();
            $monthly = monthly::findOrFail($id);
            $tanggal = $monthly->date;
            $max = Carbon::parse($tanggal / 1000)
                ->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(5)->subSecond(1);
            // if (now() > $max) {
            //     return ResponseFormatter::error(
            //         null,
            //         'Tidak bisa merubah monthly sudah lebih dari H+5 atau tanggal ' . Carbon::parse($monthly->date / 1000)
            //             ->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))
            //             ->startOfMonth()
            //             ->addDay(5)
            //             ->subSecond(1)
            //             ->format('d M Y')
            //     );
            // }
            if ($monthly->add_id) {
                return ResponseFormatter::error(null, "Tidak bisa merubah, task ini kiriman dari manager/coor/leader");
            }

            if ($monthly->tag_id) {
                return ResponseFormatter::error(null, "Tidak bisa merubah monthly tag");
            }

            if ($monthly->is_add) {
                return ResponseFormatter::error(null, "Extra task tidak bisa di rubah");
            }

            if ($monthly->tipe == 'RESULT') {
                $data['value'] = 0;
            }
            $data['date'] = Carbon::parse(strtotime($request->date))
                ->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))
                ->startOfMonth();

            $changes = Monthly::where('task', $monthly->task)->where('tag_id', Auth::id())->whereDate('date', date('y-m-d', $monthly->date / 1000))->get();
            if ($changes) {
                foreach ($changes as $change) {
                    $change->update([
                        'task' => $data['task'],
                        'date' => $data['date'],
                        'tipe' => $data['tipe'],
                        'value_plan' => $data['value_plan'],
                        'is_add' => $data['is_add'],
                        'is_update' => $data['is_update'],
                    ]);
                }
            }
            $monthly->update([
                'task' => $data['task'],
                'date' => $data['date'],
                'tipe' => $data['tipe'],
                'value_plan' => $data['value_plan'],
                'is_add' => $data['is_add'],
                'is_update' => $data['is_update'],
            ]);

            return ResponseFormatter::success(null, 'Berhasil merubah monthly');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function copy(Request $request)
    {
        try {
            // if (now() > Carbon::parse($request->tomonth)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(5)->subSecond(1)) {
            //     return ResponseFormatter::error(null, 'Tidak bisa menduplikat monthly ' . Carbon::parse($request->tomonth)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->format('M') . ' sudah lebih dari ' . Carbon::parse($request->tomonth)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->addDay(5)->subSecond(1)->format('d M Y'));
            // }
            $monthlys = Monthly::where('date', date('y-m-d', strtotime($request->frommonth)))
                ->where('user_id', Auth::id())
                ->where('is_update', 0)
                ->where('is_add', 0)
                // ->where('tag_id', null)
                // ->where('add_id', null)
                ->get()
                ->toArray();
            if (!$monthlys) {
                return ResponseFormatter::error(null, 'Tidak bisa menduplikat monthly dari monthly yang kosong');
            }
            foreach ($monthlys as $monthly) {
                unset($monthly['id']);
                if ($monthly['tipe'] == 'NON') {
                    $monthly['status_non'] = 0;
                } else {
                    $monthly['value_actual'] = 0;
                    $monthly['status_result'] = 0;
                }
                $monthly['value'] = 0;
                $monthly['date'] = Carbon::parse($request->tomonth)->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'));

                Monthly::create($monthly);
            }
            return ResponseFormatter::success(null, 'Berhasil menduplikat monthly');
        } catch (Exception $e) {
            error_log($e->getMessage());
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }
}
