@extends('layouts.app')

@section('title', '404 — Halaman Tidak Ditemukan')

@section('content')
<div class="container py-12" style="text-align: center; max-width: 600px;">
    <div style="font-size: 64px; font-weight: 700; color: var(--text-primary); line-height: 1;">404</div>
    <h1 class="mt-3">Halaman Tidak Ditemukan</h1>
    <p class="text-muted mt-2">
        Halaman atau data kebutuhan/sekolah yang Anda cari tidak tersedia, telah dihapus, atau tautan yang Anda tuju salah.
    </p>
    <div class="mt-6 d-flex justify-center gap-3">
        <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
        <a href="{{ route('public.needs.index') }}" class="btn btn-outline">Cari Kebutuhan</a>
    </div>
</div>
@endsection
