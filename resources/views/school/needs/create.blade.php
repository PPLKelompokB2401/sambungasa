@extends('layouts.dashboard')

@section('title', 'Ajukan Kebutuhan Baru — ' . $school->school_name)

@section('content')
<div class="mb-4">
    <a href="{{ route('school.needs.index') }}" class="text-sm">← Kembali ke Daftar Kebutuhan</a>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <div>
            <h2 style="font-size: 18px; font-weight: 700;">Ajukan Kebutuhan Sekolah 3T (F-05)</h2>
            <p class="text-sm text-muted">Kebutuhan akan ditinjau administrator sebelum diterbitkan secara publik.</p>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('school.needs.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="title" class="form-label required">Judul Kebutuhan</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required placeholder="contoh: Pengadaan 50 Buku Cerita Bergambar untuk Siswa Kelas 1-3">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="need_category_id" class="form-label required">Kategori Kebutuhan (F-06)</label>
                    <select name="need_category_id" id="need_category_id" class="form-select" required onchange="syncAssistanceType(this)">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" data-type="{{ $cat->assistance_type }}" {{ old('need_category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ ucfirst($cat->assistance_type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="assistance_type" class="form-label required">Jenis Bantuan</label>
                    <select name="assistance_type" id="assistance_type" class="form-select" required>
                        <option value="material" {{ old('assistance_type') == 'material' ? 'selected' : '' }}>Material (Barang / Perlengkapan Fisik)</option>
                        <option value="non_material" {{ old('assistance_type') == 'non_material' ? 'selected' : '' }}>Nonmaterial (Tenaga Relawan / Pelatihan)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority" class="form-label required">Tingkat Prioritas (F-07)</label>
                    <select name="priority" id="priority" class="form-select" required>
                        <option value="rendah" {{ old('priority') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                        <option value="sedang" {{ old('priority', 'sedang') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="tinggi" {{ old('priority') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                        <option value="mendesak" {{ old('priority') == 'mendesak' ? 'selected' : '' }}>Mendesak (Darurat Pembelajaran)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="target_date" class="form-label">Target Batas Waktu Kebutuhan</label>
                    <input type="date" id="target_date" name="target_date" class="form-control" value="{{ old('target_date') }}">
                </div>

                <div class="form-group">
                    <label for="quantity_required" class="form-label">Jumlah / Kapasitas yang Diperlukan</label>
                    <input type="number" min="1" id="quantity_required" name="quantity_required" class="form-control" value="{{ old('quantity_required') }}" placeholder="contoh: 50">
                </div>

                <div class="form-group">
                    <label for="unit" class="form-label">Satuan</label>
                    <input type="text" id="unit" name="unit" class="form-control" value="{{ old('unit') }}" placeholder="contoh: eksemplar, unit, set, orang relawan">
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label required">Deskripsi & Urgensi Kebutuhan</label>
                <textarea name="description" id="description" class="form-textarea" style="min-height: 120px;" required placeholder="Jelaskan kondisi saat ini di sekolah, siapa penerima manfaat, dan bagaimana bantuan ini akan digunakan secara bertanggung jawab...">{{ old('description') }}</textarea>
                <div class="form-hint">Jelaskan secara transparan agar calon donatur/relawan terdorong memberikan kontribusi nyata.</div>
            </div>

            <div class="mt-6 pt-4" style="border-top: 1px solid var(--border);">
                <button type="submit" class="btn btn-primary">Kirimkan untuk Moderasi Admin</button>
            </div>
        </form>
    </div>
</div>

<script>
    function syncAssistanceType(sel) {
        const selected = sel.options[sel.selectedIndex];
        const type = selected.getAttribute('data-type');
        if (type) {
            document.getElementById('assistance_type').value = type;
        }
    }
</script>
@endsection
