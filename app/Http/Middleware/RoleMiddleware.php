<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Cek apakah user memiliki salah satu role yang diizinkan
        if (empty($roles) || in_array($user->role, $roles)) {
            return $next($request);
        }

        // Jika tidak memiliki akses, redirect ke dashboard dengan error
        return redirect()->route('dashboard')
            ->with('error', 'Akses ditolak. Anda tidak memiliki izin yang cukup.');
    }
}