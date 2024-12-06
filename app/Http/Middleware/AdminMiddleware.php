<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah admin login menggunakan guard 'admin'
        if (Auth::guard('admin')->check()) {
            return $next($request);  // Lanjutkan jika admin
        }

        // Jika bukan admin atau tidak login, redirect ke halaman login
        return redirect()->route('login')->with('error', 'Harap login terlebih dahulu');
    }
}
