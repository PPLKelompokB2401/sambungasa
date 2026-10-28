@extends('layouts.dashboard')

@section('title', 'Detail Pengguna: ' . $user->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-sm">← Kembali ke Daftar Pengguna</a>
</div>

<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <h1 style="font-size: 22px; font-weight: 700;">Detail Pengguna</h1>
        <p class="text-sm text-muted">Informasi akun, data profil, dan kontrol verifikasi admin.</p>
    </div>

    <!-- Actions Bar -->
    <div class="d-flex align-center gap-2">
        @if($user->status === 'pending' || $user->status === 'rejected')
            <form action="{{ route('admin.users.verify', $user->id) }}" method="POST" onsubmit="return confirm('Verifikasi dan aktifkan akun ini?')">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">✓ Setujui & Aktifkan</button>
            </form>
        @endif

        @if($user->status === 'pending')
            <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('rejectModal').style.display='flex'">✕ Tolak Akun</button>
        @endif

        @if($user->status === 'active' && $user->id !== auth()->id())
            <button type="button" class="btn btn-outline btn-sm text-danger" onclick="document.getElementById('suspendModal').style.display='flex'">Bekukan (Suspend)</button>
        @elseif($user->status === 'suspended')
            <form action="{{ route('admin.users.activate', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">Aktifkan Kembali</button>
            </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <!-- User Account Summary -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-header">
            <h3 style="font-size: 16px;">Ringkasan Akun</h3>
        </div>
        <div class="card-body">
            <div class="d-flex align-center gap-3 mb-4">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--surface-alt); display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 16px;">{{ $user->name }}</div>
                    <div class="text-xs text-muted">{{ $user->email }}</div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border); padding-top: 14px;">
                <div class="mb-3">
                    <span class="text-xs text-muted">PERAN:</span>
                    <div class="mt-1">
                        @if($user->role === 'admin')
                            <span class="badge badge-danger">Admin</span>
                        @elseif($user->role === 'school')
                            <span class="badge badge-info">Sekolah 3T</span>
                        @else
                            <span class="badge badge-neutral">Donatur / Relawan</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-xs text-muted">STATUS AKUN:</span>
                    <div class="mt-1">
                        @if($user->status === 'active')
                            <span class="badge badge-success">Aktif</span>
                        @elseif($user->status === 'pending')
                            <span class="badge badge-warning">Menunggu Verifikasi</span>
                        @elseif($user->status === 'rejected')
                            <span class="badge badge-danger">Ditolak</span>
                        @else
                            <span class="badge badge-danger">Suspended</span>
                        @endif
                    </div>
                </div>

                @if($user->verification_notes)
                    <div class="mb-3 p-2" style="background: var(--surface); border-radius: var(--radius-xs); font-size: 12px;">
                        <span class="text-xs font-medium text-muted">Catatan Verifikasi:</span>
                        <p class="mt-1 text-dark">{{ $user->verification_notes }}</p>
                    </div>
                @endif

                <div class="text-xs text-muted">
                    <div>Terdaftar: {{ $user->created_at->format('d M Y, H:i') }}</div>
                    @if($user->verified_at)
                        <div>Diverifikasi: {{ $user->verified_at->format('d M Y, H:i') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Details & Audit History -->
    <div style="grid-column: span 2; display: flex; flex-direction: column; gap: 20px;">
        @if($user->isSchool() && $user->schoolProfile)
            <div class="card">
                <div class="card-header d-flex justify-between align-center">
                    <h3 style="font-size: 16px;">Profil Sekolah 3T</h3>
                    <span class="badge {{ $user->schoolProfile->isVerified() ? 'badge-success' : 'badge-warning' }}">
                        Verifikasi 3T: {{ $user->schoolProfile->verification_status }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-muted">NAMA SEKOLAH</span>
                            <div class="font-medium text-dark">{{ $user->schoolProfile->school_name }}</div>
                        </div>
                        <div>
                            <span class="text-xs text-muted">NPSN</span>
                            <div class="font-medium">{{ $user->schoolProfile->npsn ?: 'Belum diisi' }}</div>
                        </div>
                        <div>
                            <span class="text-xs text-muted">PROVINSI & KABUPATEN</span>
                            <div class="font-medium">
                                {{ $user->schoolProfile->province?->name ?? '—' }}, {{ $user->schoolProfile->regency?->name ?? '—' }}
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-muted">KECAMATAN / DISTRIK</span>
                            <div class="font-medium">{{ $user->schoolProfile->district ?: '—' }}</div>
                        </div>
                        <div style="grid-column: span 2;">
                            <span class="text-xs text-muted">ALAMAT LENGKAP</span>
                            <div class="text-sm mt-1">{{ $user->schoolProfile->address }}</div>
                        </div>
                        <div>
                            <span class="text-xs text-muted">JUMLAH SISWA / GURU</span>
                            <div class="text-sm">
                                {{ $user->schoolProfile->student_count ?? '0' }} Siswa / {{ $user->schoolProfile->teacher_count ?? '0' }} Guru
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-muted">KOORDINAT LOKASI</span>
                            <div class="text-xs" style="font-family: var(--font-mono);">
                                {{ $user->schoolProfile->latitude ?? '—' }}, {{ $user->schoolProfile->longitude ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($user->isContributor() && $user->contributorProfile)
            <div class="card">
                <div class="card-header d-flex justify-between align-center">
                    <h3 style="font-size: 16px;">Profil Donatur / Relawan</h3>
                    <span class="badge badge-info">{{ ucfirst($user->contributorProfile->contributor_type) }}</span>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-2 gap-4">
                        @if($user->contributorProfile->organization_name)
                            <div style="grid-column: span 2;">
                                <span class="text-xs text-muted">NAMA LEMBAGA / ORGANISASI</span>
                                <div class="font-bold text-dark">{{ $user->contributorProfile->organization_name }}</div>
                            </div>
                        @endif
                        <div>
                            <span class="text-xs text-muted">NOMOR TELEPON / WA</span>
                            <div class="font-medium">{{ $user->contributorProfile->phone ?: '—' }}</div>
                        </div>
                        <div>
                            <span class="text-xs text-muted">SITUS WEB</span>
                            <div>
                                @if($user->contributorProfile->website)
                                    <a href="{{ $user->contributorProfile->website }}" target="_blank">{{ $user->contributorProfile->website }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                        <div style="grid-column: span 2;">
                            <span class="text-xs text-muted">NO. IDENTITAS / LEGALITAS (TERLINDUNGI)</span>
                            <div class="font-medium" style="font-family: var(--font-mono);">
                                {{ $user->contributorProfile->identity_number ? substr($user->contributorProfile->identity_number, 0, 4) . '******' : 'Belum diisi' }}
                            </div>
                        </div>
                    </div>

                    @if($user->contributorProfile->volunteerProfile)
                        <div class="mt-4 pt-3" style="border-top: 1px solid var(--border);">
                            <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">Profil Relawan & Keahlian</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-xs text-muted">PENDIDIKAN</span>
                                    <div>{{ $user->contributorProfile->volunteerProfile->education ?: '—' }}</div>
                                </div>
                                <div>
                                    <span class="text-xs text-muted">PENGALAMAN</span>
                                    <div>{{ $user->contributorProfile->volunteerProfile->experience_years }} Tahun</div>
                                </div>
                                <div style="grid-column: span 2;">
                                    <span class="text-xs text-muted">BIDANG KEAHLIAN / SPESIALISASI</span>
                                    <div class="font-medium text-dark">{{ $user->contributorProfile->volunteerProfile->expertise ?: '—' }}</div>
                                </div>
                                <div style="grid-column: span 2;">
                                    <span class="text-xs text-muted">BIO / MOTIVASI MENGAJAR</span>
                                    <p class="text-sm mt-1 text-muted">{{ $user->contributorProfile->volunteerProfile->bio ?: '—' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Recent Audit Trail for this user -->
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px;">Riwayat Aktivitas Terakhir (F-24)</h3>
            </div>
            <div class="table-responsive">
                <table class="table text-sm">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                            <th>Alamat IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->activityLogs as $log)
                            <tr>
                                <td class="text-xs text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td><code>{{ $log->action }}</code></td>
                                <td>{{ $log->description }}</td>
                                <td class="text-xs text-muted">{{ $log->ip_address ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada catatan aktivitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="modal-backdrop" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 style="font-size: 16px; color: var(--error);">Tolak Pendaftaran Akun</h3>
            </div>
            <div class="card-body">
                <p class="text-sm text-muted mb-3">
                    Berikan alasan yang jelas mengapa akun ini ditolak. Alasan ini akan tercatat dalam sistem dan disampaikan kepada pengguna.
                </p>
                <div class="form-group">
                    <label for="reject_reason" class="form-label required">Alasan Penolakan</label>
                    <textarea name="reason" id="reject_reason" class="form-textarea" required placeholder="contoh: Dokumen tidak valid atau nomor kontak tidak dapat dihubungi..."></textarea>
                </div>
            </div>
            <div class="card-footer d-flex justify-end gap-2">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('rejectModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-danger btn-sm">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Suspend -->
<div id="suspendModal" class="modal-backdrop" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST">
            @csrf
            <div class="card-header">
                <h3 style="font-size: 16px; color: var(--error);">Nonaktifkan (Suspend) Akun</h3>
            </div>
            <div class="card-body">
                <p class="text-sm text-muted mb-3">
                    Pengguna tidak akan dapat masuk ke sistem selama status akun dalam kondisi suspended.
                </p>
                <div class="form-group">
                    <label for="suspend_reason" class="form-label required">Alasan Pembekuan</label>
                    <textarea name="reason" id="suspend_reason" class="form-textarea" required placeholder="contoh: Terindikasi pelanggaran aturan komunitas atau komitmen palsu..."></textarea>
                </div>
            </div>
            <div class="card-footer d-flex justify-end gap-2">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('suspendModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-danger btn-sm">Bekukan Akun</button>
            </div>
        </form>
    </div>
</div>
@endsection
