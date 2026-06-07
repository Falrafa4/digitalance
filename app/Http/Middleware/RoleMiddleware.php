<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->errorResponse('Tidak terautentikasi. Silahkan login.', 401);
        }

        if (! method_exists($user, 'getRole')) {
            return $this->errorResponse('Method peran tidak didefinisikan.', 500);
        }

        if (! in_array($user->getRole(), $roles)) {
            return $this->errorResponse('Akses ditolak. Anda tidak memiliki akses.', 403);
        }

        return $next($request);
    }
}
