@extends('layouts.dashboard')

@section('title', 'Kelola Komitmen Bantuan (Pledges) — Admin')

@section('content')
<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <h1 style="font-size: 22px; font-weight: 700;">Kelola Komitmen Bantuan (F-21)</h1>
        <p class="text-sm text-muted">Pantau, validasi pengajuan komitmen bantuan donatur, dan kelola transisi status bantuan.</p>
    </div>
</div>

<!-- Stats Counter -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card p-3">
        <div class="text-xs text-muted font-medium">TOTAL PLEDGE</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px;">{{ $stats['total'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--warning);">
        <div class="text-xs text-muted font-medium">MENUNGGU VERIFIKASI</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--warning);">{{ $stats['submitted'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--secondary);">
        <div class="text-xs text-muted font-medium">DALAM PENGIRIMAN</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--secondary);">{{ $stats['in_delivery'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--success);">
        <div class="text-xs text-muted font-medium">SELESAI TERKONFIRMASI</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--success);">{{ $stats['completed'] }}</div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-6">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.pledges.index') }}" class="d-flex align-center flex-wrap gap-3">
            <div style="flex: 1; min-width: 220px;">
                <input type="text" name="search" class="form-control" placeholder="Cari kode tracking, kebutuhan, atau donatur..." value="{{ request('search') }}">
            </div>

            <div style="width: 180px;">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Diajukan (Review)</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="in_delivery" {{ request('status') == 'in_delivery' ? 'selected' : '' }}>Dalam Pengiriman</option>
                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Diterima</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <button type="submit" class="btn btn-dark btn-sm">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.pledges.index') }}" class="btn btn-outline btn-sm">Reset</a>
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
                    <th>Kode Tracking</th>
                    <th>Kebutuhan & Sekolah 3T</th>
                    <th>Donatur / Mitra</th>
                    <th>Kuantitas</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pledges as $p)
                    <tr>
                        <td>
                            <div class="font-bold text-dark" style="font-family: var(--font-mono); font-size: 14px;">
                                {{ $p->tracking_code }}
                            </div>
                            <div class="text-xs text-muted">{{ $p->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td>
                            <div class="font-bold text-dark">{{ $p->need->title }}</div>
                            <div class="text-xs text-muted">
                                🏫 {{ $p->need->schoolProfile->school_name }} ({{ $p->need->schoolProfile->regency?->name }})
                            </div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $p->contributorProfile->display_name }}</div>
                            <div class="text-xs text-muted">{{ ucfirst($p->contributorProfile->contributor_type) }}</div>
                        </td>
                        <td>
                            <strong>{{ $p->quantity }}</strong> {{ $p->need->unit }}
                        </td>
                        <td>
                            @if($p->status === 'completed')
                                <span class="badge badge-success">Selesai</span>
                            @elseif($p->status === 'received')
                                <span class="badge badge-success">Diterima</span>
                            @elseif($p->status === 'in_delivery')
                                <span class="badge badge-info">Dalam Pengiriman</span>
                            @elseif($p->status === 'verified')
                                <span class="badge badge-neutral">Diverifikasi</span>
                            @elseif($p->status === 'submitted')
                                <span class="badge badge-warning">Menunggu Review</span>
                            @else
                                <span class="badge badge-danger">{{ $p->status_label }}</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('admin.pledges.show', $p->id) }}" class="btn btn-outline btn-sm">
                                Detail & Kelola Status
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-6">
                            Tidak ada komitmen bantuan yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pledges->hasPages())
        <div class="card-footer d-flex justify-between align-center">
            <div class="text-sm text-muted">
                Menampilkan {{ $pledges->firstItem() }} - {{ $pledges->lastItem() }} dari {{ $pledges->total() }} komitmen
            </div>
            <div>
                {{ $pledges->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
