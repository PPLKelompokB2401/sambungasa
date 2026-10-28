<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Need;
use App\Models\NeedCategory;
use App\Models\Pledge;
use App\Models\Region;
use App\Models\SchoolProfile;

class StatisticsController extends Controller
{
    public function index()
    {
        // Core metrics aggregated directly from DB
        $totalVerifiedSchools = SchoolProfile::where('verification_status', 'verified')->count();
        $total3TRegencies = Region::where('is_3t', true)->count();
        $totalFulfilledNeeds = Need::where('status', 'fulfilled')->count();
        $totalActiveNeeds = Need::where('status', 'published')->count();
        $totalCompletedPledges = Pledge::where('status', 'completed')->count();
        $totalActivePledges = Pledge::whereIn('status', ['verified', 'in_delivery', 'received'])->count();

        // Breakdown by Province / Island
        $provinceDistribution = SchoolProfile::with(['province', 'regency'])
            ->where('verification_status', 'verified')
            ->get()
            ->groupBy(function ($school) {
                return $school->province?->name ?? ($school->regency?->island ?? 'Wilayah 3T');
            })
            ->map(function ($group, $name) {
                return (object)[
                    'province_name' => $name,
                    'total_schools' => $group->count(),
                ];
            })
            ->values();

        // Needs by Category
        $categoryBreakdown = NeedCategory::withCount([
            'needs as total_needs',
            'needs as fulfilled_needs' => function ($query) {
                $query->where('status', 'fulfilled');
            },
            'needs as active_needs' => function ($query) {
                $query->where('status', 'published');
            }
        ])->get();

        // Distribution by Assistance Type
        $itemTypePledges = Pledge::whereHas('need', function ($q) {
            $q->where('assistance_type', 'material');
        })->where('status', 'completed')->count();

        $volunteerTypePledges = Pledge::whereHas('need', function ($q) {
            $q->where('assistance_type', 'non_material');
        })->where('status', 'completed')->count();

        // Recent Completed Assistances
        $recentCompleted = Pledge::with(['need.schoolProfile.province', 'need.schoolProfile.regency', 'need.category', 'contributorProfile.user'])
            ->where('status', 'completed')
            ->latest('updated_at')
            ->take(6)
            ->get();

        return view('public.statistics', compact(
            'totalVerifiedSchools',
            'total3TRegencies',
            'totalFulfilledNeeds',
            'totalActiveNeeds',
            'totalCompletedPledges',
            'totalActivePledges',
            'provinceDistribution',
            'categoryBreakdown',
            'itemTypePledges',
            'volunteerTypePledges',
            'recentCompleted'
        ));
    }
}
