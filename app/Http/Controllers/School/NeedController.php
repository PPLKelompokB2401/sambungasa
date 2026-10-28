<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Need;
use App\Models\NeedCategory;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;

class NeedController extends Controller
{
    public function index(Request $request)
    {
        $school = auth()->user()->schoolProfile;

        if (!$school) {
            return redirect()->route('school.profile.edit')->with('warning', 'Silakan lengkapi profil sekolah Anda terlebih dahulu.');
        }

        $query = $school->needs()->with('category')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        $needs = $query->paginate(10)->withQueryString();

        return view('school.needs.index', compact('school', 'needs'));
    }

    public function create()
    {
        $school = auth()->user()->schoolProfile;

        if (!$school || !$school->isVerified()) {
            return redirect()->route('school.dashboard')->with('error', 'Hanya sekolah yang telah lolos verifikasi 3T oleh admin yang dapat mengajukan kebutuhan.');
        }

        $categories = NeedCategory::where('is_active', true)->get();

        return view('school.needs.create', compact('school', 'categories'));
    }

    public function store(Request $request)
    {
        $school = auth()->user()->schoolProfile;

        if (!$school || !$school->isVerified()) {
            abort(403, 'Sekolah belum terverifikasi 3T.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'need_category_id' => ['required', 'exists:need_categories,id'],
            'assistance_type' => ['required', 'in:material,non_material'],
            'priority' => ['required', 'in:rendah,sedang,tinggi,mendesak'],
            'quantity_required' => ['nullable', 'integer', 'min:1'],
            'unit' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string', 'min:20'],
            'target_date' => ['nullable', 'date', 'after:today'],
        ]);

        $need = Need::create([
            'school_profile_id' => $school->id,
            'need_category_id' => $validated['need_category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'assistance_type' => $validated['assistance_type'],
            'priority' => $validated['priority'],
            'quantity_required' => $validated['quantity_required'],
            'quantity_fulfilled' => 0,
            'unit' => $validated['unit'] ?? ($validated['assistance_type'] === 'material' ? 'unit' : 'orang relawan'),
            'status' => 'pending_review',
            'target_date' => $validated['target_date'] ?? null,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_need',
            'entity_type' => 'Need',
            'entity_id' => $need->id,
            'description' => "Sekolah {$school->school_name} mengajukan kebutuhan: {$need->title}.",
            'ip_address' => $request->ip(),
        ]);

        // Notify admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'Kebutuhan Sekolah Menunggu Moderasi',
                'message' => "Sekolah {$school->school_name} mengajukan kebutuhan baru '{$need->title}'.",
                'type' => 'need_review',
                'link' => route('admin.needs.show', $need->id),
            ]);
        }

        return redirect()->route('school.needs.index')
            ->with('success', 'Kebutuhan sekolah berhasil diajukan dan sedang menunggu moderasi administrator.');
    }

    public function edit(Need $need)
    {
        $school = auth()->user()->schoolProfile;

        if ($need->school_profile_id !== $school->id) {
            abort(403, 'Anda tidak berwenang mengedit kebutuhan milik sekolah lain.');
        }

        $activePledgesCount = $need->pledges()
            ->whereIn('status', ['submitted', 'verified', 'in_delivery', 'received', 'completed'])
            ->count();

        $categories = NeedCategory::where('is_active', true)->get();

        return view('school.needs.edit', compact('school', 'need', 'categories', 'activePledgesCount'));
    }

    public function update(Request $request, Need $need)
    {
        $school = auth()->user()->schoolProfile;

        if ($need->school_profile_id !== $school->id) {
            abort(403);
        }

        $hasActivePledges = $need->pledges()
            ->whereIn('status', ['submitted', 'verified', 'in_delivery', 'received', 'completed'])
            ->exists();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'need_category_id' => ['required', 'exists:need_categories,id'],
            'assistance_type' => ['required', 'in:material,non_material'],
            'priority' => ['required', 'in:rendah,sedang,tinggi,mendesak'],
            'quantity_required' => ['nullable', 'integer', 'min:1'],
            'unit' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string', 'min:20'],
            'target_date' => ['nullable', 'date'],
        ]);

        // If active pledges exist, quantity cannot be reduced below committed amount
        if ($hasActivePledges && $validated['quantity_required']) {
            $committed = $need->getActivePledgesQuantity();
            if ($validated['quantity_required'] < $committed) {
                return back()->with('error', "Kuantitas kebutuhan tidak dapat diubah menjadi lebih kecil dari komitmen yang sudah disetujui ({$committed} {$need->unit}).")->withInput();
            }
        }

        $need->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_need',
            'entity_type' => 'Need',
            'entity_id' => $need->id,
            'description' => "Sekolah {$school->school_name} memperbarui kebutuhan '{$need->title}'.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('school.needs.index')->with('success', 'Kebutuhan berhasil diperbarui.');
    }

    public function destroy(Need $need)
    {
        $school = auth()->user()->schoolProfile;

        if ($need->school_profile_id !== $school->id) {
            abort(403);
        }

        $hasActivePledges = $need->pledges()
            ->whereIn('status', ['submitted', 'verified', 'in_delivery', 'received', 'completed'])
            ->exists();

        if ($hasActivePledges) {
            return back()->with('error', 'Kebutuhan ini tidak dapat dihapus karena sudah memiliki komitmen bantuan aktif dari donatur. Anda dapat menutup status kebutuhan jika tidak lagi menerima bantuan.');
        }

        $title = $need->title;
        $need->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_need',
            'entity_type' => 'Need',
            'entity_id' => $need->id,
            'description' => "Sekolah {$school->school_name} menghapus kebutuhan '{$title}'.",
        ]);

        return redirect()->route('school.needs.index')->with('success', 'Kebutuhan berhasil dihapus.');
    }

    public function close(Need $need)
    {
        $school = auth()->user()->schoolProfile;

        if ($need->school_profile_id !== $school->id) {
            abort(403);
        }

        $need->update(['status' => 'closed']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'close_need',
            'entity_type' => 'Need',
            'entity_id' => $need->id,
            'description' => "Sekolah {$school->school_name} menutup kebutuhan '{$need->title}'.",
        ]);

        return back()->with('success', 'Kebutuhan telah ditutup dan tidak lagi menerima pengajuan baru.');
    }
}
