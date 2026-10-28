<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Need;
use App\Models\Pledge;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_schools' => SchoolProfile::count(),
            'pending_schools' => SchoolProfile::where('verification_status', 'pending')->count(),
            'total_needs' => Need::count(),
            'pending_needs' => Need::where('status', 'pending_review')->count(),
            'active_needs' => Need::where('status', 'published')->count(),
            'fulfilled_needs' => Need::where('status', 'fulfilled')->count(),
            'total_pledges' => Pledge::count(),
            'pending_pledges' => Pledge::where('status', 'submitted')->count(),
            'in_progress_pledges' => Pledge::whereIn('status', ['verified', 'in_delivery', 'received'])->count(),
            'completed_pledges' => Pledge::where('status', 'completed')->count(),
            'total_users' => User::count(),
        ];

        $pendingVerifications = SchoolProfile::with(['user', 'province', 'regency'])
            ->where('verification_status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $pendingModerations = Need::with(['schoolProfile', 'category'])
            ->where('status', 'pending_review')
            ->latest()
            ->take(5)
            ->get();

        $recentPledges = Pledge::with(['contributorProfile.user', 'need.schoolProfile'])
            ->latest()
            ->take(5)
            ->get();

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingVerifications', 'pendingModerations', 'recentPledges', 'recentActivities'));
    }
}
