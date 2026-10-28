@extends('layouts.dashboard')

@section('title', 'Edit Kebutuhan: ' . $need->title)

@section('content')
<div class="mb-4">
    <a href="{{ route('school.needs.index') }}" class="text-sm">← Kembali ke Daftar Kebutuhan</a>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-header d-flex justify-between align-center">
        <div>
            <h2 style="font-size: 18px; font-weight: 700;">Edit Kebutuhan Sekolah</h2>
            <p class="text-sm text-muted">Status saat ini: <span class="badge badge-neutral">{{ $need->status }}</span></p>
        </div>
        @if($activePledgesCount === 0)
            <form action="{{ route('school.needs.destroy', $need->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kebutuhan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline btn-sm text-danger">Hapus Kebutuhan</button>
            </form>
        @endif
    </div>

    <div class="card-body">
        @if($activePledgesCount > 0)
            <div class="alert alert-info mb-4">
                <span>ℹ</span>
                <div>
                    <strong>Perhatian: Kebutuhan memiliki {{ $activePledgesCount }} komitmen bantuan aktif ({{ $need->getActivePledgesQuantity() }} {{ $need->unit }}).</strong>
                    <div class="mt-1 text-xs">Untuk menjaga konsistensi komitmen donatur, kuantitas kebutuhan tidak dapat diubah menjadi lebih kecil dari jumlah yang telah dikomitmenkan. Kebutuhan juga tidak dapat dihapus.</div>
                </div>
            </div>
        @endif

        @if($need->rejection_notes)
            <div class="alert alert-danger mb-4">
                <span>⚠</span>
                <div>
                    <strong>Catatan Penolakan Moderasi Sebelumnya:</strong>
                    <div class="mt-1">{{ $need->rejection_notes }}</div>
                </div>
            </div>
        @endif

        <form action="{{ route('school.needs.update', $need->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title" class="form-label required">Judul Kebutuhan</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $need->title) }}" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="need_category_id" class="form-label required">Kategori Kebutuhan</label>
                    <select name="need_category_id" id="need_category_id" class="form-select" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('need_category_id', $need->need_category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="assistance_type" class="form-label required">Jenis Bantuan</label>
                    <select name="assistance_type" id="assistance_type" class="form-select" required>
                        <option value="material" {{ old('assistance_type', $need->assistance_type) == 'material' ? 'selected' : '' }}>Material (Barang Fisik)</option>
                        <option value="non_material" {{ old('assistance_type', $need->assistance_type) == 'non_material' ? 'selected' : '' }}>Nonmaterial (Relawan / Pelatihan)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority" class="form-label required">Tingkat Prioritas</label>
                    <select name="priority" id="priority" class="form-select" required>
                        <option value="rendah" {{ old('priority', $need->priority) == 'rendah' ? 'selected' : '' }}>Rendah</option>
                        <option value="sedang" {{ old('priority', $need->priority) == 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="tinggi" {{ old('priority', $need->priority) == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                        <option value="mendesak" {{ old('priority', $need->priority) == 'mendesak' ? 'selected' : '' }}>Mendesak</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="target_date" class="form-label">Target Batas Waktu</label>
                    <input type="date" id="target_date" name="target_date" class="form-control" value="{{ old('target_date', $need->target_date?->format('Y-m-d')) }}">
                </div>

                <div class="form-group">
                    <label for="quantity_required" class="form-label">Jumlah Diperlukan</label>
                    <input type="number" min="{{ $need->getActivePledgesQuantity() ?: 1 }}" id="quantity_required" name="quantity_required" class="form-control" value="{{ old('quantity_required', $need->quantity_required) }}">
                    @if($activePledgesCount > 0)
                        <div class="form-hint">Minimal: {{ $need->getActivePledgesQuantity() }} (sudah terkomitmen)</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="unit" class="form-label">Satuan</label>
                    <input type="text" id="unit" name="unit" class="form-control" value="{{ old('unit', $need->unit) }}">
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label required">Deskripsi & Urgensi Kebutuhan</label>
                <textarea name="description" id="description" class="form-textarea" style="min-height: 120px;" required>{{ old('description', $need->description) }}</textarea>
            </div>

            <div class="mt-6 pt-4 d-flex justify-between align-center" style="border-top: 1px solid var(--border);">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
