<?php

namespace App\Http\Controllers;

use App\Exports\ReportIndividu;
use App\Helpers\ConvertDate;
use App\Models\Daily;
use App\Models\Divisi;
use App\Models\Monthly;
use App\Models\User;
use App\Models\Weekly;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->divisi_id) {
            $users = User::with('area', 'divisi')
                ->where('divisi_id', $request->divisi_id)
                ->orderBy('nama_lengkap')
                ->get();
        } else {
            $users = User::with('area', 'divisi')->orderBy('nama_lengkap')->limit(1)->skip(1)->get();
        }
        // $users = User::where('id', 1165)->get();
        $report = array();

        foreach ($users as $user) {
            ##SETUP VARIABLE
            //DAILY
            $onTimePoint = 0;
            $dailyPoint = 0;
            $dailyOntime = 0;
            $dailysLength = [];
            $actTarget = [];
            //WEEKLY
            $weeklyValue = 0;
            $kpiWeekly = 0;
            $totalTaskWeekly = 0;
            $closedTaskWeekly = 0;
            for ($i = 0; $i < 7; $i++) {
                $dailyClose = 0;
                $monday = $request->year ? ConvertDate::getMondayOrSaturday($request->year, $request->week, true) : now()->startOfDay()->startOfWeek();
                $monday1 = $request->year ? ConvertDate::getMondayOrSaturday($request->year, $request->week, true) : now()->startOfDay()->startOfWeek();
                if ($i == 0) {
                    $daily = Daily::where('date', $monday)->where('user_id', $user->id)->orderBy('time')->get();
                    $dailyClose += count($daily->where('status', 1));
                    $actTarget[$monday->format('D')] = $dailyClose . '/' . count($daily->where('isplan', 1));
                } else {
                    $daily = Daily::where('date', $monday->addDay($i))->where('user_id', $user->id)->orderBy('time')->get();
                    $dailyClose += count($daily->where('status', 1));
                    $actTarget[$monday1->addDay($i)->format('D')] = $dailyClose . '/' . count($daily->where('isplan', 1));
                }

                if (count($daily) > 0) {
                    array_push($dailysLength, $daily);
                }
            }
            $monday = $request->year ? ConvertDate::getMondayOrSaturday($request->year, $request->week, true) : now()->startOfDay()->startOfWeek();
            $dailys = Daily::whereBetween('date', [$monday->format('y-m-d'), $monday->addDay(6)->format('y-m-d')])->where('user_id', $user->id)->get();
            ##DAILYPOINT
            $dailyDone = count($dailys->where('status', 1));
            $dailySubmit = count($dailysLength);
            $dailyTaskPlanTotal = count($dailys->where('isplan', 1));
            foreach ($dailys as $singleDaily) {
                $onTimePoint += $singleDaily->ontime;
            }
            #SUMMARY DAILY
            if ($dailyDone) {
                if (!$user->wr && !$user->wn) {
                    if ($dailyTaskPlanTotal) {
                        $dailyPoint = ($dailySubmit / 6) * ($dailyDone / $dailyTaskPlanTotal) * 80 > 80 ? 80 : (($dailySubmit / 6) * ($dailyDone / $dailyTaskPlanTotal) * 80);
                    }
                } else {
                    if ($dailyTaskPlanTotal) {
                        $dailyPoint = ($dailySubmit / 6) * ($dailyDone / $dailyTaskPlanTotal) * 40 > 40 ? 40 : (($dailySubmit / 6) * ($dailyDone / $dailyTaskPlanTotal) * 40);
                    }
                }
                if ($dailyTaskPlanTotal) {
                    $dailyOntime = ($dailySubmit / 6) * ($onTimePoint / $dailyTaskPlanTotal) * 20 > 20 ? 20 : ($dailySubmit / 6) * ($onTimePoint / $dailyTaskPlanTotal) * 20;
                }
            }
            ##DAILYONTIME
            // dd($dailySubmit);
            $kpiDaily = $dailyPoint;
            $kpiDailyOntime = $dailyOntime;

            if ($user->wr || $user->wn) {
                ##WEEKLY
                $weeklys = Weekly::where('year', $request->year ?? now()->year)->where('week', $request->week ?? now()->weekOfYear)->where('user_id', $user->id)->get();
                $totalTaskWeekly = count($weeklys);
                foreach ($weeklys as $weekly) {
                    if ($weekly->value) {
                        $weeklyValue += $weekly->value;
                        $closedTaskWeekly++;
                    }
                }
                if ($weeklyValue) {
                    $kpiWeekly = ($weeklyValue / $totalTaskWeekly) > 1 ? 40 : ($weeklyValue / $totalTaskWeekly) * 40;
                }
            }


            // if ($user->mn || $user->mr) {
            //     ##MONTHLY
            //     $mondayMonth = $request->year ? ConvertDate::getMondayOrSaturday($request->year, $request->week, true) : now()->startOfDay()->startOfWeek();
            //     $sundayMonth = $request->year ? ConvertDate::getMondayOrSaturday($request->year, $request->week, false) : now()->startOfDay()->endOfWeek();
            //     $endOfMonth = ConvertDate::getEndOfMonth(now()->year, now()->weekOfYear);
            //     if (($mondayMonth->month == $sundayMonth->month && $sundayMonth->diffInDays($endOfMonth) < 3) || ($mondayMonth->month != $sundayMonth->month && $mondayMonth->endOfMonth()->diffInDays($sundayMonth) < 4)) {
            //         $monthlys = Monthly::whereDate('date', $mondayMonth->startOfMonth())->where('user_id', $user->id)->get();
            //         $totalTaskmonthly = count($monthlys);
            //         foreach ($monthlys as $monthly) {
            //             $monthlyValue += $monthly->value;
            //         }
            //         if ($monthlyValue) {
            //             $kpiMonthly += ($monthlyValue / $totalTaskmonthly) > 1 ? 20 : ($monthlyValue / $totalTaskmonthly) * 20;
            //         }
            //     }
            // }
            $totalKpi = $kpiDaily + $kpiWeekly + $kpiDailyOntime;
            // if (($user->mn || $user->mr) && $kpiMonthly != 0) {
            //     $totalKpi = ($totalKpi * 0.8) + $kpiMonthly;
            // }
            array_push($report, [
                'user' => $user,
                'daily' => $kpiDaily,
                'ontime_point' => $kpiDailyOntime,
                'weekly' => $kpiWeekly,
                'total' => $totalKpi,
                'act' => $actTarget,
                'actw' => $closedTaskWeekly . '/' . $totalTaskWeekly,
            ]);
        }

        return view('admin.report.index')->with([
            'title' => 'Report',
            'active' => 'report',
            'divisis' => Divisi::all()->except(17),
            'reports' => $report,
            'now' => $request->year ? ConvertDate::getMondayOrSaturday($request->year, $request->week, true) : null,
        ]);
    }

    public function exportIndividu(Request $request)
    {
        $divisi = Divisi::find($request->divisi_id);
        return Excel::download(new ReportIndividu($request->week, $request->year, User::orderBy('nama_lengkap')->where('divisi_id', $request->divisi_id)->get()), strtolower($divisi->name) . '_report_week_' . $request->week . '_year_' . $request->year . '.xlsx',);
    }

    public function indexuser(Request $request)
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
                $monday = $request->week && $request->year ? ConvertDate::getMondayOrSaturday($request->year, $request->week, true) :  now()->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfDay()->startOfWeek();
                $daily = Daily::where('date', $monday)->where('user_id', auth()->id())->orderBy('time')->get();
            } else {
                $monday1 =  $request->week && $request->year ? ConvertDate::getMondayOrSaturday($request->year, $request->week, true) : now()->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfDay()->startOfWeek();
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
            $weeklys = Weekly::where('week', $request->week ?? now()->weekOfYear)->where('year', $request->year ?? now()->year)->where('user_id', auth()->id())->get();
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
            $startMonth = $request->week && $request->year ? ConvertDate::getMondayOrSaturday($request->year, $request->week, true)->startOfMonth() : now()->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfDay()->startOfMonth();
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
        return view('result.index', [
            'title' => 'RESULT',
            'active' => 'result',
            'week' => $request->week,
            'year' => $request->year,
            'monday' => $request->week && $request->year ? ConvertDate::getMondayOrSaturday($request->year, $request->week, true) : null,
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
                'date' => $request->week ? ConvertDate::getMondayOrSaturday($request->year, $request->week, true) : null
            ],
        ]);
    }
}
