<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Middleware untuk mengecek role pengguna dan memberikan akses sesuai role
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();
        
        // Cek apakah role user sesuai dengan yang diizinkan
        if (!in_array($user->role, $roles)) {
            // Redirect berdasarkan role user
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
                case 'seller':
                    return redirect()->route('seller.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
                case 'customer':
                    return redirect()->route('customer.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
                default:
                    return redirect()->route('login')->with('error', 'Role tidak valid.');
            }
        }

        // Tambahan: Cek verifikasi untuk customer (kecuali untuk route tertentu)
        if ($user->role === 'customer') {
            // Skip verification check for password reset routes
            $passwordResetRoutes = [
                'password.request',
                'password.send-reset-code',
                'password.reset.verify.form',
                'password.verify-reset-code',
                'password.reset.form',
                'password.reset'
            ];
            
            if (!in_array($request->route()->getName(), $passwordResetRoutes)) {
                if (!$user->isEmailVerified()) {
                    return redirect()->route('verification.email.notice');
                }
                if (!$user->isWaVerified()) {
                    return redirect()->route('verification.wa.notice');
                }
            }
        }

        return $next($request);
    }
}