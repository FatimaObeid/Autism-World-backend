<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {

            // If request comes from API (Flutter)
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated'
                ], 401);
            }

            return redirect()->route('login');
        }

        if (Auth::user()->role !== $role) {

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden'
                ], 403);
            }

            abort(403, "You are not authorized to access this page");
        }

        return $next($request);
    }
}
