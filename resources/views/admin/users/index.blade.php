@extends('layouts.dashboard')

@section('title', 'Kelola Pengguna — Admin SambungAsa')

@section('content')
<div class="d-flex justify-between align-center flex-wrap gap-3 mb-6">
    <div>
        <h1 style="font-size: 22px; font-weight: 700;">Manajemen Pengguna</h1>
        <p class="text-sm text-muted">Kelola akun admin, perwakilan sekolah 3T, dan donatur/mitra.</p>
    </div>
</div>

<!-- Stats Counter Cards -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card p-3">
        <div class="text-xs text-muted font-medium">TOTAL PENGGUNA</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px;">{{ $stats['total'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--warning);">
        <div class="text-xs text-muted font-medium">MENUNGGU VERIFIKASI</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--warning);">{{ $stats['pending'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--success);">
        <div class="text-xs text-muted font-medium">AKUN AKTIF</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--success);">{{ $stats['active'] }}</div>
    </div>
    <div class="card p-3" style="border-left: 3px solid var(--secondary);">
        <div class="text-xs text-muted font-medium">SEKOLAH TERDAFTAR</div>
        <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: var(--secondary);">{{ $stats['school'] }}</div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-6">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex align-center flex-wrap gap-3">
            <div style="flex: 1; min-width: 220px;">
                <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
            </div>

            <div style="width: 160px;">
                <select name="role" class="form-select">
                    <option value="">Semua Peran</option>
                    <option value="school" {{ request('role') == 'school' ? 'selected' : '' }}>Sekolah 3T</option>
                    <option value="contributor" {{ request('role') == 'contributor' ? 'selected' : '' }}>Donatur/Relawan</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div style="width: 160px;">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <button type="submit" class="btn btn-dark btn-sm">Filter</button>
            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Peran</th>
                    <th>Detail Profil</th>
                    <th>Status</th>
                    <th>Terdaftar Pada</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="font-medium text-dark">{{ $user->name }}</div>
                            <div class="text-xs text-muted">{{ $user->email }}</div>
                        </td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge badge-danger">Admin</span>
                            @elseif($user->role === 'school')
                                <span class="badge badge-info">Sekolah 3T</span>
                            @else
                                <span class="badge badge-neutral">
                                    {{ $user->contributorProfile?->contributor_type ? ucfirst($user->contributorProfile->contributor_type) : 'Donatur' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->role === 'school' && $user->schoolProfile)
                                <div class="text-sm font-medium">{{ $user->schoolProfile->school_name }}</div>
                                <div class="text-xs text-muted">
                                    Verifikasi 3T: 
                                    <span class="badge {{ $user->schoolProfile->isVerified() ? 'badge-success' : 'badge-warning' }}" style="font-size:9px;">
                                        {{ $user->schoolProfile->verification_status }}
                                    </span>
                                </div>
                            @elseif($user->role === 'contributor' && $user->contributorProfile)
                                @if($user->contributorProfile->organization_name)
                                    <div class="text-sm">{{ $user->contributorProfile->organization_name }}</div>
                                @else
                                    <div class="text-sm text-muted">Donatur Perorangan</div>
                                @endif
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge badge-success">Aktif</span>
                            @elseif($user->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($user->status === 'rejected')
                                <span class="badge badge-danger">Ditolak</span>
                            @else
                                <span class="badge badge-danger">Suspended</span>
                            @endif
                        </td>
                        <td class="text-sm text-muted">
                            {{ $user->created_at->format('d M Y, H:i') }}
                        </td>
                        <td style="text-align: right;">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline btn-sm">Detail & Verifikasi</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;" class="text-muted">
                            Tidak ada data pengguna yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="card-footer d-flex justify-between align-center">
            <div class="text-sm text-muted">
                Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} pengguna
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
