@extends('layouts.app')

@section('title', '403 — Akses Ditolak')

@section('content')
<div class="container py-12" style="text-align: center; max-width: 600px;">
    <div style="font-size: 64px; font-weight: 700; color: var(--primary); line-height: 1;">403</div>
    <h1 class="mt-3">Akses Ditolak</h1>
    <p class="text-muted mt-2">
        {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses untuk membuka halaman atau data ini. Pastikan Anda masuk dengan akun yang memiliki kewenangan yang sesuai.' }}
    </p>
    <div class="mt-6 d-flex justify-center gap-3">
        <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">Dashboard Admin</a>
            @elseif(auth()->user()->isSchool())
                <a href="{{ route('school.dashboard') }}" class="btn btn-outline">Dashboard Sekolah</a>
            @else
                <a href="{{ route('contributor.dashboard') }}" class="btn btn-outline">Dashboard Donatur</a>
            @endif
        @endauth
    </div>
</div>
@endsection
