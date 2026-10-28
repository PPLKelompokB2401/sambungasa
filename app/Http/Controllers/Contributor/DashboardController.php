<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pledge;
use App\Models\Need;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $contributor = $user->contributorProfile;

        $pledgesQuery = $contributor ? $contributor->pledges()->with(['need.schoolProfile.regency', 'evidenceFiles']) : null;

        $pledges = $pledgesQuery ? $pledgesQuery->latest()->limit(5)->get() : collect();

        $stats = [
            'total_pledges' => $contributor ? $contributor->pledges()->count() : 0,
            'active_pledges' => $contributor ? $contributor->pledges()->whereIn('status', ['submitted', 'verified', 'in_delivery'])->count() : 0,
            'completed_pledges' => $contributor ? $contributor->pledges()->where('status', 'completed')->count() : 0,
            'items_delivered' => $contributor ? $contributor->pledges()->where('status', 'completed')->sum('quantity') : 0,
        ];

        // Recommendations: Urgent needs
        $urgentNeeds = Need::with(['schoolProfile.regency', 'category'])
            ->where('status', 'published')
            ->whereIn('priority', ['mendesak', 'tinggi'])
            ->latest()
            ->limit(4)
            ->get();

        return view('contributor.dashboard', compact('user', 'contributor', 'pledges', 'stats', 'urgentNeeds'));
    }
}
