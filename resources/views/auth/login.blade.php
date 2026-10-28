@extends('layouts.guest')

@section('title', 'Masuk ke SambungAsa')

@section('content')
<div class="card" style="box-shadow: var(--elevation-2);">
    <div class="card-header" style="background: #fff; padding: 20px 24px;">
        <div>
            <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 4px;">Masuk ke Akun</h2>
            <p class="text-sm text-muted">Akses dashboard sekolah, donatur, atau administrator.</p>
        </div>
    </div>

    <div class="card-body" style="padding: 24px;">
        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label required">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="contoh: nama@domain.com">
            </div>

            <div class="form-group">
                <div class="d-flex justify-between align-center mb-1">
                    <label for="password" class="form-label required" style="margin-bottom:0;">Kata Sandi</label>
                </div>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Masukkan kata sandi akun">
            </div>

            <div class="form-group d-flex align-center gap-2">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }} style="width: 16px; height: 16px;">
                <label for="remember" style="font-size: 13px; color: var(--text-secondary); cursor: pointer; margin-bottom: 0;">Ingat saya di perangkat ini</label>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk Sekarang</button>
            </div>
        </form>

        <div class="mt-6" style="border-top: 1px solid var(--border); padding-top: 18px; text-align: center;">
            <p class="text-sm text-muted">
                Belum memiliki akun?
                <a href="{{ route('register') }}" style="font-weight: 500;">Daftar akun SambungAsa</a>
            </p>
        </div>

        <div class="mt-4 p-3" style="background: var(--surface); border-radius: var(--radius-sm); font-size: 12px; color: var(--text-secondary);">
            <strong style="color: var(--text-primary); display: block; margin-bottom: 4px;">Akun Demo Tersedia:</strong>
            <div>• Admin: <code>admin@sambungasa.id</code> / <code>password123</code></div>
            <div>• Sekolah (Papua): <code>sdn.waisilip@sambungasa.sch.id</code> / <code>password123</code></div>
            <div>• Donatur (Yayasan): <code>yayasan.lentera@sambungasa.id</code> / <code>password123</code></div>
            <div>• Relawan Pengajar: <code>budi.santoso@sambungasa.id</code> / <code>password123</code></div>
        </div>
    </div>
</div>
@endsection
