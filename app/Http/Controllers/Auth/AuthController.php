<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\SchoolProfile;
use App\Models\ContributorProfile;
use App\Models\VolunteerProfile;
use App\Models\ActivityLog;
use App\Models\Notification;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Terlalu banyak percobaan masuk. Silakan coba lagi dalam {$seconds} detik.")->withInput();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => "Pengguna {$user->name} berhasil masuk.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            if ($user->status === 'suspended') {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan sementara oleh administrator.');
            }

            return $this->redirectBasedOnRole($user);
        }

        RateLimiter::hit($throttleKey, 60);

        return back()
            ->withErrors(['email' => 'Email atau kata sandi yang Anda masukkan salah.'])
            ->with('error', 'Email atau kata sandi yang Anda masukkan salah.')
            ->withInput($request->only('email'));
    }

    public function showRegisterForm(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $defaultRole = $request->query('role', 'contributor');
        if (!in_array($defaultRole, ['school', 'contributor'])) {
            $defaultRole = 'contributor';
        }

        return view('auth.register', compact('defaultRole'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:school,contributor'],
            'contributor_type' => ['nullable', 'required_if:role,contributor', 'in:donor,volunteer,organization'],
            'organization_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:25'],
            'school_name' => ['nullable', 'required_if:role,school', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'region_id' => ['nullable', 'exists:regions,id'],
        ]);

        $status = 'pending'; // Default status for review
        if ($validated['role'] === 'contributor' && ($request->input('contributor_type') === 'donor')) {
            $status = 'active'; // Simple individual donors can be active immediately
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $status,
        ]);

        if ($user->role === 'school') {
            SchoolProfile::create([
                'user_id' => $user->id,
                'school_name' => $request->input('school_name', $user->name),
                'npsn' => $request->input('npsn'),
                'region_id' => $request->input('region_id'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address', 'Belum dilengkapi'),
                'verification_status' => 'pending',
            ]);
        } elseif ($user->role === 'contributor') {
            $contributor = ContributorProfile::create([
                'user_id' => $user->id,
                'contributor_type' => $request->input('contributor_type', 'donor'),
                'organization_name' => $request->input('organization_name'),
                'phone' => $request->input('phone'),
                'verification_status' => $status === 'active' ? 'verified' : 'pending',
            ]);

            if ($contributor->contributor_type === 'volunteer') {
                VolunteerProfile::create([
                    'contributor_profile_id' => $contributor->id,
                ]);
            }
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'register',
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'description' => "Pengguna baru terdaftar sebagai {$user->role} (status: {$user->status}).",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Notify admins about new registration
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'Pendaftaran Pengguna Baru',
                'message' => "Pengguna baru {$user->name} ({$user->email}) mendaftar dengan peran {$user->role}.",
                'type' => 'user_registered',
                'link' => route('admin.users.show', $user->id),
            ]);
        }

        Auth::login($user);

        return $this->redirectBasedOnRole($user)
            ->with('success', 'Pendaftaran berhasil! Selamat datang di SambungAsa.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'logout',
                'description' => "Pengguna " . Auth::user()->name . " telah keluar.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Anda telah berhasil keluar dari akun SambungAsa.');
    }

    protected function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'school' => redirect()->route('school.dashboard'),
            default => redirect()->route('contributor.dashboard'),
        };
    }
}
