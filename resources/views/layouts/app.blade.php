<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SambungAsa') — Menghubungkan Sekolah 3T dengan Bantuan Pendidikan</title>
    <meta name="description" content="@yield('meta_description', 'Platform transparansi dan kolaborasi menghubungkan sekolah di daerah tertinggal, terdepan, dan terluar (3T) dengan donatur, relawan, dan organisasi.')">
    <link rel="stylesheet" href="{{ asset('css/sambungasa.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Top Navbar -->
    <header class="navbar">
        <div class="container navbar-inner">
            <a href="{{ url('/') }}" class="navbar-brand">
                <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:var(--primary);color:#fff;border-radius:6px;font-size:16px;font-weight:700;">SA</span>
                <span>Sambung<span style="color:var(--primary);">Asa</span></span>
                <span class="brand-badge">3T</span>
            </a>

            <nav class="navbar-nav">
                <a href="{{ route('public.needs.index') }}" class="nav-link {{ request()->routeIs('public.needs.*') ? 'active' : '' }}">Daftar Kebutuhan</a>
                <a href="{{ route('public.schools.index') }}" class="nav-link {{ request()->routeIs('public.schools.*') ? 'active' : '' }}">Direktori Sekolah</a>
                <a href="{{ route('public.regions.index') }}" class="nav-link {{ request()->routeIs('public.regions.*') ? 'active' : '' }}">Wilayah 3T</a>
                <a href="{{ route('public.statistics') }}" class="nav-link {{ request()->routeIs('public.statistics') ? 'active' : '' }}">Statistik & Dampak</a>
            </nav>

            <div class="d-flex align-center gap-2">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm">Dashboard Admin</a>
                    @elseif(auth()->user()->isSchool())
                        <a href="{{ route('school.dashboard') }}" class="btn btn-dark btn-sm">Dashboard Sekolah</a>
                    @else
                        <a href="{{ route('contributor.dashboard') }}" class="btn btn-dark btn-sm">Dashboard Donatur</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar Akun</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
            <div class="container mt-4">
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
                            <strong>Terdapat kesalahan:</strong>
                            <ul style="margin-left: 20px; margin-top: 4px;">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-inner">
                <div class="footer-col" style="max-width:380px;">
                    <div class="d-flex align-center gap-2 mb-2">
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:var(--primary);color:#fff;border-radius:4px;font-size:14px;font-weight:700;">SA</span>
                        <strong style="color:var(--text-primary);font-size:16px;">SambungAsa</strong>
                    </div>
                    <p class="text-sm text-muted">
                        Platform keterbukaan informasi dan kolaborasi bantuan pendidikan untuk sekolah-sekolah di wilayah Tertinggal, Terdepan, dan Terluar (3T) di seluruh Indonesia.
                    </p>
                    <p class="text-xs text-muted mt-3">
                        Mendukung <strong>SDGs 10: Berkurangnya Kesenjangan</strong>.
                    </p>
                </div>

                <div class="footer-col">
                    <h4>Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('public.needs.index') }}">Cari Kebutuhan</a></li>
                        <li><a href="{{ route('public.schools.index') }}">Direktori Sekolah 3T</a></li>
                        <li><a href="{{ route('public.regions.index') }}">Daftar Wilayah 3T</a></li>
                        <li><a href="{{ route('public.statistics') }}">Statistik Publik</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Partisipasi</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('register', ['role' => 'contributor']) }}">Daftar Jadi Donatur / Relawan</a></li>
                        <li><a href="{{ route('register', ['role' => 'school']) }}">Daftarkan Sekolah 3T</a></li>
                        <li><a href="{{ route('login') }}">Masuk ke Sistem</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Komitmen Platform</h4>
                    <p class="text-sm text-muted" style="max-width: 280px;">
                        SambungAsa bukan marketplace dan tidak memproses transaksi uang. Seluruh bantuan disalurkan langsung secara transparan dengan pelacakan akuntabel.
                    </p>
                </div>
            </div>

            <div style="border-top:1px solid var(--border);padding-top:20px;" class="d-flex justify-between align-center flex-wrap gap-2 text-xs text-muted">
                <div>&copy; {{ date('Y') }} SambungAsa — Inisiatif Gotong Royong Pendidikan 3T Indonesia.</div>
                <div>Aplikasi Web Responsif (Non-PWA / Online Database).</div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
