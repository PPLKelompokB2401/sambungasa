@extends('layouts.app')

@section('title', 'SambungAsa — Menghubungkan Sekolah 3T dengan Bantuan Pendidikan')
@section('meta_description', 'SambungAsa memfasilitasi kebutuhan sekolah di wilayah 3T Papua, Kalimantan, dan Sulawesi dengan donatur, relawan, dan organisasi peduli pendidikan.')

@section('content')
<!-- Hero Section -->
<section style="background: linear-gradient(180deg, #FFFFFF 0%, #F8F9FA 100%); border-bottom: 1px solid var(--border); padding: 64px 0 48px;">
    <div class="container text-center" style="max-width: 920px; text-align: center;">
        <div class="d-inline-flex align-center gap-2 mb-3" style="background: var(--surface-alt); padding: 5px 14px; border-radius: var(--radius-pill); font-size: 13px; font-weight: 500;">
            <span style="color: var(--primary); font-weight: 700;">● GOTONG ROYONG 3T</span>
            <span>Papua • Kalimantan • Sulawesi</span>
        </div>

        <h1 style="font-size: 40px; line-height: 48px; font-weight: 700; color: var(--text-primary); margin-bottom: 18px; letter-spacing: -0.02em;">
            Setiap Anak di Ujung Negeri Berhak Atas <span style="color: var(--primary);">Harapan Pendidikan yang Layak</span>
        </h1>

        <p class="body-text" style="font-size: 16px; line-height: 26px; max-width: 720px; margin: 0 auto 32px; color: var(--text-secondary);">
            SambungAsa adalah jembatan digital yang menghubungkan sekolah-sekolah di wilayah Tertinggal, Terdepan, dan Terluar (3T) dengan donatur, relawan pendidik, dan organisasi untuk menyalurkan bantuan material dan pelatihan secara transparan dan akuntabel.
        </p>

        <div class="d-flex justify-center align-center flex-wrap gap-3 mb-6">
            <a href="{{ route('public.needs.index') }}" class="btn btn-primary btn-lg" style="box-shadow: 0 4px 14px rgba(255, 0, 0, 0.25);">
                Telusuri Kebutuhan Sekolah 3T →
            </a>
            <a href="{{ route('register', ['role' => 'contributor']) }}" class="btn btn-outline btn-lg">
                Daftar Jadi Donatur / Relawan
            </a>
        </div>

        <!-- Search Bar Red Broadcast Style -->
        <div style="max-width: 640px; margin: 32px auto 0;">
            <form action="{{ route('public.needs.index') }}" method="GET" class="search-bar-wrap" style="max-width: 100%;">
                <input type="text" name="search" class="search-input" placeholder="Cari buku, laptop ANBK, guru matematika, atau nama sekolah...">
                <button type="submit" class="search-btn" title="Cari">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Impact Counters -->
<section style="background: #FFFFFF; border-bottom: 1px solid var(--border); padding: 32px 0;">
    <div class="container">
        <div class="grid grid-cols-4 gap-6 text-center" style="text-align: center;">
            <div>
                <div style="font-size: 32px; font-weight: 700; color: var(--text-primary); font-family: var(--font-mono);">
                    {{ $stats['total_schools'] }}
                </div>
                <div class="text-xs text-muted font-medium mt-1">SEKOLAH 3T TERVERIFIKASI</div>
            </div>
            <div>
                <div style="font-size: 32px; font-weight: 700; color: var(--primary); font-family: var(--font-mono);">
                    {{ $stats['total_needs_published'] }}
                </div>
                <div class="text-xs text-muted font-medium mt-1">KEBUTUHAN AKTIF DIBUKA</div>
            </div>
            <div>
                <div style="font-size: 32px; font-weight: 700; color: var(--success); font-family: var(--font-mono);">
                    {{ $stats['total_items_delivered'] }}
                </div>
                <div class="text-xs text-muted font-medium mt-1">UNIT BARANG TERSALURKAN</div>
            </div>
            <div>
                <div style="font-size: 32px; font-weight: 700; color: var(--secondary); font-family: var(--font-mono);">
                    {{ $stats['total_regions_3t'] }}
                </div>
                <div class="text-xs text-muted font-medium mt-1">KABUPATEN 3T TERJANGKAU</div>
            </div>
        </div>
    </div>
