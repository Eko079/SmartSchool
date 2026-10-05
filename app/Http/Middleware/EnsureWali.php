<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureWali
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('wali')->user() ?? Auth::user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('portal.login');
        }

        if (($user->role ?? 'wali') !== 'wali') {
            Auth::guard('wali')->logout();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Wali only.'], 403);
            }

            return redirect()->route('portal.login')->withErrors(['identifier' => 'Akun ini bukan akun wali.']);
        }

        return $next($request);
    }
}
