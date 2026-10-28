@extends('layouts.app')

@section('title', $need->title . ' — SambungAsa')
@section('meta_description', Str::limit(strip_tags($need->description), 150))

@section('content')
<div class="container py-8">
    <div class="mb-4">
        <a href="{{ route('public.needs.index') }}" class="text-sm">← Kembali ke Direktori Kebutuhan</a>
    </div>

    <div class="grid grid-cols-3 gap-8">
        <!-- Main Content -->
        <div style="grid-column: span 2;">
            <div class="card mb-6">
                <div class="card-body">
                    <div class="d-flex align-center flex-wrap gap-2 mb-3">
                        <span class="badge {{ $need->priority == 'mendesak' ? 'badge-danger' : ($need->priority == 'tinggi' ? 'badge-warning' : 'badge-neutral') }}">
                            Prioritas: {{ ucfirst($need->priority) }}
                        </span>
                        <span class="badge badge-neutral">Kategori: {{ $need->category->name }}</span>
                        <span class="badge badge-info">Bantuan {{ ucfirst($need->assistance_type) }}</span>
                    </div>

                    <h1 style="font-size: 26px; line-height: 34px; margin-bottom: 16px;">{{ $need->title }}</h1>

                    <!-- Progress Bar Component -->
                    <div class="p-4 mb-6" style="background: var(--surface); border-radius: var(--radius-sm); border: 1px solid var(--border);">
                        <div class="d-flex justify-between align-center mb-2">
                            <span class="text-xs text-muted font-medium">PROGRES PEMENUHAN BANTUAN</span>
                            <span class="font-bold {{ $need->progress_percentage == 100 ? 'text-success' : 'text-primary' }}" style="font-size: 16px;">
                                {{ $need->progress_percentage }}%
                            </span>
                        </div>
                        <div class="progress-bar-wrap" style="height: 10px;">
                            <div class="progress-fill {{ $need->progress_percentage == 100 ? 'success' : '' }}" style="width: {{ $need->progress_percentage }}%;"></div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mt-4 pt-3 text-center" style="border-top: 1px solid var(--border); text-align: center;">
                            <div>
                                <div class="text-xs text-muted">TARGET DIBUTUHKAN</div>
                                <div style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin-top: 2px;">
                                    {{ $need->quantity_required ?? '—' }} {{ $need->unit }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-muted">TELAH TERPENUHI</div>
                                <div style="font-size: 18px; font-weight: 700; color: var(--success); margin-top: 2px;">
                                    {{ $need->quantity_fulfilled }} {{ $need->unit }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-muted">SISA KEBUTUHAN</div>
                                <div style="font-size: 18px; font-weight: 700; color: var(--primary); margin-top: 2px;">
                                    {{ $remainingQuantity }} {{ $need->unit }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <h3 style="font-size: 18px; margin-bottom: 12px;">Latar Belakang & Urgensi Kebutuhan</h3>
                    <div class="body-text text-dark" style="line-height: 1.7; font-size: 15px; white-space: pre-line; margin-bottom: 24px;">
                        {{ $need->description }}
                    </div>

                    @if($need->target_date)
                        <div class="text-xs text-muted">
                            Target batas penyaluran: <strong>{{ $need->target_date->format('d F Y') }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Public Timeline of Delivered Commitments -->
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size: 16px;">Catatan Penyaluran & Komitmen Terverifikasi</h3>
                </div>
                <div class="card-body">
                    @forelse($need->pledges as $pl)
                        <div class="d-flex align-start gap-3 pb-3 mb-3" style="border-bottom: 1px solid var(--border);">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--surface-alt); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; color: var(--text-primary);">
                                ✓
                            </div>
                            <div class="flex-1">
                                <div class="d-flex justify-between align-center">
                                    <span class="font-bold text-dark text-sm">{{ $pl->contributorProfile->display_name }}</span>
                                    <span class="badge {{ $pl->status == 'completed' ? 'badge-success' : 'badge-info' }}">{{ $pl->status_label }}</span>
                                </div>
                                <div class="text-sm mt-1">
                                    Menyalurkan <strong>{{ $pl->quantity }} {{ $need->unit }}</strong>: <em>"{{ $pl->notes }}"</em>
                                </div>
                                <div class="text-xs text-muted mt-1" style="font-family: var(--font-mono);">
                                    Kode Tracking: {{ $pl->tracking_code }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-muted text-center py-4">
                            Belum ada komitmen bantuan yang disalurkan. Jadilah pihak pertama yang mewujudkan asa pendidikan sekolah ini!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar / Action Card -->
        <div style="grid-column: span 1;">
            <!-- CTA Card -->
            <div class="card mb-6" style="box-shadow: var(--elevation-2); border-top: 4px solid var(--primary);">
                <div class="card-body">
                    <h3 style="font-size: 18px; margin-bottom: 8px;">Dukung Sekolah Ini</h3>
                    <p class="text-sm text-muted mb-4">
                        Bantuan langsung diserahkan tanpa perantara. Catatan dan foto bukti serah terima akan terdokumentasi di sistem.
                    </p>

                    @if(!$isOpen)
                        <div class="alert alert-success mb-3">
                            <span>✓</span>
                            <div>Kebutuhan ini telah terpenuhi secara lengkap. Terima kasih atas kepedulian para donatur dan relawan!</div>
                        </div>
                    @elseif(auth()->check() && auth()->user()->isContributor())
                        <button type="button" class="btn btn-primary" style="width: 100%; height: 44px;" onclick="document.getElementById('pledgeModal').style.display='flex'">
                            Ajukan Komitmen Bantuan (Pledge) →
                        </button>
                    @elseif(auth()->check() && auth()->user()->isSchool())
                        <div class="alert alert-info">
                            <span>ℹ</span>
                            <div class="text-xs">Anda masuk sebagai akun perwakilan sekolah. Masuk sebagai akun donatur/mitra untuk mengajukan bantuan.</div>
                        </div>
                    @elseif(auth()->check() && auth()->user()->isAdmin())
                        <a href="{{ route('admin.needs.show', $need->id) }}" class="btn btn-dark" style="width: 100%;">
                            Moderasi Kebutuhan (Admin)
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary mb-2" style="width: 100%;">
                            Masuk untuk Memberikan Bantuan
                        </a>
                        <a href="{{ route('register', ['role' => 'contributor']) }}" class="btn btn-outline" style="width: 100%;">
                            Daftar Sebagai Donatur / Relawan
                        </a>
                    @endif
                </div>
            </div>

            <!-- School Profile Widget -->
            <div class="card">
                <div class="card-header">
                    <h4 style="font-size: 14px; font-weight: 700;">Profil Sekolah Penerima</h4>
                </div>
                <div class="card-body">
                    <span class="badge badge-success mb-2">✓ Terverifikasi Wilayah 3T</span>
                    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">
                        <a href="{{ route('public.schools.show', $need->schoolProfile->id) }}" style="color: var(--text-primary); text-decoration: none;">
                            {{ $need->schoolProfile->school_name }}
                        </a>
                    </h3>
                    <div class="text-xs text-muted mb-3">
                        📍 {{ $need->schoolProfile->regency?->name }}, Provinsi {{ $need->schoolProfile->province?->name }} ({{ $need->schoolProfile->regency?->island }})
                    </div>
                    <div class="text-sm text-dark mb-3">
                        <strong>Kecamatan:</strong> {{ $need->schoolProfile->district ?: '—' }}<br>
                        <strong>Alamat:</strong> {{ $need->schoolProfile->address }}
                    </div>
                    <div class="text-xs text-muted">
                        Jumlah Siswa: <strong>{{ $need->schoolProfile->student_count ?? 0 }} anak</strong><br>
                        NPSN: <strong>{{ $need->schoolProfile->npsn ?: '—' }}</strong>
                    </div>

                    <div class="mt-4 pt-3" style="border-top: 1px solid var(--border);">
                        <a href="{{ route('public.schools.show', $need->schoolProfile->id) }}" class="text-sm font-medium">
                            Lihat Profil Lengkap Sekolah →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pledge Submission Modal -->
@if(auth()->check() && auth()->user()->isContributor())
<div id="pledgeModal" class="modal-backdrop" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ route('contributor.pledges.store', $need->id) }}" method="POST">
            @csrf
            <div class="card-header d-flex justify-between align-center">
                <h3 style="font-size: 16px;">Ajukan Komitmen Bantuan (F-10)</h3>
                <button type="button" onclick="document.getElementById('pledgeModal').style.display='none'" style="border:none;background:none;cursor:pointer;font-size:18px;">✕</button>
            </div>
            <div class="card-body">
                <p class="text-xs text-muted mb-3">
                    Komitmen bantuan ini akan diverifikasi oleh administrator sebelum dilanjutkan ke proses penyaluran ke <strong>{{ $need->schoolProfile->school_name }}</strong>.
                </p>

                <div class="form-group">
                    <label for="quantity" class="form-label required">
                        Jumlah yang Dikomitmenkan (Maksimal: {{ $remainingQuantity }} {{ $need->unit }})
                    </label>
                    <input type="number" min="1" max="{{ $remainingQuantity }}" id="quantity" name="quantity" class="form-control" value="{{ min(1, $remainingQuantity) }}" required>
                    <div class="form-hint">Satuan: {{ $need->unit }}</div>
                </div>

                <div class="form-group">
                    <label for="estimated_delivery_at" class="form-label">Perkiraan Waktu Penyaluran / Pelaksanaan</label>
                    <input type="date" id="estimated_delivery_at" name="estimated_delivery_at" class="form-control" min="{{ date('Y-m-d') }}">
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label required">Catatan & Rincian Bantuan</label>
                    <textarea name="notes" id="notes" class="form-textarea" required placeholder="Jelaskan spesifikasi barang yang akan dikirim, metode pengiriman kargo, atau detail kegiatan relawan..."></textarea>
                </div>
            </div>
            <div class="card-footer d-flex justify-end gap-2">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('pledgeModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Kirim Komitmen Bantuan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
