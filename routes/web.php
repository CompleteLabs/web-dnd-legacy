<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeReviewController;
use App\Http\Controllers\KpiCategoryController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\KpiDashboardController;
use App\Http\Controllers\KpiDescriptionController;
use App\Http\Controllers\KpiTypeController;
use App\Http\Controllers\MonthlyController;
use App\Http\Controllers\OverOpenController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WeeklyController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Models\Attendance;
use App\Models\KpiDescription;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('admin');
})->name('/');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(
    function () {
        ##DASHBOARD
        Route::get('dashboard', [DashboardController::class, 'index']);
        ##LOGOUT
        Route::post('/logout', [AuthController::class, 'logout']);

        ##DAILY
        Route::get('/daily', [DailyController::class, 'indexUser'])->name('dailyuser');
        Route::post('/daily', [DailyController::class, 'store']);
        Route::post('/daily/change', [DailyController::class, 'change']);
        Route::post('/daily/delete', [DailyController::class, 'destroy']);
        Route::post('/daily/edit/', [DailyController::class, 'update']);
        Route::get('/daily/edit/{id}', [DailyController::class, 'edituser']);
        Route::get('/daily/template', [DailyController::class, 'templateUser']);
        Route::post('/daily/import', [DailyController::class, 'importDailyUser']);
        ##WEEKLY
        Route::get('/weekly', [WeeklyController::class, 'indexUser']);
        Route::post('/weekly', [WeeklyController::class, 'store']);
        Route::get('/weekly/template', [WeeklyController::class, 'templateUser']);
        Route::post('/weekly/import', [WeeklyController::class, 'importWeeklyUser']);
        Route::post('/weekly/change', [WeeklyController::class, 'change']);
        Route::get('/weekly/change/result/{id}', [WeeklyController::class, 'showresult']);
        Route::post('/weekly/delete', [WeeklyController::class, 'destroy']);
        Route::post('/weekly/update/{id}', [WeeklyController::class, 'update']);
        Route::get('/weekly/edit/{id}', [WeeklyController::class, 'edit']);
        ##MONTHLY
        Route::get('/monthly/template', [MonthlyController::class, 'templateUser']);
        Route::post('/monthly/import', [MonthlyController::class, 'importMonthlyUser']);
        Route::get('/monthly', [MonthlyController::class, 'indexUser']);
        Route::post('/monthly', [MonthlyController::class, 'store']);
        Route::post('/monthly/change', [MonthlyController::class, 'change']);
        Route::get('/monthly/change/result/{id}', [MonthlyController::class, 'showresult']);
        Route::post('/monthly/delete', [MonthlyController::class, 'destroy']);
        Route::post('/monthly/update/{id}', [MonthlyController::class, 'update']);
        Route::get('/monthly/edit/{id}', [MonthlyController::class, 'edit']);
        ##REPORT
        Route::get('/result', [ReportController::class, 'indexuser']);
        ##REQUEST CHANGE TASK
        Route::get('/request', [DashboardController::class, 'request']);
        Route::post('/request', [DashboardController::class, 'submit']);
        Route::get('/request/create', [DashboardController::class, 'requestcreate']);
        Route::get('/request/cancel/{id}', [DashboardController::class, 'cancel']);

        ##APPROVAL
        Route::get('/req', [DashboardController::class, 'reqindex']);
        Route::get('/req/exist', [DashboardController::class, 'exist']);
        Route::get('/req/replace', [DashboardController::class, 'replace']);
        Route::get('/req/approve/{id}', [DashboardController::class, 'approve']);
        Route::get('/req/reject/{id}', [DashboardController::class, 'reject']);

        ##TEAMS
        #DAILY
        Route::get('/teams/daily', [DailyController::class, 'indexTeamsDaily']);
        Route::get('/teams/daily/edit/{daily}', [DailyController::class, 'teamsDailyEdit']);
        Route::post('/teams/daily/edit/{daily}', [DailyController::class, 'teamsDailyUpdate']);
        Route::post('/teams/sendDaily', [DailyController::class, 'sendDaily']);

        //keluarin dari middleware isAdmin
        Route::post('admin/daily/import', [DailyController::class, 'importDailyUser']);

        #WEEKLY
        Route::get('/teams/weekly', [WeeklyController::class, 'indexTeamsWeekly']);
        Route::get('/teams/weekly/edit/{weekly}', [WeeklyController::class, 'teamsWeeklyEdit']);
        Route::post('/teams/weekly/edit/{weekly}', [WeeklyController::class, 'teamsWeeklyUpdate']);
        Route::post('/teams/sendWeekly', [WeeklyController::class, 'sendWeekly']);

        //keluarin dari middleware isAdmin
        Route::post('admin/weekly/import', [WeeklyController::class, 'importWeeklyUser']);

        // Route::get('/teams/weekly/change/result/{id}', [WeeklyController::class, 'teamsShowResult']);

        ##KPI DASHBOARD
        Route::get('dash-kpi', [KpiDashboardController::class, 'indexKpi']);
        Route::post('/dash/change', [KpiDashboardController::class, 'changeStatus']);

        #DAILY
        Route::get('dash-daily', [KpiDashboardController::class, 'indexDaily']);
        #WEEKLY
        Route::get('dash-weekly', [KpiDashboardController::class, 'indexWeekly']);
        #MONTHLY
        Route::get('dash-monthly', [KpiDashboardController::class, 'indexMonthly']);

        #KPI
        Route::get('kpi', [KpiController::class, 'index']);
        Route::get('/kpi/create', [KpiController::class, 'create']);
        Route::post('kpi', [KpiController::class, 'store']);
        Route::get('/kpi/{kpi}/show', [KpiController::class, 'show']);
        Route::get('/kpi/{kpi}/edit', [KpiController::class, 'edit']);
        Route::get('/kpi/{kpiId}/kpiDetail', [KpiController::class, 'getKpiDetail']);
        Route::post('/kpi/{kpi}/update', [KpiController::class, 'update']);
        Route::get('/kpi/{kpi}/delete', [KpiController::class, 'destroy']);
        Route::post('/kpi/import', [KpiController::class, 'import']);
        Route::get('/kpi/exportMonthly', [KpiController::class, 'exportMonthly']);
        Route::post('/kpi/export', [KpiController::class, 'exportPerDivision']);
        Route::post('/kpi/copy', [KpiController::class, 'copyKpi']);

        Route::get('/teams', [EmployeeReviewController::class, 'myTeam']);
        Route::get('/attendance/download', [EmployeeReviewController::class, 'downloadTeams']);
        Route::get('attendance/export', [AttendanceController::class, 'exportAttendance'])->name('attendance.export');

        ##ROUTE ATTENDANCE
        Route::resource('attendance', AttendanceController::class);
        Route::post('attendance/import', [AttendanceController::class, 'import'])->name('attendance.import');
        Route::get('attendance/download', [AttendanceController::class, 'download']);

        ##ROUTE EMPLOYEE REVIEW
        Route::get('employee_reviews/download', [EmployeeReviewController::class, 'download']);
        Route::post('employee_reviews/import', [EmployeeReviewController::class, 'import']);
        Route::resource('employee_reviews', EmployeeReviewController::class);

        ##ROUTE LEADERBOARD
        Route::get('leaderboard', [KpiDashboardController::class, 'leaderboard']);
        Route::get('leaderboard/export', [KpiDashboardController::class, 'export'])->name('leaderboard.export');

        ##Route Extra Task
        Route::post('extra-task/store', [KpiController::class, 'storeExtraTask']);
        Route::delete('extra-task/{id}', [KpiController::class, 'destroyExtraTask'])->name('extra-task.destroy');


        ##ROUTE ADMIN
        Route::middleware('isAdmin')->group(function () {
            ##USER
            Route::get('user', [UserController::class, 'index']);
            Route::post('user', [UserController::class, 'store']);
            Route::post('user/update/{id}', [UserController::class, 'update']);
            Route::get('user/delete/{id}', [UserController::class, 'destroy']);
            Route::get('user/active/{id}', [UserController::class, 'active']);
            Route::get('user/create', [UserController::class, 'create']);
            Route::get('user/export', [UserController::class, 'export']);
            Route::get('user/template', [UserController::class, 'template']);
            Route::post('user/import', [UserController::class, 'import']);
            Route::get('user/{id}', [UserController::class, 'edit']);

            ##DAILY ADMIN
            Route::get('admin/daily', [DailyController::class, 'index']);
            Route::post('admin/daily/export', [DailyController::class, 'exportAdmin']);
            Route::get('admin/daily/{id}', [DailyController::class, 'edit']);
            Route::post('admin/daily/{id}', [DailyController::class, 'update']);
            ##WEEKLY ADMIN
            Route::get('admin/weekly', [WeeklyController::class, 'index']);
            Route::post('admin/weekly/export', [WeeklyController::class, 'exportAdmin']);
            ##MONTLY ADMIN
            Route::get('admin/monthly', [MonthlyController::class, 'index']);
            Route::post('admin/monthly/export', [MonthlyController::class, 'exportAdmin']);
            Route::post('admin/monthly/report', [MonthlyController::class, 'reportAdmin']);

            ##REPORT
            Route::get('/admin/report', [ReportController::class, 'index']);
            Route::post('/admin/report/export', [ReportController::class, 'exportIndividu']);

            ##OVEROPEN
            Route::get('/admin/overopen', [OverOpenController::class, 'index']);
            Route::post('/admin/overopen', [OverOpenController::class, 'store']);
            Route::get('/admin/overopen/create', [OverOpenController::class, 'create']);
            Route::post('/admin/overopen/export', [OverOpenController::class, 'show']);
            Route::post('/admin/overopen/delete', [OverOpenController::class, 'destroy']);


            ##SETTING
            //ROLE
            Route::get('setting/role', [SettingController::class, 'role']);
            Route::post('setting/role', [SettingController::class, 'roleadd']);
            // Route::get('setting/role/{id}', [SettingController::class, 'roleedit']);

            //DIVISI
            Route::get('setting/divisi', [SettingController::class, 'divisi']);
            Route::post('setting/divisi', [SettingController::class, 'divadd']);
            // Route::get('setting/divisi/{id}', [SettingController::class, 'divedit']);

            //AREA
            Route::get('setting/area', [SettingController::class, 'area']);
            Route::post('setting/area', [SettingController::class, 'areaadd']);
            // Route::get('setting/area/{id}', [SettingController::class, 'areaedit']);

            //TASK CATEGORY
            Route::get('setting/taskcategory', [SettingController::class, 'taskcategory']);
            Route::post('setting/taskcategory', [SettingController::class, 'taskcategoryadd']);

            //TASK STATUS
            Route::get('setting/taskstatus', [SettingController::class, 'taskstatus']);
            Route::post('setting/taskstatus', [SettingController::class, 'taskstatusadd']);

            ##BROADCAST
            Route::post('broadcast', [DashboardController::class, 'broadcast']);

            ##KPI
            #KPI CATEGORY
            Route::get('kpicategory', [KpiCategoryController::class, 'index']);
            Route::post('kpicategory', [KpiCategoryController::class, 'store']);
            Route::get('/kpicategory/{kpiCategory}/edit', [KpiCategoryController::class, 'edit']);
            Route::post('/kpicategory/{kpiCategory}/update', [KpiCategoryController::class, 'update']);
            Route::get('/kpicategory/{kpiCategory}/delete', [KpiCategoryController::class, 'destroy']);

            #KPI DESCRIPTION
            Route::get('kpidescription', [KpiDescriptionController::class, 'index']);
            Route::post('kpidescription', [KpiDescriptionController::class, 'store']);
            Route::get('/kpidescription/{kpiDescription}/edit', [KpiDescriptionController::class, 'edit']);
            Route::post('/kpidescription/{kpiDescription}/update', [KpiDescriptionController::class, 'update']);
            Route::get('/kpidescription/{kpiDescription}/delete', [KpiDescriptionController::class, 'destroy']);

            #KPI TYPE
            Route::get('kpitype', [KpiTypeController::class, 'index']);
            Route::post('kpitype', [KpiTypeController::class, 'store']);
            Route::get('/kpitype/{kpiType}/edit', [KpiTypeController::class, 'edit']);
            Route::post('/kpitype/{kpiType}/update', [KpiTypeController::class, 'update']);
            Route::get('/kpitype/{kpiType}/delete', [KpiTypeController::class, 'destroy']);

            #POSITION
            Route::get('position', [PositionController::class, 'index']);
            Route::post('position', [PositionController::class, 'store']);
            Route::get('/position/{position}/edit', [PositionController::class, 'edit']);
            Route::post('/position/{position}/update', [PositionController::class, 'update']);
            Route::get('/position/{position}/delete', [PositionController::class, 'destroy']);
            Route::post('/position/import', [PositionController::class, 'import']);
            Route::get('/position/template', [PositionController::class, 'template']);
        });
    }
);

##HELPER
Route::get('divisi/get/{id}', [UserController::class, 'getdivisi']);
Route::get('approval/get', [UserController::class, 'getapproval']);
Route::get('download/app', [SettingController::class, 'download']);
Route::get('daily/get', [DailyController::class, 'getdaily']);
Route::get('weekly/get', [WeeklyController::class, 'getweekly']);
Route::get('monthly/get', [MonthlyController::class, 'getmonthly']);
Route::get('userresult/get', [UserController::class, 'getuserresult']);

Route::get('kpidescription/get', [KpiDescriptionController::class, 'get']);
