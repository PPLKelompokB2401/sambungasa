@extends('layouts.app')

@section('title', 'Referensi Wilayah 3T — SambungAsa')
@section('meta_description', 'Daftar resmi wilayah Tertinggal, Terdepan, dan Terluar (3T) di Papua, Kalimantan, dan Sulawesi yang menjadi prioritas bantuan pendidikan SambungAsa.')

@section('content')
<div class="container py-8">
    <div class="mb-6">
        <span class="badge badge-danger mb-2">Referensi Wilayah 3T Resmi</span>
        <h1>Wilayah Tertinggal, Terdepan, dan Terluar (3T)</h1>
        <p class="text-muted mt-2" style="max-width: 800px;">
            SambungAsa memfokuskan inisiatif pada sekolah-sekolah di wilayah 3T Indonesia, khususnya gugusan kepulauan perbatasan dan pedalaman di <strong>Papua, Kalimantan, dan Sulawesi</strong> sesuai acuan ketetapan daerah 3T nasional.
        </p>
    </div>

    <!-- Island Stats Cards -->
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="card p-3" style="border-top: 3px solid var(--primary);">
            <div class="text-xs text-muted font-medium">TOTAL KABUPATEN 3T</div>
            <div style="font-size: 26px; font-weight: 700; margin-top: 4px; color: var(--primary);">{{ $stats['total_3t'] }}</div>
            <div class="text-xs text-muted mt-1">Wilayah Prioritas</div>
        </div>
        <div class="card p-3">
            <div class="text-xs text-muted font-medium">WILAYAH PAPUA</div>
            <div style="font-size: 26px; font-weight: 700; margin-top: 4px;">{{ $stats['papua_3t'] }}</div>
            <div class="text-xs text-muted mt-1">Pegunungan & Pesisir 3T</div>
        </div>
        <div class="card p-3">
            <div class="text-xs text-muted font-medium">WILAYAH KALIMANTAN</div>
            <div style="font-size: 26px; font-weight: 700; margin-top: 4px;">{{ $stats['kalimantan_3t'] }}</div>
            <div class="text-xs text-muted mt-1">Perbatasan & Pedalaman</div>
        </div>
        <div class="card p-3">
            <div class="text-xs text-muted font-medium">WILAYAH SULAWESI</div>
            <div style="font-size: 26px; font-weight: 700; margin-top: 4px;">{{ $stats['sulawesi_3t'] }}</div>
            <div class="text-xs text-muted mt-1">Kepulauan Terluar</div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card mb-6">
        <div class="card-body py-3">
            <form action="{{ route('public.regions.index') }}" method="GET" class="d-flex align-center flex-wrap gap-3">
                <div style="flex: 1; min-width: 220px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama kabupaten atau daerah..." value="{{ request('search') }}">
                </div>

                <div style="width: 180px;">
                    <select name="island" class="form-select">
                        <option value="">Semua Pulau</option>
                        <option value="Papua" {{ request('island') == 'Papua' ? 'selected' : '' }}>Papua</option>
                        <option value="Kalimantan" {{ request('island') == 'Kalimantan' ? 'selected' : '' }}>Kalimantan</option>
                        <option value="Sulawesi" {{ request('island') == 'Sulawesi' ? 'selected' : '' }}>Sulawesi</option>
                    </select>
                </div>

                <div style="width: 180px;">
                    <select name="is_3t" class="form-select">
                        <option value="">Semua Kategori</option>
                        <option value="1" {{ request('is_3t') === '1' ? 'selected' : '' }}>Hanya Wilayah 3T</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-dark btn-sm">Terapkan Filter</button>
                @if(request()->hasAny(['search', 'island', 'is_3t']))
                    <a href="{{ route('public.regions.index') }}" class="btn btn-outline btn-sm">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Regions Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode Wilayah</th>
                        <th>Kabupaten / Kota</th>
                        <th>Provinsi</th>
                        <th>Pulau</th>
                        <th>Klasifikasi 3T</th>
                        <th>Catatan Geografis</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($regions as $reg)
                        <tr>
                            <td>
                                <code style="font-size: 12px; font-weight: 700;">{{ $reg->code }}</code>
                            </td>
                            <td>
                                <div class="font-bold text-dark">{{ $reg->name }}</div>
                            </td>
                            <td>{{ $reg->parent?->name ?? '—' }}</td>
                            <td>{{ $reg->island }}</td>
                            <td>
                                @if($reg->is_3t)
                                    <span class="badge badge-success">✓ Wilayah 3T</span>
                                @else
                                    <span class="badge badge-neutral">Non-3T</span>
                                @endif
                            </td>
                            <td class="text-sm text-muted">
                                {{ $reg->notes ?: 'Wilayah administrasi fokus SambungAsa' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-6">
                                Tidak ada data wilayah yang cocok dengan pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($regions->hasPages())
            <div class="card-footer d-flex justify-between align-center">
                <div class="text-sm text-muted">
                    Menampilkan {{ $regions->firstItem() }} - {{ $regions->lastItem() }} dari {{ $regions->total() }} daerah
                </div>
                <div>
                    {{ $regions->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
