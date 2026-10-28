@extends('layouts.dashboard')

@section('title', 'Log Aktivitas Sistem (F-24)')

@section('content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <h1 class="page-title">Audit Trail & Log Aktivitas (F-24)</h1>
        <p class="page-subtitle">Rekam jejak seluruh aksi penting pengguna dan administrator dalam sistem SambungAsa.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="d-flex flex-wrap gap-3 align-end">
            <div style="flex: 1; min-width: 160px;">
                <label class="form-label text-xs">Tindakan (Action)</label>
                <select name="action" class="form-control text-sm">
                    <option value="">Semua Tindakan</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 180px;">
                <label class="form-label text-xs">Pengguna (User)</label>
                <select name="user_id" class="form-control text-sm">
                    <option value="">Semua Pengguna</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 130px;">
                <label class="form-label text-xs">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control text-sm" value="{{ request('date_from') }}">
            </div>

            <div style="flex: 1; min-width: 130px;">
                <label class="form-label text-xs">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control text-sm" value="{{ request('date_to') }}">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-dark btn-sm">Filter</button>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Log Table -->
<div class="card">
    <div class="card-header d-flex justify-between align-center">
        <h2 class="card-title" style="font-size: 16px;">Daftar Rekam Jejak ({{ $logs->total() }} aktivitas)</h2>
    </div>
    <div class="card-body p-0">
        @if($logs->isEmpty())
            <div class="empty-state p-5 text-center">
                <p class="text-muted mb-0">Belum ada aktivitas tercatat yang sesuai filter.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu (WIB)</th>
                            <th>Pengguna</th>
                            <th>Tindakan</th>
                            <th>Deskripsi Aktivitas</th>
                            <th>Subjek Target</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td class="text-xs text-muted" style="white-space: nowrap;">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td>
                                    @if($log->user)
                                        <div class="font-semibold text-sm">{{ $log->user->name }}</div>
                                        <span class="badge {{ $log->user->isAdmin() ? 'badge-danger' : ($log->user->isSchool() ? 'badge-info' : 'badge-neutral') }}" style="font-size: 9px;">
                                            {{ $log->user->role }}
                                        </span>
                                    @else
                                        <span class="text-muted text-sm">Sistem</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-neutral font-mono" style="font-size: 10px;">{{ $log->action }}</span>
                                </td>
                                <td class="text-sm">
                                    {{ $log->description }}
                                </td>
                                <td class="text-xs text-muted font-mono">
                                    {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                </td>
                                <td class="text-xs text-muted font-mono">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
