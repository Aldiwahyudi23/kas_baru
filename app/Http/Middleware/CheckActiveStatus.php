<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan pengguna sudah login
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->is_active == 0) {
                // Jika tidak aktif, tampilkan SweetAlert yang tidak bisa ditutup
                return response()->view('errors.account_inactive'); // Ganti dengan view yang sesuai
            }
        }

        return $next($request);
    }
}
