<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Need;
use App\Models\Pledge;
use App\Models\TrackingLog;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;

class PledgeController extends Controller
{
    public function index(Request $request)
    {
        $contributor = auth()->user()->contributorProfile;

        if (!$contributor) {
            return redirect()->route('contributor.profile.edit')->with('warning', 'Harap lengkapi profil donatur terlebih dahulu.');
        }

        $query = $contributor->pledges()->with(['need.schoolProfile.regency', 'evidenceFiles'])->latest();

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

        return view('contributor.pledges.index', compact('contributor', 'pledges'));
    }

    public function show(Pledge $pledge)
    {
        $contributor = auth()->user()->contributorProfile;

        if ($pledge->contributor_profile_id !== $contributor->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat komitmen bantuan donatur lain.');
        }

        $pledge->load(['need.schoolProfile.regency', 'need.category', 'trackingLogs.creator', 'evidenceFiles.uploader']);

        return view('contributor.pledges.show', compact('pledge'));
    }

    public function store(Request $request, Need $need)
    {
        $user = auth()->user();
        $contributor = $user->contributorProfile;

        if (!$contributor) {
            return redirect()->route('contributor.profile.edit')->with('warning', 'Harap lengkapi profil Anda sebelum mengajukan bantuan.');
        }

        if ($need->status !== 'published') {
            return back()->with('error', 'Kebutuhan ini tidak dalam status menerima bantuan.');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['required', 'string', 'min:10'],
            'estimated_delivery_at' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $createdPledge = null;

        // DB Transaction with pessimistic locking to prevent race-condition over-pledging
        DB::transaction(function () use ($need, $contributor, $validated, $user, $request, &$createdPledge) {
            $lockedNeed = Need::where('id', $need->id)->lockForUpdate()->first();

            $remaining = $lockedNeed->getRemainingQuantity();

            if ($lockedNeed->quantity_required !== null && $validated['quantity'] > $remaining) {
                throw new \Exception("Jumlah komitmen ({$validated['quantity']}) melebihi sisa kebutuhan yang tersedia ({$remaining} {$lockedNeed->unit}).");
            }

            // Generate unique tracking code (Format: SA-YYYYMM-XXXX)
            $trackingCode = null;
            do {
                $candidate = 'SA-' . date('Ym') . '-' . strtoupper(Str::random(4));
            } while (Pledge::where('tracking_code', $candidate)->exists());

            $createdPledge = Pledge::create([
                'need_id' => $lockedNeed->id,
                'contributor_profile_id' => $contributor->id,
                'tracking_code' => $candidate,
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'],
                'estimated_delivery_at' => $validated['estimated_delivery_at'],
                'status' => 'submitted',
            ]);

            // Initial Tracking Log
            TrackingLog::create([
                'pledge_id' => $createdPledge->id,
                'old_status' => null,
                'new_status' => 'submitted',
                'notes' => 'Komitmen bantuan diajukan oleh donatur: "' . $createdPledge->notes . '".',
                'created_by' => $user->id,
                'created_at' => now(),
            ]);

            // Activity Log
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'submit_pledge',
                'entity_type' => 'Pledge',
                'entity_id' => $createdPledge->id,
                'description' => "Pengguna {$user->name} mengajukan bantuan {$createdPledge->quantity} {$lockedNeed->unit} untuk '{$lockedNeed->title}' dengan kode {$createdPledge->tracking_code}.",
                'ip_address' => $request->ip(),
            ]);

            // Notify Admin
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'Pengajuan Komitmen Bantuan Baru',
                    'message' => "Donatur {$user->name} mengajukan bantuan {$createdPledge->quantity} {$lockedNeed->unit} (Kode: {$createdPledge->tracking_code}).",
                    'type' => 'pledge_submitted',
                    'link' => route('admin.pledges.show', $createdPledge->id),
                ]);
            }
        });

        return redirect()->route('contributor.pledges.show', $createdPledge->id)
            ->with('success', "Komitmen bantuan berhasil diajukan dengan Kode Tracking: {$createdPledge->tracking_code}. Administrator akan memverifikasi pengajuan Anda.");
    }
}
