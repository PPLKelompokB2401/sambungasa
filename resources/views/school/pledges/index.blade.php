@extends('layouts.dashboard')

@section('title', 'Bantuan Masuk & Tracking — ' . $school->school_name)

@section('content')
<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <h1 style="font-size: 22px; font-weight: 700;">Bantuan Masuk & Tracking (F-14)</h1>
        <p class="text-sm text-muted">Daftar bantuan yang dikomitmenkan oleh para donatur dan relawan untuk sekolah Anda.</p>
    </div>
</div>

<div class="card mb-6">
    <div class="card-body py-3">
        <form action="{{ route('school.pledges.index') }}" method="GET" class="d-flex align-center flex-wrap gap-3">
            <div style="flex: 1; min-width: 220px;">
                <input type="text" name="search" class="form-control" placeholder="Cari kode tracking atau judul kebutuhan..." value="{{ request('search') }}">
            </div>

            <div style="width: 180px;">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Diverifikasi Admin</option>
                    <option value="in_delivery" {{ request('status') == 'in_delivery' ? 'selected' : '' }}>Dalam Pengiriman</option>
                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Diterima Sekolah</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <button type="submit" class="btn btn-dark btn-sm">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('school.pledges.index') }}" class="btn btn-outline btn-sm">Reset</a>
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
                    <th>Kebutuhan</th>
                    <th>Donatur / Relawan</th>
                    <th>Jumlah</th>
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
                            <div class="font-medium text-dark">{{ $p->need->title }}</div>
                            <div class="text-xs text-muted">{{ $p->need->category->name }}</div>
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
                                <span class="badge badge-success">Diterima Sekolah</span>
                            @elseif($p->status === 'in_delivery')
                                <span class="badge badge-info">Dalam Pengiriman</span>
                            @elseif($p->status === 'verified')
                                <span class="badge badge-neutral">Diverifikasi Admin</span>
                            @else
                                <span class="badge badge-warning">{{ $p->status_label }}</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('school.pledges.show', $p->id) }}" class="btn btn-outline btn-sm">
                                Periksa & Konfirmasi
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-6">
                            Belum ada komitmen bantuan yang terhubung dengan kebutuhan sekolah Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pledges->hasPages())
        <div class="card-footer d-flex justify-between align-center">
            <div class="text-sm text-muted">
                Menampilkan {{ $pledges->firstItem() }} - {{ $pledges->lastItem() }} dari {{ $pledges->total() }} bantuan
            </div>
            <div>
                {{ $pledges->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
