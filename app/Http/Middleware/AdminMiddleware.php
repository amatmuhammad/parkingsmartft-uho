<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman admin.');
        }

        // 2. Cek apakah user memiliki role admin
        $user = Auth::user();
        
        // Asumsi: Anda memiliki kolom 'role' di tabel users
        if ($user->role !== 'admin') {
            // Jika bukan admin, redirect ke dashboard user dengan pesan error
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Hanya administrator yang dapat mengakses halaman tersebut.');
        }

        // 3. Jika semua kondisi terpenuhi, lanjutkan request
        return $next($request);
    }
}
