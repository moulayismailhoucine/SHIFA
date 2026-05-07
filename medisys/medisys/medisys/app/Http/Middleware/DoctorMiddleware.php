<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isDoctor()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Doctor role required.',
            ], 403);
        }

        return $next($request);
    }
}
