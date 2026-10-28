<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Need;
use App\Models\Pledge;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $school = $user->schoolProfile;

        if (!$school) {
            return redirect()->route('school.profile.edit')->with('warning', 'Harap lengkapi profil sekolah Anda terlebih dahulu.');
        }

        $needs = $school->needs()->with('category')->latest()->limit(5)->get();

        $pledges = Pledge::whereIn('need_id', $school->needs()->pluck('id'))
            ->with(['contributorProfile', 'need'])
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'total_needs' => $school->needs()->count(),
            'published_needs' => $school->needs()->where('status', 'published')->count(),
            'fulfilled_needs' => $school->needs()->where('status', 'fulfilled')->count(),
            'total_pledges' => Pledge::whereIn('need_id', $school->needs()->pluck('id'))->count(),
            'active_pledges' => Pledge::whereIn('need_id', $school->needs()->pluck('id'))->whereIn('status', ['verified', 'in_delivery'])->count(),
        ];

        return view('school.dashboard', compact('user', 'school', 'needs', 'pledges', 'stats'));
    }
}
