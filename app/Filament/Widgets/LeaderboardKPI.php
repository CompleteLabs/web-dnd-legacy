<?php

namespace App\Filament\Widgets;

use App\Models\Area;
use App\Models\Divisi;
use App\Models\Kpi;
use App\Models\Cutpoint;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaderboardKPI extends Widget
{
    protected static string $view = 'filament.widgets.leaderboard-kpi';
    protected int | string | array $columnSpan = 'full';

    public $user_id;
    public $month;
    public $area;
    public $division;

    public function mount($user_id = null, $month = null): void
    {
        $this->user_id = $user_id;
        $this->month = $month ?? now()->format('Y-m');
        $this->area = '';
        $this->division = '';
    }

    protected function getAreas()
    {
        return Area::all();
    }

    protected function getDivisions()
    {
        if ($this->area) {
            return Divisi::where('area_id', $this->area)->get();
        }
        return Divisi::all();
    }

    protected function getLeaderboardData()
    {
        $date = Carbon::createFromFormat('Y-m', $this->month);

        $query = \App\Models\User::query()
            ->with([
                'divisi.area',
                'area',
                'kpi' => function($query) use ($date) {
                    $query->select('id', 'user_id', 'percentage', 'date')
                        ->where('kpi_type_id', 3)
                        ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$this->month])
                        ->orderBy('date', 'DESC')
                        ->with(['kpi_detail' => function($query) {
                            $query->whereNotNull('value_result')->where('value_result', '>=', 0);
                        }]);
                },
                'attendance' => function($query) use ($date) {
                    $query->select('user_id', 'late_less_30', 'late_more_30', 'sick_days', 'work_days', 'periode')
                        ->where('periode', $this->month);
                },
                'employeeReview' => function($query) use ($date) {
                    $query->select('user_id', 'responsiveness', 'problem_solver', 'helpfulness', 'initiative', 'periode')
                        ->where('periode', $this->month);
                }
            ]);

        if ($this->area) {
            $query->where('area_id', $this->area);
        }

        if ($this->division) {
            $query->where('divisi_id', $this->division);
        }

        $users = $query->get();
        $leaderboardData = [];

        foreach ($users as $user) {
            $kpiScore = $this->calculateKPIScore($user);
            $attendanceScore = $this->calculateAttendanceScore($user);
            $activityScore = $this->calculateActivityScore($user);

            $totalScore = ($kpiScore + $attendanceScore + $activityScore);

            // Kurangi totalScore dengan cutpoint user jika ada
            // Jika ada lebih dari satu cutpoint untuk user dan periode, jumlahkan semua point
            $cutpointValue = \App\Models\Cutpoint::where('user_id', $user->id)
                ->where('periode', $this->month)
                ->sum('point');
            $totalScore = max(0, $totalScore - $cutpointValue);

            $leaderboardData[] = [
                'user' => $user,
                'kpiScore' => $kpiScore,
                'attendanceScore' => $attendanceScore,
                'activityScore' => $activityScore,
                'totalScore' => $totalScore,
                'cutpoint' => $cutpointValue,
            ];
        }

        return collect($leaderboardData)->sortByDesc('totalScore')->values()->all();
    }

    protected function calculateKPIScore($user)
    {
        $kpiScore = 0;

        foreach ($user->kpi as $kpi) {
            $actualCount = $kpi->kpi_detail->sum('value_result');
            $count = $kpi->kpi_detail->count();
            $divisor = $count > 0 ? $count : 1;
            $score = ($kpi->percentage / 100) * ($actualCount / $divisor);
            $kpiScore += $score * 100;
        }

        return min(40, $kpiScore * 0.4);
    }

    protected function calculateAttendanceScore($user)
    {
        if (!$user->attendance) return 0;

        $attendance = $user->attendance;
        $lateLess30 = $attendance->late_less_30 ?? 0;
        $lateMore30 = $attendance->late_more_30 ?? 0;
        $sickDays = $attendance->sick_days ?? 0;
        $permissionDays = $attendance->permission_days ?? 0;
        $nonCompliance = $attendance->non_compliance ?? 0;
        $workDays = $attendance->work_days ?? 0;

        if ($workDays <= 0) return 0;

        $initialAttendanceAchv = ($workDays - $lateLess30 - $lateMore30 - $sickDays - $permissionDays - $nonCompliance) / $workDays * 100;
        $penalty = ($lateLess30 * 1) + ($lateMore30 * 3) + ($sickDays * 5) + ($permissionDays * 5) + ($nonCompliance * 5);
        $finalAttendanceAchv = max(0, $initialAttendanceAchv - $penalty);

        return ($finalAttendanceAchv / 100) * 40;
    }

    protected function calculateActivityScore($user)
    {
        if (!$user->employeeReview) return 0;

        $review = $user->employeeReview;
        $responsiveness = $review->responsiveness ?? 0;
        $problemSolver = $review->problem_solver ?? 0;
        $helpfulness = $review->helpfulness ?? 0;
        $initiative = $review->initiative ?? 0;

        return ($responsiveness + $problemSolver + $helpfulness + $initiative) / 20 * 100 * 0.2;
    }
}
