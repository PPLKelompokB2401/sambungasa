@extends('layouts.dashboard')

@section('title', 'Kelola Pledge: ' . $pledge->tracking_code)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.pledges.index') }}" class="text-sm">← Kembali ke Daftar Komitmen Bantuan</a>
</div>

<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <div class="text-xs text-muted font-medium">KODE PELACAKAN RESMI</div>
        <h1 style="font-size: 24px; font-weight: 700; font-family: var(--font-mono); color: var(--primary);">
            {{ $pledge->tracking_code }}
        </h1>
        <p class="text-sm text-muted">
            Donatur: <strong>{{ $pledge->contributorProfile->display_name }}</strong> ➔ Sekolah: <strong>{{ $pledge->need->schoolProfile->school_name }}</strong>
        </p>
    </div>

    <!-- Actions -->
    <div class="d-flex align-center gap-2">
        @if($pledge->status === 'submitted')
            <form action="{{ route('admin.pledges.approve', $pledge->id) }}" method="POST" onsubmit="return confirm('Setujui komitmen bantuan ini?')">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">✓ Setujui Komitmen</button>
            </form>

            <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('rejectModal').style.display='flex'">
                ✕ Tolak Komitmen
            </button>
        @elseif(!in_array($pledge->status, ['completed', 'rejected', 'cancelled']))
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('statusModal').style.display='flex'">
                ➔ Perbarui Status Bantuan (F-12)
            </button>
        @else
            <span class="badge {{ $pledge->status == 'completed' ? 'badge-success' : 'badge-danger' }}" style="font-size: 13px; padding: 6px 14px;">
                Status: {{ $pledge->status_label }}
            </span>
        @endif
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <!-- Tracking Timeline Column -->
    <div style="grid-column: span 2; display: flex; flex-direction: column; gap: 24px;">
        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Riwayat Pelacakan Status Bantuan (F-14)</h3>
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
                                <div class="text-xs text-muted mt-1">📍 Lokasi: {{ $log->location }}</div>
                            @endif

                            <div class="text-xs text-muted mt-1">Oleh: {{ $log->creator->name }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Evidence Files -->
        <div class="card">
            <div class="card-header d-flex justify-between align-center">
                <h3 style="font-size: 16px;">Arsip Bukti & Dokumen Penyaluran (F-13)</h3>
                <span class="badge badge-neutral">Private Storage</span>
            </div>
            <div class="card-body">
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
                                    Unduh / Lihat Berkas ↗
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-muted text-center py-4">Belum ada dokumen bukti yang diunggah untuk komitmen ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Details Column -->
    <div style="grid-column: span 1; display: flex; flex-direction: column; gap: 20px;">
        <!-- Pledge Specs -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Spesifikasi Komitmen</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-xs text-muted">JUMLAH KONTRIBUSI</span>
                    <div style="font-size: 22px; font-weight: 700; color: var(--primary);">
                        {{ $pledge->quantity }} {{ $pledge->need->unit }}
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-xs text-muted">STATUS SAAT INI</span>
                    <div class="mt-1">
                        <span class="badge {{ $pledge->status == 'completed' ? 'badge-success' : 'badge-info' }}" style="font-size: 12px;">
                            {{ $pledge->status_label }}
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-xs text-muted">CATATAN PENGUSUL:</span>
                    <p class="text-sm text-dark mt-1">{{ $pledge->notes }}</p>
                </div>

                @if($pledge->rejection_reason)
                    <div class="p-2" style="background: var(--error-bg); border-radius: var(--radius-xs);">
                        <span class="text-xs font-bold text-danger">ALASAN PENOLAKAN ADMIN:</span>
                        <div class="text-xs text-dark mt-1">{{ $pledge->rejection_reason }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Destination School Info -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Tujuan Sekolah 3T</h3>
            </div>
            <div class="card-body">
                <div class="font-bold text-dark">{{ $pledge->need->schoolProfile->school_name }}</div>
                <div class="text-xs text-muted mt-1">📍 {{ $pledge->need->schoolProfile->regency?->name }}, {{ $pledge->need->schoolProfile->province?->name }}</div>
                <div class="text-sm text-dark mt-2">
                    <strong>Alamat:</strong> {{ $pledge->need->schoolProfile->address }}
                </div>
            </div>
        </div>

        <!-- Contributor Info -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Data Donatur</h3>
            </div>
            <div class="card-body">
                <div class="font-bold text-dark">{{ $pledge->contributorProfile->display_name }}</div>
                <div class="text-xs text-muted">{{ $pledge->contributorProfile->user->email }}</div>
                <div class="text-xs text-dark mt-1">Telepon: {{ $pledge->contributorProfile->phone ?: '—' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Advance Status -->
<div id="statusModal" class="modal-backdrop" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ route('admin.pledges.status', $pledge->id) }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 style="font-size: 16px;">Perbarui Status Bantuan (F-12)</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="next_status" class="form-label required">Pilih Status Berikutnya</label>
                    <select name="status" id="next_status" class="form-select" required>
                        @foreach($pledge->getAllowedNextStatuses() as $st)
                            <option value="{{ $st }}">
                                {{ match($st) {
                                    'in_delivery' => 'Dalam Pengiriman (in_delivery)',
                                    'received' => 'Diterima Sekolah (received)',
                                    'completed' => 'Selesai Terkonfirmasi (completed)',
                                    'cancelled' => 'Dibatalkan (cancelled)',
                                    default => ucfirst($st),
                                } }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="status_location" class="form-label">Lokasi Saat Ini (Opsional)</label>
                    <input type="text" name="location" id="status_location" class="form-control" placeholder="contoh: Bandara Mozes Kilangin Timika / Gudang Kargo...">
                </div>

                <div class="form-group">
                    <label for="status_notes" class="form-label">Catatan Perkembangan</label>
                    <textarea name="notes" id="status_notes" class="form-textarea" placeholder="Catatan logistik atau laporan verifikasi serah terima..."></textarea>
                </div>
            </div>
            <div class="card-footer d-flex justify-end gap-2">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('statusModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Transisi Status</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="modal-backdrop" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ route('admin.pledges.reject', $pledge->id) }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 style="font-size: 16px; color: var(--error);">Tolak Komitmen Bantuan</h3>
            </div>
            <div class="card-body">
                <p class="text-sm text-muted mb-3">
                    Berikan alasan mengapa komitmen bantuan ini ditolak. Alasan akan disampaikan kepada donatur.
                </p>
                <div class="form-group">
                    <label for="rej_reason" class="form-label required">Alasan Penolakan</label>
                    <textarea name="reason" id="rej_reason" class="form-textarea" required placeholder="contoh: Barang yang diajukan tidak sesuai spesifikasi kebutuhan sekolah..."></textarea>
                </div>
            </div>
            <div class="card-footer d-flex justify-end gap-2">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('rejectModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-danger btn-sm">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>
@endsection
