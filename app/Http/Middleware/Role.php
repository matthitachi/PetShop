<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (HttpResponse)  $next
     * @param  bool  $role
     */
    public function handle(Request $request, Closure $next, $role = false): Response
    {
        $user = User::query()->where('id', Auth::id())->firstOrFail();
        if ($role == 'admin' && $user->is_admin) {
            return $next($request);
        }

        if ($role == 'user' && ! $user->is_admin) {
            return $next($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred!',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
