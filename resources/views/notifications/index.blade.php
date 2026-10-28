@extends('layouts.dashboard')

@section('title', 'Notifikasi Saya')

@section('content')
<div class="page-header d-flex justify-between align-center">
    <div>
        <h1 class="page-title">Pemberitahuan Sistem (F-21)</h1>
        <p class="page-subtitle">Informasi status verifikasi, pembaruan komitmen bantuan, dan kabar terbaru.</p>
    </div>
    @if($notifications->whereNull('read_at')->count() > 0)
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm">
                ✓ Tandai Semua Telah Dibaca
            </button>
        </form>
    @endif
</div>

<div class="card">
    <div class="card-body p-0">
        @if($notifications->isEmpty())
            <div class="empty-state p-5 text-center">
                <div style="font-size: 32px; margin-bottom: 8px;">🔔</div>
                <h3 class="font-bold text-sm">Belum Ada Notifikasi</h3>
                <p class="text-muted text-xs mb-0">Semua pembaruan terkait akun, verifikasi, dan bantuan Anda akan muncul di sini.</p>
            </div>
        @else
            <ul style="list-style: none; margin: 0; padding: 0;">
                @foreach($notifications as $n)
                    <li style="padding: 16px; border-bottom: 1px solid var(--border); background: {{ $n->isRead() ? '#FFFFFF' : '#FFF9F9' }}; display: flex; justify-content: space-between; align-items: start; gap: 16px;">
                        <div style="display: flex; gap: 12px; align-items: start;">
                            <div style="font-size: 20px; line-height: 1;">
                                @if($n->type === 'verification')
                                    🏫
                                @elseif($n->type === 'pledge')
                                    🤝
                                @elseif($n->type === 'tracking')
                                    🚚
                                @else
                                    📢
                                @endif
                            </div>
                            <div>
                                <div class="d-flex align-center gap-2 mb-1">
                                    <span class="font-bold text-sm" style="color: {{ $n->isRead() ? 'var(--text-primary)' : 'var(--primary)' }};">
                                        {{ $n->title }}
                                    </span>
                                    @if(!$n->isRead())
                                        <span class="badge badge-danger" style="font-size: 9px; padding: 1px 6px;">Baru</span>
                                    @endif
                                </div>
                                <p class="text-sm text-secondary mb-1" style="line-height: 1.4;">{{ $n->message }}</p>
                                <div class="text-xs text-muted">{{ $n->created_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-center gap-2" style="white-space: nowrap;">
                            @if($n->target_url)
                                <a href="{{ route('notifications.read', $n->id) }}" class="btn btn-primary btn-sm" style="font-size: 11px; padding: 4px 10px;">
                                    Buka →
                                </a>
                            @elseif(!$n->isRead())
                                <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm" style="font-size: 11px; padding: 4px 8px;">
                                        Tandai Dibaca
                                    </button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="p-3 border-top">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
