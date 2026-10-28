<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Need;
use App\Models\NeedCategory;
use App\Models\Region;

class PublicNeedController extends Controller
{
    public function index(Request $request)
    {
        $query = Need::with(['schoolProfile.regency', 'category'])
            ->where('status', 'published');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('schoolProfile', function ($sq) use ($search) {
                      $sq->where('school_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($cq) use ($request) {
                $cq->where('slug', $request->input('category'));
            });
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('type')) {
            $query->where('assistance_type', $request->input('type'));
        }

        if ($request->filled('island')) {
            $island = $request->input('island');
            $query->whereHas('schoolProfile.regency', function ($rq) use ($island) {
                $rq->where('island', $island);
            });
        }

        $needs = $query->orderByRaw("FIELD(priority, 'mendesak', 'tinggi', 'sedang', 'rendah')")
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $categories = NeedCategory::where('is_active', true)->get();

        return view('public.needs.index', compact('needs', 'categories'));
    }

    public function show(Need $need)
    {
        if ($need->status !== 'published' && (!auth()->check() || (!auth()->user()->isAdmin() && auth()->user()->id !== $need->schoolProfile->user_id))) {
            abort(404, 'Kebutuhan belum dipublikasikan untuk umum.');
        }

        $need->load([
            'schoolProfile.province',
            'schoolProfile.regency',
            'category',
            'pledges' => function ($q) {
                $q->whereIn('status', ['in_delivery', 'received', 'completed'])->with('contributorProfile');
            }
        ]);

        $remainingQuantity = $need->getRemainingQuantity();
        $isOpen = $need->isOpenForPledges();

        return view('public.needs.show', compact('need', 'remainingQuantity', 'isOpen'));
    }
}
