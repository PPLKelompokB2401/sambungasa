@extends('layouts.app')

@section('title', 'Transparansi & Statistik Dampak')
@section('meta_description', 'Pantau metrik dan transparansi penyaluran bantuan pendidikan sekolah 3T di Papua, Kalimantan, dan Sulawesi secara nyata dan terverifikasi.')

@section('content')
<!-- Hero Header Section with Red Broadcast Aesthetic -->
<div class="page-header py-6" style="background: linear-gradient(180deg, #FFFFFF 0%, #F8F9FA 100%); border-bottom: 1px solid var(--border);">
    <div class="container text-center">
        <div class="d-inline-flex align-center gap-2 mb-3" style="background: #FFF1F0; border: 1px solid #FFA39E; padding: 4px 14px; border-radius: var(--radius-pill);">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--primary); display: inline-block; box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.2);"></span>
            <span style="font-size: 12px; font-weight: 700; color: var(--primary); letter-spacing: 0.05em; text-transform: uppercase;">
                Transparansi Data Nyata (Live)
            </span>
        </div>
        <h1 class="page-title mb-3" style="font-size: 36px; font-weight: 700; letter-spacing: -0.02em; color: var(--text-primary);">
            Statistik & Capaian Bantuan 3T
        </h1>
        <p class="page-subtitle mx-auto text-muted mb-4" style="max-width: 680px; font-size: 15px; line-height: 1.6;">
            Setiap data pada dashboard ini dihimpun langsung secara *real-time* dari verifikasi data pokok pendidikan (Dapodik), pelacakan nomor kargo resmi, dan Berita Acara Serah Terima (BAST) yang divalidasi oleh kurator SambungAsa.
        </p>
        <div class="d-flex justify-center flex-wrap gap-2 text-xs">
            <span class="chip" style="cursor: default;">Wilayah Prioritas: Papua, Kalimantan, Sulawesi</span>
            <span class="chip" style="cursor: default;">Diaudit: Tim Verifikator SambungAsa</span>
            <span class="chip" style="cursor: default;">Diperbarui: {{ now()->translatedFormat('d F Y, H:i') }} WITA</span>
        </div>
    </div>
</div>

