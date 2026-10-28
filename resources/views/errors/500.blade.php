@extends('layouts.app')

@section('title', '500 — Terjadi Kesalahan')

@section('content')
<div class="container py-12" style="text-align: center; max-width: 600px;">
    <div style="font-size: 64px; font-weight: 700; color: var(--error); line-height: 1;">500</div>
    <h1 class="mt-3">Terjadi Kesalahan Server</h1>
    <p class="text-muted mt-2">
        Sistem sedang mengalami kendala teknis internal. Tim teknis telah mencatat kejadian ini. Silakan coba kembali beberapa saat lagi.
    </p>
    <div class="mt-6 d-flex justify-center gap-3">
        <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
</div>
@endsection
