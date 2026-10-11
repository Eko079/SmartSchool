<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
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

        if (($user->role ?? '') !== 'superadmin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Super admin only.'], 403);
            }

            return redirect()->route('admin.dashboard')->with('error', 'Halaman ini hanya untuk Super Admin.');
        }

        return $next($request);
    }
}
