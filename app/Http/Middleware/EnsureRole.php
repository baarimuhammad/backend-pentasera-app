<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        
        // Jika pengguna belum login atau rolenya tidak terdaftar di daftar role yang boleh lewat
        if (! $user || ! in_array($user->role, $roles)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki akses. Role yang dibutuhkan: ' . implode(', ', $roles),
            ], 403);
        }

        return $next($request);
    }
}
