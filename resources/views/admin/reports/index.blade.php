@extends('layouts.dashboard')

@section('title', 'Laporan & Ekspor Data')

@section('content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <h1 class="page-title">Laporan Penyaluran Bantuan</h1>
        <p class="page-subtitle">Analisis ketercapaian bantuan pendidikan 3T dan ekspor data CSV terverifikasi.</p>
    </div>
    <div>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn btn-primary btn-sm">
            📥 Unduh Laporan CSV
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="d-flex flex-wrap gap-3 align-end">
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label text-xs">Status Penyaluran</label>
                <select name="status" class="form-control text-sm">
                    <option value="">Semua Status</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Disetujui Admin</option>
                    <option value="in_delivery" {{ request('status') === 'in_delivery' ? 'selected' : '' }}>Dalam Pengiriman</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Diterima Sekolah</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai Terverifikasi</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div style="flex: 1; min-width: 140px;">
                <label class="form-label text-xs">Jenis Bantuan</label>
                <select name="assistance_type" class="form-control text-sm">
                    <option value="">Semua Jenis</option>
                    <option value="material" {{ request('assistance_type') === 'material' ? 'selected' : '' }}>Barang / Material</option>
                    <option value="non_material" {{ request('assistance_type') === 'non_material' ? 'selected' : '' }}>Tenaga / Relawan</option>
                </select>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label class="form-label text-xs">Kategori Kebutuhan</label>
                <select name="category_id" class="form-control text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 140px;">
                <label class="form-label text-xs">Provinsi</label>
                <select name="province" class="form-control text-sm">
                    <option value="">Semua Provinsi</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov }}" {{ request('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 130px;">
                <label class="form-label text-xs">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control text-sm" value="{{ request('date_from') }}">
            </div>

            <div style="flex: 1; min-width: 130px;">
                <label class="form-label text-xs">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control text-sm" value="{{ request('date_to') }}">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark btn-sm">Filter</button>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Aggregated Metrics for Filter Result -->
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-label">Total Komitmen Terfilter</div>
        <div class="stat-value">{{ $totalPledgesCount }}</div>
        <div class="stat-meta">komitmen bantuan</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid var(--success);">
        <div class="stat-label">Bantuan Selesai (100%)</div>
        <div class="stat-value" style="color: var(--success-dark);">{{ $completedPledgesCount }}</div>
        <div class="stat-meta">{{ $totalPledgesCount > 0 ? round(($completedPledgesCount / $totalPledgesCount) * 100) : 0 }}% tingkat keberhasilan</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid var(--info);">
        <div class="stat-label">Sedang Dalam Perjalanan</div>
        <div class="stat-value" style="color: var(--info-dark);">{{ $inTransitCount }}</div>
        <div class="stat-meta">pengiriman aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Fisik Barang</div>
        <div class="stat-value">{{ number_format($totalItemsCommitted) }}</div>
        <div class="stat-meta">unit barang disalurkan</div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header d-flex justify-between align-center">
        <h2 class="card-title" style="font-size: 16px;">Rincian Data Penyaluran ({{ $pledges->total() }} entri)</h2>
    </div>
    <div class="card-body p-0">
        @if($pledges->isEmpty())
            <div class="empty-state p-5 text-center">
                <p class="text-muted mb-0">Tidak ada data komitmen yang sesuai dengan kriteria filter.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode Tracking</th>
                            <th>Tanggal</th>
                            <th>Donatur / Mitra</th>
                            <th>Sekolah Penerima</th>
                            <th>Kebutuhan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pledges as $p)
                            <tr>
                                <td>
                                    <div class="font-mono font-bold text-xs">{{ $p->tracking_code }}</div>
                                    <span class="badge {{ ($p->need->assistance_type ?? 'material') === 'non_material' ? 'badge-info' : 'badge-neutral' }}" style="font-size: 10px;">
                                        {{ ($p->need->assistance_type ?? 'material') === 'non_material' ? 'Relawan' : 'Barang' }}
                                    </span>
                                </td>
                                <td class="text-xs text-muted">{{ $p->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="font-semibold text-sm">
                                        {{ $p->contributorProfile->user->name ?? 'Donatur' }}
                                    </div>
                                    <div class="text-xs text-muted">{{ $p->contributorProfile->user->email ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="font-semibold text-sm">{{ $p->need->schoolProfile->school_name ?? '-' }}</div>
                                    <div class="text-xs text-muted">
                                        {{ $p->need->schoolProfile->regency->name ?? '' }}, {{ $p->need->schoolProfile->province->name ?? ($p->need->schoolProfile->regency->island ?? '') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">{{ Str::limit($p->need->title ?? '-', 24) }}</div>
                                    <div class="text-xs text-muted">{{ $p->need->category->name ?? '-' }}</div>
                                </td>
                                <td class="font-bold text-sm">
                                    {{ $p->quantity }} <span class="text-xs font-normal text-muted">{{ $p->need->unit ?? 'unit' }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $p->status === 'completed' ? 'badge-success' : ($p->status === 'in_delivery' ? 'badge-info' : ($p->status === 'submitted' ? 'badge-warning' : 'badge-neutral')) }}" style="font-size: 10px;">
                                        {{ $p->status_label ?? $p->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.pledges.show', $p->id) }}" class="btn btn-outline btn-sm" style="padding: 4px 8px; font-size: 11px;">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top">
                {{ $pledges->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
