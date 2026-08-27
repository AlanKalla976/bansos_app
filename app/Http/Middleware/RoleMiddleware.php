<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if ($request->is('admin/petugas') || $request->is('admin/petugas/*')) {
            $user = Auth::guard('petugas')->user();
        } elseif ($request->is('admin/lurah') || $request->is('admin/lurah/*')) {
            $user = Auth::guard('lurah')->user();
        } elseif ($request->is('admin') || $request->is('admin/*')) {
            $user = Auth::guard('admin')->user();
        } else {
            $user = Auth::guard('web')->user();
        }

        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $next($request);
    }
}
