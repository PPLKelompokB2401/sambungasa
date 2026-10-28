@extends('layouts.dashboard')

@section('title', 'Profil & Legalitas Donatur')

@section('content')
<div class="mb-4">
    <h1 style="font-size: 22px; font-weight: 700;">Profil Donatur & Relawan</h1>
    <p class="text-sm text-muted">Lengkapi data identitas dan informasi kontribusi Anda untuk akuntabilitas bantuan.</p>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-body">
        <form action="{{ route('contributor.profile.update') }}" method="POST">
            @csrf

            <h3 style="font-size: 16px; margin-bottom: 16px; border-bottom: 1px solid var(--border); padding-bottom: 8px;">Informasi Akun</h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="name" class="form-label required">Nama Lengkap / Kontak Person</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled style="background: var(--surface);">
                    <div class="form-hint">Email tidak dapat diubah sendiri demi keamanan akun.</div>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Nomor Telepon / WhatsApp</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $contributor->phone) }}" placeholder="08xxxxxxxxxx">
                </div>

                <div class="form-group">
                    <label for="contributor_type" class="form-label required">Kategori Kontributor</label>
                    <select name="contributor_type" id="contributor_type" class="form-select" onchange="toggleTypeSpecificFields(this.value)">
                        <option value="donor" {{ old('contributor_type', $contributor->contributor_type) == 'donor' ? 'selected' : '' }}>Donatur Perorangan</option>
                        <option value="volunteer" {{ old('contributor_type', $contributor->contributor_type) == 'volunteer' ? 'selected' : '' }}>Relawan Pengajar / Ahli</option>
                        <option value="organization" {{ old('contributor_type', $contributor->contributor_type) == 'organization' ? 'selected' : '' }}>Organisasi / Yayasan / CSR Perusahaan</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Alamat Domisili / Kantor</label>
                <textarea name="address" id="address" class="form-textarea" placeholder="Alamat lengkap...">{{ old('address', $contributor->address) }}</textarea>
            </div>

            <!-- Organization Section -->
            <div id="orgSection" style="display: {{ old('contributor_type', $contributor->contributor_type) == 'organization' ? 'block' : 'none' }}; border-top: 1px solid var(--border); padding-top: 16px; margin-top: 16px;">
                <h3 style="font-size: 16px; margin-bottom: 14px;">Data Lembaga / Perusahaan</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="organization_name" class="form-label">Nama Lembaga</label>
                        <input type="text" id="organization_name" name="organization_name" class="form-control" value="{{ old('organization_name', $contributor->organization_name) }}">
                    </div>

                    <div class="form-group">
                        <label for="pic_name" class="form-label">Nama PIC / Penanggung Jawab</label>
                        <input type="text" id="pic_name" name="pic_name" class="form-control" value="{{ old('pic_name', $contributor->pic_name) }}">
                    </div>

                    <div class="form-group">
                        <label for="identity_number" class="form-label">Nomor Izin / SK Kemenkumham / NPWP</label>
                        <input type="text" id="identity_number" name="identity_number" class="form-control" value="{{ old('identity_number', $contributor->identity_number) }}">
                    </div>

                    <div class="form-group">
                        <label for="website" class="form-label">Situs Web Resmi</label>
                        <input type="url" id="website" name="website" class="form-control" value="{{ old('website', $contributor->website) }}" placeholder="https://...">
                    </div>
                </div>
            </div>

            <!-- Volunteer Section -->
            <div id="volunteerSection" style="display: {{ old('contributor_type', $contributor->contributor_type) == 'volunteer' ? 'block' : 'none' }}; border-top: 1px solid var(--border); padding-top: 16px; margin-top: 16px;">
                <h3 style="font-size: 16px; margin-bottom: 14px;">Kualifikasi Relawan (F-02)</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="education" class="form-label">Pendidikan Terakhir</label>
                        <input type="text" id="education" name="education" class="form-control" value="{{ old('education', $volunteer?->education) }}" placeholder="contoh: S1 Pendidikan Fisika UNY">
                    </div>

                    <div class="form-group">
                        <label for="experience_years" class="form-label">Pengalaman Relevan (Tahun)</label>
                        <input type="number" min="0" id="experience_years" name="experience_years" class="form-control" value="{{ old('experience_years', $volunteer?->experience_years ?? 0) }}">
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label for="expertise" class="form-label">Bidang Keahlian / Keterampilan Ajar</label>
                        <input type="text" id="expertise" name="expertise" class="form-control" value="{{ old('expertise', $volunteer?->expertise) }}" placeholder="contoh: Guru Matematika, Literasi Kelas Rendah, IT & Komputer">
                    </div>

                    <div class="form-group">
                        <label for="availability" class="form-label">Ketersediaan Waktu</label>
                        <select name="availability" id="availability" class="form-select">
                            <option value="flexible" {{ old('availability', $volunteer?->availability) == 'flexible' ? 'selected' : '' }}>Fleksibel</option>
                            <option value="weekends" {{ old('availability', $volunteer?->availability) == 'weekends' ? 'selected' : '' }}>Akhir Pekan (Sabtu/Minggu)</option>
                            <option value="full_time" {{ old('availability', $volunteer?->availability) == 'full_time' ? 'selected' : '' }}>Penuh Waktu (Penempatan Periode)</option>
                            <option value="remote_only" {{ old('availability', $volunteer?->availability) == 'remote_only' ? 'selected' : '' }}>Daring / Jarak Jauh Saja</option>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label for="bio" class="form-label">Bio Singkat & Motivasi Berbagi</label>
                        <textarea name="bio" id="bio" class="form-textarea" placeholder="Tuliskan pengalaman mengajar atau motivasi membantu sekolah 3T...">{{ old('bio', $volunteer?->bio) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4" style="border-top: 1px solid var(--border);">
                <button type="submit" class="btn btn-primary">Simpan Perubahan Profil</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleTypeSpecificFields(val) {
        document.getElementById('orgSection').style.display = val === 'organization' ? 'block' : 'none';
        document.getElementById('volunteerSection').style.display = val === 'volunteer' ? 'block' : 'none';
    }
</script>
@endsection
