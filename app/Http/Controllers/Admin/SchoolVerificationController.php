<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolProfile;
use App\Models\Region;
use App\Models\ActivityLog;
use App\Models\Notification;

class SchoolVerificationController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolProfile::with(['user', 'province', 'regency'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('school_name', 'like', "%{$search}%")
                  ->orWhere('npsn', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('verification_status', $request->input('status'));
        }

        if ($request->filled('island')) {
            $island = $request->input('island');
            $query->whereHas('regency', function ($rq) use ($island) {
                $rq->where('island', $island);
            });
        }

        $schools = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => SchoolProfile::count(),
            'pending' => SchoolProfile::where('verification_status', 'pending')->count(),
            'verified' => SchoolProfile::where('verification_status', 'verified')->count(),
            'rejected' => SchoolProfile::where('verification_status', 'rejected')->count(),
            'needs_revision' => SchoolProfile::where('verification_status', 'needs_revision')->count(),
        ];

        return view('admin.schools.index', compact('schools', 'stats'));
    }

    public function show(SchoolProfile $school)
    {
        $school->load(['user', 'province', 'regency', 'verifier', 'needs.category']);

        $is3tRegion = $school->regency ? $school->regency->is_3t : false;

        return view('admin.schools.show', compact('school', 'is3tRegion'));
    }

    public function verify(Request $request, SchoolProfile $school)
    {
        $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $notes = $request->input('notes', 'Lokasi sekolah terverifikasi sesuai referensi wilayah 3T resmi.');

        $school->update([
            'verification_status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'verify_school_3t',
            'entity_type' => 'SchoolProfile',
            'entity_id' => $school->id,
            'description' => "Admin memverifikasi status 3T sekolah {$school->school_name}.",
            'ip_address' => $request->ip(),
        ]);

        Notification::create([
            'user_id' => $school->user_id,
            'title' => 'Verifikasi Lokasi 3T Disetujui',
            'message' => "Selamat, profil sekolah {$school->school_name} telah resmi terverifikasi sebagai sekolah di wilayah 3T. Anda kini dapat mempublikasikan daftar kebutuhan sekolah.",
            'type' => 'school_verified',
            'link' => route('school.needs.index'),
        ]);

        return back()->with('success', "Sekolah {$school->school_name} berhasil diverifikasi sebagai sekolah wilayah 3T.");
    }

    public function reject(Request $request, SchoolProfile $school)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5'],
        ]);

        $reason = $request->input('reason');

        $school->update([
            'verification_status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'verification_notes' => $reason,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'reject_school_3t',
            'entity_type' => 'SchoolProfile',
            'entity_id' => $school->id,
            'description' => "Admin menolak verifikasi 3T sekolah {$school->school_name}. Alasan: {$reason}",
            'ip_address' => $request->ip(),
        ]);

        Notification::create([
            'user_id' => $school->user_id,
            'title' => 'Verifikasi Sekolah Ditolak',
            'message' => "Verifikasi 3T sekolah {$school->school_name} ditolak. Alasan: {$reason}",
            'type' => 'school_rejected',
            'link' => route('school.profile.edit'),
        ]);

        return back()->with('success', "Verifikasi sekolah {$school->school_name} berhasil ditolak.");
    }

    public function requestRevision(Request $request, SchoolProfile $school)
    {
        $request->validate([
            'notes' => ['required', 'string', 'min:5'],
        ]);

        $notes = $request->input('notes');

        $school->update([
            'verification_status' => 'needs_revision',
            'verification_notes' => $notes,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'request_school_revision',
            'entity_type' => 'SchoolProfile',
            'entity_id' => $school->id,
            'description' => "Admin meminta revisi data sekolah {$school->school_name}. Catatan: {$notes}",
            'ip_address' => $request->ip(),
        ]);

        Notification::create([
            'user_id' => $school->user_id,
            'title' => 'Permintaan Revisi Profil Sekolah',
            'message' => "Administrator meminta pembaruan data profil sekolah Anda: {$notes}",
            'type' => 'school_needs_revision',
            'link' => route('school.profile.edit'),
        ]);

        return back()->with('success', "Permintaan revisi berhasil dikirimkan ke perwakilan sekolah.");
    }
}
