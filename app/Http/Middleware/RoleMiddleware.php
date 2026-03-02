<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (!method_exists($user, 'getRole')) {
            return response()->json([
                'message' => 'Role method not defined.'
            ], 500);
        }

        if (!in_array($user->getRole(), $roles)) {
            return response()->json([
                'status' => false,
                'message' => 'Forbidden. You do not have access.',
            ], 403);
        }

        return $next($request);
    }
}
