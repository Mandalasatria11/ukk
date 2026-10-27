<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Support both comma-separated and multiple parameter lists
        $allowed = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $part) {
                $allowed[] = trim($part);
            }
        }

        // SUPER_ADMIN has access to ADMIN privileges
        if ($user->role === 'SUPER_ADMIN' && in_array('ADMIN', $allowed, true)) {
            return $next($request);
        }

        if (!in_array($user->role, $allowed, true)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Forbidden: role tidak memiliki akses'], 403);
            }
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
