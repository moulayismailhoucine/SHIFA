<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Generic role middleware: checks the authenticated user/patient's role.
 * Usage in routes: middleware('role:admin') or middleware('role:doctor')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            // For web routes, redirect to login
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Users (admin/doctor) have a 'role' attribute
        if (property_exists($user, 'role') || isset($user->role)) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // For web routes, show access denied page or redirect
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => "Access denied. Required role: {$role}.",
            ], 403);
        }
        
        // For web routes, abort with 403 or redirect to dashboard
        abort(403, "Access denied. Required role: {$role}.");
    }
}
