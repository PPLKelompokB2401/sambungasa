<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — SambungAsa</title>
    <link rel="stylesheet" href="{{ asset('css/sambungasa.css') }}">
    @stack('styles')
</head>
<body style="background-color: var(--surface);">
    <!-- Topbar -->
    <header class="navbar" style="background-color: #FFFFFF;">
        <div class="container-fluid navbar-inner">
            <div class="d-flex align-center gap-3">
                <a href="{{ url('/') }}" class="navbar-brand">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:var(--primary);color:#fff;border-radius:6px;font-size:16px;font-weight:700;">SA</span>
                    <span>Sambung<span style="color:var(--primary);">Asa</span></span>
                </a>
                <span class="badge {{ auth()->user()->isAdmin() ? 'badge-danger' : (auth()->user()->isSchool() ? 'badge-info' : 'badge-neutral') }}">
                    {{ auth()->user()->isAdmin() ? 'Admin Portal' : (auth()->user()->isSchool() ? 'Sekolah 3T' : 'Donatur / Mitra') }}
                </span>
            </div>

            <div class="d-flex align-center gap-3">
                <a href="{{ url('/') }}" class="text-sm text-muted" target="_blank" title="Lihat Website Publik">
                    Lihat Web Publik ↗
                </a>

                <div class="d-flex align-center gap-2">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--surface-alt); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--text-primary);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="d-none d-md-block" style="text-align: left;">
                        <div style="font-size: 13px; font-weight: 700; line-height: 1.2;">{{ auth()->user()->name }}</div>
                        <div style="font-size: 11px; color: var(--text-secondary);">
                            Status: <span class="badge {{ auth()->user()->isActive() ? 'badge-success' : 'badge-warning' }}" style="padding: 1px 5px; font-size: 9px;">{{ auth()->user()->status }}</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm" style="border-radius: var(--radius-pill);">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Shell -->
    <div class="dashboard-shell">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                @if(auth()->user()->isAdmin())
                    <li class="sidebar-heading">Ringkasan</li>
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            📊 Dashboard Admin
                        </a>
                    </li>

                    <li class="sidebar-heading">Verifikasi & Moderasi</li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            👥 Kelola Pengguna
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.schools.index') }}" class="sidebar-link {{ request()->routeIs('admin.schools.*') ? 'active' : '' }}">
                            🏫 Verifikasi Sekolah 3T
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.needs.index') }}" class="sidebar-link {{ request()->routeIs('admin.needs.*') ? 'active' : '' }}">
                            📋 Moderasi Kebutuhan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pledges.index') }}" class="sidebar-link {{ request()->routeIs('admin.pledges.*') ? 'active' : '' }}">
                            🤝 Kelola Komitmen (Pledge)
                        </a>
                    </li>

                    <li class="sidebar-heading">Laporan & Audit</li>
                    <li>
                        <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            📈 Laporan & Ekspor CSV
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.activity-logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                            📝 Log Aktivitas (F-24)
                        </a>
                    </li>
                @elseif(auth()->user()->isSchool())
                    <li class="sidebar-heading">Menu Sekolah</li>
                    <li>
                        <a href="{{ route('school.dashboard') }}" class="sidebar-link {{ request()->routeIs('school.dashboard') ? 'active' : '' }}">
                            📊 Dashboard Sekolah
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('school.profile.edit') }}" class="sidebar-link {{ request()->routeIs('school.profile.*') ? 'active' : '' }}">
                            🏫 Profil & Lokasi Sekolah
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('school.needs.index') }}" class="sidebar-link {{ request()->routeIs('school.needs.*') ? 'active' : '' }}">
                            📦 Kebutuhan Sekolah
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('school.pledges.index') }}" class="sidebar-link {{ request()->routeIs('school.pledges.*') ? 'active' : '' }}">
                            🚚 Bantuan Masuk & Tracking
                        </a>
                    </li>
                @else
                    <li class="sidebar-heading">Menu Donatur / Mitra</li>
                    <li>
                        <a href="{{ route('contributor.dashboard') }}" class="sidebar-link {{ request()->routeIs('contributor.dashboard') ? 'active' : '' }}">
                            📊 Dashboard Donatur
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contributor.profile.edit') }}" class="sidebar-link {{ request()->routeIs('contributor.profile.*') ? 'active' : '' }}">
                            👤 Profil & Legalitas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contributor.pledges.index') }}" class="sidebar-link {{ request()->routeIs('contributor.pledges.*') ? 'active' : '' }}">
                            🤝 Komitmen Bantuan Saya
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.needs.index') }}" class="sidebar-link">
                            🔍 Jelajahi Kebutuhan Baru
                        </a>
                    </li>
                @endif
            </ul>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success">
                    <span>✓</span>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    <span>⚠</span>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">
                    <span>!</span>
                    <div>{{ session('warning') }}</div>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <span>⚠</span>
                    <div>
                        <strong>Periksa kembali form:</strong>
                        <ul style="margin-left: 20px; margin-top: 4px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
