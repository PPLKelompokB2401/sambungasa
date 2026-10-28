@extends('layouts.dashboard')

@section('title', 'Profil & Lokasi Sekolah: ' . $school->school_name)

@section('content')
<div class="mb-4">
    <h1 style="font-size: 22px; font-weight: 700;">Profil & Lokasi Sekolah 3T (F-01)</h1>
    <p class="text-sm text-muted">Pastikan data lokasi sesuai referensi wilayah 3T untuk verifikasi administrator.</p>
</div>

<div class="card" style="max-width: 860px;">
    <div class="card-header d-flex justify-between align-center">
        <h3 style="font-size: 16px;">Formulir Profil Sekolah</h3>
        <div>
            Status 3T: 
            <span class="badge {{ $school->isVerified() ? 'badge-success' : ($school->verification_status == 'needs_revision' ? 'badge-warning' : 'badge-neutral') }}">
                {{ $school->verification_status }}
            </span>
        </div>
    </div>

    <div class="card-body">
        @if($school->verification_notes)
            <div class="alert alert-info mb-4">
                <span>ℹ</span>
                <div>
                    <strong>Catatan Admin Verifikator:</strong>
                    <div class="mt-1">{{ $school->verification_notes }}</div>
                </div>
            </div>
        @endif

        <form action="{{ route('school.profile.update') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group" style="grid-column: span 2;">
                    <label for="school_name" class="form-label required">Nama Resmi Sekolah</label>
                    <input type="text" id="school_name" name="school_name" class="form-control" value="{{ old('school_name', $school->school_name) }}" required placeholder="contoh: SD Inpres Waisilip">
                </div>

                <div class="form-group">
                    <label for="npsn" class="form-label">Nomor Pokok Sekolah Nasional (NPSN)</label>
                    <input type="text" id="npsn" name="npsn" class="form-control" value="{{ old('npsn', $school->npsn) }}" placeholder="8 digit angka">
                </div>

                <div class="form-group">
                    <label for="principal_name" class="form-label">Nama Kepala Sekolah / Penanggung Jawab</label>
                    <input type="text" id="principal_name" name="principal_name" class="form-control" value="{{ old('principal_name', $school->principal_name) }}" placeholder="Nama beserta gelar">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Nomor Kontak / WhatsApp Sekolah</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $school->phone) }}" placeholder="08xxxxxxxxxx">
                </div>

                <div class="form-group">
                    <label for="postal_code" class="form-label">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-control" value="{{ old('postal_code', $school->postal_code) }}">
                </div>
            </div>

            <h3 style="font-size: 16px; margin: 20px 0 14px; border-top: 1px solid var(--border); padding-top: 16px;">
                Lokasi & Wilayah 3T (F-04)
            </h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="province_id" class="form-label required">Provinsi</label>
                    <select name="province_id" id="province_id" class="form-select" required onchange="filterRegencies(this.value)">
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}" {{ old('province_id', $school->province_id) == $prov->id ? 'selected' : '' }}>
                                {{ $prov->name }} ({{ $prov->island }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="regency_id" class="form-label required">Kabupaten / Kota</label>
                    <select name="regency_id" id="regency_id" class="form-select" required>
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                        @foreach($regencies as $reg)
                            <option value="{{ $reg->id }}" data-parent="{{ $reg->parent_id }}" {{ old('regency_id', $school->regency_id) == $reg->id ? 'selected' : '' }}>
                                {{ $reg->name }} {{ $reg->is_3t ? '★ [Wilayah 3T]' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Tanda ★ [Wilayah 3T] menandakan daerah prioritas 3T resmi.</div>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="district" class="form-label">Kecamatan / Distrik</label>
                    <input type="text" id="district" name="district" class="form-control" value="{{ old('district', $school->district) }}" placeholder="contoh: Distrik Ilaga">
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="address" class="form-label required">Alamat Lengkap Sekolah</label>
                    <textarea name="address" id="address" class="form-textarea" required placeholder="Jalan, kampung/desa, RT/RW...">{{ old('address', $school->address) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="latitude" class="form-label">Latitude Koordinat Geografis</label>
                    <input type="text" id="latitude" name="latitude" class="form-control" value="{{ old('latitude', $school->latitude) }}" placeholder="contoh: -3.7381200">
                </div>

                <div class="form-group">
                    <label for="longitude" class="form-label">Longitude Koordinat Geografis</label>
                    <input type="text" id="longitude" name="longitude" class="form-control" value="{{ old('longitude', $school->longitude) }}" placeholder="contoh: 137.6482100">
                </div>
            </div>

            <h3 style="font-size: 16px; margin: 20px 0 14px; border-top: 1px solid var(--border); padding-top: 16px;">
                Informasi Kapasitas
            </h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="student_count" class="form-label">Jumlah Murid Aktif</label>
                    <input type="number" min="0" id="student_count" name="student_count" class="form-control" value="{{ old('student_count', $school->student_count) }}">
                </div>

                <div class="form-group">
                    <label for="teacher_count" class="form-label">Jumlah Tenaga Pendidik / Guru</label>
                    <input type="number" min="0" id="teacher_count" name="teacher_count" class="form-control" value="{{ old('teacher_count', $school->teacher_count) }}">
                </div>
            </div>

            <div class="mt-6 pt-4" style="border-top: 1px solid var(--border);">
                <button type="submit" class="btn btn-primary">Simpan Profil Sekolah</button>
            </div>
        </form>
    </div>
</div>

<script>
    function filterRegencies(provId) {
        const regSelect = document.getElementById('regency_id');
        const options = regSelect.querySelectorAll('option');

        options.forEach(opt => {
            if (!opt.value) return;
            const parent = opt.getAttribute('data-parent');
            if (!provId || parent === provId) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const provVal = document.getElementById('province_id').value;
        if (provVal) {
            filterRegencies(provVal);
        }
    });
</script>
@endsection
