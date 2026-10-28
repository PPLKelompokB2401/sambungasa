@extends('layouts.dashboard')

@section('title', 'Pelacakan Bantuan: ' . $pledge->tracking_code)

@section('content')
<div class="mb-4">
    <a href="{{ route('contributor.pledges.index') }}" class="text-sm">← Kembali ke Komitmen Saya</a>
</div>

<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <div class="text-xs text-muted font-medium">KODE PELACAKAN RESMI (F-16)</div>
        <h1 style="font-size: 24px; font-weight: 700; font-family: var(--font-mono); color: var(--primary);">
            {{ $pledge->tracking_code }}
        </h1>
        <p class="text-sm text-muted">Untuk Kebutuhan: <strong>{{ $pledge->need->title }}</strong></p>
    </div>
    <div>
        <span class="badge {{ $pledge->status === 'completed' ? 'badge-success' : ($pledge->status === 'rejected' ? 'badge-danger' : 'badge-info') }}" style="font-size: 13px; padding: 6px 14px;">
            Status: {{ $pledge->status_label }}
        </span>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <!-- Tracking Timeline Column -->
    <div style="grid-column: span 2; display: flex; flex-direction: column; gap: 24px;">
        <!-- Timeline Card -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Riwayat Pelacakan Status Bantuan (F-14)</h3>
            </div>
            <div class="card-body">
                <div style="position: relative; padding-left: 28px;">
                    <!-- Timeline vertical line -->
                    <div style="position: absolute; left: 10px; top: 8px; bottom: 8px; width: 2px; background: var(--border);"></div>

                    @foreach($pledge->trackingLogs as $log)
                        <div style="position: relative; margin-bottom: 24px;">
                            <!-- Dot -->
                            <div style="position: absolute; left: -28px; top: 2px; width: 18px; height: 18px; border-radius: 50%; background: {{ $loop->last ? 'var(--primary)' : 'var(--success)' }}; border: 3px solid #fff; box-shadow: 0 0 0 2px var(--border);"></div>

                            <div class="d-flex justify-between align-center">
                                <span class="badge badge-neutral" style="font-size: 10px;">{{ strtoupper($log->new_status) }}</span>
                                <span class="text-xs text-muted">{{ $log->created_at->format('d M Y, H:i') }}</span>
                            </div>

                            <div class="text-sm font-medium text-dark mt-1">{{ $log->notes }}</div>

                            @if($log->location)
                                <div class="text-xs text-muted mt-1">📍 Lokasi: {{ $log->location }}</div>
                            @endif

                            <div class="text-xs text-muted mt-1">Dicatat oleh: {{ $log->creator->name }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Evidence Upload and Gallery -->
        <div class="card">
            <div class="card-header d-flex justify-between align-center">
                <h3 style="font-size: 16px;">Dokumentasi & Bukti Penyaluran (F-13)</h3>
                <span class="badge badge-neutral">Private Storage Terenkripsi</span>
            </div>
            <div class="card-body">
                <!-- Upload Form -->
                <form action="{{ route('evidence.upload', $pledge->id) }}" method="POST" enctype="multipart/form-data" class="p-3 mb-4" style="background: var(--surface); border: 1px dashed var(--border); border-radius: var(--radius-sm);">
                    @csrf
                    <strong style="font-size: 13px; display: block; margin-bottom: 8px;">+ Unggah Berkas Bukti (Resi Pengiriman / Foto Barang / BAST)</strong>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="form-label required text-xs">Pilih Berkas (JPG, PNG, PDF, Maks 5MB)</label>
                            <input type="file" name="evidence_file" class="form-control" required style="padding: 5px;">
                        </div>

                        <div>
                            <label class="form-label required text-xs">Jenis Bukti</label>
                            <select name="evidence_type" class="form-select" required>
                                <option value="delivery_receipt">Resi Kargo / Pengiriman</option>
                                <option value="photo_documentation">Foto Dokumentasi Barang</option>
                                <option value="activity_report">Laporan Kegiatan Relawan</option>
                                <option value="handover_bast">Berita Acara Serah Terima (BAST)</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label text-xs">Keterangan / Caption Berkas</label>
                        <input type="text" name="caption" class="form-control" placeholder="contoh: Resi kargo pesawat rute Jayapura - Puncak...">
                    </div>

                    <button type="submit" class="btn btn-dark btn-sm">Simpan Bukti ke Arsip</button>
                </form>

                <!-- Evidence List -->
                <div>
                    <h4 style="font-size: 13px; font-weight: 700; margin-bottom: 8px;">Daftar Berkas Bukti Tersimpan:</h4>
                    <div class="d-flex flex-column gap-2">
                        @forelse($pledge->evidenceFiles as $ev)
                            <div class="p-3 d-flex justify-between align-center" style="border: 1px solid var(--border); border-radius: var(--radius-sm); background: #fff;">
                                <div>
                                    <div class="font-bold text-dark text-sm">{{ $ev->original_name }}</div>
                                    <div class="text-xs text-muted">
                                        {{ ucfirst(str_replace('_', ' ', $ev->evidence_type)) }} • {{ $ev->formatted_size }} • Diunggah oleh: {{ $ev->uploader->name }} ({{ $ev->created_at->format('d/m/Y H:i') }})
                                    </div>
                                    @if($ev->caption)
                                        <div class="text-xs text-dark mt-1"><em>"{{ $ev->caption }}"</em></div>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('evidence.download', $ev->id) }}" class="btn btn-outline btn-sm" target="_blank">
                                        Unduh / Buka ↗
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-muted text-center py-3">Belum ada berkas bukti yang diunggah.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Column -->
    <div style="grid-column: span 1; display: flex; flex-direction: column; gap: 20px;">
        <!-- Commitment Details -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Rincian Komitmen</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-xs text-muted">JUMLAH KONTRIBUSI:</span>
                    <div style="font-size: 18px; font-weight: 700; color: var(--text-primary);">
                        {{ $pledge->quantity }} {{ $pledge->need->unit }}
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-xs text-muted">ESTIMASI WAKTU PENYALURAN:</span>
                    <div class="text-sm font-medium">
                        {{ $pledge->estimated_delivery_at ? $pledge->estimated_delivery_at->format('d F Y') : 'Sesuai koordinasi' }}
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-xs text-muted">CATATAN DONATUR:</span>
                    <p class="text-sm text-dark mt-1" style="white-space: pre-line;">{{ $pledge->notes }}</p>
                </div>

                @if($pledge->rejection_reason)
                    <div class="p-2" style="background: var(--error-bg); border-radius: var(--radius-xs);">
                        <span class="text-xs font-bold text-danger">ALASAN PENOLAKAN:</span>
                        <div class="text-xs text-dark mt-1">{{ $pledge->rejection_reason }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- School Destination -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Sekolah Penerima Manfaat</h3>
            </div>
            <div class="card-body">
                <div class="font-bold text-dark">{{ $pledge->need->schoolProfile->school_name }}</div>
                <div class="text-xs text-muted mt-1">📍 {{ $pledge->need->schoolProfile->regency?->name }}, {{ $pledge->need->schoolProfile->province?->name }}</div>
                <div class="text-sm text-dark mt-2">
                    <strong>Alamat Penyaluran:</strong><br>
                    {{ $pledge->need->schoolProfile->address }}
                </div>
                <div class="text-xs text-muted mt-2">
                    Kepala Sekolah: {{ $pledge->need->schoolProfile->principal_name ?: '—' }}<br>
                    Kontak: {{ $pledge->need->schoolProfile->phone ?: 'Tersedia setelah verifikasi' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
