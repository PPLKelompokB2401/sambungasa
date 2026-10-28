<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->status === 'suspended') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan (suspended) oleh administrator.');
        }

        if ($user->status === 'rejected') {
            return redirect()->back()->with('error', 'Akun Anda ditolak oleh admin. Alasan: ' . ($user->verification_notes ?? 'Data tidak valid.'));
        }

        return $next($request);
    }
}
