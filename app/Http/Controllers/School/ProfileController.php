<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolProfile;
use App\Models\Region;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $school = $user->schoolProfile;

        if (!$school) {
            $school = SchoolProfile::create([
                'user_id' => $user->id,
                'school_name' => $user->name,
                'address' => 'Belum dilengkapi',
                'verification_status' => 'pending',
            ]);
        }

        $provinces = Region::where('level', 'province')->where('is_active', true)->orderBy('name')->get();
        $regencies = Region::where('level', 'regency')->where('is_active', true)->orderBy('name')->get();

        return view('school.profile', compact('user', 'school', 'provinces', 'regencies'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $school = $user->schoolProfile;

        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:25'],
            'address' => ['required', 'string'],
            'province_id' => ['required', 'exists:regions,id'],
            'regency_id' => ['required', 'exists:regions,id'],
            'district' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'student_count' => ['nullable', 'integer', 'min:0'],
            'teacher_count' => ['nullable', 'integer', 'min:0'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $regency = Region::find($validated['regency_id']);
        $wasVerified = $school->verification_status === 'verified';

        $school->update($validated);

        // If the school changed location and was previously needs_revision or pending, notify admins
        if (!$wasVerified) {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'Pembaruan Profil Sekolah 3T',
                    'message' => "Sekolah {$school->school_name} di {$regency?->name} telah memperbarui data lokasi dan menunggu verifikasi 3T.",
                    'type' => 'school_profile_updated',
                    'link' => route('admin.schools.show', $school->id),
                ]);
            }
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'update_school_profile',
            'entity_type' => 'SchoolProfile',
            'entity_id' => $school->id,
            'description' => "Sekolah {$school->school_name} memperbarui data profil dan lokasi.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Data profil sekolah berhasil disimpan.');
    }
}
