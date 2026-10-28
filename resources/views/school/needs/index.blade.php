@extends('layouts.dashboard')

@section('title', 'Kebutuhan Sekolah — ' . $school->school_name)

@section('content')
<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <h1 style="font-size: 22px; font-weight: 700;">Daftar Kebutuhan Sekolah (F-05)</h1>
        <p class="text-sm text-muted">Kelola pengajuan kebutuhan material dan nonmaterial untuk {{ $school->school_name }}.</p>
    </div>
    @if($school->isVerified())
        <div>
            <a href="{{ route('school.needs.create') }}" class="btn btn-primary btn-sm">+ Ajukan Kebutuhan Baru</a>
        </div>
    @endif
</div>

@if(!$school->isVerified())
    <div class="alert alert-warning mb-6">
        <span>⚠</span>
        <div>
            Sekolah Anda saat ini berstatus <strong>{{ strtoupper($school->verification_status) }}</strong>. Anda belum dapat mempublikasikan kebutuhan baru sebelum disetujui verifikasi 3T oleh administrator.
        </div>
    </div>
@endif

<!-- Needs Table -->
<div class="card">
    <div class="card-header d-flex justify-between align-center flex-wrap gap-2">
        <form method="GET" action="{{ route('school.needs.index') }}" class="d-flex align-center gap-2" style="width: 100%; max-width: 480px;">
            <input type="text" name="search" class="form-control" placeholder="Cari judul kebutuhan..." value="{{ request('search') }}" style="height: 34px;">
            <select name="status" class="form-select" style="height: 34px; width: 140px;">
                <option value="">Semua Status</option>
                <option value="pending_review" {{ request('status') == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <button type="submit" class="btn btn-dark btn-sm">Cari</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Judul & Tanggal</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Kuantitas & Progres</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($needs as $need)
                    <tr>
                        <td>
                            <div class="font-bold text-dark">{{ $need->title }}</div>
                            <div class="text-xs text-muted">Diajukan: {{ $need->created_at->format('d M Y') }}</div>
                        </td>
                        <td>
                            <span class="badge badge-neutral">{{ $need->category->name }}</span>
                            <div class="text-xs text-muted mt-1">{{ ucfirst($need->assistance_type) }}</div>
                        </td>
                        <td>
                            @if($need->priority === 'mendesak')
                                <span class="badge badge-danger">Mendesak</span>
                            @elseif($need->priority === 'tinggi')
                                <span class="badge badge-warning">Tinggi</span>
                            @else
                                <span class="badge badge-neutral">{{ ucfirst($need->priority) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-xs font-bold">{{ $need->quantity_fulfilled }} / {{ $need->quantity_required ?? '—' }} {{ $need->unit }}</div>
                            <div class="progress-bar-wrap mt-1" style="width: 120px; height: 6px;">
                                <div class="progress-fill {{ $need->progress_percentage == 100 ? 'success' : '' }}" style="width: {{ $need->progress_percentage }}%;"></div>
                            </div>
                            <div class="text-xs text-muted mt-1">{{ $need->progress_percentage }}% terpenuhi</div>
                        </td>
                        <td>
                            @if($need->status === 'published')
                                <span class="badge badge-success">Dipublikasi</span>
                            @elseif($need->status === 'pending_review')
                                <span class="badge badge-warning">Menunggu Review</span>
                            @elseif($need->status === 'fulfilled')
                                <span class="badge badge-info">Terpenuhi</span>
                            @elseif($need->status === 'rejected')
                                <span class="badge badge-danger" title="{{ $need->rejection_notes }}">Ditolak</span>
                            @else
                                <span class="badge badge-neutral">Ditutup</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('public.needs.show', $need->id) }}" class="btn btn-outline btn-sm" target="_blank" title="Lihat Publik">Lihat</a>
                                <a href="{{ route('school.needs.edit', $need->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                @if($need->status === 'published')
                                    <form action="{{ route('school.needs.close', $need->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tutup kebutuhan ini agar tidak lagi menerima bantuan baru?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm text-warning" title="Tutup Kebutuhan">Tutup</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-6">
                            Belum ada kebutuhan yang diajukan.
                            @if($school->isVerified())
                                <div class="mt-2">
                                    <a href="{{ route('school.needs.create') }}" class="btn btn-primary btn-sm">+ Buat Kebutuhan Baru</a>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($needs->hasPages())
        <div class="card-footer d-flex justify-between align-center">
            <div class="text-sm text-muted">
                Menampilkan {{ $needs->firstItem() }} - {{ $needs->lastItem() }} dari {{ $needs->total() }} kebutuhan
            </div>
            <div>
                {{ $needs->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
