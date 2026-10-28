<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContributorProfile;
use App\Models\VolunteerProfile;
use App\Models\ActivityLog;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $contributor = $user->contributorProfile;

        if (!$contributor) {
            $contributor = ContributorProfile::create([
                'user_id' => $user->id,
                'contributor_type' => 'donor',
                'verification_status' => 'pending',
            ]);
        }

        $volunteer = $contributor->volunteerProfile;
        if ($contributor->contributor_type === 'volunteer' && !$volunteer) {
            $volunteer = VolunteerProfile::create([
                'contributor_profile_id' => $contributor->id,
            ]);
        }

        return view('contributor.profile', compact('user', 'contributor', 'volunteer'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $contributor = $user->contributorProfile;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:25'],
            'address' => ['nullable', 'string'],
            'contributor_type' => ['required', 'in:donor,volunteer,organization'],
            'organization_name' => ['nullable', 'string', 'max:255'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'identity_number' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            // Volunteer fields
            'education' => ['nullable', 'string', 'max:255'],
            'expertise' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'availability' => ['nullable', 'in:flexible,weekends,full_time,remote_only'],
            'bio' => ['nullable', 'string'],
        ]);

        $user->update(['name' => $validated['name']]);

        $contributor->update([
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'contributor_type' => $validated['contributor_type'],
            'organization_name' => $validated['organization_name'],
            'pic_name' => $validated['pic_name'],
            'identity_number' => $validated['identity_number'],
            'website' => $validated['website'],
        ]);

        if ($validated['contributor_type'] === 'volunteer') {
            $volunteer = $contributor->volunteerProfile ?: new VolunteerProfile(['contributor_profile_id' => $contributor->id]);
            $volunteer->education = $validated['education'] ?? null;
            $volunteer->expertise = $validated['expertise'] ?? null;
            $volunteer->experience_years = $validated['experience_years'] ?? 0;
            $volunteer->availability = $validated['availability'] ?? 'flexible';
            $volunteer->bio = $validated['bio'] ?? null;
            $volunteer->save();
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'update_profile',
            'entity_type' => 'ContributorProfile',
            'entity_id' => $contributor->id,
            'description' => "Pengguna {$user->name} memperbarui data profil kontributor.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
