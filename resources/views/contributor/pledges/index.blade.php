@extends('layouts.dashboard')

@section('title', 'Komitmen Bantuan Saya — Donatur SambungAsa')

@section('content')
<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <h1 style="font-size: 22px; font-weight: 700;">Daftar Komitmen Bantuan Saya</h1>
        <p class="text-sm text-muted">Pantau status verifikasi, pengiriman, dan riwayat penerimaan sekolah.</p>
    </div>
    <div>
        <a href="{{ route('public.needs.index') }}" class="btn btn-primary btn-sm">+ Beri Bantuan Baru</a>
    </div>
</div>

<div class="card mb-6">
    <div class="card-body py-3">
        <form action="{{ route('contributor.pledges.index') }}" method="GET" class="d-flex align-center flex-wrap gap-3">
            <div style="flex: 1; min-width: 220px;">
                <input type="text" name="search" class="form-control" placeholder="Cari kode tracking atau judul kebutuhan..." value="{{ request('search') }}">
            </div>

            <div style="width: 180px;">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Diajukan (Review)</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="in_delivery" {{ request('status') == 'in_delivery' ? 'selected' : '' }}>Dalam Pengiriman</option>
                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Diterima Sekolah</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <button type="submit" class="btn btn-dark btn-sm">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('contributor.pledges.index') }}" class="btn btn-outline btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Tracking</th>
                    <th>Kebutuhan & Sekolah 3T</th>
                    <th>Jumlah Disalurkan</th>
                    <th>Estimasi Waktu</th>
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
                            <div class="text-xs text-muted">{{ $p->created_at->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div class="font-bold text-dark">{{ $p->need->title }}</div>
                            <div class="text-xs text-muted">
                                🏫 {{ $p->need->schoolProfile->school_name }} ({{ $p->need->schoolProfile->regency?->name }})
                            </div>
                        </td>
                        <td>
                            <strong>{{ $p->quantity ?: '1' }}</strong> {{ $p->need->unit }}
                        </td>
                        <td class="text-sm text-muted">
                            {{ $p->estimated_delivery_at ? $p->estimated_delivery_at->format('d M Y') : '—' }}
                        </td>
                        <td>
                            @if($p->status === 'completed')
                                <span class="badge badge-success">Selesai</span>
                            @elseif($p->status === 'received')
                                <span class="badge badge-success">Diterima Sekolah</span>
                            @elseif($p->status === 'in_delivery')
                                <span class="badge badge-info">Dalam Pengiriman</span>
                            @elseif($p->status === 'verified')
                                <span class="badge badge-neutral">Diverifikasi Admin</span>
                            @elseif($p->status === 'submitted')
                                <span class="badge badge-warning">Menunggu Review</span>
                            @else
                                <span class="badge badge-danger">{{ $p->status_label }}</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('contributor.pledges.show', $p->id) }}" class="btn btn-outline btn-sm">Detail & Tracking →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-6">
                            Belum ada komitmen bantuan yang diajukan.
                            <div class="mt-2">
                                <a href="{{ route('public.needs.index') }}" class="btn btn-primary btn-sm">Mulai Memberi Bantuan</a>
                            </div>
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
