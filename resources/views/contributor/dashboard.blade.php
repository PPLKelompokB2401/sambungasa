@extends('layouts.dashboard')

@section('title', 'Dashboard Donatur & Mitra')

@section('content')
<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <h1 style="font-size: 22px; font-weight: 700;">Selamat Datang, {{ $user->name }}</h1>
        <p class="text-sm text-muted">Pantau status komitmen bantuan Anda untuk sekolah 3T di Indonesia.</p>
    </div>
    <div>
        <a href="{{ route('public.needs.index') }}" class="btn btn-primary btn-sm">+ Berikan Bantuan Baru</a>
    </div>
</div>

<!-- Metrics Cards -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card p-3">
        <div class="text-xs text-muted font-medium">TOTAL PLEDGE</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px;">{{ $stats['total_pledges'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--warning);">
        <div class="text-xs text-muted font-medium">SEDANG BERJALAN</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--warning);">{{ $stats['active_pledges'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--success);">
        <div class="text-xs text-muted font-medium">BANTUAN SELESAI</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--success);">{{ $stats['completed_pledges'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--primary);">
        <div class="text-xs text-muted font-medium">BARANG TERSALURKAN</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--primary);">{{ $stats['items_delivered'] }} unit/eksemplar</div>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <!-- Active / Recent Pledges -->
    <div style="grid-column: span 2;" class="card">
        <div class="card-header d-flex justify-between align-center">
            <h3 style="font-size: 16px;">Komitmen Bantuan Terakhir</h3>
            <a href="{{ route('contributor.pledges.index') }}" class="text-xs font-medium">Lihat Semua →</a>
        </div>
        <div class="table-responsive">
            <table class="table text-sm">
                <thead>
                    <tr>
                        <th>Kode & Kebutuhan</th>
                        <th>Sekolah 3T</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pledges as $p)
                        <tr>
                            <td>
                                <div class="font-bold text-dark" style="font-family: var(--font-mono);">{{ $p->tracking_code }}</div>
                                <div class="text-xs text-muted">{{ Str::limit($p->need->title, 40) }}</div>
                            </td>
                            <td>
                                <div class="font-medium">{{ $p->need->schoolProfile->school_name }}</div>
                                <div class="text-xs text-muted">{{ $p->need->schoolProfile->regency?->name ?? '3T' }}</div>
                            </td>
                            <td>
                                <strong>{{ $p->quantity ?: '1' }}</strong> {{ $p->need->unit ?: 'item' }}
                            </td>
                            <td>
                                @if($p->status === 'completed')
                                    <span class="badge badge-success">Selesai</span>
                                @elseif($p->status === 'in_delivery')
                                    <span class="badge badge-info">Dalam Pengiriman</span>
                                @elseif($p->status === 'verified')
                                    <span class="badge badge-neutral">Diverifikasi</span>
                                @elseif($p->status === 'submitted')
                                    <span class="badge badge-warning">Diajukan</span>
                                @elseif($p->status === 'received')
                                    <span class="badge badge-success">Diterima Sekolah</span>
                                @else
                                    <span class="badge badge-danger">{{ ucfirst($p->status) }}</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('contributor.pledges.show', $p->id) }}" class="btn btn-outline btn-sm">Tracking</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Anda belum memiliki komitmen bantuan aktif.
                                <div class="mt-2">
                                    <a href="{{ route('public.needs.index') }}" class="btn btn-primary btn-sm">Mulai Beri Bantuan</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recommended Urgent Needs -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-header">
            <h3 style="font-size: 16px;">Kebutuhan Mendesak 3T</h3>
        </div>
        <div class="card-body d-flex flex-column gap-3">
            @forelse($urgentNeeds as $un)
                <div class="p-3" style="border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--surface);">
                    <div class="d-flex justify-between align-center mb-1">
                        <span class="badge badge-danger" style="font-size: 9px;">{{ $un->priority }}</span>
                        <span class="text-xs text-muted">{{ $un->schoolProfile->regency?->island }}</span>
                    </div>
                    <div class="font-bold text-sm text-dark mt-1">
                        <a href="{{ route('public.needs.show', $un->id) }}" style="color: inherit;">{{ Str::limit($un->title, 48) }}</a>
                    </div>
                    <div class="text-xs text-muted mt-1">{{ $un->schoolProfile->school_name }}</div>
                    <div class="mt-2 text-xs">
                        Sisa: <strong>{{ $un->getRemainingQuantity() }} {{ $un->unit }}</strong> lagi
                    </div>
                </div>
            @empty
                <div class="text-muted text-sm">Tidak ada kebutuhan mendesak saat ini.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
