@extends('layouts.app')

@section('title', $school->school_name . ' — Profil Sekolah 3T')
@section('meta_description', 'Profil dan daftar kebutuhan pendidikan untuk ' . $school->school_name . ', Kabupaten ' . ($school->regency?->name ?? '3T'))

@section('content')
<div class="container py-8">
    <div class="mb-4">
        <a href="{{ route('public.schools.index') }}" class="text-sm">← Kembali ke Direktori Sekolah</a>
    </div>

    <!-- School Header Card -->
    <div class="card mb-8">
        <div class="card-body p-6">
            <div class="d-flex justify-between align-start flex-wrap gap-4">
                <div>
                    <div class="d-flex align-center gap-2 mb-2">
                        <span class="badge badge-success">✓ Sekolah Wilayah 3T Terverifikasi</span>
                        <span class="badge badge-neutral">{{ $school->regency?->island }}</span>
                    </div>

                    <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 6px;">{{ $school->school_name }}</h1>

                    <div class="text-muted text-sm">
                        📍 {{ $school->district ?: 'Kecamatan Terpencil' }}, {{ $school->regency?->name }}, Provinsi {{ $school->province?->name }}
                    </div>
                </div>

                <div class="p-3" style="background: var(--surface); border-radius: var(--radius-sm); border: 1px solid var(--border); min-width: 200px;">
                    <div class="text-xs text-muted">NOMOR POKOK SEKOLAH NASIONAL</div>
                    <div style="font-size: 16px; font-weight: 700; font-family: var(--font-mono); margin-top: 2px;">{{ $school->npsn ?: 'Belum diisi' }}</div>
                    <div class="text-xs text-muted mt-2">SISWA AKTIF: <strong>{{ $school->student_count ?? 0 }} anak</strong></div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6 mt-6 pt-6" style="border-top: 1px solid var(--border);">
                <div>
                    <span class="text-xs text-muted">ALAMAT LENGKAP</span>
                    <div class="text-sm font-medium mt-1">{{ $school->address }}</div>
                </div>
                <div>
                    <span class="text-xs text-muted">KEPALA SEKOLAH</span>
                    <div class="text-sm font-medium mt-1">{{ $school->principal_name ?: 'Perwakilan Sekolah' }}</div>
                </div>
                <div>
                    <span class="text-xs text-muted">WILAYAH GEOGRAFIS 3T</span>
                    <div class="text-sm font-medium mt-1">{{ $school->regency?->notes ?: 'Wilayah Terdepan & Terluar' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- School's Active Needs -->
    <div class="mb-4 d-flex justify-between align-center">
        <div>
            <h2 style="font-size: 20px; font-weight: 700;">Kebutuhan yang Diajukan Sekolah ({{ $school->needs->count() }})</h2>
            <p class="text-sm text-muted">Pilih kebutuhan untuk menyalurkan bantuan langsung ke sekolah ini.</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        @forelse($school->needs as $need)
            <div class="card d-flex flex-column" style="height: 100%;">
                <div class="card-body d-flex flex-column flex-1">
                    <div class="d-flex justify-between align-center mb-2">
                        <span class="badge {{ $need->priority == 'mendesak' ? 'badge-danger' : 'badge-warning' }}">
                            {{ ucfirst($need->priority) }}
                        </span>
                        <span class="badge badge-neutral" style="font-size: 10px;">{{ $need->category->name }}</span>
                    </div>

                    <h3 style="font-size: 16px; margin-bottom: 8px;">
                        <a href="{{ route('public.needs.show', $need->id) }}" style="color: var(--text-primary); text-decoration: none;">
                            {{ $need->title }}
                        </a>
                    </h3>

                    <p class="text-sm text-muted mb-4 flex-1">
                        {{ Str::limit($need->description, 100) }}
                    </p>

                    <!-- Progress -->
                    <div class="mt-auto">
                        <div class="d-flex justify-between align-center text-xs mb-1">
                            <span>{{ $need->quantity_fulfilled }} / {{ $need->quantity_required ?? '—' }} {{ $need->unit }}</span>
                            <span class="font-bold {{ $need->progress_percentage == 100 ? 'text-success' : 'text-primary' }}">{{ $need->progress_percentage }}%</span>
                        </div>
                        <div class="progress-bar-wrap">
                            <div class="progress-fill {{ $need->progress_percentage == 100 ? 'success' : '' }}" style="width: {{ $need->progress_percentage }}%;"></div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-between align-center">
                    <span class="text-xs text-muted">Sisa: <strong>{{ $need->getRemainingQuantity() }} {{ $need->unit }}</strong></span>
                    <a href="{{ route('public.needs.show', $need->id) }}" class="btn btn-primary btn-sm">Beri Bantuan →</a>
                </div>
            </div>
        @empty
            <div style="grid-column: span 3; text-align: center; padding: 48px;" class="card">
                <p class="text-muted">Saat ini sekolah belum memiliki kebutuhan aktif yang dipublikasikan.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
