@extends('layouts.app')

@section('title', 'Transparansi & Statistik Dampak')
@section('meta_description', 'Pantau metrik dan transparansi penyaluran bantuan pendidikan sekolah 3T di Papua, Kalimantan, dan Sulawesi secara nyata dan terverifikasi.')

@section('content')
<div class="page-header py-5" style="background: linear-gradient(180deg, #FFFFFF 0%, var(--surface-alt) 100%); border-bottom: 1px solid var(--border);">
    <div class="container text-center">
        <span class="badge badge-primary mb-2">Transparansi Data Nyata</span>
        <h1 class="page-title mb-2" style="font-size: 32px;">Statistik & Capaian Bantuan 3T</h1>
        <p class="page-subtitle max-w-700 mx-auto" style="margin: 0 auto; max-width: 650px; font-size: 15px;">
            Setiap angka pada laman ini dihimpun langsung dari verifikasi data sekolah dan bukti penyaluran yang telah divalidasi oleh tim kurasi SambungAsa.
        </p>
    </div>
</div>

<div class="container py-5">
    <!-- Highlight Stat Cards -->
    <div class="stats-grid mb-5">
        <div class="stat-card" style="border-top: 4px solid var(--primary);">
            <div class="stat-label">Sekolah 3T Terverifikasi</div>
            <div class="stat-value" style="color: var(--primary);">{{ number_format($totalVerifiedSchools) }}</div>
            <div class="stat-meta">di {{ $total3TRegencies }} Kabupaten Wilayah 3T</div>
        </div>
        <div class="stat-card" style="border-top: 4px solid var(--success);">
            <div class="stat-label">Kebutuhan Selesai Dipenuhi</div>
            <div class="stat-value" style="color: var(--success-dark);">{{ number_format($totalFulfilledNeeds) }}</div>
            <div class="stat-meta">{{ number_format($totalActiveNeeds) }} kebutuhan masih membuka bantuan</div>
        </div>
        <div class="stat-card" style="border-top: 4px solid var(--info);">
            <div class="stat-label">Penyaluran Berhasil Selesai</div>
            <div class="stat-value" style="color: var(--info-dark);">{{ number_format($totalCompletedPledges) }}</div>
            <div class="stat-meta">dengan konfirmasi penerimaan sekolah</div>
        </div>
        <div class="stat-card" style="border-top: 4px solid var(--warning);">
            <div class="stat-label">Sedang Dalam Penyaluran</div>
            <div class="stat-value" style="color: var(--warning-dark);">{{ number_format($totalActivePledges) }}</div>
            <div class="stat-meta">dalam pengiriman atau persiapan relawan</div>
        </div>
    </div>

    <!-- Category & Region Breakdown Grid -->
    <div class="grid grid-2 gap-4 mb-5">
        <!-- Distribution by Category -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title" style="font-size: 18px;">📦 Distribusi Berdasarkan Kategori Kebutuhan</h2>
                <p class="text-xs text-muted mb-0">Realisasi pemenuhan kebutuhan berdasarkan kategori spesifik.</p>
            </div>
            <div class="card-body">
                @if($categoryBreakdown->isEmpty())
                    <p class="text-muted text-sm">Belum ada data kategori.</p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($categoryBreakdown as $cat)
                            @php
                                $total = $cat->total_needs ?: 1;
                                $percentage = round(($cat->fulfilled_needs / $total) * 100);
                            @endphp
                            <div>
                                <div class="d-flex justify-between align-center mb-1 text-sm">
                                    <span class="font-semibold">{{ $cat->name }}</span>
                                    <span class="text-muted">
                                        <strong style="color: var(--success-dark);">{{ $cat->fulfilled_needs }} terpenuhi</strong> / {{ $cat->total_needs }} total
                                    </span>
                                </div>
                                <div style="background: var(--surface-alt); height: 8px; border-radius: 4px; overflow: hidden;">
                                    <div style="background: var(--primary); height: 100%; width: {{ min(100, $percentage) }}%; border-radius: 4px; transition: width 0.3s ease;"></div>
                                </div>
                                <div class="text-right text-xs text-muted mt-1">{{ $percentage }}% terpenuhi</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Distribution by Province -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title" style="font-size: 18px;">🗺️ Sebaran Sekolah 3T Terverifikasi</h2>
                <p class="text-xs text-muted mb-0">Jumlah sekolah mitra per provinsi sasaran.</p>
            </div>
            <div class="card-body">
                @if($provinceDistribution->isEmpty())
                    <p class="text-muted text-sm">Belum ada sekolah terverifikasi di provinsi sasaran.</p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Provinsi</th>
                                    <th style="text-align: right;">Sekolah Terverifikasi</th>
                                    <th>Porsi Wilayah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $maxSchools = max($provinceDistribution->pluck('total_schools')->max(), 1); @endphp
                                @foreach($provinceDistribution as $prov)
                                    <tr>
                                        <td class="font-semibold text-sm">{{ $prov->province_name }}</td>
                                        <td style="text-align: right;" class="font-bold">{{ $prov->total_schools }}</td>
                                        <td style="width: 40%;">
                                            <div style="background: var(--surface-alt); height: 6px; border-radius: 3px; overflow: hidden;">
                                                <div style="background: var(--info); height: 100%; width: {{ round(($prov->total_schools / $maxSchools) * 100) }}%;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="mt-3 p-3" style="background: var(--surface-alt); border-radius: 8px; font-size: 13px;">
                    <div class="font-semibold mb-1">Jenis Bantuan Yang Telah Diterima:</div>
                    <div class="d-flex gap-4 text-xs">
                        <div>📦 <strong>{{ $itemTypePledges }}</strong> Pengiriman Barang Fisik</div>
                        <div>👥 <strong>{{ $volunteerTypePledges }}</strong> Misi Relawan / Pengajar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Completed Assistance Feed -->
    <div class="card">
        <div class="card-header d-flex justify-between align-center">
            <div>
                <h2 class="card-title" style="font-size: 18px;">✅ Bukti Penyaluran Selesai Terbaru</h2>
                <p class="text-xs text-muted mb-0">Daftar bantuan yang telah sampai di lokasi dan diverifikasi.</p>
            </div>
            <a href="{{ route('public.needs.index', ['status' => 'fulfilled']) }}" class="btn btn-outline btn-sm">
                Lihat Semua Selesai →
            </a>
        </div>
        <div class="card-body">
            @if($recentCompleted->isEmpty())
                <div class="empty-state text-center p-4">
                    <p class="text-muted text-sm mb-0">Belum ada bantuan yang berstatus selesai saat ini.</p>
                </div>
            @else
                <div class="grid grid-3 gap-3">
                    @foreach($recentCompleted as $p)
                        <div style="border: 1px solid var(--border); border-radius: 8px; padding: 14px; background: #FFF;">
                            <div class="d-flex justify-between align-center mb-2">
                                <span class="badge badge-success" style="font-size: 10px;">SELESAI DITERIMA</span>
                                <span class="font-mono text-xs text-muted">{{ $p->tracking_code }}</span>
                            </div>
                            <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 4px; line-height: 1.3;">
                                {{ Str::limit($p->need->title ?? '-', 45) }}
                            </h3>
                            <div class="text-xs text-muted mb-2">
                                🏫 {{ $p->need->schoolProfile->school_name ?? '-' }} ({{ $p->need->schoolProfile->regency->name ?? ($p->need->schoolProfile->province->name ?? '-') }})
                            </div>
                            <div style="font-size: 12px; background: var(--surface-alt); padding: 6px 10px; border-radius: 6px;">
                                <strong>Jumlah:</strong> {{ $p->quantity }} {{ $p->need->unit ?? 'unit' }}
                                <br>
                                <span class="text-muted">Oleh: {{ $p->contributorProfile->user->name ?? 'Donatur SambungAsa' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
