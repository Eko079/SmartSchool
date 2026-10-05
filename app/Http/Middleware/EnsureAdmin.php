<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login');
        }

        if (($user->role ?? 'admin') === 'wali') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Admin only.'], 403);
            }

            return redirect()->route('portal.dashboard');
        }

        return $next($request);
    }
}
