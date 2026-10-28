<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Need;
use App\Models\NeedCategory;
use App\Models\SchoolProfile;
use App\Models\Pledge;
use App\Models\Region;

class HomeController extends Controller
{
    public function index()
    {
        $categories = NeedCategory::where('is_active', true)->get();

        $urgentNeeds = Need::with(['schoolProfile.regency', 'category'])
            ->where('status', 'published')
            ->orderByRaw("FIELD(priority, 'mendesak', 'tinggi', 'sedang', 'rendah')")
            ->latest('published_at')
            ->limit(6)
            ->get();

        $stats = [
            'total_schools' => SchoolProfile::where('verification_status', 'verified')->count(),
            'total_needs_published' => Need::where('status', 'published')->count(),
            'total_needs_fulfilled' => Need::where('status', 'fulfilled')->count(),
            'total_items_delivered' => (int) Pledge::where('status', 'completed')->sum('quantity'),
            'total_regions_3t' => Region::where('level', 'regency')->where('is_3t', true)->count(),
        ];

        $schools = SchoolProfile::with('regency')
            ->where('verification_status', 'verified')
            ->latest('verified_at')
            ->limit(4)
            ->get();

        return view('public.home', compact('categories', 'urgentNeeds', 'stats', 'schools'));
    }
}
