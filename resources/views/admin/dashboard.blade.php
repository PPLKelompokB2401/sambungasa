@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <h1 class="page-title">Ringkasan Sistem SambungAsa</h1>
        <p class="page-subtitle">Pusat kendali verifikasi sekolah 3T, moderasi kebutuhan, dan pemantauan bantuan.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline btn-sm">
            📈 Laporan & Ekspor
        </a>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-dark btn-sm">
            📝 Log Aktivitas
        </a>
    </div>
</div>

<!-- Key Stat Cards -->
<div class="stats-grid mb-4">
    <div class="stat-card" style="border-left: 4px solid var(--warning);">
        <div class="stat-label">Menunggu Verifikasi Sekolah</div>
        <div class="stat-value" style="color: var(--warning-dark);">{{ $stats['pending_schools'] }}</div>
        <div class="stat-meta">dari {{ $stats['total_schools'] }} sekolah terdaftar</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid var(--info);">
        <div class="stat-label">Moderasi Kebutuhan Pending</div>
        <div class="stat-value" style="color: var(--info-dark);">{{ $stats['pending_needs'] }}</div>
        <div class="stat-meta">{{ $stats['active_needs'] }} kebutuhan aktif tayang</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid var(--primary);">
        <div class="stat-label">Pledge Menunggu Review</div>
        <div class="stat-value" style="color: var(--primary);">{{ $stats['pending_pledges'] }}</div>
        <div class="stat-meta">{{ $stats['in_progress_pledges'] }} dalam penyaluran</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid var(--success);">
        <div class="stat-label">Bantuan Selesai Disalurkan</div>
        <div class="stat-value" style="color: var(--success-dark);">{{ $stats['completed_pledges'] }}</div>
        <div class="stat-meta">{{ $stats['fulfilled_needs'] }} kebutuhan terpenuhi 100%</div>
    </div>
</div>

<div class="grid grid-2 gap-4 mb-4">
    <!-- Queue 1: Verifikasi Sekolah 3T -->
    <div class="card">
        <div class="card-header d-flex justify-between align-center">
            <h2 class="card-title" style="font-size: 16px;">🏫 Antrean Verifikasi Sekolah 3T</h2>
            <a href="{{ route('admin.schools.index', ['status' => 'pending']) }}" class="text-sm font-semibold text-primary">Lihat Semua ({{ $stats['pending_schools'] }}) →</a>
        </div>
        <div class="card-body p-0">
            @if($pendingVerifications->isEmpty())
                <div class="empty-state p-4 text-center">
                    <p class="text-muted text-sm mb-0">Tidak ada pengajuan verifikasi sekolah yang menunggu review saat ini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Sekolah</th>
                                <th>Wilayah</th>
                                <th>Diajukan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingVerifications as $school)
                                <tr>
                                    <td>
                                        <div class="font-semibold">{{ $school->school_name }}</div>
                                        <div class="text-xs text-muted">NPSN: {{ $school->npsn }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $school->region->regency_name ?? '-' }}</div>
                                        <div class="text-xs text-muted">{{ $school->region->province_name ?? '-' }}</div>
                                    </td>
                                    <td class="text-xs text-muted">{{ $school->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('admin.schools.show', $school->id) }}" class="btn btn-primary btn-sm" style="padding: 4px 10px; font-size: 12px;">
                                            Periksa
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Queue 2: Moderasi Kebutuhan Baru -->
    <div class="card">
        <div class="card-header d-flex justify-between align-center">
            <h2 class="card-title" style="font-size: 16px;">📋 Antrean Moderasi Kebutuhan</h2>
            <a href="{{ route('admin.needs.index', ['status' => 'pending']) }}" class="text-sm font-semibold text-primary">Lihat Semua ({{ $stats['pending_needs'] }}) →</a>
        </div>
        <div class="card-body p-0">
            @if($pendingModerations->isEmpty())
                <div class="empty-state p-4 text-center">
                    <p class="text-muted text-sm mb-0">Semua kebutuhan telah dimoderasi. Belum ada antrean baru.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Judul Kebutuhan</th>
                                <th>Sekolah</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingModerations as $need)
                                <tr>
                                    <td>
                                        <div class="font-semibold">{{ Str::limit($need->title, 28) }}</div>
                                        <span class="badge {{ $need->priority === 'urgent' ? 'badge-danger' : 'badge-neutral' }}" style="font-size: 10px;">
                                            {{ ucfirst($need->priority) }}
                                        </span>
                                    </td>
                                    <td class="text-sm">{{ Str::limit($need->schoolProfile->school_name ?? '-', 20) }}</td>
                                    <td class="text-xs text-muted">{{ $need->category->name ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.needs.show', $need->id) }}" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 12px;">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-2 gap-4">
    <!-- Queue 3: Komitmen Bantuan Terbaru -->
    <div class="card">
        <div class="card-header d-flex justify-between align-center">
            <h2 class="card-title" style="font-size: 16px;">🤝 Komitmen Bantuan (Pledges) Terbaru</h2>
            <a href="{{ route('admin.pledges.index') }}" class="text-sm font-semibold text-primary">Kelola Semua →</a>
        </div>
        <div class="card-body p-0">
            @if($recentPledges->isEmpty())
                <div class="empty-state p-4 text-center">
                    <p class="text-muted text-sm mb-0">Belum ada komitmen bantuan yang diajukan.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode / Donatur</th>
                                <th>Kebutuhan & Sekolah</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPledges as $pledge)
                                <tr>
                                    <td>
                                        <div class="font-mono font-bold text-xs">{{ $pledge->tracking_code }}</div>
                                        <div class="text-xs text-muted">{{ $pledge->contributor->name ?? 'Anonim' }}</div>
                                    </td>
                                    <td>
                                        <div class="text-sm font-semibold">{{ Str::limit($pledge->need->title ?? '-', 22) }}</div>
                                        <div class="text-xs text-muted">{{ Str::limit($pledge->need->schoolProfile->school_name ?? '-', 22) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $pledge->status === 'submitted' ? 'badge-warning' : ($pledge->status === 'completed' ? 'badge-success' : 'badge-info') }}" style="font-size: 10px;">
                                            {{ $pledge->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.pledges.show', $pledge->id) }}" class="btn btn-outline btn-sm" style="padding: 4px 8px; font-size: 11px;">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Queue 4: Log Aktivitas Terbaru -->
    <div class="card">
        <div class="card-header d-flex justify-between align-center">
            <h2 class="card-title" style="font-size: 16px;">📝 Audit Log Aktivitas (F-24)</h2>
            <a href="{{ route('admin.activity-logs.index') }}" class="text-sm font-semibold text-primary">Lihat Seluruh Log →</a>
        </div>
        <div class="card-body p-0">
            @if($recentActivities->isEmpty())
                <div class="empty-state p-4 text-center">
                    <p class="text-muted text-sm mb-0">Belum ada aktivitas tercatat.</p>
                </div>
            @else
                <ul style="list-style: none; margin: 0; padding: 0; divide-y: 1px solid var(--border);">
                    @foreach($recentActivities as $log)
                        <li style="padding: 10px 16px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                            <div>
                                <span class="font-semibold">{{ $log->user->name ?? 'Sistem' }}</span>
                                <span class="text-muted"> {{ $log->description }}</span>
                            </div>
                            <span class="text-xs text-muted" style="white-space: nowrap; margin-left: 12px;">{{ $log->created_at->diffForHumans() }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
