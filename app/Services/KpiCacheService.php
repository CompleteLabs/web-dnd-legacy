<?php

namespace App\Services;

use App\Models\KpiCategory;
use App\Models\KpiDescription;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class KpiCacheService
{
    public static function getKpiCategories(): array
    {
        return Cache::remember('kpi_categories', 3600, function () {
            return KpiCategory::pluck('name', 'id')->toArray();
        });
    }

    public static function getKpiDescriptionsByCategory(int $categoryId): array
    {
        return Cache::remember("kpi_descriptions_category_{$categoryId}", 3600, function () use ($categoryId) {
            return KpiDescription::where('kpi_category_id', $categoryId)
                ->pluck('description', 'id')
                ->toArray();
        });
    }

    public static function getPositionsForUser(): array
    {
        $userId = Auth::id();
        $userRole = Auth::user()->role_id;
        $userDivision = Auth::user()->divisi_id;

        $cacheKey = "positions_user_{$userId}_{$userRole}_{$userDivision}";

        return Cache::remember($cacheKey, 1800, function () use ($userRole, $userDivision) {
            $query = Position::query();

            if ($userRole == 5 && $userDivision == 3) {
                // BU VEGA
                $query->whereHas('user', function ($q) use ($userDivision) {
                    $q->whereIn('role_id', [4, 5, 3, 2])
                        ->where('divisi_id', $userDivision);
                });
            } elseif ($userRole == 5 && $userDivision != 3) {
                // MANAGER
                $query->whereHas('user', function ($q) use ($userDivision) {
                    $q->whereIn('role_id', [4, 5])
                        ->where('divisi_id', $userDivision);
                });
            } elseif ($userRole == 4) {
                // COORDINATOR
                $query->whereHas('user', function ($q) use ($userDivision) {
                    $q->whereIn('role_id', [4, 3, 2])
                        ->where('divisi_id', $userDivision);
                });
            }

            return $query->with('user')->get()->mapWithKeys(function ($position) {
                $userNames = $position->user->pluck('nama_lengkap')->implode(', ');
                return [$position->id => "{$position->name} - {$userNames}"];
            })->toArray();
        });
    }

    public static function clearKpiCache(): void
    {
        Cache::forget('kpi_categories');
        Cache::forget('kpi_descriptions_category_1');
        Cache::forget('kpi_descriptions_category_2');
        Cache::forget('kpi_descriptions_category_3');

        // Clear user-specific position cache only if user is authenticated
        if (Auth::check()) {
            $userId = Auth::id();
            $userRole = Auth::user()->role_id;
            $userDivision = Auth::user()->divisi_id;

            Cache::forget("positions_user_{$userId}_{$userRole}_{$userDivision}");
        }

        // Clear all position cache patterns
        Cache::flush();
    }
}
