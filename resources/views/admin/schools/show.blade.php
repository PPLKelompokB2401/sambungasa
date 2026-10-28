@extends('layouts.dashboard')

@section('title', 'Periksa Verifikasi 3T: ' . $school->school_name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.schools.index') }}" class="text-sm">← Kembali ke Antrean Verifikasi Sekolah</a>
</div>

<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <h1 style="font-size: 22px; font-weight: 700;">{{ $school->school_name }}</h1>
        <p class="text-sm text-muted">Pemeriksaan kelayakan dan penetapan wilayah 3T sekolah.</p>
    </div>

    <!-- Actions -->
    <div class="d-flex align-center gap-2">
        @if($school->verification_status !== 'verified')
            <form action="{{ route('admin.schools.verify', $school->id) }}" method="POST" onsubmit="return confirm('Tetapkan sekolah ini sebagai Terverifikasi 3T?')">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">✓ Setujui Verifikasi 3T</button>
            </form>
        @endif

        <button type="button" class="btn btn-outline btn-sm text-info" onclick="document.getElementById('revisionModal').style.display='flex'">
            Minta Revisi
        </button>

        @if($school->verification_status !== 'rejected')
            <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('rejectModal').style.display='flex'">
                ✕ Tolak Verifikasi
            </button>
        @endif
    </div>
</div>

<!-- 3T Evaluation Banner -->
<div class="card mb-6" style="border-left: 4px solid {{ $is3tRegion ? 'var(--success)' : 'var(--warning)' }};">
    <div class="card-body d-flex align-center justify-between flex-wrap gap-3 py-3">
        <div>
            <div class="text-xs text-muted font-medium">HASIL PENCOCOKAN DATA 3T</div>
            <div style="font-size: 16px; font-weight: 700;" class="{{ $is3tRegion ? 'text-success' : 'text-warning' }}">
                {{ $is3tRegion ? '✓ Lokasi Berada di Kabupaten Terdaftar 3T Resmi' : '⚠ Perhatian: Lokasi Bukan Kabupaten 3T Prioritas' }}
            </div>
            <div class="text-xs text-muted mt-1">
                Kabupaten: <strong>{{ $school->regency?->name ?? 'Belum dipilih' }}</strong> 
                (Kepulauan: {{ $school->regency?->island ?? '—' }}) • Status 3T Database: {{ $is3tRegion ? 'Ya' : 'Tidak' }}
            </div>
        </div>
        <div>
            <span class="badge {{ $school->isVerified() ? 'badge-success' : ($school->verification_status == 'needs_revision' ? 'badge-info' : 'badge-warning') }}" style="font-size: 12px; padding: 6px 12px;">
                Status Saat Ini: {{ strtoupper($school->verification_status) }}
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <!-- School Profile Info -->
    <div style="grid-column: span 2;" class="card">
        <div class="card-header">
            <h3 style="font-size: 16px;">Data Lengkap Sekolah</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-xs text-muted">NAMA SEKOLAH</span>
                    <div class="font-bold text-dark">{{ $school->school_name }}</div>
                </div>
                <div>
                    <span class="text-xs text-muted">NPSN</span>
                    <div class="font-medium">{{ $school->npsn ?: '—' }}</div>
                </div>
                <div>
                    <span class="text-xs text-muted">KEPALA SEKOLAH</span>
                    <div class="font-medium">{{ $school->principal_name ?: '—' }}</div>
                </div>
                <div>
                    <span class="text-xs text-muted">KONTAK TELEPON / WA</span>
                    <div class="font-medium">{{ $school->phone ?: '—' }}</div>
                </div>
                <div style="grid-column: span 2;">
                    <span class="text-xs text-muted">ALAMAT LENGKAP</span>
                    <div class="text-sm mt-1">{{ $school->address }}</div>
                </div>
                <div>
                    <span class="text-xs text-muted">KECAMATAN / DISTRIK</span>
                    <div class="font-medium">{{ $school->district ?: '—' }}</div>
                </div>
                <div>
                    <span class="text-xs text-muted">KABUPATEN & PROVINSI</span>
                    <div class="font-medium">{{ $school->regency?->name }}, {{ $school->province?->name }}</div>
                </div>
                <div>
                    <span class="text-xs text-muted">KOORDINAT GEOGRAFIS</span>
                    <div class="text-xs" style="font-family: var(--font-mono);">
                        Lat: {{ $school->latitude ?? '—' }}, Lng: {{ $school->longitude ?? '—' }}
                    </div>
                </div>
                <div>
                    <span class="text-xs text-muted">SISWA / GURU</span>
                    <div class="font-medium">{{ $school->student_count ?? 0 }} Murid / {{ $school->teacher_count ?? 0 }} Guru</div>
                </div>
            </div>

            @if($school->verification_notes)
                <div class="mt-4 p-3" style="background: var(--surface); border-radius: var(--radius-sm);">
                    <span class="text-xs font-medium text-muted">CATATAN VERIFIKASI ADMIN SEBELUMNYA:</span>
                    <p class="mt-1 text-sm text-dark">{{ $school->verification_notes }}</p>
                    @if($school->verified_at)
                        <div class="text-xs text-muted mt-1">Diverifikasi pada: {{ $school->verified_at->format('d M Y, H:i') }} oleh {{ $school->verifier?->name ?? 'Admin' }}</div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- User Rep & Needs Info -->
    <div style="grid-column: span 1; display: flex; flex-direction: column; gap: 20px;">
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Akun Perwakilan Sekolah</h3>
            </div>
            <div class="card-body">
                <div class="font-bold text-dark">{{ $school->user->name }}</div>
                <div class="text-xs text-muted">{{ $school->user->email }}</div>
                <div class="mt-3">
                    <span class="text-xs text-muted">STATUS AKUN PENGGUNA:</span>
                    <div>
                        <span class="badge {{ $school->user->isActive() ? 'badge-success' : 'badge-warning' }}">
                            {{ $school->user->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Kebutuhan Terdaftar</h3>
            </div>
            <div class="card-body">
                <div style="font-size: 24px; font-weight: 700;">{{ $school->needs->count() }}</div>
                <div class="text-xs text-muted mt-1">Kebutuhan yang diajukan oleh sekolah ini.</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Revision -->
<div id="revisionModal" class="modal-backdrop" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ route('admin.schools.revision', $school->id) }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 style="font-size: 16px; color: var(--secondary);">Minta Revisi Profil Sekolah</h3>
            </div>
            <div class="card-body">
                <p class="text-sm text-muted mb-3">
                    Tuliskan bagian mana yang perlu diperbaiki oleh perwakilan sekolah (misal: koordinat peta, alamat detail, atau lampiran SK).
                </p>
                <div class="form-group">
                    <label for="rev_notes" class="form-label required">Instruksi Revisi</label>
                    <textarea name="notes" id="rev_notes" class="form-textarea" required placeholder="contoh: Mohon lengkapi NPSN resmi dan koordinat lintang/bujur sekolah..."></textarea>
                </div>
            </div>
            <div class="card-footer d-flex justify-end gap-2">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('revisionModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm">Kirim Instruksi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="modal-backdrop" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ route('admin.schools.reject', $school->id) }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 style="font-size: 16px; color: var(--error);">Tolak Verifikasi 3T</h3>
            </div>
            <div class="card-body">
                <p class="text-sm text-muted mb-3">
                    Penolakan dilakukan jika lokasi sekolah bukan berada pada wilayah 3T yang sah atau data terindikasi palsu.
                </p>
                <div class="form-group">
                    <label for="rej_reason" class="form-label required">Alasan Penolakan</label>
                    <textarea name="reason" id="rej_reason" class="form-textarea" required placeholder="contoh: Lokasi sekolah berada di wilayah perkotaan non-3T..."></textarea>
                </div>
            </div>
            <div class="card-footer d-flex justify-end gap-2">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('rejectModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-danger btn-sm">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>
@endsection
