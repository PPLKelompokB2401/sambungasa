@extends('layouts.dashboard')

@section('title', 'Dashboard Sekolah: ' . $school->school_name)

@section('content')
<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <h1 style="font-size: 22px; font-weight: 700;">{{ $school->school_name }}</h1>
        <p class="text-sm text-muted">
            {{ $school->regency?->name ?? 'Wilayah 3T' }} • Status 3T: 
            <span class="badge {{ $school->isVerified() ? 'badge-success' : ($school->verification_status == 'needs_revision' ? 'badge-warning' : 'badge-neutral') }}">
                {{ $school->verification_status }}
            </span>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('school.profile.edit') }}" class="btn btn-outline btn-sm">Edit Profil Sekolah</a>
        @if($school->isVerified())
            <a href="{{ route('school.needs.create') }}" class="btn btn-primary btn-sm">+ Buat Kebutuhan Baru</a>
        @endif
    </div>
</div>

@if(!$school->isVerified())
    <div class="alert alert-warning mb-6">
        <span>⚠</span>
        <div>
            <strong>Status Verifikasi: {{ strtoupper($school->verification_status) }}</strong>
            <p class="mt-1 text-sm">
                @if($school->verification_status === 'pending')
                    Profil sekolah Anda sedang ditinjau oleh Administrator SambungAsa untuk verifikasi wilayah 3T. Anda baru dapat mempublikasikan kebutuhan sekolah setelah verifikasi disetujui.
                @elseif($school->verification_status === 'needs_revision')
                    Administrator meminta perbaikan data profil: <em>"{{ $school->verification_notes }}"</em>. Silakan perbarui data di <a href="{{ route('school.profile.edit') }}" style="text-decoration: underline; font-weight: 700;">Profil Sekolah</a>.
                @else
                    Verifikasi sekolah ditolak. Alasan: <em>"{{ $school->verification_notes }}"</em>.
                @endif
            </p>
        </div>
    </div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card p-3">
        <div class="text-xs text-muted font-medium">TOTAL KEBUTUHAN</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px;">{{ $stats['total_needs'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--secondary);">
        <div class="text-xs text-muted font-medium">AKTIF DI PUBLIK</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--secondary);">{{ $stats['published_needs'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--success);">
        <div class="text-xs text-muted font-medium">TERPENUHI LENGKAP</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--success);">{{ $stats['fulfilled_needs'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--primary);">
        <div class="text-xs text-muted font-medium">KOMITMEN BANTUAN MASUK</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--primary);">{{ $stats['total_pledges'] }}</div>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <!-- Needs list -->
    <div style="grid-column: span 2;" class="card">
        <div class="card-header d-flex justify-between align-center">
            <h3 style="font-size: 16px;">Kebutuhan Sekolah</h3>
            <a href="{{ route('school.needs.index') }}" class="text-xs font-medium">Lihat Semua →</a>
        </div>
        <div class="table-responsive">
            <table class="table text-sm">
                <thead>
                    <tr>
                        <th>Judul Kebutuhan</th>
                        <th>Kategori</th>
                        <th>Prioritas</th>
                        <th>Target & Progres</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($needs as $n)
                        <tr>
                            <td>
                                <div class="font-bold text-dark">{{ $n->title }}</div>
                                <div class="text-xs text-muted">{{ $n->created_at->format('d M Y') }}</div>
                            </td>
                            <td><span class="badge badge-neutral">{{ $n->category->name }}</span></td>
                            <td>
                                @if($n->priority === 'mendesak')
                                    <span class="badge badge-danger">Mendesak</span>
                                @elseif($n->priority === 'tinggi')
                                    <span class="badge badge-warning">Tinggi</span>
                                @else
                                    <span class="badge badge-neutral">{{ ucfirst($n->priority) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-xs font-medium">{{ $n->quantity_fulfilled }} / {{ $n->quantity_required ?? '—' }} {{ $n->unit }}</div>
                                <div class="progress-bar-wrap mt-1" style="height: 6px;">
                                    <div class="progress-fill {{ $n->progress_percentage == 100 ? 'success' : '' }}" style="width: {{ $n->progress_percentage }}%;"></div>
                                </div>
                            </td>
                            <td>
                                @if($n->status === 'published')
                                    <span class="badge badge-success">Terbit</span>
                                @elseif($n->status === 'pending_review')
                                    <span class="badge badge-warning">Review Admin</span>
                                @elseif($n->status === 'fulfilled')
                                    <span class="badge badge-info">Terpenuhi</span>
                                @else
                                    <span class="badge badge-neutral">{{ ucfirst($n->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada kebutuhan yang didaftarkan.
                                @if($school->isVerified())
                                    <div class="mt-2">
                                        <a href="{{ route('school.needs.create') }}" class="btn btn-primary btn-sm">+ Tambah Kebutuhan</a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pledges / Assistance Incoming -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-header d-flex justify-between align-center">
            <h3 style="font-size: 16px;">Bantuan Masuk</h3>
            <a href="{{ route('school.pledges.index') }}" class="text-xs font-medium">Tracking →</a>
        </div>
        <div class="card-body d-flex flex-column gap-3">
            @forelse($pledges as $p)
                <div class="p-3" style="border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--surface);">
                    <div class="d-flex justify-between align-center">
                        <span class="font-bold text-xs" style="font-family: var(--font-mono);">{{ $p->tracking_code }}</span>
                        <span class="badge badge-pill {{ $p->status == 'completed' ? 'badge-success' : 'badge-info' }}" style="font-size: 9px;">
                            {{ $p->status_label }}
                        </span>
                    </div>
                    <div class="text-sm font-medium mt-1">{{ $p->contributorProfile->display_name }}</div>
                    <div class="text-xs text-muted mt-1">{{ $p->quantity }} {{ $p->need->unit }} • {{ Str::limit($p->need->title, 32) }}</div>
                    <div class="mt-2 text-right" style="text-align: right;">
                        <a href="{{ route('school.pledges.show', $p->id) }}" class="btn btn-outline btn-sm" style="height: 26px; padding: 0 10px; font-size: 11px;">Lihat Tracking</a>
                    </div>
                </div>
            @empty
                <div class="text-sm text-muted text-center py-3">Belum ada bantuan yang diajukan donatur.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