</section>

<!-- Category Chip Filter Bar -->
<section style="padding: 32px 0 16px;">
    <div class="container">
        <div class="d-flex justify-between align-center mb-4">
            <div>
                <h2 style="font-size: 20px; font-weight: 700;">Kategori Bantuan Pendidikan</h2>
                <p class="text-sm text-muted">Pilih jenis bantuan sesuai kepedulian Anda.</p>
            </div>
            <a href="{{ route('public.needs.index') }}" class="text-sm font-medium">Lihat Semua Kebutuhan →</a>
        </div>

        <div class="chip-bar">
            <a href="{{ route('public.needs.index') }}" class="chip active">Semua Kategori</a>
            @foreach($categories as $cat)
                <a href="{{ route('public.needs.index', ['category' => $cat->slug]) }}" class="chip">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Urgent Needs Section -->
<section style="padding: 24px 0 60px;">
    <div class="container">
        <div class="d-flex justify-between align-center mb-6">
            <div>
                <span class="badge badge-danger mb-1">Prioritas Mendesak</span>
                <h2 style="font-size: 24px; font-weight: 700;">Kebutuhan Darurat Sekolah 3T</h2>
                <p class="text-sm text-muted">Sekolah-sekolah ini membutuhkan dukungan segera untuk kelancaran kegiatan belajar.</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            @forelse($urgentNeeds as $need)
                <div class="card d-flex flex-column" style="height: 100%;">
                    <div class="card-body d-flex flex-column flex-1">
                        <div class="d-flex justify-between align-start gap-2 mb-2">
                            <span class="badge {{ $need->priority == 'mendesak' ? 'badge-danger' : 'badge-warning' }}">
                                {{ ucfirst($need->priority) }}
                            </span>
                            <span class="badge badge-neutral" style="font-size: 10px;">
                                {{ $need->category->name }}
                            </span>
                        </div>

                        <h3 style="font-size: 16px; line-height: 22px; margin-bottom: 8px;">
                            <a href="{{ route('public.needs.show', $need->id) }}" style="color: var(--text-primary); text-decoration: none;">
                                {{ $need->title }}
                            </a>
                        </h3>

                        <div class="text-sm text-muted mb-3 d-flex align-center gap-1">
                            <span>🏫</span>
                            <strong>{{ $need->schoolProfile->school_name }}</strong>
                            <span>• {{ $need->schoolProfile->regency?->name ?? '3T' }}</span>
                        </div>

                        <p class="text-sm text-muted mb-4 flex-1" style="line-height: 20px;">
                            {{ Str::limit($need->description, 110) }}
                        </p>

                        <!-- Progress Bar -->
                        <div class="mt-auto">
                            <div class="d-flex justify-between align-center text-xs mb-1">
                                <span class="font-medium text-dark">
                                    {{ $need->quantity_fulfilled }} dari {{ $need->quantity_required ?? '—' }} {{ $need->unit }}
                                </span>
                                <span class="font-bold {{ $need->progress_percentage == 100 ? 'text-success' : 'text-primary' }}">
                                    {{ $need->progress_percentage }}%
                                </span>
                            </div>
                            <div class="progress-bar-wrap">
                                <div class="progress-fill {{ $need->progress_percentage == 100 ? 'success' : '' }}" style="width: {{ $need->progress_percentage }}%;"></div>
                            </div>
                            <div class="text-xs text-muted mt-2">
                                Sisa kebutuhan: <strong>{{ $need->getRemainingQuantity() }} {{ $need->unit }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-between align-center">
                        <span class="text-xs text-muted">
                            {{ $need->schoolProfile->regency?->island }}
                        </span>
                        <a href="{{ route('public.needs.show', $need->id) }}" class="btn btn-primary btn-sm">
                            Beri Bantuan →
                        </a>
                    </div>
                </div>
            @empty
                <div style="grid-column: span 3; text-align: center; padding: 48px;" class="text-muted">
                    Saat ini belum ada kebutuhan mendesak yang dipublikasikan.
                </div>
            @endforelse
        </div>

        <div class="text-center mt-8" style="text-align: center;">
            <a href="{{ route('public.needs.index') }}" class="btn btn-dark">
                Lihat Semua Kebutuhan di Direktori →
            </a>
        </div>
    </div>
</section>

<!-- How it works (3 Steps) -->
<section style="background-color: var(--surface); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 60px 0;">
    <div class="container text-center" style="max-width: 1000px; text-align: center;">
        <span class="badge badge-neutral mb-2">Transparansi Penuh</span>
        <h2 style="font-size: 26px; font-weight: 700; margin-bottom: 12px;">Bagaimana SambungAsa Bekerja?</h2>
        <p class="text-muted text-sm mb-8" style="max-width: 600px; margin-left: auto; margin-right: auto;">
            Sistem akuntabilitas tanpa perantara dana: bantuan fisik langsung dikirimkan ke sekolah 3T dengan kode pelacakan resmi.
        </p>

        <div class="grid grid-cols-3 gap-6 text-left" style="text-align: left;">
            <div class="card p-4">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 16px;">1</div>
                <h3 style="font-size: 16px; margin-bottom: 8px;">1. Pilih Kebutuhan</h3>
                <p class="text-sm text-muted">
                    Telusuri daftar kebutuhan riil yang telah diverifikasi lokasi 3T-nya oleh administrator. Pilih sesuai kapasitas bantuan Anda.
                </p>
            </div>

            <div class="card p-4">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 16px;">2</div>
                <h3 style="font-size: 16px; margin-bottom: 8px;">2. Ajukan Komitmen (Pledge)</h3>
                <p class="text-sm text-muted">
                    Ajukan jumlah barang atau kesediaan relawan. Sistem menghasilkan kode pelacakan unik (misal: <code>SA-202610-8841</code>) setelah disetujui admin.
                </p>
            </div>

            <div class="card p-4">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-bottom: 16px;">3</div>
                <h3 style="font-size: 16px; margin-bottom: 8px;">3. Salurkan & Pantau Bukti</h3>
                <p class="text-sm text-muted">
                    Salurkan barang ke alamat sekolah. Unggah resi dan bukti serah terima (BAST). Pihak sekolah mengonfirmasi penerimaan secara transparan.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 3T Priority Islands Section -->
<section style="padding: 60px 0;">
    <div class="container">
        <div class="d-flex justify-between align-center mb-6">
            <div>
                <span class="badge badge-info mb-1">Fokus Geografis</span>
                <h2 style="font-size: 24px; font-weight: 700;">Sekolah 3T Terdaftar per Wilayah</h2>
                <p class="text-sm text-muted">Mendukung sekolah di pulau terluar dan pedalaman.</p>
            </div>
            <a href="{{ route('public.schools.index') }}" class="text-sm font-medium">Buka Direktori Sekolah →</a>
        </div>

        <div class="grid grid-cols-4 gap-6">
            @foreach($schools as $sch)
                <div class="card p-4">
                    <span class="badge badge-success mb-2" style="font-size: 10px;">✓ Terverifikasi 3T</span>
                    <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 6px;">
                        <a href="{{ route('public.schools.show', $sch->id) }}" style="color: var(--text-primary); text-decoration: none;">
                            {{ $sch->school_name }}
                        </a>
                    </h4>
                    <div class="text-xs text-muted mb-2">
                        📍 {{ $sch->regency?->name }}, {{ $sch->regency?->island }}
                    </div>
                    <div class="text-xs text-muted">
                        {{ $sch->student_count ?? 0 }} Murid • NPSN: {{ $sch->npsn ?: '—' }}
                    </div>
                    <div class="mt-3 pt-3" style="border-top: 1px solid var(--border);">
                        <a href="{{ route('public.schools.show', $sch->id) }}" class="text-xs font-medium">Lihat Kebutuhan Sekolah →</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
