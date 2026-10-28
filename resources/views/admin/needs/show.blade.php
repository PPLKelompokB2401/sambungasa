@extends('layouts.dashboard')

@section('title', 'Tinjau Kebutuhan: ' . $need->title)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.needs.index') }}" class="text-sm">← Kembali ke Antrean Moderasi Kebutuhan</a>
</div>

<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <h1 style="font-size: 22px; font-weight: 700;">{{ $need->title }}</h1>
        <p class="text-sm text-muted">Diajukan oleh: <strong>{{ $need->schoolProfile->school_name }}</strong></p>
    </div>

    <!-- Actions -->
    <div class="d-flex align-center gap-2">
        @if($need->status !== 'published')
            <form action="{{ route('admin.needs.publish', $need->id) }}" method="POST" onsubmit="return confirm('Publikasikan kebutuhan ini ke direktori publik?')">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">✓ Setujui & Terbitkan ke Publik</button>
            </form>
        @endif

        @if($need->status === 'pending_review')
            <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('rejectModal').style.display='flex'">
                ✕ Tolak Kebutuhan
            </button>
        @endif

        @if($need->status === 'published')
            <form action="{{ route('admin.needs.close', $need->id) }}" method="POST" onsubmit="return confirm('Tutup status kebutuhan ini?')">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm text-warning">Tutup Kebutuhan</button>
            </form>
            <a href="{{ route('public.needs.show', $need->id) }}" target="_blank" class="btn btn-outline btn-sm">Lihat Halaman Publik ↗</a>
        @endif
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <div style="grid-column: span 2;" class="card">
        <div class="card-header d-flex justify-between align-center">
            <h3 style="font-size: 16px;">Detail Kebutuhan</h3>
            <div>
                <span class="badge {{ $need->status === 'published' ? 'badge-success' : ($need->status === 'pending_review' ? 'badge-warning' : 'badge-neutral') }}">
                    Status: {{ strtoupper($need->status) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <span class="text-xs text-muted">KATEGORI</span>
                    <div class="font-bold text-dark">{{ $need->category->name }}</div>
                </div>
                <div>
                    <span class="text-xs text-muted">JENIS BANTUAN</span>
                    <div class="font-medium">{{ ucfirst($need->assistance_type) }}</div>
                </div>
                <div>
                    <span class="text-xs text-muted">TINGKAT PRIORITAS</span>
                    <div class="mt-1">
                        @if($need->priority === 'mendesak')
                            <span class="badge badge-danger">Mendesak</span>
                        @elseif($need->priority === 'tinggi')
                            <span class="badge badge-warning">Tinggi</span>
                        @else
                            <span class="badge badge-neutral">{{ ucfirst($need->priority) }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    <span class="text-xs text-muted">KUANTITAS TARGET</span>
                    <div class="font-bold text-dark" style="font-size: 16px;">
                        {{ $need->quantity_required ?? '—' }} {{ $need->unit }}
                    </div>
                    <div class="text-xs text-muted mt-1">Terpenuhi: {{ $need->quantity_fulfilled }} {{ $need->unit }} ({{ $need->progress_percentage }}%)</div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border); padding-top: 16px;">
                <span class="text-xs text-muted font-medium">DESKRIPSI & URGENSI KEBUTUHAN:</span>
                <div class="mt-2 text-dark" style="line-height: 1.6; white-space: pre-line;">
                    {{ $need->description }}
                </div>
            </div>

            @if($need->rejection_notes)
                <div class="mt-4 p-3" style="background: var(--error-bg); border-radius: var(--radius-sm);">
                    <strong style="color: var(--error); font-size: 12px;">CATATAN PENOLAKAN ADMIN:</strong>
                    <p class="mt-1 text-sm text-dark">{{ $need->rejection_notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- School & Pledges Summary -->
    <div style="grid-column: span 1; display: flex; flex-direction: column; gap: 20px;">
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Informasi Sekolah 3T</h3>
            </div>
            <div class="card-body">
                <div class="font-bold text-dark">{{ $school = $need->schoolProfile->school_name }}</div>
                <div class="text-xs text-muted mt-1">{{ $need->schoolProfile->address }}</div>
                <div class="text-sm font-medium mt-2">
                    {{ $need->schoolProfile->regency?->name }}, {{ $need->schoolProfile->province?->name }}
                </div>
                <div class="mt-3">
                    <span class="badge {{ $need->schoolProfile->isVerified() ? 'badge-success' : 'badge-warning' }}">
                        Sekolah 3T: {{ $need->schoolProfile->verification_status }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Komitmen Masuk ({{ $need->pledges->count() }})</h3>
            </div>
            <div class="card-body">
                @forelse($need->pledges as $p)
                    <div class="p-2 mb-2" style="background: var(--surface); border-radius: var(--radius-xs); font-size: 12px;">
                        <div class="d-flex justify-between">
                            <strong>{{ $p->tracking_code }}</strong>
                            <span class="badge {{ $p->status == 'completed' ? 'badge-success' : 'badge-info' }}">{{ $p->status_label }}</span>
                        </div>
                        <div class="text-muted mt-1">{{ $p->contributorProfile->display_name }} • {{ $p->quantity }} {{ $need->unit }}</div>
                    </div>
                @empty
                    <div class="text-sm text-muted">Belum ada donatur yang mengajukan komitmen untuk kebutuhan ini.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="modal-backdrop" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ route('admin.needs.reject', $need->id) }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 style="font-size: 16px; color: var(--error);">Tolak Kebutuhan Sekolah</h3>
            </div>
            <div class="card-body">
                <p class="text-sm text-muted mb-3">
                    Jelaskan alasan kebutuhan ini ditolak (misal: deskripsi kurang jelas, data tidak wajar, atau duplikasi).
                </p>
                <div class="form-group">
                    <label for="mod_reason" class="form-label required">Alasan Penolakan</label>
                    <textarea name="reason" id="mod_reason" class="form-textarea" required placeholder="Tuliskan catatan penolakan..."></textarea>
                </div>
            </div>
            <div class="card-footer d-flex justify-end gap-2">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('rejectModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-danger btn-sm">Tolak Kebutuhan</button>
            </div>
        </form>
    </div>
</div>
@endsection
