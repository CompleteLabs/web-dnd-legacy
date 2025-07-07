<?php

namespace App\Http\Controllers\API;

use App\Helpers\ConvertDate;
use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Daily;
use App\Models\Monthly;
use App\Models\Weekly;
use Exception;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function result(Request $request)
    {
        try {
            $user = Auth::user();
            $week = $request->week;
            $year = $request->year;

            //GET ALL DAILY MONDAY TO SUNDAY
            $dailys = array();
            for ($i = 0; $i < 7; $i++) {
                $monday = ConvertDate::getMondayOrSaturday($year, $week, true);
                $i == 0 ?
                    $daily = Daily::where('date', $monday)->where('user_id', $user->id)->orderBy('time')->get() :
                    $daily = Daily::where('date', $monday->addDay($i))->where('user_id', $user->id)->orderBy('time')->get();

                if (count($daily) > 0) {
                    array_push($dailys, $daily);
                }
            }
            //WEEK BY REQUEST
            $weekly = Weekly::where('week', $week)->where('year', $year)->where('user_id', $user->id)->get();

            $monday = ConvertDate::getMondayOrSaturday($year, $week, true);
            // $sunday = ConvertDate::getMondayOrSaturday($year, $week, false);
            // $endOfMonth = ConvertDate::getEndOfMonth($year, $week);

            // if (($monday->month == $sunday->month && $sunday->diffInDays($endOfMonth) < 3) || ($monday->month != $sunday->month && $monday->endOfMonth()->diffInDays($sunday) < 4)) {
            $monthly = Monthly::whereDate('date', $monday->startOfMonth())->where('user_id', $user->id)->get();
            // }

            //RESPONSE
            return ResponseFormatter::success([
                'daily' => $dailys,
                'weekly' => $weekly,
                'monthly' => $monthly ?? [],
            ], 'berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function resultbyuser(Request $request, $id)
    {
        try {
            $week = $request->week;
            $year = $request->year;

            //GET ALL DAILY MONDAY TO SUNDAY
            $dailys = array();
            for ($i = 0; $i < 7; $i++) {
                $monday = ConvertDate::getMondayOrSaturday($year, $week, true);
                $i == 0 ?
                    $daily = Daily::where('date', $monday)->where('user_id', $id)->orderBy('time')->get() :
                    $daily = Daily::where('date', $monday->addDay($i))->where('user_id', $id)->orderBy('time')->get();

                if (count($daily) > 0) {
                    array_push($dailys, $daily);
                }
            }
            //WEEK BY REQUEST
            $weekly = Weekly::where('week', $week)->where('year', $year)->where('user_id', $id)->get();

            $monday = ConvertDate::getMondayOrSaturday($year, $week, true);
            // $sunday = ConvertDate::getMondayOrSaturday($year, $week, false);
            // $endOfMonth = ConvertDate::getEndOfMonth($year, $week);

            // if (($monday->month == $sunday->month && $sunday->diffInDays($endOfMonth) < 3) || ($monday->month != $sunday->month && $monday->endOfMonth()->diffInDays($sunday) < 4)) {
            $monthly = Monthly::whereDate('date', $monday->startOfMonth())->where('user_id', $id)->get();
            // }

            //RESPONSE
            return ResponseFormatter::success([
                'daily' => $dailys,
                'weekly' => $weekly,
                'monthly' => $monthly ?? [],
            ], 'berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }
}
