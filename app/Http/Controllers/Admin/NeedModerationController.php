<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Need;
use App\Models\NeedCategory;
use App\Models\ActivityLog;
use App\Models\Notification;

class NeedModerationController extends Controller
{
    public function index(Request $request)
    {
        $query = Need::with(['schoolProfile.regency', 'category'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category_id')) {
            $query->where('need_category_id', $request->input('category_id'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('schoolProfile', function ($sq) use ($search) {
                      $sq->where('school_name', 'like', "%{$search}%");
                  });
            });
        }

        $needs = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Need::count(),
            'pending_review' => Need::where('status', 'pending_review')->count(),
            'published' => Need::where('status', 'published')->count(),
            'fulfilled' => Need::where('status', 'fulfilled')->count(),
            'rejected' => Need::where('status', 'rejected')->count(),
        ];

        $categories = NeedCategory::where('is_active', true)->get();

        return view('admin.needs.index', compact('needs', 'stats', 'categories'));
    }

    public function show(Need $need)
    {
        $need->load(['schoolProfile.regency', 'schoolProfile.user', 'category', 'reviewer', 'pledges.contributorProfile']);

        return view('admin.needs.show', compact('need'));
    }

    public function publish(Request $request, Need $need)
    {
        $need->update([
            'status' => 'published',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'published_at' => now(),
            'rejection_notes' => null,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'publish_need',
            'entity_type' => 'Need',
            'entity_id' => $need->id,
            'description' => "Admin menyetujui dan mempublikasikan kebutuhan '{$need->title}' dari {$need->schoolProfile->school_name}.",
            'ip_address' => $request->ip(),
        ]);

        Notification::create([
            'user_id' => $need->schoolProfile->user_id,
            'title' => 'Kebutuhan Sekolah Telah Diterbitkan',
            'message' => "Kebutuhan '{$need->title}' telah disetujui oleh admin dan kini tampil pada direktori publik.",
            'type' => 'need_published',
            'link' => route('public.needs.show', $need->id),
        ]);

        return back()->with('success', "Kebutuhan '{$need->title}' berhasil dipublikasikan ke direktori publik.");
    }

    public function reject(Request $request, Need $need)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5'],
        ]);

        $reason = $request->input('reason');

        $need->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_notes' => $reason,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'reject_need',
            'entity_type' => 'Need',
            'entity_id' => $need->id,
            'description' => "Admin menolak kebutuhan '{$need->title}'. Alasan: {$reason}",
            'ip_address' => $request->ip(),
        ]);

        Notification::create([
            'user_id' => $need->schoolProfile->user_id,
            'title' => 'Kebutuhan Sekolah Ditolak',
            'message' => "Pengajuan kebutuhan '{$need->title}' ditolak oleh admin. Catatan: {$reason}",
            'type' => 'need_rejected',
            'link' => route('school.needs.edit', $need->id),
        ]);

        return back()->with('success', 'Kebutuhan berhasil ditolak dengan catatan.');
    }

    public function close(Request $request, Need $need)
    {
        $need->update([
            'status' => 'closed',
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'close_need',
            'entity_type' => 'Need',
            'entity_id' => $need->id,
            'description' => "Admin menutup status kebutuhan '{$need->title}'.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Status kebutuhan berhasil ditutup.');
    }
}
