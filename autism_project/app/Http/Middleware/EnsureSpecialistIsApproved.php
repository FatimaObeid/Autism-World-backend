<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSpecialistIsApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if the authenticated user has a specialist profile and if it is NOT approved
        if ($user && $user->specialist && $user->specialist->status !== 'approved') {
            return response()->json([
                'success' => false,
                'status'  => $user->specialist->status, // Sends 'pending' or 'declined'
                'message' => $user->specialist->status === 'pending'
                    ? 'Your profile is pending admin approval. Please wait.'
                    : 'Your profile registration has been declined.'
            ], 403); // 403 Forbidden catches this instantly in Flutter
        }

        return $next($request);
    }
}
