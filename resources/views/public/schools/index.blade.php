@extends('layouts.app')

@section('title', 'Direktori Sekolah 3T — SambungAsa')
@section('meta_description', 'Direktori resmi sekolah-sekolah di wilayah Tertinggal, Terdepan, dan Terluar (3T) yang terverifikasi di platform SambungAsa.')

@section('content')
<div class="container py-8">
    <div class="mb-6">
        <h1>Direktori Sekolah 3T Terverifikasi (F-19)</h1>
        <p class="text-muted mt-1">
            Daftar sekolah di wilayah 3T yang telah divalidasi keabsahan Dapodik dan lokasi geografisnya.
        </p>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-6">
        <div class="card-body py-4">
            <form action="{{ route('public.schools.index') }}" method="GET" class="d-flex align-center flex-wrap gap-3">
                <div style="flex: 2; min-width: 240px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama sekolah, NPSN, atau kecamatan..." value="{{ request('search') }}">
                </div>

                <div style="width: 180px;">
                    <select name="island" class="form-select">
                        <option value="">Semua Wilayah</option>
                        <option value="Papua" {{ request('island') == 'Papua' ? 'selected' : '' }}>Papua</option>
                        <option value="Kalimantan" {{ request('island') == 'Kalimantan' ? 'selected' : '' }}>Kalimantan</option>
                        <option value="Sulawesi" {{ request('island') == 'Sulawesi' ? 'selected' : '' }}>Sulawesi</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-dark">Cari Sekolah</button>
                @if(request()->hasAny(['search', 'island']))
                    <a href="{{ route('public.schools.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- School Cards Grid -->
    <div class="grid grid-cols-3 gap-6">
        @forelse($schools as $school)
            <div class="card d-flex flex-column" style="height: 100%;">
                <div class="card-body d-flex flex-column flex-1">
                    <div class="d-flex justify-between align-center mb-2">
                        <span class="badge badge-success" style="font-size: 10px;">✓ Terverifikasi 3T</span>
                        <span class="badge badge-neutral" style="font-size: 10px;">{{ $school->regency?->island }}</span>
                    </div>

                    <h3 style="font-size: 17px; margin-bottom: 6px;">
                        <a href="{{ route('public.schools.show', $school->id) }}" style="color: var(--text-primary); text-decoration: none;">
                            {{ $school->school_name }}
                        </a>
                    </h3>

                    <div class="text-xs text-muted mb-3">
                        📍 {{ $school->regency?->name }}, Provinsi {{ $school->province?->name }}
                    </div>

                    <div class="text-sm text-dark mb-4 flex-1">
                        <div class="text-xs text-muted">KECAMATAN / LOKASI:</div>
                        <div>{{ $school->district ?: '—' }} ({{ $school->address }})</div>
                    </div>

                    <div class="p-3" style="background: var(--surface); border-radius: var(--radius-sm); font-size: 12px;">
                        <div class="d-flex justify-between">
                            <span class="text-muted">Jumlah Murid:</span>
                            <strong>{{ $school->student_count ?? 0 }} Siswa</strong>
                        </div>
                        <div class="d-flex justify-between mt-1">
                            <span class="text-muted">Kebutuhan Terbuka:</span>
                            <strong style="color: var(--primary);">{{ $school->needs->count() }} Kebutuhan</strong>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-between align-center">
                    <span class="text-xs text-muted">NPSN: {{ $school->npsn ?: '—' }}</span>
                    <a href="{{ route('public.schools.show', $school->id) }}" class="btn btn-outline btn-sm">
                        Profil & Kebutuhan →
                    </a>
                </div>
            </div>
        @empty
            <div style="grid-column: span 3; text-align: center; padding: 60px;" class="card">
                <div style="font-size: 36px; margin-bottom: 12px;">🏫</div>
                <h3 style="font-size: 18px; margin-bottom: 6px;">Tidak Ditemukan Sekolah</h3>
                <p class="text-sm text-muted">Tidak ada sekolah terverifikasi yang cocok dengan kriteria pencarian.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($schools->hasPages())
        <div class="mt-8 d-flex justify-between align-center">
            <div class="text-sm text-muted">
                Menampilkan {{ $schools->firstItem() }} - {{ $schools->lastItem() }} dari total {{ $schools->total() }} sekolah
            </div>
            <div>
                {{ $schools->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
