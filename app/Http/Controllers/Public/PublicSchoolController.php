<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolProfile;

class PublicSchoolController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolProfile::with(['province', 'regency', 'needs' => function ($q) {
            $q->where('status', 'published');
        }])->where('verification_status', 'verified');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('school_name', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('npsn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('island')) {
            $island = $request->input('island');
            $query->whereHas('regency', function ($rq) use ($island) {
                $rq->where('island', $island);
            });
        }

        $schools = $query->latest('verified_at')->paginate(12)->withQueryString();

        return view('public.schools.index', compact('schools'));
    }

    public function show(SchoolProfile $school)
    {
        if (!$school->isVerified() && (!auth()->check() || (!auth()->user()->isAdmin() && auth()->user()->id !== $school->user_id))) {
            abort(404, 'Sekolah belum terverifikasi untuk tampilan publik.');
        }

        $school->load(['province', 'regency', 'needs' => function ($q) {
            $q->where('status', 'published')->with('category');
        }]);

        return view('public.schools.show', compact('school'));
    }
}
