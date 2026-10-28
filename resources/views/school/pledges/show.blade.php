@extends('layouts.dashboard')

@section('title', 'Detail Bantuan: ' . $pledge->tracking_code)

@section('content')
<div class="mb-4">
    <a href="{{ route('school.pledges.index') }}" class="text-sm">← Kembali ke Bantuan Masuk</a>
</div>

<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <div class="text-xs text-muted font-medium">KODE PELACAKAN RESMI</div>
        <h1 style="font-size: 24px; font-weight: 700; font-family: var(--font-mono); color: var(--primary);">
            {{ $pledge->tracking_code }}
        </h1>
        <p class="text-sm text-muted">Kebutuhan: <strong>{{ $pledge->need->title }}</strong></p>
    </div>

    <!-- Actions / Confirmation -->
    <div class="d-flex align-center gap-2">
        @if(in_array($pledge->status, ['in_delivery', 'verified']))
            <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('receiptModal').style.display='flex'">
                ✓ Konfirmasi Penerimaan Bantuan (F-14)
            </button>
        @else
            <span class="badge {{ $pledge->status === 'completed' ? 'badge-success' : 'badge-neutral' }}" style="font-size: 13px; padding: 6px 14px;">
                Status: {{ $pledge->status_label }}
            </span>
        @endif
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <!-- Tracking Timeline & Evidence Column -->
    <div style="grid-column: span 2; display: flex; flex-direction: column; gap: 24px;">
        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Riwayat Pelacakan Bantuan</h3>
            </div>
            <div class="card-body">
                <div style="position: relative; padding-left: 28px;">
                    <div style="position: absolute; left: 10px; top: 8px; bottom: 8px; width: 2px; background: var(--border);"></div>

                    @foreach($pledge->trackingLogs as $log)
                        <div style="position: relative; margin-bottom: 24px;">
                            <div style="position: absolute; left: -28px; top: 2px; width: 18px; height: 18px; border-radius: 50%; background: {{ $loop->last ? 'var(--primary)' : 'var(--success)' }}; border: 3px solid #fff; box-shadow: 0 0 0 2px var(--border);"></div>

                            <div class="d-flex justify-between align-center">
                                <span class="badge badge-neutral" style="font-size: 10px;">{{ strtoupper($log->new_status) }}</span>
                                <span class="text-xs text-muted">{{ $log->created_at->format('d M Y, H:i') }}</span>
                            </div>

                            <div class="text-sm font-medium text-dark mt-1">{{ $log->notes }}</div>

                            @if($log->location)
                                <div class="text-xs text-muted mt-1">📍 {{ $log->location }}</div>
                            @endif

                            <div class="text-xs text-muted mt-1">Oleh: {{ $log->creator->name }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Evidence Documentation -->
        <div class="card">
            <div class="card-header d-flex justify-between align-center">
                <h3 style="font-size: 16px;">Dokumentasi & Bukti Serah Terima (F-13)</h3>
                <span class="badge badge-neutral">Private Storage</span>
            </div>
            <div class="card-body">
                <!-- Upload form for School -->
                <form action="{{ route('evidence.upload', $pledge->id) }}" method="POST" enctype="multipart/form-data" class="p-3 mb-4" style="background: var(--surface); border: 1px dashed var(--border); border-radius: var(--radius-sm);">
                    @csrf
                    <strong style="font-size: 13px; display: block; margin-bottom: 8px;">+ Unggah Bukti dari Pihak Sekolah (Foto Siswa Menerima / BAST Bertandatangan)</strong>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="form-label required text-xs">Pilih Berkas (JPG, PNG, PDF, Maks 5MB)</label>
                            <input type="file" name="evidence_file" class="form-control" required style="padding: 5px;">
                        </div>

                        <div>
                            <label class="form-label required text-xs">Jenis Bukti</label>
                            <select name="evidence_type" class="form-select" required>
                                <option value="handover_bast">Berita Acara Serah Terima (BAST)</option>
                                <option value="photo_documentation">Foto Serah Terima Siswa / Guru</option>
                                <option value="activity_report">Laporan Pemanfaatan Barang</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label text-xs">Keterangan Singkat</label>
                        <input type="text" name="caption" class="form-control" placeholder="contoh: Penandatanganan BAST oleh Kepala Sekolah...">
                    </div>

                    <button type="submit" class="btn btn-dark btn-sm">Simpan Bukti Sekolah</button>
                </form>

                <!-- Evidence list -->
                <div>
                    <h4 style="font-size: 13px; font-weight: 700; margin-bottom: 8px;">Berkas Bukti Tersimpan:</h4>
                    <div class="d-flex flex-column gap-2">
                        @forelse($pledge->evidenceFiles as $ev)
                            <div class="p-3 d-flex justify-between align-center" style="border: 1px solid var(--border); border-radius: var(--radius-sm); background: #fff;">
                                <div>
                                    <div class="font-bold text-dark text-sm">{{ $ev->original_name }}</div>
                                    <div class="text-xs text-muted">
                                        {{ ucfirst(str_replace('_', ' ', $ev->evidence_type)) }} • {{ $ev->formatted_size }} • Oleh: {{ $ev->uploader->name }} ({{ $ev->created_at->format('d/m/Y H:i') }})
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
        <!-- Donors Info -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Donatur / Relawan</h3>
            </div>
            <div class="card-body">
                <div class="font-bold text-dark" style="font-size: 16px;">{{ $pledge->contributorProfile->display_name }}</div>
                <div class="text-xs text-muted mt-1">{{ ucfirst($pledge->contributorProfile->contributor_type) }}</div>
                @if($pledge->contributorProfile->phone)
                    <div class="text-xs text-dark mt-2">Kontak: {{ $pledge->contributorProfile->phone }}</div>
                @endif
                <div class="mt-3 pt-3" style="border-top: 1px solid var(--border);">
                    <span class="text-xs text-muted">CATATAN KOMITMEN:</span>
                    <p class="text-sm text-dark mt-1">{{ $pledge->notes }}</p>
                </div>
            </div>
        </div>

        <!-- Quantity Summary -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Kuantitas Bantuan</h3>
            </div>
            <div class="card-body">
                <div style="font-size: 26px; font-weight: 700; color: var(--primary);">
                    {{ $pledge->quantity }} {{ $pledge->need->unit }}
                </div>
                <div class="text-xs text-muted mt-1">Untuk Kebutuhan: {{ $pledge->need->title }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Receipt Confirmation -->
<div id="receiptModal" class="modal-backdrop" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ route('school.pledges.confirmReceipt', $pledge->id) }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 style="font-size: 16px; color: var(--success);">Konfirmasi Penerimaan Bantuan (F-14)</h3>
            </div>
            <div class="card-body">
                <p class="text-sm text-muted mb-3">
                    Pastikan barang atau pelaksanaan relawan telah benar-benar tiba di sekolah dalam kondisi baik.
                </p>

                <div class="form-group">
                    <label for="recipient_name" class="form-label required">Nama Penerima / Guru Penanggung Jawab</label>
                    <input type="text" id="recipient_name" name="recipient_name" class="form-control" value="{{ auth()->user()->name }}" required>
                </div>

                <div class="form-group">
                    <label for="receipt_notes" class="form-label required">Catatan Kondisi Barang / Penerimaan</label>
                    <textarea name="notes" id="receipt_notes" class="form-textarea" required placeholder="contoh: Paket telah tiba di sekolah dalam kardus bersegel utuh dan diperiksa oleh guru..."></textarea>
                </div>
            </div>
            <div class="card-footer d-flex justify-end gap-2">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('receiptModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-success btn-sm">Konfirmasi Terima</button>
            </div>
        </form>
    </div>
</div>
@endsection
