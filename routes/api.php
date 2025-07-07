<?php

use App\Helpers\ConvertDate;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DailyController;
use App\Http\Controllers\API\ResultController;
use App\Http\Controllers\API\WeeklyController;
use App\Http\Controllers\API\MonthlyController;
use App\Http\Controllers\API\RequestTaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/user/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    ##DAILY
    Route::get('/daily', [DailyController::class, 'fetch']);
    Route::post('/daily', [DailyController::class, 'insert']);
    Route::get('/daily/fetchweek',[DailyController::class,'fetchweek']);
    Route::post('/daily/copy',[DailyController::class,'copy']);
    Route::get('/daily/delete/{id}', [DailyController::class, 'delete']);
    Route::get('/daily/change/{id}', [DailyController::class, 'change']);
    // KALAU GANTI VER DND PAKE POST:
    // Route::post('/daily/change', [DailyController::class, 'change']);
    Route::post('/daily/edit/{id}', [DailyController::class, 'edit']);
    Route::get('/daily/getuser', [DailyController::class, 'getTagUser']);

    Route::get('/user', [UserController::class, 'fetch']);
    Route::get('/user/tag', [UserController::class, 'tag']);
    Route::get('/user/team',[UserController::class,'team']);

    ##WEEKLY
    Route::get('/weekly', [WeeklyController::class, 'fetch']);
    Route::post('/weekly', [WeeklyController::class, 'insert']);
    Route::post('/weekly/copy', [WeeklyController::class, 'copy']);
    Route::post('/weekly/change', [WeeklyController::class, 'change']);
    Route::post('/weekly/edit/{id}', [WeeklyController::class, 'edit']);
    Route::get('/weekly/delete/{id}', [WeeklyController::class, 'delete']);

    ##MONTHLY
    Route::get('/monthly', [MonthlyController::class, 'getmonthly']);
    Route::post('/monthly', [MonthlyController::class, 'insert']);
    Route::post('/monthly', [MonthlyController::class, 'insert']);
    Route::post('/monthly/copy', [MonthlyController::class, 'copy']);
    Route::post('/monthly/change', [MonthlyController::class, 'change']);
    Route::post('/monthly/edit/{id}', [MonthlyController::class, 'edit']);
    Route::get('/monthly/delete/{id}', [MonthlyController::class, 'delete']);

    ##USER
    Route::post('/user/logout', [UserController::class, 'logout']);
    Route::post('/user/changepicture', [UserController::class, 'profilepicture']);

    ##RESULT
    Route::get('/result', [ResultController::class, 'result']);
    Route::get('/result/{id}/', [ResultController::class, 'resultbyuser']);


    ##REQUEST
    Route::get('/request', [RequestTaskController::class, 'fetchuser']);
    Route::post('/request', [RequestTaskController::class, 'submit']);
    Route::get('/request/approve', [RequestTaskController::class, 'fetchapprove']);
    Route::post('/request/approve', [RequestTaskController::class, 'approve']);
    Route::post('/request/reject', [RequestTaskController::class, 'reject']);
    Route::post('/request/cancel', [RequestTaskController::class, 'cancel']);

});

Route::get('/date', function (Request $request) {
    $monday = ConvertDate::getMondayOrSaturday($request->year, $request->week, true);
    $sunday = ConvertDate::getMondayOrSaturday($request->year, $request->week, false);
    return ResponseFormatter::success([
        'monday' => $monday->getPreciseTimestamp(3),
        'sunday' => $sunday->subSecond(1)->getPreciseTimestamp(3)
    ], 'berhasil');
});
Route::post('/testsubmit', [RequestTaskController::class, 'submit']);
Route::get('/tes', function (Request $request) {
    dd(now());

    $week_number = $request->minggu;
    $year = $request->tahun;
    $monday = ConvertDate::getMondayOrSaturday($year, $week_number, true);
    dd(now() > $monday->addDay(8)->addHour(10));
    // $sunday = ConvertDate::getMondayOrSaturday($year, $week_number, false);
    // $endOfMonth = ConvertDate::getEndOfMonth($year, $week_number);

    // if ($monday->month == $sunday->month) {
    //     if ($sunday->diffInDays($endOfMonth) > 3) {
    //         echo "INI UNTUK WEEKLY BUKAN AKHIR BULAN KETIKA SENIN DAN MINGGU DALAM BULAN YANG SAMA";
    //         echo "</br>";
    //         echo "HANYA PENARIKAN TDL DAN WO (MO BELUM WAKTUNYA)";
    //         echo "</br>";
    //         echo "AMBIL WO MINGGU KE " . $week_number;
    //         echo "</br>";
    //         echo "AMBIL TDL DARI " . $monday . " SAMPAI " . $sunday;
    //     } else {
    //         ##kalo seminggu setelah sabtu bukan akhir bulan minggu ini adalah tarik data bulan ini
    //         echo "INI UNTUK WEEKLY AKHIR BULAN AWAL";
    //         echo "</br>";
    //         echo "INI PENARIK SEMUA (MO,WO & TDL)";
    //         echo "</br>";
    //         echo "AMBIL MO PADA BULAN KE " . $monday->month;
    //         echo "</br>";
    //         echo "AMBIL WO MINGGU KE " . $week_number;
    //         echo "</br>";
    //         echo "AMBIL TDL DARI " . $monday . " SAMPAI " . $sunday;
    //         echo "</br>";
    //     }
    // } else {
    //     if ($monday->endOfMonth()->diffInDays($sunday) < 4) {
    //         ##kalo senin dan sabtu berbeda bulan maka jika hari terakhir dari bulan ini kurang dari sama dengan 3 dari hari sabtu ini maka tarik data bulan yang masuk dalam senin
    //         echo "INI UNTUK WEEKLY AKHIR BULAN AKHIR";
    //         echo "</br>";
    //         echo "INI PENARIK SEMUA (MO,WO & TDL)";
    //         echo "</br>";
    //         echo "AMBIL MO PADA BULAN KE " . $monday->month;
    //         echo "</br>";
    //         echo "AMBIL WO MINGGU KE " . $week_number;
    //         echo "</br>";
    //         echo "AMBIL TDL DARI " . $monday->startOfWeek() . " SAMPAI " . $sunday;
    //         echo "</br>";
    //     } else {
    //         echo "INI UNTUK WEEKLY BUKAN AKHIR BULAN KETIKA SENIN DAN MINGGU DALAM BULAN YANG BERBEDA";
    //         echo "</br>";
    //         echo "HANYA PENARIKAN TDL DAN WO (MO BELUM WAKTUNYA)";
    //         echo "</br>";
    //         echo "AMBIL WO MINGGU KE " . $week_number;
    //         echo "</br>";
    //         echo "AMBIL TDL DARI " . $monday->startOfWeek() . " SAMPAI " . $sunday;
    //     }
    // }
});
