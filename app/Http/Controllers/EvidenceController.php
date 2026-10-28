<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Pledge;
use App\Models\EvidenceFile;
use App\Models\TrackingLog;
use App\Models\ActivityLog;

class EvidenceController extends Controller
{
    public function upload(Request $request, Pledge $pledge)
    {
        $user = auth()->user();

        // Authorization check: User must be admin, or the pledge's contributor, or the receiving school
        $isContributor = $user->isContributor() && $pledge->contributor_profile_id === $user->contributorProfile?->id;
        $isSchool = $user->isSchool() && $pledge->need->school_profile_id === $user->schoolProfile?->id;
        $isAdmin = $user->isAdmin();

        if (!$isContributor && !$isSchool && !$isAdmin) {
            abort(403, 'Anda tidak berwenang mengunggah bukti pada komitmen bantuan ini.');
        }

        $validated = $request->validate([
            'evidence_file' => ['required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'], // Max 5MB
            'evidence_type' => ['required', 'in:delivery_receipt,photo_documentation,activity_report,handover_bast,other'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('evidence_file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();

        // Save privately in storage/app/private/evidence
        $path = $file->store('private/evidence');

        $evidence = EvidenceFile::create([
            'pledge_id' => $pledge->id,
            'uploaded_by' => $user->id,
            'file_path' => $path,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'evidence_type' => $validated['evidence_type'],
            'caption' => $validated['caption'],
        ]);

        TrackingLog::create([
            'pledge_id' => $pledge->id,
            'old_status' => $pledge->status,
            'new_status' => $pledge->status,
            'notes' => "Berkas bukti baru diunggah: {$originalName} (" . ucfirst(str_replace('_', ' ', $validated['evidence_type'])) . ").",
            'created_by' => $user->id,
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'upload_evidence',
            'entity_type' => 'EvidenceFile',
            'entity_id' => $evidence->id,
            'description' => "Pengguna {$user->name} mengunggah bukti {$originalName} untuk komitmen {$pledge->tracking_code}.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Berkas bukti berhasil diunggah secara aman ke arsip privat.');
    }

    public function download(EvidenceFile $evidence)
    {
        $user = auth()->user();
        $pledge = $evidence->pledge;

        // Authorization: Only admin, the contributor, or the school can download/view
        $isContributor = $user->isContributor() && $pledge->contributor_profile_id === $user->contributorProfile?->id;
        $isSchool = $user->isSchool() && $pledge->need->school_profile_id === $user->schoolProfile?->id;
        $isAdmin = $user->isAdmin();

        if (!$isContributor && !$isSchool && !$isAdmin) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengunduh bukti ini.');
        }

        if (!Storage::exists($evidence->file_path)) {
            abort(404, 'Berkas fisik tidak ditemukan di penyimpanan server.');
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'view_evidence',
            'entity_type' => 'EvidenceFile',
            'entity_id' => $evidence->id,
            'description' => "Pengguna {$user->name} mengakses berkas bukti {$evidence->original_name}.",
            'ip_address' => request()->ip(),
        ]);

        return Storage::response($evidence->file_path, $evidence->original_name);
    }
}
