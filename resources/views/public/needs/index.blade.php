@extends('layouts.app')

@section('title', 'Direktori Kebutuhan Sekolah 3T — SambungAsa')
@section('meta_description', 'Cari dan saring kebutuhan fasilitas, buku, laptop, infrastruktur, dan relawan guru untuk sekolah-sekolah di wilayah 3T Indonesia.')

@section('content')
<div class="container py-8">
    <div class="mb-6">
        <h1>Direktori Kebutuhan Sekolah 3T (F-08)</h1>
        <p class="text-muted mt-1">
            Seluruh kebutuhan di bawah ini telah diverifikasi dan disetujui untuk menerima bantuan langsung.
        </p>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card mb-6">
        <div class="card-body py-4">
            <form action="{{ route('public.needs.index') }}" method="GET">
                <div class="d-flex align-center flex-wrap gap-3 mb-3">
                    <div style="flex: 2; min-width: 260px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari kata kunci (buku, komputer, nama sekolah)..." value="{{ request('search') }}">
                    </div>

                    <div style="flex: 1; min-width: 160px;">
                        <select name="category" class="form-select">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="flex: 1; min-width: 150px;">
                        <select name="island" class="form-select">
                            <option value="">Semua Wilayah</option>
                            <option value="Papua" {{ request('island') == 'Papua' ? 'selected' : '' }}>Papua</option>
                            <option value="Kalimantan" {{ request('island') == 'Kalimantan' ? 'selected' : '' }}>Kalimantan</option>
                            <option value="Sulawesi" {{ request('island') == 'Sulawesi' ? 'selected' : '' }}>Sulawesi</option>
                        </select>
                    </div>

                    <div style="flex: 1; min-width: 140px;">
                        <select name="priority" class="form-select">
                            <option value="">Semua Prioritas</option>
                            <option value="mendesak" {{ request('priority') == 'mendesak' ? 'selected' : '' }}>Mendesak</option>
                            <option value="tinggi" {{ request('priority') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="sedang" {{ request('priority') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="rendah" {{ request('priority') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                        </select>
                    </div>

                    <div style="flex: 1; min-width: 140px;">
                        <select name="type" class="form-select">
                            <option value="">Semua Jenis</option>
                            <option value="material" {{ request('type') == 'material' ? 'selected' : '' }}>Material</option>
                            <option value="non_material" {{ request('type') == 'non_material' ? 'selected' : '' }}>Nonmaterial</option>
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-dark">Terapkan Filter</button>
                    </div>
                </div>

                @if(request()->hasAny(['search', 'category', 'island', 'priority', 'type']))
                    <div class="d-flex align-center gap-2 text-xs">
                        <span class="text-muted">Filter aktif:</span>
                        <a href="{{ route('public.needs.index') }}" class="text-danger font-medium">✕ Reset Semua Filter</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Category Quick Chips -->
    <div class="chip-bar mb-6">
        <a href="{{ route('public.needs.index') }}" class="chip {{ !request('category') ? 'active' : '' }}">Semua</a>
        @foreach($categories as $cat)
            <a href="{{ route('public.needs.index', array_merge(request()->query(), ['category' => $cat->slug])) }}" class="chip {{ request('category') == $cat->slug ? 'active' : '' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    <!-- Needs Grid -->
    <div class="grid grid-cols-3 gap-6">
        @forelse($needs as $need)
            <div class="card d-flex flex-column" style="height: 100%;">
                <div class="card-body d-flex flex-column flex-1">
                    <div class="d-flex justify-between align-start gap-2 mb-2">
                        <span class="badge {{ $need->priority == 'mendesak' ? 'badge-danger' : ($need->priority == 'tinggi' ? 'badge-warning' : 'badge-neutral') }}">
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
                    </div>
                    <div class="text-xs text-muted mb-3">
                        📍 {{ $need->schoolProfile->regency?->name ?? '3T' }}, {{ $need->schoolProfile->regency?->island }}
                    </div>

                    <p class="text-sm text-muted mb-4 flex-1" style="line-height: 20px;">
                        {{ Str::limit($need->description, 120) }}
                    </p>

                    <!-- Progress Bar -->
                    <div class="mt-auto">
                        <div class="d-flex justify-between align-center text-xs mb-1">
                            <span class="font-medium text-dark">
                                {{ $need->quantity_fulfilled }} / {{ $need->quantity_required ?? '—' }} {{ $need->unit }}
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
                        Dipublikasi: {{ $need->published_at ? $need->published_at->format('d M Y') : $need->created_at->format('d M Y') }}
                    </span>
                    <a href="{{ route('public.needs.show', $need->id) }}" class="btn btn-primary btn-sm">
                        Beri Bantuan →
                    </a>
                </div>
            </div>
        @empty
            <div style="grid-column: span 3; text-align: center; padding: 60px;" class="card">
                <div style="font-size: 36px; margin-bottom: 12px;">🔍</div>
                <h3 style="font-size: 18px; margin-bottom: 6px;">Tidak Ditemukan Kebutuhan</h3>
                <p class="text-sm text-muted">Tidak ada data kebutuhan yang cocok dengan kata kunci atau filter yang Anda pilih.</p>
                <div class="mt-4">
                    <a href="{{ route('public.needs.index') }}" class="btn btn-outline btn-sm">Hapus Semua Filter</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($needs->hasPages())
        <div class="mt-8 d-flex justify-between align-center">
            <div class="text-sm text-muted">
                Menampilkan {{ $needs->firstItem() }} - {{ $needs->lastItem() }} dari total {{ $needs->total() }} kebutuhan
            </div>
            <div>
                {{ $needs->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
