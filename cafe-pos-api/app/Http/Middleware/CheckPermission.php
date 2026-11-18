<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $user = $request->user();

        if (!$user || !$user->role) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        $allowed = $user->role
            ->permissions()
            ->where('name', $permission)
            ->exists();

        if (!$allowed) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses.',
            ], 403);
        }

        return $next($request);
    }
}