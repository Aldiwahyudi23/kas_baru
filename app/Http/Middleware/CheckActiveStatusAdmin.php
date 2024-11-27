<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveStatusAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan pengguna sudah login
        if (Auth::guard('admin')->check()) { // Gunakan guard admin jika Anda menggunakan guard berbeda
            $admin = Auth::guard('admin')->user(); // Ambil admin yang login
            if ($admin->is_active == 0) {
                // Jika tidak aktif, tampilkan SweetAlert yang tidak bisa ditutup
                return response()->view('errors.account_inactive'); // Ganti dengan view yang sesuai
            }
        }
        return $next($request);
    }
}
