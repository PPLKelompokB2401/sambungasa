<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;

class RegionController extends Controller
{
    public function index(Request $request)
    {
        $query = Region::where('level', 'regency')->with(['parent', 'schoolProfiles'])->where('is_active', true);

        if ($request->filled('island')) {
            $query->where('island', $request->input('island'));
        }

        if ($request->filled('is_3t')) {
            $query->where('is_3t', $request->boolean('is_3t'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $regions = $query->orderBy('island')->orderBy('name')->paginate(20)->withQueryString();

        $stats = [
            'total_3t' => Region::where('level', 'regency')->where('is_3t', true)->count(),
            'papua_3t' => Region::where('level', 'regency')->where('island', 'Papua')->where('is_3t', true)->count(),
            'kalimantan_3t' => Region::where('level', 'regency')->where('island', 'Kalimantan')->where('is_3t', true)->count(),
            'sulawesi_3t' => Region::where('level', 'regency')->where('island', 'Sulawesi')->where('is_3t', true)->count(),
        ];

        return view('public.regions.index', compact('regions', 'stats'));
    }
}
