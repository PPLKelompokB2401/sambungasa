@extends('layouts.guest')

@section('title', 'Daftar Akun Baru — SambungAsa')

@section('content')
<div class="card" style="box-shadow: var(--elevation-2);">
    <div class="card-header" style="background: #fff; padding: 20px 24px;">
        <div>
            <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 4px;">Daftar Akun SambungAsa</h2>
            <p class="text-sm text-muted">Pilih peran akun Anda untuk memulai kolaborasi pendidikan 3T.</p>
        </div>
    </div>

    <div class="card-body" style="padding: 24px;">
        <form action="{{ route('register') }}" method="POST" id="registerForm">
            @csrf

            <!-- Role Selector -->
            <div class="form-group">
                <label class="form-label required">Daftar Sebagai:</label>
                <div class="d-flex gap-2">
                    <label style="flex: 1; border: 1px solid var(--border); padding: 12px; border-radius: var(--radius-sm); cursor: pointer; text-align: center;" id="labelRoleSchool">
                        <input type="radio" name="role" value="school" {{ (old('role', $defaultRole) === 'school') ? 'checked' : '' }} onchange="toggleRoleFields('school')" style="margin-right: 6px;">
                        <strong>Perwakilan Sekolah 3T</strong>
                        <div class="text-xs text-muted mt-1">Mengajukan & mengelola kebutuhan sekolah</div>
                    </label>

                    <label style="flex: 1; border: 1px solid var(--border); padding: 12px; border-radius: var(--radius-sm); cursor: pointer; text-align: center;" id="labelRoleContributor">
                        <input type="radio" name="role" value="contributor" {{ (old('role', $defaultRole) === 'contributor') ? 'checked' : '' }} onchange="toggleRoleFields('contributor')" style="margin-right: 6px;">
                        <strong>Donatur / Relawan / Mitra</strong>
                        <div class="text-xs text-muted mt-1">Memberi bantuan material atau nonmaterial</div>
                    </label>
                </div>
            </div>

            <!-- Contributor Specific: Type -->
            <div id="contributorTypeGroup" class="form-group" style="display: {{ (old('role', $defaultRole) === 'contributor') ? 'block' : 'none' }};">
                <label for="contributor_type" class="form-label required">Jenis Kontribusi</label>
                <select name="contributor_type" id="contributor_type" class="form-select" onchange="toggleOrgField(this.value)">
                    <option value="donor" {{ old('contributor_type') == 'donor' ? 'selected' : '' }}>Donatur Perorangan (Bantuan Material/Barang)</option>
                    <option value="volunteer" {{ old('contributor_type') == 'volunteer' ? 'selected' : '' }}>Relawan Pengajar / Ahli (Bantuan Nonmaterial/Pelatihan)</option>
                    <option value="organization" {{ old('contributor_type') == 'organization' ? 'selected' : '' }}>Organisasi / Yayasan / Perusahaan (CSR)</option>
                </select>
            </div>

            <!-- Organization Name (if organization) -->
            <div id="orgNameGroup" class="form-group" style="display: {{ old('contributor_type') == 'organization' ? 'block' : 'none' }};">
                <label for="organization_name" class="form-label">Nama Lembaga / Perusahaan / Yayasan</label>
                <input type="text" id="organization_name" name="organization_name" class="form-control" value="{{ old('organization_name') }}" placeholder="contoh: Yayasan Peduli Negeri">
            </div>

            <!-- School Specific: School Name -->
            <div id="schoolNameGroup" class="form-group" style="display: {{ (old('role', $defaultRole) === 'school') ? 'block' : 'none' }};">
                <label for="school_name" class="form-label required">Nama Resmi Sekolah</label>
                <input type="text" id="school_name" name="school_name" class="form-control" value="{{ old('school_name') }}" placeholder="contoh: SD Inpres Waisilip">
                <div class="form-hint">Data profil dan lokasi 3T lengkap dapat diisi setelah pendaftaran.</div>
            </div>

            <!-- Name Field -->
            <div class="form-group">
                <label for="name" class="form-label required" id="nameLabel">
                    {{ (old('role', $defaultRole) === 'school') ? 'Nama Kepala Sekolah / Penanggung Jawab' : 'Nama Lengkap' }}
                </label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Nama lengkap">
            </div>

            <!-- Email Field -->
            <div class="form-group">
                <label for="email" class="form-label required">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="email@domain.com">
                <div class="form-hint">Email aktif untuk verifikasi dan notifikasi status bantuan.</div>
            </div>

            <!-- Phone Field -->
            <div class="form-group">
                <label for="phone" class="form-label">Nomor Telepon / WhatsApp</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="contoh: 081234567890">
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label required">Kata Sandi (Minimal 8 karakter)</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Buat kata sandi akun">
            </div>

            <!-- Password Confirmation -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label required">Konfirmasi Kata Sandi</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Ulangi kata sandi">
            </div>

            <div class="mt-6">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Daftarkan Akun Sekarang</button>
            </div>
        </form>

        <div class="mt-4" style="border-top: 1px solid var(--border); padding-top: 16px; text-align: center;">
            <p class="text-sm text-muted">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" style="font-weight: 500;">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>

<script>
    function toggleRoleFields(role) {
        const contributorGroup = document.getElementById('contributorTypeGroup');
        const schoolGroup = document.getElementById('schoolNameGroup');
        const nameLabel = document.getElementById('nameLabel');

        if (role === 'school') {
            contributorGroup.style.display = 'none';
            schoolGroup.style.display = 'block';
            nameLabel.innerText = 'Nama Kepala Sekolah / Penanggung Jawab';
            document.getElementById('orgNameGroup').style.display = 'none';
        } else {
            contributorGroup.style.display = 'block';
            schoolGroup.style.display = 'none';
            nameLabel.innerText = 'Nama Lengkap';
            toggleOrgField(document.getElementById('contributor_type').value);
        }
    }

    function toggleOrgField(val) {
        const orgGroup = document.getElementById('orgNameGroup');
        if (val === 'organization') {
            orgGroup.style.display = 'block';
        } else {
            orgGroup.style.display = 'none';
        }
    }
</script>
@endsection