<div class="container py-6">
    <!-- Highlight Stat Cards Grid -->
    <div class="stats-grid mb-6">
        <!-- 1. Verified Schools -->
        <div class="stat-card" style="border-top: 4px solid var(--primary);">
            <div class="stat-card-header">
                <span class="stat-label">Sekolah 3T Terverifikasi</span>
                <div class="stat-icon primary">🏫</div>
            </div>
            <div class="stat-value" style="color: var(--primary);">
                {{ number_format($totalVerifiedSchools) }}
            </div>
            <div class="stat-meta">
                <span style="display: inline-flex; align-items: center; gap: 4px;">
                    📍 Tersebar di <strong>{{ $total3TRegencies }}</strong> Kabupaten 3T
                </span>
            </div>
        </div>

        <!-- 2. Fulfilled Needs -->
        <div class="stat-card" style="border-top: 4px solid var(--success);">
            <div class="stat-card-header">
                <span class="stat-label">Kebutuhan Selesai Dipenuhi</span>
                <div class="stat-icon success">🎯</div>
            </div>
            <div class="stat-value" style="color: var(--success);">
                {{ number_format($totalFulfilledNeeds) }}
            </div>
            <div class="stat-meta">
                <span style="color: var(--text-secondary);">
                    ⏳ <strong>{{ number_format($totalActiveNeeds) }}</strong> kebutuhan aktif membuka bantuan
                </span>
            </div>
        </div>

        <!-- 3. Completed Pledges -->
        <div class="stat-card" style="border-top: 4px solid var(--info);">
            <div class="stat-card-header">
                <span class="stat-label">Penyaluran Berhasil Sampai</span>
                <div class="stat-icon info">📦</div>
            </div>
            <div class="stat-value" style="color: var(--info);">
                {{ number_format($totalCompletedPledges) }}
            </div>
            <div class="stat-meta">
                <span style="color: var(--text-secondary);">
                    ✅ Terkonfirmasi Berita Acara (BAST)
                </span>
            </div>
        </div>

        <!-- 4. Active Pledges in Delivery -->
        <div class="stat-card" style="border-top: 4px solid var(--warning);">
            <div class="stat-card-header">
                <span class="stat-label">Sedang Dalam Penyaluran</span>
                <div class="stat-icon warning">🚚</div>
            </div>
            <div class="stat-value" style="color: var(--warning);">
                {{ number_format($totalActivePledges) }}
            </div>
            <div class="stat-meta">
                <span style="color: var(--text-secondary);">
                    🌐 Dalam pengiriman / persiapan relawan
                </span>
            </div>
        </div>
    </div>

    <!-- Category & Region Breakdown Grid -->
    <div class="grid grid-2 gap-6 mb-6">
        <!-- Distribution by Category -->
        <div class="card" style="box-shadow: var(--elevation-1);">
            <div class="card-header d-flex justify-between align-center">
                <div>
                    <h2 class="card-title" style="font-size: 17px; font-weight: 700; margin: 0;">
                        📚 Distribusi Berdasarkan Kategori Kebutuhan
                    </h2>
                    <p class="text-xs text-muted mb-0 mt-1">Realisasi pemenuhan kebutuhan berdasarkan kategori spesifik.</p>
                </div>
                <span class="badge badge-neutral">{{ $categoryBreakdown->count() }} Kategori</span>
            </div>
            <div class="card-body">
                @if($categoryBreakdown->isEmpty())
                    <div class="text-center py-6 text-muted">
                        <div style="font-size: 32px; margin-bottom: 8px;">📊</div>
                        <p class="text-sm mb-0">Belum ada data kategori kebutuhan yang terdaftar.</p>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        @foreach($categoryBreakdown as $cat)
                            @php
                                $total = $cat->total_needs ?: 1;
                                $percentage = round(($cat->fulfilled_needs / $total) * 100);
                            @endphp
                            <div>
                                <div class="d-flex justify-between align-center mb-1 text-sm">
                                    <span class="font-semibold text-dark">{{ $cat->name }}</span>
                                    <span class="text-xs">
                                        <strong style="color: var(--success);">{{ $cat->fulfilled_needs }} terpenuhi</strong>
                                        <span class="text-muted">/ {{ $cat->total_needs }} diajukan</span>
                                    </span>
                                </div>
                                <div style="background: var(--surface-alt); height: 10px; border-radius: var(--radius-pill); overflow: hidden; position: relative;">
                                    <div style="background: linear-gradient(90deg, var(--primary) 0%, #FF4D4F 100%); height: 100%; width: {{ max(4, min(100, $percentage)) }}%; border-radius: var(--radius-pill); transition: width 0.4s ease;"></div>
                                </div>
                                <div class="d-flex justify-between align-center text-xs text-muted mt-1">
                                    <span>Tingkat pemenuhan</span>
                                    <span class="font-mono font-bold" style="color: {{ $percentage >= 100 ? 'var(--success)' : 'var(--text-primary)' }};">
                                        {{ $percentage }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Distribution by Province -->
        <div class="card" style="box-shadow: var(--elevation-1);">
            <div class="card-header d-flex justify-between align-center">
                <div>
                    <h2 class="card-title" style="font-size: 17px; font-weight: 700; margin: 0;">
                        🗺️ Sebaran Sekolah 3T Terverifikasi
                    </h2>
                    <p class="text-xs text-muted mb-0 mt-1">Jumlah sekolah sasaran per provinsi yang telah divalidasi.</p>
                </div>
                <a href="{{ route('public.regions.index') }}" class="btn btn-outline btn-sm" style="font-size: 11px; height: 28px; padding: 0 10px;">
                    Daftar Wilayah →
                </a>
            </div>
            <div class="card-body">
                @if($provinceDistribution->isEmpty())
                    <div class="text-center py-6 text-muted">
                        <div style="font-size: 32px; margin-bottom: 8px;">🌏</div>
                        <p class="text-sm mb-0">Belum ada sekolah terverifikasi di provinsi sasaran saat ini.</p>
                    </div>
                @else
                    <div class="table-responsive mb-4">
                        <table class="table" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>Provinsi Sasaran</th>
                                    <th style="text-align: right;">Sekolah Mitra</th>
                                    <th style="width: 35%;">Proporsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $maxSchools = max($provinceDistribution->pluck('total_schools')->max(), 1); @endphp
                                @foreach($provinceDistribution as $prov)
                                    <tr>
                                        <td>
                                            <div class="font-semibold text-sm">{{ $prov->province_name }}</div>
                                        </td>
                                        <td style="text-align: right;" class="font-mono font-bold text-sm">
                                            {{ $prov->total_schools }}
                                        </td>
                                        <td>
                                            <div style="background: var(--surface-alt); height: 8px; border-radius: var(--radius-pill); overflow: hidden;">
                                                <div style="background: var(--info); height: 100%; width: {{ max(10, round(($prov->total_schools / $maxSchools) * 100)) }}%; border-radius: var(--radius-pill);"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="p-3" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm);">
                    <div class="text-xs font-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.05em;">
                        KOMPOSISI BANTUAN YANG DISALURKAN
                    </div>
                    <div class="d-flex gap-4 flex-wrap">
                        <div class="d-flex align-center gap-2">
                            <span style="width: 28px; height: 28px; border-radius: 50%; background: rgba(6, 95, 212, 0.1); display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">📦</span>
                            <div>
                                <div class="font-bold text-sm">{{ $itemTypePledges }} Komitmen</div>
                                <div class="text-xs text-muted">Pengiriman Bantuan Fisik</div>
                            </div>
                        </div>
                        <div class="d-flex align-center gap-2">
                            <span style="width: 28px; height: 28px; border-radius: 50%; background: rgba(255, 0, 0, 0.1); display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">👥</span>
                            <div>
                                <div class="font-bold text-sm">{{ $volunteerTypePledges }} Misi</div>
                                <div class="text-xs text-muted">Relawan & Pelatihan Pengajar</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Completed Assistance Feed (Live Transparency) -->
    <div class="card mb-6" style="box-shadow: var(--elevation-1);">
        <div class="card-header d-flex justify-between align-center flex-wrap gap-2">
            <div>
                <h2 class="card-title" style="font-size: 18px; font-weight: 700; margin: 0;">
                    ✅ Bukti Penyaluran Selesai Terbaru
                </h2>
                <p class="text-xs text-muted mb-0 mt-1">Daftar bantuan yang telah sampai di sekolah tujuan dan diverifikasi oleh admin.</p>
            </div>
            <a href="{{ route('public.needs.index', ['status' => 'fulfilled']) }}" class="btn btn-outline btn-sm">
                Lihat Semua Selesai →
            </a>
        </div>
        <div class="card-body">
            @if($recentCompleted->isEmpty())
                <div class="text-center py-6">
                    <div style="font-size: 36px; margin-bottom: 8px;">📭</div>
                    <h3 style="font-size: 16px; font-weight: 600;" class="mb-1">Belum Ada Bantuan Berstatus Selesai</h3>
                    <p class="text-muted text-sm mb-3">Bantuan yang sedang dalam pengiriman akan muncul di sini setelah sekolah mengonfirmasi penerimaan.</p>
                    <a href="{{ route('public.needs.index') }}" class="btn btn-primary btn-sm">
                        Jelajahi Kebutuhan Sekolah 3T
                    </a>
                </div>
            @else
                <div class="grid grid-3 gap-4">
                    @foreach($recentCompleted as $p)
                        <div style="border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 16px; background: #FFFFFF; display: flex; flex-direction: column; transition: transform 0.15s ease, box-shadow 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--elevation-1)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                            <div class="d-flex justify-between align-center mb-2">
                                <span class="badge badge-success" style="font-size: 10px; padding: 2px 8px;">
                                    ✓ SELESAI DITERIMA
                                </span>
                                <span class="font-mono text-xs text-muted" title="Kode Pelacakan">{{ $p->tracking_code }}</span>
                            </div>
                            <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 6px; line-height: 1.4; color: var(--text-primary);">
                                {{ Str::limit($p->need->title ?? '-', 50) }}
                            </h3>
                            <div class="text-xs text-muted mb-3 d-flex align-center gap-1">
                                <span>🏫</span>
                                <span>{{ $p->need->schoolProfile->school_name ?? '-' }}</span>
                                <span>•</span>
                                <span>{{ $p->need->schoolProfile->regency->name ?? ($p->need->schoolProfile->province->name ?? '-') }}</span>
                            </div>
                            <div style="margin-top: auto; font-size: 12px; background: var(--surface-alt); padding: 8px 12px; border-radius: var(--radius-xs);">
                                <div class="d-flex justify-between align-center mb-1">
                                    <span class="text-muted">Kuantitas:</span>
                                    <span class="font-bold text-dark">{{ $p->quantity }} {{ $p->need->unit ?? 'unit' }}</span>
                                </div>
                                <div class="d-flex justify-between align-center">
                                    <span class="text-muted">Pemberi:</span>
                                    <span class="font-semibold text-dark truncate" style="max-width: 140px;" title="{{ $p->contributorProfile->user->name ?? 'Donatur SambungAsa' }}">
                                        {{ $p->contributorProfile->user->name ?? 'Donatur SambungAsa' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Trust & Open Governance Card -->
    <div class="card" style="background: linear-gradient(135deg, #0F0F0F 0%, #1F1F1F 100%); color: #FFFFFF; border: none; box-shadow: var(--elevation-2);">
        <div class="card-body p-6 text-center" style="max-width: 760px; margin: 0 auto;">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255, 255, 255, 0.1); display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 16px;">
                🛡️
            </div>
            <h2 style="font-size: 24px; font-weight: 700; color: #FFFFFF; margin-bottom: 10px;">
                Komitmen Transparansi 100% Tanpa Perantara Uang
            </h2>
            <p style="font-size: 14px; color: #A0A0A0; line-height: 1.6; margin-bottom: 24px;">
                SambungAsa memfasilitasi komitmen barang fisik dan misi relawan secara langsung dari donatur ke sekolah tujuan di kawasan 3T. Setiap bantuan memiliki nomor pelacakan unik dan dipertanggungjawabkan melalui Berita Acara Serah Terima (BAST).
            </p>
            <div class="d-flex justify-center flex-wrap gap-3">
                <a href="{{ route('public.needs.index') }}" class="btn btn-primary btn-lg" style="box-shadow: 0 4px 14px rgba(255, 0, 0, 0.4);">
                    Bantu Kebutuhan Sekolah 3T Sekarang
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline btn-lg" style="color: #FFFFFF; border-color: rgba(255, 255, 255, 0.3);">
                    Daftar Sebagai Donatur / Relawan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
