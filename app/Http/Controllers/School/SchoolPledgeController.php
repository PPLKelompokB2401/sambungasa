<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pledge;
use App\Models\TrackingLog;
use App\Models\ActivityLog;
use App\Models\Notification;

class SchoolPledgeController extends Controller
{
    public function index(Request $request)
    {
        $school = auth()->user()->schoolProfile;

        if (!$school) {
            return redirect()->route('school.dashboard');
        }

        $query = Pledge::whereIn('need_id', $school->needs()->pluck('id'))
            ->with(['contributorProfile.user', 'need'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhereHas('need', function ($nq) use ($search) {
                      $nq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $pledges = $query->paginate(10)->withQueryString();

        return view('school.pledges.index', compact('school', 'pledges'));
    }

    public function show(Pledge $pledge)
    {
        $school = auth()->user()->schoolProfile;

        if ($pledge->need->school_profile_id !== $school->id) {
            abort(403, 'Anda tidak berwenang melihat komitmen bantuan milik sekolah lain.');
        }

        $pledge->load(['contributorProfile.user', 'need.category', 'trackingLogs.creator', 'evidenceFiles.uploader']);

        return view('school.pledges.show', compact('school', 'pledge'));
    }

    public function confirmReceipt(Request $request, Pledge $pledge)
    {
        $school = auth()->user()->schoolProfile;

        if ($pledge->need->school_profile_id !== $school->id) {
            abort(403);
        }

        if (!in_array($pledge->status, ['in_delivery', 'verified'])) {
            return back()->with('error', 'Konfirmasi penerimaan hanya dapat dilakukan jika bantuan sudah berstatus Dalam Pengiriman.');
        }

        $request->validate([
            'notes' => ['required', 'string', 'min:5'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
        ]);

        $recipient = $request->input('recipient_name', auth()->user()->name);
        $notes = "Bantuan fisik/kegiatan telah diterima di sekolah oleh {$recipient}. Catatan: " . $request->input('notes');

        DB::transaction(function () use ($pledge, $notes, $school, $request) {
            $oldStatus = $pledge->status;

            // Advance status to received
            $pledge->update([
                'status' => 'received',
            ]);

            TrackingLog::create([
                'pledge_id' => $pledge->id,
                'old_status' => $oldStatus,
                'new_status' => 'received',
                'notes' => $notes,
                'location' => $school->school_name . ' (' . ($school->district ?: $school->address) . ')',
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'confirm_receipt',
                'entity_type' => 'Pledge',
                'entity_id' => $pledge->id,
                'description' => "Sekolah {$school->school_name} mengonfirmasi penerimaan bantuan {$pledge->tracking_code}.",
                'ip_address' => $request->ip(),
            ]);

            // Notify contributor
            Notification::create([
                'user_id' => $pledge->contributorProfile->user_id,
                'title' => 'Bantuan Telah Diterima oleh Sekolah!',
                'message' => "Sekolah {$school->school_name} telah mengonfirmasi penerimaan bantuan ({$pledge->tracking_code}): {$notes}",
                'type' => 'pledge_received',
                'link' => route('contributor.pledges.show', $pledge->id),
            ]);
        });

        return back()->with('success', 'Konfirmasi penerimaan bantuan berhasil dicatat pada riwayat pelacakan.');
    }
}
