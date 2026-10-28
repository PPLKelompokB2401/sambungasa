@extends('layouts.dashboard')

@section('title', 'Moderasi Kebutuhan Sekolah — Admin')

@section('content')
<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <h1 style="font-size: 22px; font-weight: 700;">Moderasi Kebutuhan Sekolah (F-20)</h1>
        <p class="text-sm text-muted">Tinjau, setujui penerbitan ke publik, atau tolak pengajuan kebutuhan.</p>
    </div>
</div>

<!-- Stats Counter -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card p-3">
        <div class="text-xs text-muted font-medium">TOTAL KEBUTUHAN</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px;">{{ $stats['total'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--warning);">
        <div class="text-xs text-muted font-medium">MENUNGGU MODERASI</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--warning);">{{ $stats['pending_review'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--success);">
        <div class="text-xs text-muted font-medium">TERBIT DI PUBLIK</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--success);">{{ $stats['published'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--secondary);">
        <div class="text-xs text-muted font-medium">TERPENUHI LENGKAP</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--secondary);">{{ $stats['fulfilled'] }}</div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-6">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.needs.index') }}" class="d-flex align-center flex-wrap gap-3">
            <div style="flex: 1; min-width: 220px;">
                <input type="text" name="search" class="form-control" placeholder="Cari judul atau nama sekolah..." value="{{ request('search') }}">
            </div>

            <div style="width: 160px;">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending_review" {{ request('status') == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div style="width: 180px;">
                <select name="category_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="width: 140px;">
                <select name="priority" class="form-select">
                    <option value="">Semua Prioritas</option>
                    <option value="mendesak" {{ request('priority') == 'mendesak' ? 'selected' : '' }}>Mendesak</option>
                    <option value="tinggi" {{ request('priority') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                    <option value="sedang" {{ request('priority') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="rendah" {{ request('priority') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                </select>
            </div>

            <button type="submit" class="btn btn-dark btn-sm">Filter</button>
            @if(request()->hasAny(['search', 'status', 'category_id', 'priority']))
                <a href="{{ route('admin.needs.index') }}" class="btn btn-outline btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Judul Kebutuhan</th>
                    <th>Sekolah 3T</th>
                    <th>Kategori & Jenis</th>
                    <th>Prioritas</th>
                    <th>Target</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($needs as $need)
                    <tr>
                        <td>
                            <div class="font-bold text-dark">{{ $need->title }}</div>
                            <div class="text-xs text-muted">Diajukan: {{ $need->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $need->schoolProfile->school_name }}</div>
                            <div class="text-xs text-muted">{{ $need->schoolProfile->regency?->name ?? '3T' }} ({{ $need->schoolProfile->regency?->island }})</div>
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
                            <div class="text-sm font-bold">{{ $need->quantity_required ?? '—' }} {{ $need->unit }}</div>
                            <div class="text-xs text-muted">Terpenuhi: {{ $need->quantity_fulfilled }}</div>
                        </td>
                        <td>
                            @if($need->status === 'published')
                                <span class="badge badge-success">Terbit</span>
                            @elseif($need->status === 'pending_review')
                                <span class="badge badge-warning">Pending Review</span>
                            @elseif($need->status === 'rejected')
                                <span class="badge badge-danger">Ditolak</span>
                            @elseif($need->status === 'fulfilled')
                                <span class="badge badge-info">Terpenuhi</span>
                            @else
                                <span class="badge badge-neutral">Ditutup</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('admin.needs.show', $need->id) }}" class="btn btn-outline btn-sm">Tinjau & Moderasi</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-6">
                            Tidak ada pengajuan kebutuhan yang sesuai dengan filter.
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
