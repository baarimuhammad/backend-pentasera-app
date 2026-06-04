<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     * Returns JSON 403 if user hasn't verified their email.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() ||
            ($request->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail())) {
            return response()->json([
                'message' => 'Email belum diverifikasi. Silakan cek email Anda.',
                'requires_verification' => true,
            ], 403);
        }

        return $next($request);
    }
}
