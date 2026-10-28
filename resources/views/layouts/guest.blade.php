<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Autentikasi') — SambungAsa</title>
    <link rel="stylesheet" href="{{ asset('css/sambungasa.css') }}">
</head>
<body style="background-color: var(--surface); min-height: 100vh; display: flex; flex-direction: column;">
    <header style="padding: 24px; text-align: center;">
        <a href="{{ url('/') }}" class="navbar-brand justify-center">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;background:var(--primary);color:#fff;border-radius:6px;font-size:18px;font-weight:700;">SA</span>
            <span style="font-size:24px;">Sambung<span style="color:var(--primary);">Asa</span></span>
            <span class="brand-badge">3T</span>
        </a>
    </header>

    <main style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="width: 100%; max-width: 480px;">
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
            @if($errors->any())
                <div class="alert alert-danger">
                    <span>⚠</span>
                    <div>
                        <ul style="margin-left: 16px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')

            <div class="text-center mt-4 text-xs text-muted" style="text-align: center;">
                <a href="{{ url('/') }}">← Kembali ke Halaman Utama SambungAsa</a>
            </div>
        </div>
    </main>

    <footer style="text-align: center; padding: 16px; font-size: 12px; color: var(--neutral);">
        &copy; {{ date('Y') }} SambungAsa. Platform Kolaborasi Bantuan Pendidikan 3T.
    </footer>
</body>
</html>
