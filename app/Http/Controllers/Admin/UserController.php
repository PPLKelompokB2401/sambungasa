<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['schoolProfile', 'contributorProfile'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'pending' => User::where('status', 'pending')->count(),
            'active' => User::where('status', 'active')->count(),
            'school' => User::where('role', 'school')->count(),
            'contributor' => User::where('role', 'contributor')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function show(User $user)
    {
        $user->load(['schoolProfile.province', 'schoolProfile.regency', 'contributorProfile.volunteerProfile', 'activityLogs' => function ($q) {
            $q->latest()->limit(10);
        }]);

        return view('admin.users.show', compact('user'));
    }

    public function verify(Request $request, User $user)
    {
        $user->update([
            'status' => 'active',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'verification_notes' => $request->input('notes', 'Akun disetujui oleh admin.'),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'verify_user',
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'description' => "Admin menyetujui akun {$user->name} ({$user->email}) menjadi Aktif.",
            'ip_address' => $request->ip(),
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Akun Anda Telah Diverifikasi',
            'message' => 'Selamat, akun Anda telah diverifikasi oleh administrator. Anda sekarang dapat menggunakan seluruh fitur sesuai peran akun Anda.',
            'type' => 'account_verified',
            'link' => $user->role === 'school' ? route('school.dashboard') : route('contributor.dashboard'),
        ]);

        return back()->with('success', "Akun {$user->name} berhasil diverifikasi dan diaktifkan.");
    }

    public function reject(Request $request, User $user)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5'],
        ]);

        $reason = $request->input('reason');

        $user->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'verification_notes' => $reason,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'reject_user',
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'description' => "Admin menolak akun {$user->name} ({$user->email}). Alasan: {$reason}",
            'ip_address' => $request->ip(),
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Pendaftaran Akun Ditolak',
            'message' => "Pendaftaran akun Anda ditolak oleh admin dengan catatan: {$reason}",
            'type' => 'account_rejected',
        ]);

        return back()->with('success', "Akun {$user->name} berhasil ditolak.");
    }

    public function suspend(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $request->validate([
            'reason' => ['required', 'string'],
        ]);

        $reason = $request->input('reason');

        $user->update([
            'status' => 'suspended',
            'verification_notes' => $reason,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'suspend_user',
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'description' => "Admin menonaktifkan (suspend) akun {$user->name}. Alasan: {$reason}",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Akun {$user->name} telah dinonaktifkan (suspended).");
    }

    public function activate(Request $request, User $user)
    {
        $user->update([
            'status' => 'active',
            'verification_notes' => null,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'activate_user',
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'description' => "Admin mengaktifkan kembali akun {$user->name}.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Akun {$user->name} berhasil diaktifkan kembali.");
    }
}
