<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pledge;
use App\Models\TrackingLog;
use App\Models\ActivityLog;
use App\Models\Notification;

class PledgeManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Pledge::with(['need.schoolProfile.regency', 'contributorProfile.user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhereHas('need', function ($nq) use ($search) {
                      $nq->where('title', 'like', "%{$search}%");
                  })
                  ->orWhereHas('contributorProfile.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $pledges = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Pledge::count(),
            'submitted' => Pledge::where('status', 'submitted')->count(),
            'in_delivery' => Pledge::where('status', 'in_delivery')->count(),
            'completed' => Pledge::where('status', 'completed')->count(),
            'rejected' => Pledge::where('status', 'rejected')->count(),
        ];

        return view('admin.pledges.index', compact('pledges', 'stats'));
    }

    public function show(Pledge $pledge)
    {
        $pledge->load([
            'need.schoolProfile.regency',
            'need.category',
            'contributorProfile.user',
            'reviewer',
            'trackingLogs.creator',
            'evidenceFiles.uploader',
        ]);

        return view('admin.pledges.show', compact('pledge'));
    }

    public function approve(Request $request, Pledge $pledge)
    {
        if ($pledge->status !== 'submitted') {
            return back()->with('error', 'Hanya komitmen berstatus Diajukan (submitted) yang dapat disetujui verifikasinya.');
        }

        $oldStatus = $pledge->status;

        $pledge->update([
            'status' => 'verified',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        TrackingLog::create([
            'pledge_id' => $pledge->id,
            'old_status' => $oldStatus,
            'new_status' => 'verified',
            'notes' => $request->input('notes', 'Komitmen bantuan disetujui oleh admin. Donatur dapat memulai pengiriman/penyaluran.'),
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'approve_pledge',
            'entity_type' => 'Pledge',
            'entity_id' => $pledge->id,
            'description' => "Admin menyetujui komitmen bantuan {$pledge->tracking_code}.",
            'ip_address' => $request->ip(),
        ]);

        // Notify Contributor
        Notification::create([
            'user_id' => $pledge->contributorProfile->user_id,
            'title' => 'Komitmen Bantuan Anda Disetujui',
            'message' => "Pengajuan bantuan {$pledge->tracking_code} untuk {$pledge->need->title} telah disetujui. Silakan lanjutkan ke tahap pengiriman barang/kegiatan.",
            'type' => 'pledge_approved',
            'link' => route('contributor.pledges.show', $pledge->id),
        ]);

        // Notify School
        Notification::create([
            'user_id' => $pledge->need->schoolProfile->user_id,
            'title' => 'Bantuan Baru Disetujui Admin',
            'message' => "Donatur {$pledge->contributorProfile->display_name} telah disetujui memberikan bantuan {$pledge->quantity} {$pledge->need->unit} (Kode: {$pledge->tracking_code}).",
            'type' => 'pledge_incoming',
            'link' => route('school.pledges.show', $pledge->id),
        ]);

        return back()->with('success', "Komitmen bantuan {$pledge->tracking_code} berhasil disetujui.");
    }

    public function reject(Request $request, Pledge $pledge)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5'],
        ]);

        $reason = $request->input('reason');
        $oldStatus = $pledge->status;

        $pledge->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        TrackingLog::create([
            'pledge_id' => $pledge->id,
            'old_status' => $oldStatus,
            'new_status' => 'rejected',
            'notes' => "Pengajuan bantuan ditolak oleh admin. Alasan: {$reason}",
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'reject_pledge',
            'entity_type' => 'Pledge',
            'entity_id' => $pledge->id,
            'description' => "Admin menolak komitmen bantuan {$pledge->tracking_code}. Alasan: {$reason}",
            'ip_address' => $request->ip(),
        ]);

        Notification::create([
            'user_id' => $pledge->contributorProfile->user_id,
            'title' => 'Komitmen Bantuan Ditolak',
            'message' => "Pengajuan bantuan {$pledge->tracking_code} ditolak oleh admin. Catatan: {$reason}",
            'type' => 'pledge_rejected',
            'link' => route('contributor.pledges.show', $pledge->id),
        ]);

        return back()->with('success', "Komitmen bantuan {$pledge->tracking_code} berhasil ditolak.");
    }

    public function updateStatus(Request $request, Pledge $pledge)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:in_delivery,received,completed'],
            'notes' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $allowedNext = $pledge->getAllowedNextStatuses();
        if (!in_array($validated['status'], $allowedNext)) {
            return back()->with('error', "Transisi status ilegal: Tidak dapat berpindah dari {$pledge->status_label} ke " . ucfirst($validated['status']));
        }

        $oldStatus = $pledge->status;
        $newStatus = $validated['status'];

        DB::transaction(function () use ($pledge, $oldStatus, $newStatus, $validated, $request) {
            $updateData = ['status' => $newStatus];
            if ($newStatus === 'completed') {
                $updateData['completed_at'] = now();
            }

            $pledge->update($updateData);

            TrackingLog::create([
                'pledge_id' => $pledge->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'notes' => $validated['notes'] ?: "Status diperbarui menjadi {$newStatus} oleh admin.",
                'location' => $validated['location'] ?? null,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);

            // If completed, update need quantity_fulfilled and check if need is 100% fulfilled
            if ($newStatus === 'completed') {
                $need = $pledge->need;
                $totalDelivered = Pledge::where('need_id', $need->id)->where('status', 'completed')->sum('quantity');
                $need->quantity_fulfilled = $totalDelivered;
                if ($need->quantity_required !== null && $totalDelivered >= $need->quantity_required) {
                    $need->status = 'fulfilled';
                }
                $need->save();
            }

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'update_pledge_status',
                'entity_type' => 'Pledge',
                'entity_id' => $pledge->id,
                'description' => "Status komitmen {$pledge->tracking_code} diubah dari {$oldStatus} ke {$newStatus}.",
                'ip_address' => $request->ip(),
            ]);

            // Notify contributor and school
            Notification::create([
                'user_id' => $pledge->contributorProfile->user_id,
                'title' => 'Pembaruan Status Penyaluran Bantuan',
                'message' => "Status bantuan {$pledge->tracking_code} kini: {$pledge->status_label}.",
                'type' => 'pledge_status_updated',
                'link' => route('contributor.pledges.show', $pledge->id),
            ]);

            Notification::create([
                'user_id' => $pledge->need->schoolProfile->user_id,
                'title' => 'Pembaruan Status Bantuan Masuk',
                'message' => "Status bantuan {$pledge->tracking_code} kini: {$pledge->status_label}.",
                'type' => 'pledge_status_updated',
                'link' => route('school.pledges.show', $pledge->id),
            ]);
        });

        return back()->with('success', "Status komitmen bantuan berhasil diperbarui menjadi {$pledge->status_label}.");
    }
}
