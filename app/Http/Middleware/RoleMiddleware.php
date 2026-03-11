<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Cara pakai di routes
     * ->middleware('role:namaRole')
     * ->middleware('role:namaRole, namaRole2') -> multirole
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, String ...$roles): Response
    {
        // Kalau belum login, redirect ke login
        if(!auth()->check()){
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Cek role
        if(!in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini. ');
        }

        return $next($request);
    }
}
