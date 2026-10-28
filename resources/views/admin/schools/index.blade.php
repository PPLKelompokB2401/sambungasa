@extends('layouts.dashboard')

@section('title', 'Verifikasi Sekolah 3T — Admin')

@section('content')
<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <h1 style="font-size: 22px; font-weight: 700;">Verifikasi Lokasi & Sekolah 3T (F-04)</h1>
        <p class="text-sm text-muted">Periksa legalitas dan kesesuaian lokasi sekolah terhadap referensi wilayah 3T.</p>
    </div>
</div>

<!-- Stats Counter -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card p-3">
        <div class="text-xs text-muted font-medium">TOTAL PENDAFTAR SEKOLAH</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px;">{{ $stats['total'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--warning);">
        <div class="text-xs text-muted font-medium">MENUNGGU VERIFIKASI</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--warning);">{{ $stats['pending'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--success);">
        <div class="text-xs text-muted font-medium">TERVERIFIKASI 3T</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--success);">{{ $stats['verified'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--secondary);">
        <div class="text-xs text-muted font-medium">PERLU REVISI / DITOLAK</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--secondary);">{{ $stats['needs_revision'] + $stats['rejected'] }}</div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-6">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.schools.index') }}" class="d-flex align-center flex-wrap gap-3">
            <div style="flex: 1; min-width: 220px;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama sekolah, NPSN, perwakilan..." value="{{ request('search') }}">
            </div>

            <div style="width: 160px;">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified 3T</option>
                    <option value="needs_revision" {{ request('status') == 'needs_revision' ? 'selected' : '' }}>Perlu Revisi</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div style="width: 160px;">
                <select name="island" class="form-select">
                    <option value="">Semua Wilayah</option>
                    <option value="Papua" {{ request('island') == 'Papua' ? 'selected' : '' }}>Papua</option>
                    <option value="Kalimantan" {{ request('island') == 'Kalimantan' ? 'selected' : '' }}>Kalimantan</option>
                    <option value="Sulawesi" {{ request('island') == 'Sulawesi' ? 'selected' : '' }}>Sulawesi</option>
                </select>
            </div>

            <button type="submit" class="btn btn-dark btn-sm">Filter</button>
            @if(request()->hasAny(['search', 'status', 'island']))
                <a href="{{ route('admin.schools.index') }}" class="btn btn-outline btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Sekolah & NPSN</th>
                    <th>Provinsi / Kabupaten</th>
                    <th>Status Wilayah 3T</th>
                    <th>Perwakilan</th>
                    <th>Status Verifikasi</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schools as $sch)
                    <tr>
                        <td>
                            <div class="font-bold text-dark">{{ $sch->school_name }}</div>
                            <div class="text-xs text-muted">NPSN: {{ $sch->npsn ?: '—' }} • {{ $sch->student_count ?? 0 }} Siswa</div>
                        </td>
                        <td>
                            <div class="text-sm font-medium">{{ $sch->regency?->name ?? '—' }}</div>
                            <div class="text-xs text-muted">{{ $sch->province?->name ?? '—' }} ({{ $sch->regency?->island ?? '3T' }})</div>
                        </td>
                        <td>
                            @if($sch->regency && $sch->regency->is_3t)
                                <span class="badge badge-success">✓ Sesuai Daftar 3T</span>
                            @elseif($sch->regency)
                                <span class="badge badge-warning">Bukan Wilayah 3T</span>
                            @else
                                <span class="badge badge-neutral">Belum Pilih Wilayah</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-sm font-medium">{{ $sch->user->name }}</div>
                            <div class="text-xs text-muted">{{ $sch->user->email }}</div>
                        </td>
                        <td>
                            @if($sch->verification_status === 'verified')
                                <span class="badge badge-success">Terverifikasi</span>
                            @elseif($sch->verification_status === 'pending')
                                <span class="badge badge-warning">Menunggu</span>
                            @elseif($sch->verification_status === 'needs_revision')
                                <span class="badge badge-info">Revisi</span>
                            @else
                                <span class="badge badge-danger">Ditolak</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('admin.schools.show', $sch->id) }}" class="btn btn-outline btn-sm">Periksa & Verifikasi</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Tidak ada data sekolah yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($schools->hasPages())
        <div class="card-footer d-flex justify-between align-center">
            <div class="text-sm text-muted">
                Menampilkan {{ $schools->firstItem() }} - {{ $schools->lastItem() }} dari {{ $schools->total() }} sekolah
            </div>
            <div>
                {{ $schools->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
